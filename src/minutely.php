<?php
error_reporting(E_ALL);
set_time_limit(0);
require "include.php";

// Tmp Notes
$res = $db->query("SELECT `id`,`expires` FROM `tmpnotes` WHERE 1");
foreach($res as $row)
{
	if(intval($row["expires"]) < time())
	{
		$db->query("DELETE FROM `tmpnotes` WHERE `id`=?", "s", $row["id"]);
	}
}
