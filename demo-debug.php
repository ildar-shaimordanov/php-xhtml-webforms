<html>

<head>
<title>PHP :: Debug</title>
<style type="text/css">

/**
 *
 * Common styles
 *
 */
body	{
	background-color: #fff;
	color: #000;
	font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
}
h1	{
	color: #39c;
	margin: 0;
	margin-left: 260px;
	text-align: left;
}

/**
 *
 * Menu styles
 *
 */
div#TOC	{
	float: left;
	left: 5px;
	position: fixed !important;
	position: absolute;
	width: 250px;
}
div#TOC ul	{
	border: 1px solid #39c;
}
div#TOC ul,
div#TOC ul li	{
	list-style-type: none;
	margin: 0;
	padding: 0;
}
div#TOC ul li a	{
	color: #39c;
	display: block;
	padding: 2px;
	text-decoration: none;
	width: auto !important;
	width: 98%;
}
div#TOC ul li a:hover	{
	background-color: #39c;
	color: #fff;
	font-weight: bold;
}

.fullDemo	{
	font-weight: bold;
}
.sources	{
	font-style: italic;
	font-weight: bold;
}

div#note	{
	border-top: 1px solid #39c;
	margin-top: 20px;
	padding: 15px;
}

/**
 *
 * Content styles
 *
 */
div#BODY	{
	margin-left: 260px;
}
h2	{
	background-color: #39c;
	color: #fff;
	font-size: 1.3em;
	margin: 0;
	margin-bottom: 1px;
	padding: 5px;
}
div.elapsed	{
	font-size: 0.8em;
	padding-bottom: 20px;
}
code	{
	border: 1px dashed #39c;
	display: block;
	padding: 5px;
}

</style>
<script type="text/javascript">

function debugConsoleCollapseToggle(dbg_console, state)
{
	document.body.style.paddingTop = state ? '250px' : '';
}

</script>
</head>

<body>

<h1>PHP :: Debug</h1>

<div id="TOC">
<h2>Table of Contents</h2>
<ul>
<li><a href="/">Index</a></li>
<li><a href="?" class="sources">Start of demo</a></li>
<li><a href="?show=dump">Variable dump</a></li>
<li><a href="?show=display">Variable display</a></li>
<li><a href="?show=trace">Backtrace</a></li>
<li><a href="?show=all" class="fullDemo">Full demo within HTML</a></li>
<li><a href="?show=console" class="fullDemo">Debug console</a></li>
<li><a href="Core/" class="sources">Sources</a></li>
</ul>

<div id="note">
<ol>
<li>Use a mouse click over a type description of complex variables to toggle the collapse state of them. </li>
<li>Scroll the debugger display to see a hidden information. </li>
<li>Press Shift+Ctrl+Alt to show or to hide the debug console. </li>
</ol>
</div>
</div>


<div id="BODY">
<?php

// Include debugger and use predefined performance and behavior
require_once 'Core/Debug.php';
Debug::useNice();
if ( @$_GET['show'] == 'console' ) {
    Debug::useConsole();
}

$timer =& Debug::getTimer();
$timer->start();

function elapsedTime()
{
    printf('<div class="elapsed">Executed about %.8f seconds</div>', $GLOBALS['timer']->stop());
}

class myClass
{

    function staticMethod()
    {
        Debug::backtrace();
    }

    function dynamicMethod()
    {
        $this->staticMethod($GLOBALS);
    }

}

if ( in_array(@$_GET['show'], array('dump', 'display', 'trace', 'all', 'console')) ) {

?>

<h2>Empty execution time</h2>

<?php

$timer->start();
elapsedTime();

}

if ( in_array(@$_GET['show'], array('trace', 'all', 'console')) ) {

?>

<h2>Backtrace from the dynamically called method</h2>

<?php

$timer->start();
$object =& new myClass();
$object->dynamicMethod();
elapsedTime();

?>

<h2>Backtrace from the statically called method</h2>

<?php

$timer->start();
myClass::staticMethod($GLOBALS);
elapsedTime();

}

if ( in_array(@$_GET['show'], array('display', 'all', 'console')) ) {

?>

<h2>Display the complex variable</h2>

<?php

$timer->start();
echo Debug::display($GLOBALS, true, true);
elapsedTime();

}

if ( in_array(@$_GET['show'], array('dump', 'all', 'console')) ) {

?>

<h2>Collapsed dump of the complex variable</h2>

<?php

$timer->start();
echo Debug::dump($GLOBALS, true);
elapsedTime();

}

if ( in_array(@$_GET['show'], array('dump', 'display', 'trace', 'all', 'console')) ) {

?>

<h2>Full execution time</h2>

<?php

elapsedTime();

}

if ( ! in_array(@$_GET['show'], array('dump', 'display', 'trace', 'all', 'console')) ) {

?>

<h2>Source file</h2>

<?php

highlight_file(__FILE__);

}

?>

</div>

</body>
</html>

