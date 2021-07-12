<?php

$filename = 'includes/image-images/image.jpg';

if ( @$_GET['show_image'] == 'yes' ) {

#	require_once '../../Core.php';
	require_once 'Image.php';

	$image =& new Image($filename);
	$image->addBorder();
	$image->output();

	exit;

}

$title = 'Bordering of image';

?>

<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=border&show_image=yes" />

