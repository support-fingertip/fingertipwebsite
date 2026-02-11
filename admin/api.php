<?php
session_start();

// ── Auth Check ─────────────────────────────────────────────────────────────
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ── CSRF Check (for POST requests) ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($csrf) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

// ── Data Paths ─────────────────────────────────────────────────────────────
define('DATA_DIR', __DIR__ . '/data/');
define('BLOGS_FILE', DATA_DIR . 'blogs.json');
define('WORKS_FILE', DATA_DIR . 'works.json');

// ── Helpers ────────────────────────────────────────────────────────────────

/**
 * Read JSON file and return array
 */
function read_json(string $file): array {
    if (!file_exists($file)) {
        return [];
    }
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

/**
 * Write array to JSON file
 */
function write_json(string $file, array $data): bool {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
}

/**
 * Sanitize string input
 */
function clean(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate next ID for a dataset
 */
function next_id(array $items): int {
    if (empty($items)) {
        return 1;
    }
    $ids = array_column($items, 'id');
    return max($ids) + 1;
}

/**
 * Create slug from title
 */
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ── Route Action ───────────────────────────────────────────────────────────
$action = $_GET['action'] ?? '';

switch ($action) {

    // ── GET: Fetch All Blogs ────────────────────────────────────────────
    case 'get_blogs':
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => read_json(BLOGS_FILE)]);
        break;

    // ── GET: Fetch All Works ────────────────────────────────────────────
    case 'get_works':
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => read_json(WORKS_FILE)]);
        break;

    // ── POST: Save Blog (Create or Update) ──────────────────────────────
    case 'save_blog':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Validate required fields
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '') $errors[] = 'Title is required.';
        if ($category === '') $errors[] = 'Category is required.';
        if ($author === '') $errors[] = 'Author is required.';
        if ($content === '') $errors[] = 'Content is required.';

        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode(' ', $errors)];
            header('Location: blog.php' . (isset($_POST['id']) && $_POST['id'] ? '?action=edit&id=' . intval($_POST['id']) : '?action=new'));
            exit;
        }

        $blogs = read_json(BLOGS_FILE);
        $edit_id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : 0;

        // Parse tags
        $tags_raw = trim($_POST['tags'] ?? '');
        $tags = $tags_raw !== '' ? array_map('trim', explode(',', $tags_raw)) : [];
        $tags = array_filter($tags, fn($t) => $t !== '');

        $now = date('c');

        $blog_entry = [
            'title'          => clean($title),
            'slug'           => slugify($title),
            'category'       => clean($category),
            'author'         => clean($author),
            'featured_image' => clean(trim($_POST['featured_image'] ?? '')),
            'content'        => $content, // Allow markdown, but stored as-is; rendered with htmlspecialchars on output
            'tags'           => array_map('clean', $tags),
            'publish_date'   => clean(trim($_POST['publish_date'] ?? date('Y-m-d'))),
            'status'         => in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'draft',
            'updated_at'     => $now,
        ];

        if ($edit_id > 0) {
            // Update existing
            $found = false;
            foreach ($blogs as &$b) {
                if ($b['id'] === $edit_id) {
                    $blog_entry['id'] = $edit_id;
                    $blog_entry['created_at'] = $b['created_at'] ?? $now;
                    $b = $blog_entry;
                    $found = true;
                    break;
                }
            }
            unset($b);
            if (!$found) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Blog post not found.'];
                header('Location: blog.php');
                exit;
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Blog post updated successfully.'];
        } else {
            // Create new
            $blog_entry['id'] = next_id($blogs);
            $blog_entry['created_at'] = $now;
            array_unshift($blogs, $blog_entry); // newest first
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Blog post created successfully.'];
        }

        write_json(BLOGS_FILE, $blogs);
        header('Location: blog.php');
        exit;

    // ── POST: Delete Blog ───────────────────────────────────────────────
    case 'delete_blog':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $delete_id = intval($_GET['id'] ?? 0);
        if ($delete_id <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid blog post ID.'];
            header('Location: blog.php');
            exit;
        }

        $blogs = read_json(BLOGS_FILE);
        $original_count = count($blogs);
        $blogs = array_values(array_filter($blogs, fn($b) => $b['id'] !== $delete_id));

        if (count($blogs) < $original_count) {
            write_json(BLOGS_FILE, $blogs);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Blog post deleted successfully.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Blog post not found.'];
        }

        header('Location: blog.php');
        exit;

    // ── POST: Toggle Blog Status ────────────────────────────────────────
    case 'toggle_blog_status':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $toggle_id = intval($_GET['id'] ?? 0);
        if ($toggle_id <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid blog post ID.'];
            header('Location: blog.php');
            exit;
        }

        $blogs = read_json(BLOGS_FILE);
        foreach ($blogs as &$b) {
            if ($b['id'] === $toggle_id) {
                $b['status'] = ($b['status'] === 'published') ? 'draft' : 'published';
                $b['updated_at'] = date('c');
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Status changed to ' . $b['status'] . '.'];
                break;
            }
        }
        unset($b);

        write_json(BLOGS_FILE, $blogs);
        header('Location: blog.php');
        exit;

    // ── POST: Save Work (Create or Update) ──────────────────────────────
    case 'save_work':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Validate required fields
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $client = trim($_POST['client'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $short_description = trim($_POST['short_description'] ?? '');

        if ($title === '') $errors[] = 'Project title is required.';
        if ($client === '') $errors[] = 'Client name is required.';
        if ($category === '') $errors[] = 'Category is required.';
        if ($short_description === '') $errors[] = 'Short description is required.';

        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode(' ', $errors)];
            header('Location: work.php' . (isset($_POST['id']) && $_POST['id'] ? '?action=edit&id=' . intval($_POST['id']) : '?action=new'));
            exit;
        }

        $works = read_json(WORKS_FILE);
        $edit_id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : 0;

        // Parse technologies
        $tech_raw = trim($_POST['technologies'] ?? '');
        $technologies = $tech_raw !== '' ? array_map('trim', explode(',', $tech_raw)) : [];
        $technologies = array_filter($technologies, fn($t) => $t !== '');

        $now = date('c');

        $work_entry = [
            'title'                => clean($title),
            'client'               => clean($client),
            'category'             => clean($category),
            'project_image'        => clean(trim($_POST['project_image'] ?? '')),
            'short_description'    => clean($short_description),
            'detailed_description' => trim($_POST['detailed_description'] ?? ''),
            'technologies'         => array_map('clean', $technologies),
            'timeline'             => clean(trim($_POST['timeline'] ?? '')),
            'challenge'            => trim($_POST['challenge'] ?? ''),
            'solution'             => trim($_POST['solution'] ?? ''),
            'results'              => trim($_POST['results'] ?? ''),
            'updated_at'           => $now,
        ];

        if ($edit_id > 0) {
            // Update existing
            $found = false;
            foreach ($works as &$w) {
                if ($w['id'] === $edit_id) {
                    $work_entry['id'] = $edit_id;
                    $work_entry['created_at'] = $w['created_at'] ?? $now;
                    $w = $work_entry;
                    $found = true;
                    break;
                }
            }
            unset($w);
            if (!$found) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Work item not found.'];
                header('Location: work.php');
                exit;
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Work item updated successfully.'];
        } else {
            // Create new
            $work_entry['id'] = next_id($works);
            $work_entry['created_at'] = $now;
            array_unshift($works, $work_entry);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Work item created successfully.'];
        }

        write_json(WORKS_FILE, $works);
        header('Location: work.php');
        exit;

    // ── POST: Delete Work ───────────────────────────────────────────────
    case 'delete_work':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $delete_id = intval($_GET['id'] ?? 0);
        if ($delete_id <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid work item ID.'];
            header('Location: work.php');
            exit;
        }

        $works = read_json(WORKS_FILE);
        $original_count = count($works);
        $works = array_values(array_filter($works, fn($w) => $w['id'] !== $delete_id));

        if (count($works) < $original_count) {
            write_json(WORKS_FILE, $works);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Work item deleted successfully.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Work item not found.'];
        }

        header('Location: work.php');
        exit;

    // ── Unknown Action ──────────────────────────────────────────────────
    default:
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
        break;
}
