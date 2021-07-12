<?php

if ( @$_GET['show_image'] == 'yes' ) {

#    require_once '../../Core.php';
    require_once 'Image.php';

    $width  = 150;
    $height = 150;

    $color = rand(0, 0xffffff);
    $options = array(
        // Background options
        'bgcolor' => 0xffffff ^ $color,
        // Text options
        'color' => $color,
        'angle' => (microtime(true) * 30) % 360,
        'font' => PROJECT_CORE_DEFAULTS_PATH . '/Fonts/TTF/times.ttf',
    );

    $image =& new Image(null, $width, $height);

    $image->addBackground($options);

    $image->addText('Hello, world!', $options);
    $image->output();

    exit;

}

$title = 'Text image';

?>

<h4>Click on image for change effect</h4>
<a href="<?=$_SERVER['SCRIPT_NAME']?>?demo=text-rotated&show_image=yes" target="_blank" onclick="(function(a){if ( ! document.images ) { return; }var img = a.firstChild;while ( img.tagName.toUpperCase() != 'IMG' && ( img = img.nextSibling ) ) {}if ( ! img ) { return; }img.src = a.href + ( a.href.indexOf('?') == -1 ? '?' : '&' ) + (new Date()).getTime();a.blur();})(this); return event.returnValue = false;"><img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=text-rotated&show_image=yes" border="0" /></a>

