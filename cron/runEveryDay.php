<?php
require_once("run.php");

$task = new cron();
//$task->deleteInactiveUsers(); // Do not execute it for now
$task->deleteNotCompletedFiles();
$task->updateUpgrades();
?>
