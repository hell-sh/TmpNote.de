<?php
require "../src/include.php";
$json = [];
if(!empty($_POST["id"]))
{
	if($res = $db->query("SELECT `type`, `expires`, `formatting`, `encrypted` FROM `tmpnotes` WHERE `id`=?", "s", $_POST["id"]))
	{
		$json["type"] = $res[0]["type"];
		$json["formatting"] = $res[0]["formatting"];
		if($res[0]["type"] == "0")
		{
			$json["expires"] = $res[0]["expires"];
			$json["encrypted"] = $res[0]["encrypted"];
		}
	}
	else
	{
		$json["error"] = "Unknown TmpNote.";
	}
}
else
{
	$json["error"] = "Missing argument(s).";
}
header("Content-Type: application/json");
echo json_encode($json);
