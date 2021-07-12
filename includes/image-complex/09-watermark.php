<?php

$filename = 'includes/image-images/image.jpg';

//
// Watermark of text
//
if ( @$_GET['show_image'] == 'yes' && @$_GET['watermark'] == 'txt' ) {

#    require_once '../../Core.php';
    require_once 'Image/Watermark.php';

    $image =& new Image_Watermark($filename);
    $image->output(null, array(
        'halign' => Image::ALIGN_RIGHT,
        'valign' => Image::ALIGN_TOP,

        'width'  => 100,
        'height' => 50,

        'font' => PROJECT_CORE_DEFAULTS_PATH . '/Fonts/TTF/times.ttf',
        'color' => '#fff',
        'alpha-color' => 64,
    ));

    exit;

}


//
// Watermark of image
//
if ( @$_GET['show_image'] == 'yes' && @$_GET['watermark'] == 'img' ) {

#    require_once '../../Core.php';
    require_once 'Image/Thumbnail.php';
    require_once 'Image/Watermark.php';

    // Create a watermark from the thumbnailed original image
    $thumb =& new Image_Thumbnail($filename);
    $thumb->render();

    $image =& new Image_Watermark($filename);
    $image->output(null, array(
        'image'  => $thumb,

        'halign' => Image::ALIGN_RIGHT,
        'valign' => Image::ALIGN_TOP,

        'transparent' => 64,
    ));

    exit;

}


$title = 'Watermark image';

?>

<table border="1">
<tr>
<td>
<h3>Watermark of a text</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=watermark&watermark=txt&show_image=yes" />
</td>
<td>
<h3>Watermark of an image</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=watermark&watermark=img&show_image=yes" />
</td>
</tr>
</table>

