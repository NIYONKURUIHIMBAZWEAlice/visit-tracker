<?php
require_once 'config.php';

// only admins can open this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$visit = new Visit();

// pagination
$perPage = 4;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$total = $visit->totalAll();
$pages = max(1, ceil($total / $perPage));
if ($page > $pages) { $page = $pages; }
$offset = ($page - 1) * $perPage;

$rows = $visit->getAllPage($perPage, $offset);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 30px; color: #333; }
        .box { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; }
        .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .top a { background: #d9534f; color: #fff; padding: 8px 14px; border-radius: 5px; text-decoration: none; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #2c3e50; color: #fff; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #eef3f8; }

        .badge { padding: 4px 10px; border-radius: 12px; font-size: 13px; color: #fff; }
        .active { background: #28a745; }
        .quit { background: #dc3545; }

        .small { color: #777; font-size: 13px; }

        .pager { margin-top: 20px; text-align: center; }
        .pager a { background: #2c3e50; color: #fff; padding: 6px 12px; border-radius: 4px; text-decoration: none; margin: 0 5px; }
    </style>
</head>
<body>
<div class="box">
    <div class="top">
        <div>
            <h2>Admin: all visitors</h2>
            <p>Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
        </div>
        <a href="logout.php">Logout</a>
    </div>

    <table>
        <tr>
            <th>Name</th>
            <th>Came in</th>
            <th>Time spent</th>
            <th>Logout time</th>
        </tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td><?= $r['login_time'] ?></td>
            <td><?= gmdate('H:i:s', $r['seconds']) ?></td>
            <td>
                <?php if ($r['logout_time']): ?>
                    <?= $r['logout_time'] ?>
                <?php elseif ($r['online']): ?>
                    <span class="badge active">Active</span>
                <?php else: ?>
                    <span class="badge quit">Quit</span>
                    <span class="small">last seen <?= $r['last_seen'] ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="4">No visits yet.</td></tr>
        <?php endif; ?>
    </table>

    <div class="pager">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
        <?php endif; ?>

        Page <?= $page ?> of <?= $pages ?>

        <?php if ($page < $pages): ?>
            <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>