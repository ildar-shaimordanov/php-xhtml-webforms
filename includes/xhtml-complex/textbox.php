<?php

$fi = array(
	/**
	 *
	 * Form attributes
	 *
	 */
	'element'	=> 'form',
	'attrs'	=> array(
		'method'	=> 'post',
		'action'	=> '?demo=textbox',
		'onsubmit'	=> 'this.tb_hidden.value = Math.random()',
	),

	/**
	 *
	 * Hidden textbox
	 *
	 */
	array(
		'element'	=> 'textbox',
		'attrs'	=> array(
			'type'	=> 'hidden',
			'name'	=> 'tb_hidden',
		),
	),

	/**
	 *
	 * Text textbox
	 *
	 */
	array(
		'element'	=> 'textbox',
		'attrs'	=> array(
			'type'	=> 'text',
			'name'	=> 'tb_text',
			'style'	=> 'width: 100%',
		),
		'meta'	=> array(
			'label'	=> 'Text:',
		),
	),

	/**
	 *
	 * Password textbox
	 *
	 */
	array(
		'element'	=> 'textbox',
		'attrs'	=> array(
			'type'	=> 'password',
			'name'	=> 'tb_password',
			'style'	=> 'width: 100%',
		),
		'meta'	=> array(
			'label'	=> 'Password:',
		),
	),

	/**
	 *
	 * Textarea textbox
	 *
	 */
	array(
		'element'	=> 'textbox',
		'attrs'	=> array(
			'type'	=> 'textarea',
			'name'	=> 'tb_textarea',
			'style'	=> 'width: 100%',
		),
		'meta'	=> array(
			'label'	=> 'Textarea:',
		),
	),

	/**
	 *
	 * Submit button
	 *
	 */
	array(
		'element'	=> 'input',
		'attrs'	=> array(
			'type'	=> 'submit',
			'value'	=> 'Submit',
		),
	),
);

?>