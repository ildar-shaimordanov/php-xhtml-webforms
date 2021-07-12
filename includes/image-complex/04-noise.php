<?php

if ( @$_GET['show_image'] == 'yes' ) {

#	require_once '../../Core.php';
	require_once 'Image.php';

	$image =& new Image(null, 200, 200);
	$image->addBackground();

	$options = array(
		'noise' => 'auto',
	);

	$image->addNoise($options);
	$image->output();

	exit;

}

$title = 'Noising of image';

?>

<h4>Click on image for change effect</h4>
<a href="<?=$_SERVER['SCRIPT_NAME']?>?demo=noise&show_image=yes" target="_blank" onclick="(function(a){if ( ! document.images ) { return; }var img = a.firstChild;while ( img.tagName.toUpperCase() != 'IMG' && ( img = img.nextSibling ) ) {}if ( ! img ) { return; }img.src = a.href + ( a.href.indexOf('?') == -1 ? '?' : '&' ) + (new Date()).getTime();a.blur();})(this); return event.returnValue = false;"><img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=noise&show_image=yes" border="0" /></a>

