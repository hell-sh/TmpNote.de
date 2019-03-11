<?php
require "../src/include.php";
$json = [];
if(!empty($_POST["encrypted"]) && !empty($_POST["formatting"]) && isset($_POST["type"]) && isset($_POST["time"]))
{
	$time=intval($_POST["time"]);
	if($time<180||$time>31104000)
	{
		$json["error"] = "Your TmpNote must expire in 12 months or less and must exist for at least 3 minutes.";
	}
	else
	{
		$type=intval($_POST["type"]);
		if($type==0||$type==1)
		{
			foreach(json_decode(file_get_contents("/var/www/tmpnote.de/formattings.json"), true) as $formatting)
			{
				if($formatting["value"] == $_POST["formatting"])
				{
					$formatting = $formatting["value"];
					break;
				}
			}
			if($formatting != "")
			{
				if(strlen($_POST["encrypted"]) > 32000)
				{
					$json["error"] = "Your note can't be bigger than 32 KB.";
				}
				else
				{
					$id;
					do
					{
						$id=str_rand(rand(3,11));
						if(!$db->query("SELECT `id` FROM `tmpnotes` WHERE `id`=?","s",$id))
						{
							break;
						}
					}
					while(true);
					$db->query("INSERT INTO `tmpnotes` (`id`,`encrypted`,`formatting`,`type`,`expires`) VALUES (?,?,?,?,?)", "sssii", $id, $_POST["encrypted"], $formatting, $type, (time()+$time));
					$json["id"] = $id;
				}
			}
			else
			{
				$json["error"] = "Formatting must be one of the https://tmpnote.de/formattings.json `value`s.";
			}
		}
		else
		{
			$json["error"] = "Type must be 0 or 1.";
		}
	}
}
else
{
	$json["error"] = "Missing argument(s).";
}
header("Content-Type: application/json");
echo json_encode($json);
