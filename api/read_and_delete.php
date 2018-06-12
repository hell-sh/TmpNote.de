<?php
require "../src/include.php";
$json = [];
if(!empty($_POST["id"])&&!empty($_POST["key"]))
{
	if(strlen($_POST["key"])==8)
	{
		if($res = $db->query("SELECT `encrypted` FROM `tmpnotes` WHERE `id`=? AND `type`='1'", "s", $_POST["id"]))
		{
			$row = $res[0];
			require "../src/phpseclib1.0.7/Crypt/DES.php";
			$cipher = new Crypt_DES(CRYPT_DES_MODE_ECB);
			$cipher->setKey($_POST["key"]);
			if(substr($cipher->decrypt(base64_decode($row["encrypted"])),0,7)=="TmpNote")
			{
				$db->query("DELETE FROM `tmpnotes` WHERE `id`=?", "s", $_POST["id"]);
				$json["encrypted"]=$row["encrypted"];
			}
		}
		else
		{
			$json["error"] = "TmpNote unknown or not type 1.";
		}
	}
	else
	{
		$json["error"] = "Key length must be 8.";
	}
}
else
{
	$json["error"] = "Missing argument(s).";
}
header("Content-Type: application/json");
echo json_encode($json);
