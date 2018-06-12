<?php
header("Cache-Control: max-age=432000");
if($_SERVER["REQUEST_METHOD"]!="GET")
{
	http_response_code($_SERVER["REQUEST_METHOD"]=="OPTIONS"?200:405);
	header("Allow: OPTIONS, GET");
	exit;
}
if(empty($title))
{
	die("No title has been specified.");
}
if(empty($icon))
{
	$icon = "https://tmpnote.de/assets/img/icon.png";
}
if(empty($description))
{
	$description = "TmpNote.de is a free and open-source service for end-to-end encrypted notes and code snippets.";
}
?>
<!DOCTYPE html>
<html lang="<?=((in_array($subdomain,$supported_languages))?$subdomain:'en'); ?>">
<head>
	<title><?=$title; ?> | TmpNote.de</title>
	<link rel="icon" type="image/png" href="<?=$icon; ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.5/css/uikit.min.css">
	<link rel="stylesheet" href="/assets/css/main.css">
	<script src="https://cdn.hell.sh/jquery/3.3.1/core.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.5/js/uikit.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.5/js/uikit-icons.min.js"></script>
	<meta name="title" content="<?=$title; ?> | TmpNote.de">
	<meta name="description" content="<?=$description; ?>">
	<meta name="keywords" content="Notes, Snippets, Code Snippets, End-to-end encrypted, Secure, TmpNote">
	<meta name="copyright" content="Copyright (c) 2018, Hellsh Ltd.">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:title" content="<?=$title; ?> | TmpNote.de">
	<meta name="twitter:creator" content="@hellshltd">
	<meta name="twitter:description" content="<?=$description; ?>">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="TmpNote.de">
	<meta property="og:title" content="<?=$title; ?>">
	<meta property="og:description" content="<?=$description; ?>">
	<?php
	unset($icon);
	unset($title);
	unset($description);
	if(empty($canonical_actual_url))
	{
		$req_url = $_SERVER["REQUEST_URI"];
		if(substr($req_url, -1) == ".")
		{
			$req_url = substr($req_url, 0, -1);
		}
		if(substr($req_url, -4) == ".php")
		{
			$req_url = substr($req_url, 0, -4);
		}
		if(substr($req_url, -5) == "index")
		{
			$req_url = substr($req_url, 0, -5);
		}
		$canonical = strtolower($req_url);
	}
	else
	{
		$canonical = $canonical_actual_url;
	}
	?>
	<meta property="og:url" content="https://tmpnote.de<?=$canonical; ?>">
	<link rel="canonical" href="https://tmpnote.de<?=$canonical; ?>">
</head>
<body>