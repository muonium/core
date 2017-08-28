<?php
require_once("run.php");

$task = new cron();
$task->deleteInactiveUsers();
$task->deleteNotCompletedFiles();
$task->updateUpgrades();
?>
