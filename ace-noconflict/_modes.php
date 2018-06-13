<?php
echo "<option value=''>{tmpnote.formatting.plain}</option>\n";
foreach(scandir(".") as $file)
{
	if(substr($file, 0, 5) == "mode-" && substr($file, -3) == ".js")
	{
		$mode = substr($file, 5, -3);
		echo "<option value='ace/mode/".$mode."'>".ucwords(str_replace("_", " ", $mode))."</option>\n";
	}
}
?>