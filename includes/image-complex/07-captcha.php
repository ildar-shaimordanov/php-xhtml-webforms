<?php

session_start();


// Captcha text
$storage  = 'captcha';

if ( @$_GET['show_image'] == 'yes' ) {

#    require_once '../../Core.php';
    require_once 'Image/Captcha.php';

    // Captcha size
    $size   = 150;
    $width  = $size;
    $height = $size;

    // Captcha image options
    $text     = 'Hello, world!';
    $font     = PROJECT_CORE_DEFAULTS_PATH . '/Fonts/TTF/times.ttf';

    // Captcha creating and outputing
    $captcha =& new Image_Captcha($text, $width, $height);

    // Unlawful usage detected
    if ( empty($_SESSION[$storage]['referer']) || $_SESSION[$storage]['referer'] != md5($_SERVER['DOCUMENT_ROOT']) ) {
    	$_SESSION[$storage]['referer'] = null;
    	$captcha->disableCaptcha();
    }

    $captcha->output(null, array(
        'font' => $font,
        'noise' => 'auto',
    ));

    exit;

}

// Store image status to the session
$_SESSION[$storage]['referer'] = md5($_SERVER['DOCUMENT_ROOT']);

$title = 'Captcha image';

?>

<ul>
<li>For view varous effects - Click on image</li>
<li>For view antispam method - Clear the cache of Your favorite browser and click on image again or open immediately 
<b><?='http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']?>?demo=captcha&show_image=yes</b></li>
</ul>

<a href="<?=$_SERVER['SCRIPT_NAME']?>?demo=captcha&show_image=yes" target="_blank" onclick="(function(a){if ( ! document.images ) { return; }var img = a.firstChild;while ( img.tagName.toUpperCase() != 'IMG' && ( img = img.nextSibling ) ) {}if ( ! img ) { return; }img.src = a.href + ( a.href.indexOf('?') == -1 ? '?' : '&' ) + (new Date()).getTime();a.blur();})(this); return event.returnValue = false;"><img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=captcha&show_image=yes" border="0" /></a>

