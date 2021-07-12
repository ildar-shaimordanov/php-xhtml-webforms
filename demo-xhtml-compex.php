<?php

//
// Make all includes
//
define('SKIP_INCLUDE_PATH', true);
require_once 'Core.php';

//
// Include all files of the library ...
// ... estimate the spended time
//
require_once 'XHTML.php';

//
// Safe including from a request
//
$demoList = glob('includes/xhtml-complex/*.php');
$demo = strtolower(preg_replace('/[^a-z0-9]/i', '', @$_GET['demo']));
$demo = 'includes/xhtml-complex/' . $demo . '.php';
$demo = array_search($demo, $demoList);

if ( false !== $demo ) {

	try {

		include_once $demoList[$demo];

		if ( ! isset($form) ) {
			$form =& XHTML::loadFromArray($fi);
		}

	} catch (Exception $e) {
		print $e;
		exit;
	}

}

//
// Initiate ob-handler for place CSSes ans JSes within the HEAD
//
ob_start(array('XHTML', 'assocBuffer'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>XHTML :: Form demo</title>
<link rel="stylesheet" href="includes/xhtml-complex/styles-main.css" type="text/css" />
<link rel="stylesheet" href="includes/xhtml-complex/styles-form.css" type="text/css" />
</head>

<body>

<div id="menu">
<ul>
<li><a href="/">Index</a></li>
<li><a class="unlist" href="?">Start</a></li>
<li><a class="unlist" href="Core/">Sources</a></li>
<li><a class="unlist" href="#INCLUDE">Includes</a></li>

<?php

//
// Safe creating of HTML-menu from list of files
//
foreach ($demoList as $k => $v) {
	preg_match('/includes\/xhtml-complex\/([^\.]+)\.php/', $v, $matches);

?>
<li><a href="?demo=<?=$matches[1]?>"><?=ucfirst($matches[1])?></a></li>
<?php

}

?>

</ul>
</div>

<?php

if ( ! isset($form) ) {

	// Displays file
	highlight_file(__FILE__);

} else {

	// Imports submitted data from the request to the form
	$form->import();

	// Displays xhtml-form
	$form->output();

	// Shows debug information
//	Debug::dump(Core_Object::getStaticProperty('Core_Object', 'assoc'));
	Debug::dump(Core_Object::getStaticProperty('XHTML_Form', 'cacheFormCtrl'));

	// Displays various info about submit and form
	function demoPrint($desc, $value)
	{
//		echo '<div class="dump"><div class="dumpHeader">' . $desc . '</div>' . Debug::dump($value, true) . '</div>';
		echo '<pre><b>' . $desc . '</b><br />' . print_r($value, true) . '</pre>';
	}

	demoPrint('XHTML_Form->isXXX()',    array('isSubmitted' => (int)$form->isSubmitted(), 'isValid' => (int)$form->isValid()));
	demoPrint('XHTML_Form->getValue()', $form->getValue());
	demoPrint('GET',                    $_GET);
	demoPrint('POST',                   $_POST);
	demoPrint('FILES',                  $_FILES);
	demoPrint('Form Array Image',       XHTML::saveToArray($form));
	demoPrint('XHTML_Form->()',         $form);

	echo '<div style="margin-left: 450px;"><a name="INCLUDE"></a>';
	highlight_file($demoList[$demo]);
	echo '</div>';

}

?>

</body>
</html>

