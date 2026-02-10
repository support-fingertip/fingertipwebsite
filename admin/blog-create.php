<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid security token');
    }
    
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $featured_image = trim($_POST['featured_image'] ?? '');
    $author = trim($_POST['author'] ?? 'Fingertip Plus Team');
    $status = $_POST['status'] ?? 'draft';
    
    // Validate
    $errors = [];
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($slug)) $errors[] = 'Slug is required';
    if (empty($content)) $errors[] = 'Content is required';
    
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            if ($pdo) {
                // Check if slug already exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn() > 0) {
                    $errors[] = 'Slug already exists. Please use a different slug.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, author, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$title, $slug, $excerpt, $content, $featured_image, $author, $status]);
                    
                    header('Location: /admin/blog-list.php?success=1');
                    exit;
                }
            }
        } catch (Exception $e) {
            error_log("Blog create error: " . $e->getMessage());
            $errors[] = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post - Admin Panel</title>
    <link rel="stylesheet" href="/admin/assets/admin.css">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="admin-page">
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-content">
            <div class="page-header">
                <h1>Create New Blog Post</h1>
                <div class="page-actions">
                    <a href="/admin/blog-list.php" class="btn btn-secondary">Back to Posts</a>
                </div>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo sanitize($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="blog-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-grid">
                    <div class="form-main">
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" required 
                                   value="<?php echo sanitize($_POST['title'] ?? ''); ?>"
                                   placeholder="Enter post title">
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug * <small>(URL-friendly version of title)</small></label>
                            <input type="text" id="slug" name="slug" required 
                                   value="<?php echo sanitize($_POST['slug'] ?? ''); ?>"
                                   placeholder="post-url-slug">
                            <small>Will be used in URL: /blog/your-slug-here</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" rows="3" 
                                      placeholder="Brief description (shown in listings)"><?php echo sanitize($_POST['excerpt'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Content *</label>
                            <textarea id="content" name="content" class="tinymce-editor"><?php echo sanitize($_POST['content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-sidebar">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="draft" <?php echo ($_POST['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($_POST['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" id="author" name="author" 
                                   value="<?php echo sanitize($_POST['author'] ?? 'Fingertip Plus Team'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="featured_image">Featured Image URL</label>
                            <input type="url" id="featured_image" name="featured_image" 
                                   value="<?php echo sanitize($_POST['featured_image'] ?? ''); ?>"
                                   placeholder="https://images.unsplash.com/...">
                            <small>Recommended: Use Unsplash or upload via button below</small>
                        </div>
                        
                        <div class="form-group">
                            <button type="button" onclick="uploadImage()" class="btn btn-secondary btn-block">Upload Image</button>
                            <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                        </div>
                        
                        <div id="imagePreview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" style="max-width: 100%; border-radius: 4px;">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Create Post</button>
                    </div>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '.tinymce-editor',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            images_upload_handler: function (blobInfo, success, failure) {
                uploadImageBlob(blobInfo.blob(), success, failure);
            }
        });
        
        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
            document.getElementById('slug').value = slug;
        });
        
        // Image upload
        function uploadImage() {
            document.getElementById('imageUpload').click();
        }
        
        document.getElementById('imageUpload').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('image', this.files[0]);
                
                fetch('/admin/upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('featured_image').value = data.url;
                        document.getElementById('previewImg').src = data.url;
                        document.getElementById('imagePreview').style.display = 'block';
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Upload error: ' + error);
                });
            }
        });
        
        function uploadImageBlob(blob, success, failure) {
            const formData = new FormData();
            formData.append('image', blob);
            
            fetch('/admin/upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    success(data.url);
                } else {
                    failure('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                failure('Upload error: ' + error);
            });
        }
    </script>
</body>
</html>
