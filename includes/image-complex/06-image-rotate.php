<?php

$filename = 'includes/image-images/image.jpg';


if ( @$_GET['show_image'] == 'yes' ) {

#    require_once '../../Core.php';
    require_once 'Image.php';

    $image =& new Image($filename);

    $rotateList = array(
        'left',
        'right',
        'reverse',
        'mirror',
        'flip',
    );
    $rotate = isset($_GET['angle']) && ( is_numeric($_GET['angle']) || in_array(strtolower($_GET['angle']), $rotateList) ) 
        ? $_GET['angle'] 
        : 0;

    switch ( $rotate ) {
    case Image::ROTATE_LEFT:
    	$rotate = 90;
        $image->rotate($rotate);
        break;
    case Image::ROTATE_RIGHT:
    	$rotate = -90;
        $image->rotate($rotate);
        break;
    case Image::ROTATE_REVERSE:
        $rotate = 180;
        $image->rotate($rotate);
        break;
    case Image::ROTATE_MIRROR:
        $image->mirror();
        break;
    case Image::ROTATE_FLIP:
        $image->flip();
        break;
    }

    $image->output();

    exit;

}


$title = 'Rotating of image';

?>

<table border="1">
<tr>
<td>
<h3>Original</h3>
<img src="<?=$filename?>" border="0" />
</td>
<td>
<h3>Mirror Horizontal</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=image-rotate&show_image=yes&angle=mirror" border="0" />
</td>
</tr>
<tr>
<td>
<h3>Mirror Vertical</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=image-rotate&show_image=yes&angle=flip" border="0" />
</td>
<td>
<h3>Reverse</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=image-rotate&show_image=yes&angle=reverse" border="0" />
</td>
</tr>
<tr>
<td>
<h3>Left</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=image-rotate&show_image=yes&angle=left" border="0" />
</td>
<td>
<h3>Right</h3>
<img src="<?=$_SERVER['SCRIPT_NAME']?>?demo=image-rotate&show_image=yes&angle=right" border="0" />
</td>
</tr>
</table>

