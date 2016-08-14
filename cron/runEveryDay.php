<?php
include_once("run.php");

$task = new cron();
$task->deleteInactiveUsers();
?>