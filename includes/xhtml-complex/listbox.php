<?php

/**
 *
 * Default values
 *
 */
$value = array(
	'bas',
	'pas',
);

/**
 *
 * Available options
 *
 */
$options = array(
	'asm'	=> 'Assemblers',
	'bas'	=> 'Basic family (VB, VBA, VBScript)',
	'pas'	=> 'Pascal family',
	'c/c++'	=> 'C/C++ family',
	'js'	=> 'JavaScript/JScript',
	'unix'	=> 'Shell/Perl/PHP',
);

/**
 *
 * Form image
 *
 */
$fi = array(
	'element'	=> 'form',
	'attrs'	=> array(
		'method'	=> 'post',
		'action'	=> '?demo=listbox',
		'class'	=> 'listboxForm',
	),

	/**
	 *
	 * Checkbox list
	 *
	 */
	array(
		'element'	=> 'listbox',
		'attrs'	=> array(
			'type'	=> 'checkbox',
			'name'	=> 'listbox[checkbox]',
#			'value'	=> $value,
			'options'	=> $options,
		),
		'meta'	=> array(
			'label'	=> 'Checkboxes:',
		),
	),

	/**
	 *
	 * Multiple select
	 *
	 */
	array(
		'element'	=> 'listbox',
		'attrs'	=> array(
			'type'	=> 'multiple',
			'name'	=> 'listbox[multiple]',
#			'value'	=> $value,
			'options'	=> $options,
		),
		'meta'	=> array(
			'label'	=> 'Multiple Select:',
		),
	),

	/**
	 *
	 * Radios list
	 *
	 */
	array(
		'element'	=> 'listbox',
		'attrs'	=> array(
			'type'	=> 'radio',
			'name'	=> 'listbox[radio]',
			'options'	=> $options,
		),
		'meta'	=> array(
			'label'	=> 'Radios:',
		),
	),

	/**
	 *
	 * Single select
	 *
	 */
	array(
		'element'	=> 'listbox',
		'attrs'	=> array(
			'type'	=> 'select',
			'name'	=> 'listbox[select]',
			'options'	=> $options,
		),
		'meta'	=> array(
			'label'	=> 'Single select:',
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
			'class'	=> 'listboxButton',
		),
	),
);

?>