<?php
require_once 'config.php';

if (isset($_SESSION['visit_id'])) {
    $visit = new Visit();
    $visit->ping($_SESSION['visit_id']);
}