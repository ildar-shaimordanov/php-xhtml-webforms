<?php

$fi = array(
	/**
	 *
	 * Mime types
	 *
	 */
	array(
		'element'	=> 'select',
		'attrs'	=> array(
#			'type'	=> 'checkbox',
			'name'	=> 'mimes',
			'multiple'	=> 'multiple',
			'size'	=> 10,
		),
		'meta'	=> array(
			'label'	=> 'Mime types:',
			'source'	=> 'setMimes',
			'submit'	=> 'POST',
			'validator'	=> 'required eigenvalue',
			'message'	=> 'You must specify one mime at least',
		),
	),

	array(
		'element'	=> 'input',
		'attrs'	=> array(
			'type'	=> 'submit',
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
		'action'	=> '?demo=eigenvalue',
		'class'	=> 'eigenvalueForm',
	),
);

//
// Source callback for the MIME types
//
function setMimes($mimeSelect)
{
	require_once 'Mime/Types.php';

	$mt =& new Mime_Types();
	$mimes = $mt->getMime();
	$mimeSelect->setOptions($mimes);
}

//
// Register the source routine
//
require_once 'XHTML.php';
XHTML::registerSource('setMimes');

?>