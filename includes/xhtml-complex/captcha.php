<?php

$fi = array(
	/**
	 *
	 * Form attributes
	 *
	 */
	'element' => 'form',
	'attrs' => array(
		'method' => 'post',
		'action' => '?demo=captcha',
	),

	/**
	 *
	 * Login
	 *
	 */
	array(
		'element' => 'input',
		'attrs' => array(
			'type' => 'text',
			'name' => 'login',
		),
		'meta' => array(
			'label' => 'Login:',
		),
	),

	/**
	 *
	 * Password
	 *
	 */
	array(
		'element' => 'input',
		'attrs' => array(
			'type' => 'password',
			'name' => 'password',
		),
		'meta' => array(
			'label' => 'Password:',
		),
	),

	/**
	 *
	 * Captcha
	 *
	 */
	array(
		'element' => 'captcha',
		'attrs' => array(
			'name' => 'captcha',
		),
		'meta' => array(
			'label' => 'Verify:',
		),
	),

	/**
	 *
	 * Submit button
	 *
	 */
	array(
		'element' => 'input',
		'attrs' => array(
			'type' => 'submit',
			'value' => 'Submit',
		),
	),
);


session_start();

require_once 'Text/Password.php';
require_once 'XHTML/Captcha.php';

try {

	$result = XHTML_Captcha::generateImage(
		array(
			'Text_Password', 
			'create', 
		), 
		array(
		), 
		array(
			'font' => PROJECT_CORE_DEFAULTS_PATH . '/Fonts/TTF/times.ttf',
			'copyright-text' => 'XHTML Forms demo',
		));

	if ( $result ) {
		exit;
	}

} catch (Exception $e) {
	print $e;
	exit;
}

?>