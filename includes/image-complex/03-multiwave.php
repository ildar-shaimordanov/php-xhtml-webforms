<?php

if ( @$_GET['show_image'] == 'yes' ) {

#    require_once '../../Core.php';
    require_once 'Image.php';

    $image =& new Image(null, 200, 200);
    $image->addBackground();
    $image->addText('Hello, world!', array(
        'size'   => 20,
        'font'   => PROJECT_CORE_DEFAULTS_PATH . '/Fonts/TTF/times.ttf',
    ));
    $image->multiwave();
    $image->output();

    exit;

}

$title = 'Multiwaving of image';

?>

<h4>Click on image for change effect</h4>
<a href="<?=$_SERVER['SCRIPT_NAME']?>?demo=multiwave&show_image=yes" target="_blank" onclick="(function(a){if ( ! document.images ) { return; }var img = a.firstChild;while ( img.tagName.toUpperCase() != 'IMG' && ( img = img.nextSibling ) ) {}if ( ! img ) { return; }img.src = a.href + ( a.href.indexOf('?') == -1 ? '?' : '&' ) + (new Date()).getTime();a.blur();})(this); return event.returnValue = false;"><img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=multiwave&show_image=yes" border="0" /></a>

