<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$visit = new Visit();
$userId = $_SESSION['user_id'];

// pagination
$perPage = 4;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$total = $visit->total($userId);
$pages = max(1, ceil($total / $perPage));
if ($page > $pages) { $page = $pages; }
$offset = ($page - 1) * $perPage;

$rows = $visit->getPage($userId, $perPage, $offset);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 30px; color: #333; }
        .box { max-width: 800px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
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
        .out { background: #6c757d; }

        .pager { margin-top: 20px; text-align: center; }
        .pager a { background: #2c3e50; color: #fff; padding: 6px 12px; border-radius: 4px; text-decoration: none; margin: 0 5px; }
    </style>
</head>
<body>
<div class="box">
    <div class="top">
        <div>
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p>Time on site now: <strong id="timer">00:00:00</strong></p>
        </div>
        <a href="logout.php">Logout</a>
    </div>

    <h3>Your visit history</h3>
    <table>
        <tr>
            <th>Login time</th>
            <th>Logout / last seen</th>
            <th>Time spent</th>
            <th>Status</th>
        </tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= $r['login_time'] ?></td>
            <td><?= $r['logout_time'] ? $r['logout_time'] : $r['last_seen'] ?></td>
            <td><?= gmdate('H:i:s', $r['seconds']) ?></td>
            <td>
                <?php if ($r['logout_time']): ?>
                    <span class="badge out">Logged out</span>
                <?php elseif ($r['online']): ?>
                    <span class="badge active">Active</span>
                <?php else: ?>
                    <span class="badge quit">Quit</span>
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

    <script>
        // live timer
        var start = Date.now();
        setInterval(function () {
            var s = Math.floor((Date.now() - start) / 1000);
            var h = String(Math.floor(s / 3600)).padStart(2, '0');
            var m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
            var sec = String(s % 60).padStart(2, '0');
            document.getElementById('timer').textContent = h + ':' + m + ':' + sec;
        }, 1000);

        // tell the server we are still here every 30 seconds
        setInterval(function () { navigator.sendBeacon('ping.php'); }, 30000);

        // tell the server when the visitor closes the page (quit)
        window.addEventListener('pagehide', function () {
            navigator.sendBeacon('ping.php');
        });
    </script>
</body>
</html>