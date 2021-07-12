<?php


//
// Safe creating of HTML-menu from list of files
//
function _demoScrape($value)
{
	return preg_replace('/includes\/image-complex\/\d\d-([^\.]+)\.php/', '$1', $value);
}

$demoList = @glob('includes/image-complex/[0-9][0-9]-*.php');
$demoList = @array_combine(array_map('_demoScrape', $demoList), $demoList);
$demoList || $demoList = array();


//
// Get data from the request
//
$demo = strtolower(@$_GET['demo']);


//
// Load core library file
//
require_once 'Core.php';

//
// Load appropriate file
//
if ( in_array($demo, array_keys($demoList)) ) {
	ob_start();

	include_once $demoList[$demo];

	$ob_content = ob_get_clean();
}


$title = $_SERVER['SCRIPT_NAME'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO 8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="includes/image-styles/main.css" type="text/css" />
<title><?=$title?></title>
</head>

<body>

<div id="menu">
<ul>
<li><a href="/">Index</a></li>
<li><a class="unlist" href="?">Start</a></li>
<li><a class="unlist" href="Core/">Sources</a></li>
<li><a class="unlist" href="#INCLUDE">Includes</a></li>

<?php

foreach ($demoList as $k => $v) {

?>
<li><a href="?demo=<?=strtolower($k)?>"><?=ucfirst($k)?></a></li>
<?php

}

?>

</ul>
</div>


<div id="BODY">

<h2><?=$title?></h2>
<?=@$ob_content?>

<div class="CODE">
<a name="INCLUDE"></a>
<?php

highlight_file(isset($demoList[$demo]) 
	? $demoList[$demo] 
	: __FILE__);

?>
</div>

</div>

</body>
</html>

