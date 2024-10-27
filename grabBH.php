<?php
// CHECK AVAILABILITY OF THE API SITE
// check if reachable:
if (fsockopen("api.brick-hill.com",80,$errno,$errstr,20)) {
	// reachable by server
} else {
	// host not reachable
	exit('<html><head><title></title><script>alert("It seems that Brick Hill\'s API is not currently reachable; this tool cannot be used.");</script></head><body></body></html>');
}
// check http status 
$_ = curl_init("https://api.brick-hill.com/v1/assets/getPoly/1/1");
curl_exec($_);
$status = curl_getinfo($_,CURLINFO_HTTP_CODE);
curl_close($_);
if ($status != 200) {
	// status could be bad gateway, or some other cloudflare error, meaning the site is down.
	exit('<html><head><title></title><script>alert("It seems that Brick Hill\'s API is not currently available; this tool cannot be used.");</script></head><body></body></html>');
} else {
	// the server returned 200, success status so we do not exit
}
ob_start(); // to control output
if (isset($_POST['texture'])) {
	// parse JSON for texture value
	$jsonResp = json_decode(file_get_contents('https://api.brick-hill.com/v1/assets/getPoly/1/'.$_POST['itemID']));
	if (isset($jsonResp[0]->texture)) {// if a texture even exists
		// set the corresponding content type for the image we are downloading
		header('Content-Type: image/png');
		// tell the browser that we are downloading a file, and tell it what to download the file as.
		header('Content-Disposition: attachment; filename="'.$_POST['itemID'].'.png"');
		ob_clean();
		flush();
		// the first 8 chars of the value is asset://, which is not needed by the get api, so we skip them
		$assetID = substr($jsonResp[0]->texture,8);
		// get asset, output to http stream
		readfile('https://api.brick-hill.com/v1/assets/get/'.$assetID);
		exit();
	} else {// no texture value
		$ext = '<script>alert("No texture available.")</script>';
	}
}
if (isset($_POST['mesh'])) {
	// parse JSON for mesh value
	$jsonResp = json_decode(file_get_contents('https://api.brick-hill.com/v1/assets/getPoly/1/'.$_POST['itemID']));
	if (isset($jsonResp[0]->mesh)) {// if a mesh even exists
		//set a generic content type for an obj file
		header('Content-Type: application/octet-stream');
		// tell the browser that we are downloading the file, and tell it what to download the file as
		header('Content-Disposition: attachment; filename="'.$_POST['itemID'].'.obj"');
		ob_clean();
		flush();
		// the first 8 chars of the value is asset://, which is not needed by the get api, so we skip them
		$assetID = substr($jsonResp[0]->mesh,8);
		// get asset, output to http stream
		readfile('https://api.brick-hill.com/v1/assets/get/'.$assetID);
		exit();
	} else {// no mesh value
		$ext = '<script>alert("No mesh available.")</script>';
	}
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta charset="UTF-8">
		<meta name="robots" content="noindex">
		<title>Brick Hill Asset Grabber</title>
		<style type="text/css">
			* {
				font-family: Arial, sans-serif;
			}
		</style>
		<?=@$ext?>
	</head>
	<body>
		<form method="post">
			<input type="text" name="itemID" placeholder="Item ID" value="<?=@$_POST['itemID']?>">
			<input type="submit" value="Texture" name="texture">
			<input type="submit" value="Mesh" name="mesh">
		</form>
	</body>
</html>
