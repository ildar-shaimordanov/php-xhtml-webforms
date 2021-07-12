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

require_once 'Image.php';
require_once 'Image/Control/Form.php';
require_once 'XHTML.php';

// {{{

class Image_Control
{

	// {{{ properties

	var $_file;
	var $_mime;
	var $_thumbnail = false;

	// }}}
	// {{{

	function __construct($filename, $thumbnail=false)
	{
		if ( ! is_file($filename) || ! is_readable($filename) ) {
			throw new Image_Exception('Non-readable file: ' . $filename);
		}

		$info = getimagesize($filename);
		if ( ! preg_match('/image/', $info['mime']) ) {
			throw new Image_Exception('Non-image file: ' . $filename);
		}

		$this->_file = $filename;
		$this->_mime = $info['mime'];
		$this->thumbnail = $thumbnail;
	}

	// }}}
	// {{{

	function outputXhtmlForm($action=null)
	{
		$form =& new Image_Control_Form();
		XHTML::assocOutput();
		$form->output();
	}

	// }}}
	// {{{

	function outputXhtmlImage()
	{
		list (, , , $t) = getimagesize($this->_file);
		echo '<div id=""><img src="' . $this->_file . '" ' . $t . ' border="0" />';
	}

	// }}}
	// {{{

	function outputXhtmlThumbnail()
	{
	}

	// }}}
	// {{{

	function outputThumbnail($output=null, $options=array())
	{
		$image =& new Image_Thumbnail($this->_file);
		$image->output($output, $options);
	}

	// }}}

}

// }}}

?>