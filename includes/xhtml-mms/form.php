<?php

$mmsForm = array(
	/**
	 *
	 * Phone code
	 *
	 */
	array(
		'element'	=> 'text',
		'attrs'	=> array(
			'kind'	=> 'fieldset',
			'text'	=> 'Phone:',
		),
		array(
			'element'	=> 'select',
			'attrs'	=> array(
				'name'	=> 'phone[zone]',
			),
			'meta'	=> array(
				'source'	=> 'setPhoneZone',
				'submit'	=> 'POST',
				'validator'	=> 'required integer nonzero eigenvalue',
				'message'	=> 'You must specify the phone zone',
			),
		),
		array(
			'element'	=> 'input',
			'attrs'	=> array(
				'name'	=> 'phone[code]',
				'type'	=> 'text',
				'autocomplete'	=> 'off',
			),
			'meta'	=> array(
				'submit'	=> 'POST',
				'validator'	=> 'required length(7) integer',
				'message'	=> 'You must specify the phone code',
			),
		),
	),

	/**
	 *
	 * Multimedia uploads
	 *
	 */
	array(
		'element'	=> 'text',
		'attrs'	=> array(
			'kind'	=> 'fieldset',
			'text'	=> 'Multimedia:',
		),
		array(
			'element'	=> 'input',
			'attrs'	=> array(
				'name'	=> 'mms',
				'type'	=> 'file',
			),
			'meta'	=> array(
				'validator'	=> 'required maxfilesize(500000) mimetype(image/* application/x-shockwave-flash)',
				'message'	=> 'You must specify the valid file for upload',
			),
		),
	),

	/**
	 *
	 * Captcha
	 *
	 */
	array(
		'element'	=> 'text',
		'attrs'	=> array(
			'kind'	=> 'fieldset',
			'text'	=> 'Verification:',
		),
		array(
			'element'	=> 'captcha',
			'attrs'	=> array(
				'storage'	=> $captchaStorage,
#				'src'	=> $captchaSrc,
				'width'	=> $captchaWidth,
				'height'	=> $captchaHeight,
			),
			'meta'	=> array(
				'message'	=> 'The verification code is failed',
			),
		),
	),

	/**
	 *
	 * Buttons
	 *
	 */
	array(
		'element'	=> 'input',
		'attrs'	=> array(
			'type'	=> 'submit',
			'class'	=> 'button',
			'value'	=> 'Submit',
		),
	),

	/**
	 *
	 * Form attributes
	 *
	 */
	'element'	=> 'form',
	'attrs'	=> array(
		'method'	=> 'post',
#		'action'	=> './',
		'class'	=> 'mmsForm',
	),
);

$mmsFormSelect = array(
	'Select',
	902,
	904,
);

XHTML::registerSource('setPhoneZone');
function setPhoneZone($phoneZone)
{
	$phoneZone->setOptions($GLOBALS['mmsFormSelect']);
	$phoneZone->setDefault(0);
}

$form =& XHTML::loadFromArray($mmsForm);

/*
try {
	$form =& XHTML::loadFromArray($mmsForm);
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}
*/

?>