<?php
require_once __DIR__ . "/../Controller/CronController.php";

$isCli = (php_sapi_name() === 'cli');
$hasValidKey = isset($_GET['key']) && $_GET['key'] === 'CHANGE_THIS_TO_A_RANDOM_SECRET';

if (!$isCli && !$hasValidKey){
    http_response_code(403);
    die('Forbidden');
}

$controller = new CronController();
$cancelled = $controller->autoCancelStale();

echo "Auto-cancelled: {$cancelled}\n";