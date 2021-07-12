<?php

//
// Form image array
//
$fi = array(
	array(
		'element'	=> 'spinbutton',
		'attrs'	=> array(
			'name'	=> 'date[year]',
			'value'	=> (int)date('Y'),
		),
		'meta'	=> array(
			'validator'	=> 'required range(1900 ' . date('Y') . ')'
		),
	),
	array(
		'element'	=> 'select',
		'attrs'	=> array(
			'name'	=> 'date[month]',
			'value'	=> date('m'),
		),
		'meta'	=> array(
			'source'	=> 'setMonth',
			'validator'	=> 'eigenvalue',
		),
	),
	array(
		'element'	=> 'select',
		'attrs'	=> array(
			'name'	=> 'date[day]',
			'value'	=> date('d'),
		),
		'meta'	=> array(
			'source'	=> 'setDay',
			'validator'	=> 'eigenvalue',
		),
	),

	/**
	 *
	 * Submit
	 *
	 */
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
		'action'	=> '?demo=spinbutton',
		'class'	=> 'spinbuttonForm',
	),
);

//
// Source callback for the month selector
//
function setMonth($selector)
{
	$selector->setOptions(array(1 => 
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December',
	));
}

//
// Source callback for the month selector
//
function setDay($selector)
{
	// make array starting with index 1 (instead of default 0)
	$day = range(0, 31);
	unset($day[0]);

	$selector->setOptions($day);
}

//
// Register source routines
//
require_once 'XHTML.php';
XHTML::registerSource('setMonth');
XHTML::registerSource('setDay');

?>