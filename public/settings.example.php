<?php

// Settings
define("LOGGING_ENABLED", "0");
define("CHART_LABEL_COUNT", "20");

// Database Login
define("MYSQLI_HOST", "");
define("MYSQLI_DATABASE", "");
define("MYSQLI_USER", "");
define("MYSQLI_PASSWORD", "");

$db = new mysqli(MYSQLI_HOST, MYSQLI_USER, MYSQLI_PASSWORD, MYSQLI_DATABASE);

// emoji support
$db->set_charset('utf8mb4');
