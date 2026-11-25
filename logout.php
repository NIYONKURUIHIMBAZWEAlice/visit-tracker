<?php
require_once 'config.php';

if (isset($_SESSION['visit_id'])) {
    $visit = new Visit();
    $visit->end($_SESSION['visit_id']);
}

session_destroy();
header('Location: login.php');
exit;