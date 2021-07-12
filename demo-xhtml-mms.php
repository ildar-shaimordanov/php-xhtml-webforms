<?php

session_start();

#define('SKIP_INCLUDE_PATH', true);
require_once 'Core.php';

/**
 *
 * Captcha configuration
 *
 */
$captchaFont    = PROJECT_CORE_DEFAULTS_PATH . '/Fonts/TTF/times.ttf';
$captchaBarText = 'MMS-Center of Super Duper';

$captchaWidth   = 150;
$captchaHeight  = 150;

$captchaStorage  = 'XF_MMS_CAPTCHA';
$captchaSrc      = $_SERVER['SCRIPT_NAME'] . '?action=captcha';

/**
 *
 * Captcha generation
 *
 */
/*
$storage = isset($_REQUEST['storage']) 
	? $_REQUEST['storage'] 
	: $captchaStorage;

if ( @$_GET['action'] == 'captcha' ) {

	require_once 'Text/Password.php';
	require_once 'Image/Captcha.php';

	// Create captcha text
	$text = Text_Password::create();

	// Output of image
	$image =& new Image_Captcha($text, $captchaWidth, $captchaHeight);

	// Defend against spammers
	if ( empty($_SESSION[$storage]['referer']) || $_SESSION[$storage]['referer'] != md5($_SERVER['DOCUMENT_ROOT']) ) {
		$_SESSION[$storage]['referer'] = null;
		$image->disableCaptcha();
	}

	$image->output(null, array(
#		'noise' => 10,
		'font' => $captchaFont,
		'copyright-text' => $captchaBarText,
	));

	// Store captcha phrase and image status to the session
	$_SESSION[$storage]['captcha'] = $text;
	$_SESSION[$storage]['image-status'] = $result;

	exit;

}

// Store image status to the session
$_SESSION[$storage]['referer'] = md5($_SERVER['DOCUMENT_ROOT']);
*/

require_once 'Text/Password.php';
require_once 'XHTML/Captcha.php';

try {

	$result = XHTML_Captcha::generateImage(
		array(
			'Text_Password', 
			'create', 
		), 
		array(
			'storage' => $captchaStorage, 
#			'src' => $captchaSrc, 
			'width' => $captchaWidth, 
			'height' => $captchaHeight, 
		), 
		array(
#			'noise' => 10, 
			'font' => $captchaFont,
			'copyright-text' => $captchaBarText,
		));

	if ( $result ) {
		exit;
	}

} catch (Exception $e) {
	print $e;
	exit;
}

/**
 *
 * MMS mailing
 *
 */
require_once 'XHTML.php';
require_once 'XHTML/Form.php';

// Creates and validate form
require_once 'includes/xhtml-mms/form.php';

// Imports submitted data from the request to the form
$form->import();

if ( $form->isSubmitted() && $form->isValid() ) {
	require_once 'Mail.php';
	require_once 'Mail/mime.php';

	$submit = $form->getValue();

	// Mime type of MMS
	$type = $submit['mms']['type'];

	// Rename file as YYYY-MM-DD_hh-mm-ss.ext
	$name = pathinfo($submit['mms']['name']);
	$name = dirname($submit['mms']['tmp_name']) . '/' . date('Y-m-d_H-i-s') . '.' . $name['extension'];
	move_uploaded_file($submit['mms']['tmp_name'], $name);

	$phone = $submit['phone']['zone'];
	$phone = '+7' . $mmsFormSelect[$phone] . $submit['phone']['code'] . '@mms.super-duper-isp.com';

	$text = 'MMS-Internet Center sent the multimedia message for You';
	$hdrs = array(
		'From'	=> 'mms.center@localhost',
		'Subject'	=> 'MMS-Internet Center',
	);

	$mime =& new Mail_Mime();
	$mime->setTXTBody($text);
	$mime->addAttachment($name, $type);

	@$body = $mime->get();
	@$hdrs = $mime->headers($hdrs);

	$mailTransport = 'smtp';
	$mailTransport = 'mail';
	if ( $mailTransport == 'smtp' ) {
		$mailTransportParams = array(
			'host'	=> 'localhost',
			'port'	=> 25,
			'username'	=> '',
			'password'	=> '',
		);
	} else {
		$mailTransportParams = array();
	}
	$mail =& Mail::factory($mailTransport, $mailTransportParams);
	$mail->send($phone, $hdrs, $body);

	$_SESSION['send_status'] = array('MMS has been sent successfully.');

	header('Location: ' . $_SERVER['SCRIPT_NAME']);
	exit;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>MMS Center</title>
<link rel="stylesheet" href="includes/xhtml-mms/styles.css" type="text/css" />
</head>

<body>

<div id="menu">
<ul>
<li><a href="/">Index</a></li>
<li><a href="#">Start</a></li>
</ul>
</div>

<!--
<div class="message">
<ul>
<li>MMS has been sent successfully.</li>
</ul>
</div>
-->

<?php

if ( isset($_SESSION['send_status']) ) {
	$messages = (array)$_SESSION['send_status'];

	echo '<div class="message">';
	echo '<ul>';
	foreach ($_SESSION['send_status'] as $message) {
		echo '<li>' . $message . '</li>';
	}
	echo '</ul>';
	echo '</div>';

	unset($_SESSION['send_status']);
}

$form->output();

?>

<div id="copyright">
<a target=_top href="https://super-duper-isp.com">Super Duper: Telephony and Internet</a><br />
</div>

</body>
</html>

