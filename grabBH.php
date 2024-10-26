<?php
ob_start();
if (isset($_POST['texture'])) {
	$jsonResp = json_decode(file_get_contents('https://api.brick-hill.com/v1/assets/getPoly/1/'.$_POST['itemID']));
	if (isset($jsonResp[0]->texture)) {
		header('Content-Type: image/png');
		header('Content-Disposition: attachment; filename="'.$_POST['itemID'].'.png"');
		ob_clean();
		flush();
		readfile('https://api.brick-hill.com/v1/assets/get/'.substr($jsonResp[0]->texture,8));
		exit();
	} else {
		print('<script>alert("No texture available.")</script>'."\n");
	}
}
if (isset($_POST['mesh'])) {
	$jsonResp = json_decode(file_get_contents('https://api.brick-hill.com/v1/assets/getPoly/1/'.$_POST['itemID']));
	if (isset($jsonResp[0]->mesh)) {
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$_POST['itemID'].'.obj"');
		ob_clean();
		flush();
		readfile('https://api.brick-hill.com/v1/assets/get/'.substr($jsonResp[0]->mesh,8));
		exit();
	} else {
		print('<script>alert("No mesh available.")</script>'."\n");
	}
}
?>
<form method="post">
	<input type="text" name="itemID" placeholder="Item ID" value="<?=@$_POST['itemID']?>">
	<input type="submit" value="Texture" name="texture">
	<input type="submit" value="Mesh" name="mesh">
</form>
