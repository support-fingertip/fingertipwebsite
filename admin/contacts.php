<?php
require_once 'auth.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid security token');
    }
    
    $pdo = getDBConnection();
    if ($pdo) {
        if ($_POST['action'] === 'mark_read' && isset($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?");
            $stmt->execute([$_POST['id']]);
        } elseif ($_POST['action'] === 'mark_unread' && isset($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE contacts SET is_read = 0 WHERE id = ?");
            $stmt->execute([$_POST['id']]);
        } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$_POST['id']]);
        }
    }
    header('Location: /admin/contacts.php');
    exit;
}

// Get filters
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all'; // all, read, unread

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$contacts = [];
$total_contacts = 0;

try {
    $pdo = getDBConnection();
    
    if ($pdo) {
        // Build query
        $where = [];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(name LIKE ? OR email LIKE ? OR company LIKE ? OR message LIKE ?)";
            $search_term = "%$search%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }
        
        if ($filter === 'read') {
            $where[] = "is_read = 1";
        } elseif ($filter === 'unread') {
            $where[] = "is_read = 0";
        }
        
        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Get total count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts $where_clause");
        $stmt->execute($params);
        $total_contacts = $stmt->fetchColumn();
        
        // Get contacts
        $stmt = $pdo->prepare("
            SELECT * FROM contacts
            $where_clause
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute(array_merge($params, [$per_page, $offset]));
        $contacts = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Contacts error: " . $e->getMessage());
}

$total_pages = ceil($total_contacts / $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts - Admin Panel</title>
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-page">
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-content">
            <div class="page-header">
                <h1>Contact Submissions</h1>
                <div class="page-actions">
                    <button onclick="exportCSV()" class="btn btn-secondary">Export CSV</button>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters-bar">
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search contacts..." value="<?php echo sanitize($search); ?>">
                    <button type="submit" class="btn">Search</button>
                </form>
                
                <div class="filter-tabs">
                    <a href="?filter=all<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                       class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                        All (<?php echo $total_contacts; ?>)
                    </a>
                    <a href="?filter=unread<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                       class="filter-tab <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                        Unread
                    </a>
                    <a href="?filter=read<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                       class="filter-tab <?php echo $filter === 'read' ? 'active' : ''; ?>">
                        Read
                    </a>
                </div>
            </div>
            
            <!-- Contacts Table -->
            <?php if (empty($contacts)): ?>
                <div class="no-data">
                    <p>No contacts found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table" id="contactsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr class="<?php echo $contact['is_read'] ? '' : 'unread-row'; ?>">
                                    <td><strong><?php echo sanitize($contact['name']); ?></strong></td>
                                    <td><?php echo sanitize($contact['email']); ?></td>
                                    <td><?php echo sanitize($contact['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo sanitize($contact['company'] ?? 'N/A'); ?></td>
                                    <td><?php echo sanitize($contact['service_interest'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($contact['created_at'])); ?></td>
                                    <td>
                                        <?php if ($contact['is_read']): ?>
                                            <span class="badge badge-success">Read</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Unread</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell">
                                        <button onclick="viewContact(<?php echo $contact['id']; ?>)" class="btn-link">View</button>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                            <?php if ($contact['is_read']): ?>
                                                <input type="hidden" name="action" value="mark_unread">
                                                <button type="submit" class="btn-link">Mark Unread</button>
                                            <?php else: ?>
                                                <input type="hidden" name="action" value="mark_read">
                                                <button type="submit" class="btn-link">Mark Read</button>
                                            <?php endif; ?>
                                        </form>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-link text-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="message-row" id="message-<?php echo $contact['id']; ?>" style="display: none;">
                                    <td colspan="8">
                                        <div class="contact-message">
                                            <strong>Message:</strong><br>
                                            <?php echo nl2br(sanitize($contact['message'])); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        $query_string = http_build_query(array_filter([
                            'search' => $search,
                            'filter' => $filter !== 'all' ? $filter : null
                        ]));
                        $query_prefix = $query_string ? '&' : '';
                        ?>
                        
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $query_prefix . $query_string; ?>" class="pagination-btn">← Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $query_prefix . $query_string; ?>" 
                               class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $query_prefix . $query_string; ?>" class="pagination-btn">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
        function viewContact(id) {
            const messageRow = document.getElementById('message-' + id);
            if (messageRow.style.display === 'none') {
                messageRow.style.display = 'table-row';
            } else {
                messageRow.style.display = 'none';
            }
        }
        
        function exportCSV() {
            const table = document.getElementById('contactsTable');
            let csv = [];
            
            // Headers
            const headers = ['Name', 'Email', 'Phone', 'Company', 'Service', 'Date', 'Status'];
            csv.push(headers.join(','));
            
            // Rows
            const rows = table.querySelectorAll('tbody tr:not(.message-row)');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const rowData = [
                    cells[0].textContent.trim(),
                    cells[1].textContent.trim(),
                    cells[2].textContent.trim(),
                    cells[3].textContent.trim(),
                    cells[4].textContent.trim(),
                    cells[5].textContent.trim(),
                    cells[6].textContent.trim()
                ];
                csv.push(rowData.map(cell => `"${cell}"`).join(','));
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'contacts_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
        }
    </script>
</body>
</html>
