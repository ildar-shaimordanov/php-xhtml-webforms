<?php

/**
 * This is a control panel for the thumbnail creating
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * The PHP License, version 3.0
 *
 * Copyright (c) 1997-2005 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available through the world-wide-web at the following url:
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @author      Ildar N. Shaimordanov <ildar-sh@mail.ru>
 * @license     http://www.php.net/license/3_0.txt
 *              The PHP License, version 3.0
 */

require_once 'XHTML/Form.php';
require_once 'Image.php';

// {{{

class Image_Control_Form extends XHTML_Form
{

	// {{{

	function __construct($attrs=array())
	{
		$attrs['id'] = 'imageControlForm';
		parent::__construct($attrs);

		$this->appendContent();

		XHTML::assocOutputRegister('css', 'Image_Control_Form');
		XHTML::assocOutputRegister('js',  'Image_Control_Form');
	}

	// }}}
	// {{{

	function appendContent()
	{
		$formImage = array(
			//
			// Usage: image ID and method
			//
			array(
				array(
					'element' => 'input',
					'attrs' => array(
						'name' => 'image',
						'type' => 'hidden',
					),
					'meta' => array(
						'submit' => 'GET COOKIE POST',
						'validator' => 'eigenvalue',
					),
				),
				array(
					'element' => 'select',
					'attrs' => array(
						'name' => 'method',
						'options' => array(
							Image::METHOD_SCALE_MAX => 'Max Scale',
							Image::METHOD_SCALE_MIN => 'Min Scale',
							Image::METHOD_CROP      => 'Crop',
						),
					),
					'meta' => array(
						'label' => 'Method:',
						'submit' => 'GET COOKIE POST',
						'validator' => 'eigenvalue',
					),
				),
				'element' => 'text',
				'attrs' => array(
					'kind' => 'fieldset',
					'text' => 'Usage',
				),
			),
			//
			// Limits: width, height and percent
			//
			array(
				array(
					'element' => 'spinbox',
					'attrs' => array(
						'name' => 'width',
						'behavior' => '',
					),
					'meta' => array(
						'label' => 'Width:',
						'submit' => 'GET COOKIE POST',
						'validator' => 'integer range(1 100)',
					),
				),
				array(
					'element' => 'spinbox',
					'attrs' => array(
						'name' => 'height',
						'behavior' => '',
					),
					'meta' => array(
						'label' => 'Height:',
						'submit' => 'GET COOKIE POST',
						'validator' => 'integer range(1 100)',
					),
				),
				array(
					'element' => 'spinbox',
					'attrs' => array(
						'name' => 'percent',
						'behavior' => '',
					),
					'meta' => array(
						'label' => 'Percent:',
						'submit' => 'GET COOKIE POST',
						'validator' => 'integer range(0 100)',
					),
				),
				'element' => 'text',
				'attrs' => array(
					'kind' => 'fieldset',
					'text' => 'Limits',
				),
			),
			//
			// Alignment: horizontal and vertical
			//
			array(
				array(
					'element' => 'select',
					'attrs' => array(
						'name' => 'halign',
						'value' => Image::ALIGN_CENTER,
						'options' => array(
							Image::ALIGN_LEFT   => 'Left',
							Image::ALIGN_CENTER => 'Center',
							Image::ALIGN_RIGHT  => 'Right',
						),
					),
					'meta' => array(
						'label' => 'Horizontal:',
						'submit' => 'GET COOKIE POST',
						'validator' => 'eigenvalue',
					),
				),
				array(
					'element' => 'select',
					'attrs' => array(
						'name' => 'valign',
						'value' => Image::ALIGN_CENTER,
						'options' => array(
							Image::ALIGN_TOP    => 'Top',
							Image::ALIGN_CENTER => 'Center',
							Image::ALIGN_BOTTOM => 'Bottom',
						),
					),
					'meta' => array(
						'label' => 'Vertical:',
						'submit' => 'GET COOKIE POST',
						'validator' => 'eigenvalue',
					),
				),
				'element' => 'text',
				'attrs' => array(
					'kind' => 'fieldset',
					'text' => 'Alignment',
				),
			),
			//
			// Buttons
			//
			array(
				'element' => 'input',
				'attrs' => array(
					'type' => 'button',
					'value' => 'Border',
					'class' => 'button',
					'style' => 'background-color: #fff; color: #000;',
				),
			),
			array(
				'element' => 'input',
				'attrs' => array(
					'type' => 'button',
					'value' => 'Preview',
					'class' => 'button',
				),
			),
			array(
				'element' => 'input',
				'attrs' => array(
					'type' => 'submit',
					'value' => 'Save',
					'class' => 'button',
				),
			),
		);

		foreach ($formImage as $item) {
			$object =& XHTML::loadFromArray($item);
			$this->appendChild($object);
		}
	}

	// }}}
	// {{{

	/**
	 * Outputs stylesheets associated with the current HTML-element or appropriate class
	 *
	 * @param	void
	 * @return	void
	 * @access	public
	 */
	function outputCss()
	{

?>

/**
 *
 * Image Control Form styles
 */
#imageControlForm	{
	font-family: Verdana, Tahoma, 'Courier New', sans-serif;
	font-size: 12px;
	margin-left: auto;
	margin-right: auto;
	width: 195px;
}

#imageControlForm fieldset	{
	margin: 0 2px;
	padding: 8px 5px 10px 5px !important;
	padding: 0 5px 10px 5px;
}

#imageControlForm fieldset legend	{
	margin-bottom: 0 !important;
	margin-bottom: 8px;
}

#imageControlForm label	{
	float: left;
	f#ont-size: 11px;
	font-weight: bold;
	height: 20px;
	margin-right: 1px;
	text-align: right;
	width: 73px;
}

#imageControlForm select {
	font-family: Verdana, Tahoma, 'Courier New', sans-serif;
	margin: 0;
	padding: 0;
	width: 100px;
}

#imageControlForm div.spinbox	{
	width: 100px;
}
#imageControlForm div.spinbox[class="spinbox"]	{
	margin-left: 70px !important;
}
#imageControlForm div.spinbox input.spinboxvalue	{
	width: 82px;
}

#imageControlForm input.button	{
	font-family: Verdana, 'Courier New', Tahoma, sans-serif;
	font-size: 11px;
	margin-left: 80px !important;
	margin-left: 85px;
	margin-top: 5px;
	width: 100px;
}

<?

	}

	// }}}
	// {{{

	/**
	 * Outputs client-side scripts associated with the current HTML-element or appropriate class
	 *
	 * @param	void
	 * @return	void
	 * @access	public
	 */
	function outputJs()
	{

?>

if ( ! window.XHTMLDOM ) {
	window.XHTMLDOM = {};
}

if ( ! window.XHTMLDOM.initImageControlForm ) {

window.XHTMLDOM.initImageControlForm = function()
{
}

}

<?

	}

	// }}}

}

// }}}

?>