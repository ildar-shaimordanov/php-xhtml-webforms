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
 * @author	  Ildar N. Shaimordanov <ildar-sh@mail.ru>
 * @license	 http://www.php.net/license/3_0.txt
 *			  The PHP License, version 3.0
 */

require_once 'Image/Thumbnail.php';
require_once 'XHTML/Common.php';
require_once 'XHTML.php';

// {{{

class Image_Thumbnail_Control4 extends XHTML_Common
{

	// {{{ constants

	const CTRLID_NAME   = '__ITC_CTRLID';
	const CTRLID_PREFIX = '__xf_itc_';

	const MIN_WIDTH  = 50;
	const MIN_HEIGHT = 50;

	// }}}
	// {{{ properties

	var $_method;
	var $_action;
	var $_control;

	var $_imageId;
	var $_imageLink;
	var $_image;
	var $_imageType;

	var $_names;
	var $_values;
	var $_defaultValues;

	// }}}
	// {{{

	function & factory($load,$save)
	{
		static $itc = null;

		// Validate load and save callbacks
		if ( ! is_callable($load) ) {
			throw new Image_Thumbnail_Control_Exception('Load image callback failed');
			return false;
		}

		if ( ! is_callable($save) ) {
			throw new Image_Thumbnail_Control_Exception('Save image callback failed');
			return false;
		}

		// Initialize object
		if ( $itc == null ) {
			$itc = new Image_Thumbnail_Control4();
		}

		// Parse request and do action
		switch ( @$_GET['action'] ) {
		case '':
			break;
		}
	}

	// }}}
	// {{{

	function __construct($minWidth, $minHeight, $image, $imageLink=false)
	{
		// Server-side and client-side handlers
		$this->_method = 'get';
		$this->_action = $_SERVER['SCRIPT_NAME'];
		if ( isset($_COOKIE[Image_Thumbnail_Control4::CTRLID_NAME]) ) {
			$this->_control = $_COOKIE[Image_Thumbnail_Control4::CTRLID_NAME];
		} else {
			$this->_control = Image_Thumbnail_Control4::CTRLID_PREFIX . time();
			setcookie(Image_Thumbnail_Control4::CTRLID_NAME, $this->_control);
		}

		// Set image source
		if ( is_callable($image) ) {
			if ( isset($_GET['img']) ) {
				$this->_imageId = $_GET['img'];
			} elseif ( isset($_COOKIE['img']) ) {
				$this->_imageId = $_COOKIE['img'];
			} else {
				$this->_imageId = null;
			}
			@list($image, $imageLink) = (array)call_user_func($image, $this->_imageId);
			setcookie('img', $this->_imageId);
		}
		$this->_imageLink = $imageLink;
		$this->_image = $image;
		list($width, $height, $this->_imageType) = @getimagesize($image);

		// Set minimal width
		if ( $minWidth < Image_Thumbnail_Control4::MIN_WIDTH || $minWidth > $width ) {
			$minWidth = Image_Thumbnail_Control4::MIN_WIDTH;
		}

		// Set minimal height
		if ( $minHeight < Image_Thumbnail_Control4::MIN_HEIGHT || $minHeight > $height ) {
			$minHeight = Image_Thumbnail_Control4::MIN_HEIGHT;
		}

		// Set minimal, maximal and default values
		$this->_defaultValues = array(
			'method'  => array(
				Image_Thumbnail::METHOD_SCALE_MAX, 
				Image_Thumbnail::METHOD_CROP, 
				Image_Thumbnail::METHOD_SCALE_MAX,
			),
			'width'   => array(
				$minWidth,
				$width,
				min($minWidth, $width),
			),
			'height'  => array(
				$minHeight,
				$height,
				min($minHeight, $height),
			),
			'percent' => array(
				0,
				100,
				0,
			),
			'halign'  => array(
				Image_Thumbnail::ALIGN_LEFT,
				Image_Thumbnail::ALIGN_RIGHT,
				Image_Thumbnail::ALIGN_CENTER,
			),
			'valign'  => array(
				Image_Thumbnail::ALIGN_TOP,
				Image_Thumbnail::ALIGN_BOTTOM,
				Image_Thumbnail::ALIGN_CENTER,
			),
		);

		// Set names of the form controls
		$this->_names = array(
			'method'  => 'method',
			'width'   => 'width',
			'height'  => 'height',
			'percent' => 'percent',
			'halign'  => 'halign',
			'valign'  => 'valign',
		);

		$request = '_' . strtoupper($this->_method);

		// Set actual values from requests if they exists
		$this->_values = array();
		foreach ($this->_names as $k => $v) {
			if ( array_key_exists($v, $_GET) 
			&& $_GET[$v] >= $this->_defaultValues[$k][0] 
			&& $_GET[$v] <= $this->_defaultValues[$k][1] ) {
				$this->_values[$k] = $_GET[$v];
			} elseif ( array_key_exists($v, $_COOKIE) 
			&& $_COOKIE[$v] >= $this->_defaultValues[$k][0] 
			&& $_COOKIE[$v] <= $this->_defaultValues[$k][1] ) {
				$this->_values[$k] = $_COOKIE[$v];
			} else {
				$this->_values[$k] = $this->_defaultValues[$k][2];
			}
			setcookie($k, $this->_values[$k]);
		}

		if ( $this->_values['percent'] > 1 ) {
			$this->_values['percent'] /= 100;
		}

		// Handle requests
		switch ( @$_GET['action'] ) {
		case 'api':
			$this->outputJsApi();
			break;
		case 'apihelp':
			$this->outputJsApiHelp();
			break;
		case 'js':
			$this->outputJs();
			break;
		case 'css':
			$this->outputCss();
			break;
		case 'image':
			$this->outputImage();
			break;
		case 'thumb':
			$this->outputImageThumbnail();
			break;
		}

		XHTML::assocOutputRegister('css', $this);
		XHTML::assocOutputRegister('js',  $this);
	}

	// }}}
	// {{{

	function getControlId()
	{
		return $this->_control;
	}

	// }}}
	// {{{

	function getImage()
	{
		return $this->_image;
	}

	// }}}
	// {{{

	function getImageLink()
	{
		return $this->_imageLink;
	}

	// }}}
	// {{{

	function getMaxHeight()
	{
		return $this->_defaultValues['height'][1];
	}

	// }}}
	// {{{

	function getMaxSize()
	{
		return $this->getMaxWidth() . ' x ' . $this->getMaxHeight();
	}

	// }}}
	// {{{

	function getMaxWidth()
	{
		return $this->_defaultValues['width'][1];
	}

	// }}}
	// {{{

	function getMinHeight()
	{
		return $this->_defaultValues['height'][0];
	}

	// }}}
	// {{{

	function getMinSize()
	{
		return $this->getMinWidth() . ' x ' . $this->getMinHeight();
	}

	// }}}
	// {{{

	function getMinWidth()
	{
		return $this->_defaultValues['width'][0];
	}

	// }}}
	// {{{

	function outputCss()
	{

?>

.imageThumbnailControlForm	{
	font-family: Verdana, Tahoma, 'Courier New', sans-serif;
	font-size: 12px;
	margin-left: auto;
	margin-right: auto;
	width: 195px;
}

.imageThumbnailControlForm fieldset	{
	margin: 0 2px;
	padding: 8px 5px 10px 5px !important;
	padding: 0 5px 10px 5px;
}

.imageThumbnailControlForm fieldset legend	{
	margin-bottom: 0 !important;
	margin-bottom: 8px;
}

.imageThumbnailControlForm label	{
	float: left;
	f#ont-size: 11px;
	font-weight: bold;
	height: 20px;
	margin-right: 1px;
	text-align: right;
	width: 73px;
}

.imageThumbnailControlForm div.spinbox	{
	width: 100px;
}
.imageThumbnailControlForm div.spinbox[class="spinbox"]	{
	margin-left: 70px !important;
}

.imageThumbnailControlForm div.spinbox input.spinboxvalue	{
	width: 82px;
}

.imageThumbnailControlForm select {
	font-family: Verdana, Tahoma, 'Courier New', sans-serif;
	margin: 0;
	padding: 0;
	width: 100px;
}

.imageThumbnailControlForm input.button	{
	margin-left: 80px !important;
	margin-left: 85px;
	margin-top: 5px;
	width: 100px;
}

<?php

	}

	// }}}
	// {{{

	function outputHtmlForm()
	{
		$this->_htmlForm->output();
	}

	// }}}
	// {{{

	function outputHtmlControl()
	{
?>

<form id="<?=$this->_control?>_form" method="<?=strtolower($this->_method)?>" action="<?=$this->_action?>">
<input type="hidden" name="img" value="<?=$this->_imageId?>" />
<table class="thumbnailControl">
<?php

		$this->outputHtmlControlSelect('method', 'method', array(
			Image_Thumbnail::METHOD_SCALE_MAX => 'Max Scale',
			Image_Thumbnail::METHOD_SCALE_MIN => 'Min Scale',
			Image_Thumbnail::METHOD_CROP	  => 'Crop',
		));

		$this->outputHtmlControlSpinbutton('width', 'width');
		$this->outputHtmlControlSpinbutton('height', 'height');
		$this->outputHtmlControlSpinbutton('percent', 'percent');

		$this->outputHtmlControlSelect('halign', 'horizontal', array(
			Image_Thumbnail::ALIGN_LEFT   => 'Left',
			Image_Thumbnail::ALIGN_CENTER => 'Center',
			Image_Thumbnail::ALIGN_RIGHT  => 'Right',
		));

		$this->outputHtmlControlSelect('valign', 'vertical', array(
			Image_Thumbnail::ALIGN_TOP	=> 'Top',
			Image_Thumbnail::ALIGN_CENTER => 'Center',
			Image_Thumbnail::ALIGN_BOTTOM => 'Bottom',
		));

?>
<tr>
<td><span>Border:</span></td>
<td>
<input type="button" class="button" id="<?=$this->_control?>_border" value="#ffffff" onclick="<?=$this->_control?>.setBorder()" style="font-family: 'Courier New', Verdana, Tahoma, sans-serif; font-size: 10px;" />
<div style="position: relative;">
<div id="<?=$this->_control?>_select" style="background-color: #fff; display: none; height: 100px; overflow: auto; position: absolute; right: 0; width: 100px; z-index: 999999;"></div>
</div>
</td>
</tr>
<tr>
<td><span>&nbsp;</span></td>
<td><input type="button" class="button" value="Preview" onclick="<?=$this->_control?>.setPreview()" /></td>
</tr>
<tr>
<td><span>&nbsp;</span></td>
<td><input type="submit" class="button" value="Save" /></td>
</tr>
</table>
</form>

<?php

	}

	// }}}
	// {{{

	function outputHtmlImage()
	{
		$src = $this->_imageLink 
			? $this->_imageLink 
			: $this->_action . '?action=image';

?>

<div style="position: relative;">
<img id="<?=$this->_control?>_image" src="<?=$src?>" style="border: none; height: <?=$this->getMaxHeight()?>px; width: <?=$this->getMaxWidth()?>px;" alt="" border="0" height="<?=$this->getMaxHeight()?>" width="<?=$this->getMaxWidth()?>" />
<div id="<?=$this->_control?>_frame" style="border-style: solid; border-width: 1px; cursor: move; position: absolute;">&nbsp;</div>
</div>

<?php

	}

	// }}}
	// {{{

	function outputHtmlThumbnail()
	{
		$src = $this->_action . '?action=thumb&img=' . $this->_imageId;
		foreach ($this->_values as $k => $v) {
			if ( $v < 1 ) {
				$v *= 100;
			}
			$src .= '&' . $k . '=' . $v;
		}

?>

<div style="position: relative;">
<img id="<?=$this->_control?>_thumb" src="<?=$src?>" style="border: none;" alt="" border="0" />
</div>

<?php

	}

	// }}}
	// {{{

	function outputImage()
	{
		$image =& new Image();
		$image->output($this->_image);

		exit;
	}

	// }}}
	// {{{

	function outputImageThumbnail()
	{
		$options = $this->_values;

		switch ( $options['method'] ) {
		case Image_Thumbnail::METHOD_CROP:
			if ( $options['percent'] > 0 
			|| $options['width'] > $this->getMinWidth() 
			|| $options['height'] > $this->getMinHeight() ) {
				$image =& new Image_Thumbnail();
				$result = $image->render($this->_image, $options);
				$image->output($result, null, array(
					'method' => Image_Thumbnail::METHOD_SCALE_MAX,
					'width'  => $this->getMinWidth(),
					'height' => $this->getMinHeight(),
				));
			} else {
				$image =& new Image_Thumbnail();
				$result = $image->render($this->_image, $options);
				$image->output($result, null, array(
					'method' => Image_Thumbnail::METHOD_SCALE_MAX,
					'width'  => $this->getMinWidth(),
					'height' => $this->getMinHeight(),
				));
			}
			break;
		case Image_Thumbnail::METHOD_SCALE_MIN:
			$image =& new Image_Thumbnail();
			$result = $image->render($this->_image, array(
				'method' => $options['method'],
				'halign' => $options['halign'],
				'valign' => $options['valign'],
			));
			$image->output($result, null, array(
				'method' => Image_Thumbnail::METHOD_SCALE_MAX,
				'width'  => $this->getMinWidth(),
				'height' => $this->getMinHeight(),
			));
			break;
		case Image_Thumbnail::METHOD_SCALE_MAX:
			$image =& new Image_Thumbnail();
			$image->output($this->_image, null, array(
				'method' => Image_Thumbnail::METHOD_SCALE_MAX,
				'width'  => $this->getMinWidth(),
				'height' => $this->getMinHeight(),
			));
			break;
		}

		exit;
	}

	// }}}
	// {{{

	function outputJs()
	{

?>

/**
 * This is Javascript of control panel for the thumbnail creating
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
 * @author	  Ildar N. Shaimordanov <ildar-sh@mail.ru>
 * @license	 http://www.php.net/license/3_0.txt
 *			  The PHP License, version 3.0
 */

/**
 * Constructor
 *
 * @param  XHTML_Element form  The reference to the container of the form
 * @param  XHTML_Element image The reference to the container of the original image
 * @param  XHTML_Element thumb The reference to the container of the thumbnail image
 * @result ImageTumbnailControl4
 * @access public
 */
function ImageTumbnailControl4(form, image, thumb)
{
	if ( ! form || ! image || ! thumb ) {
		return;
	}

	var self = this;

	/**
	 * Control form (select method, limits, aligns)
	 */
	var controlForm = null;

	/**
	 * The thumbnail frame
	 */
	var thumbnailFrame = null;

	/**
	 * The original image frame
	 */
	var originalImage = null;

	/**
	 * The color select panel and button
	 */
	var borderButton = null
	var colorSelect = null;
	var colorSelectHtml = '';

	/**
	 * The thumbnail image
	 */
	var thumbnailImage = null;

	/**
	 * Upper-left corner of the thumbnail frame within the original image
	 * Width and height of the original image
	 */
	var X = 0;
	var Y = 0;
	var W = 0;
	var H = 0;

	/**
	 * Thumbnail frame border color
	 */
	var borderColor;

	/**
	 * Width and height of the thumbnail frame
	 */
	var width   = 0;
	var height  = 0;
	var percent = 0;

	/**
	 * Aligns of the thumbnail frame
	 */
	var hAlign = 0;
	var vAlign = 0;

	/**
	 * Secondary variable for temporary bypass of draw
	 */
	var drawBypass = false;

	/**
	 * Toggle of the thumbnail frame
	 *
	 * @param  string  display The value for the thumbnail frame display
	 * @result void
	 * @access private
	 */
	self.toggleFrame = function(display)
	{
		thumbnailFrame.style.display = display;
	}

	/**
	 * Displays the thumbnail frame with calculated border, width, height and aligns
	 *
	 * @param  void
	 * @result void
	 * @access private
	 * @see	ThumbnailControl.setXXX() methods
	 */
	self.draw = function()
	{
		if ( drawBypass ) {
			return;
		}

		thumbnailFrame.style.borderColor = borderColor;
		thumbnailFrame.style.left = X + 'px';
		thumbnailFrame.style.top = Y + 'px';
		thumbnailFrame.style.width = width + 'px';
		thumbnailFrame.style.height = height + 'px';
	}

	/**
	 * Set the thumbnail creating method
	 * Toggle the thumbnail frame, enable/disable controls in compliance with the method
	 *
	 * @param  void
	 * @result void
	 * @access public
	 * @see	ThumbnailControl.toggleFrame(), ThumbnailControl.setPercent()
	 */
	self.setMethod = function()
	{
		var saveDrawBypass = drawBypass;
		drawBypass = true;

		var method = controlForm.method.options[controlForm.method.selectedIndex].value;

		if ( method == 2 ) {
			//
			// Crop method
			//

			// All controls are enabled
			borderButton.disabled = 
			controlForm.width.disabled = 
			controlForm.height.disabled = 
			controlForm.percent.disabled = 
			controlForm.halign.disabled = 
			controlForm.valign.disabled = false;

			// The thumbnail frame is visible
			self.toggleFrame('');

			// If it is necessary calculate the thumbnail frame in percent
			self.setPercent()
		} else if ( method == 1 ) {
			//
			// Minimal scale method
			//

			// The border color, aligns controls are enabled
			borderButton.disabled = 
			controlForm.halign.disabled = 
			controlForm.valign.disabled = false;
			// The width, height and percent controls are disabled
			controlForm.width.disabled = 
			controlForm.height.disabled = 
			controlForm.percent.disabled = true;

			width = height = ( W > H ) ? H : W;

			// The thumbnail frame is visible
			self.toggleFrame('');

			// Calculate the thumbnail frame aligns
			self.setHalign();
			self.setValign();
		} else {
			//
			// Maximal scale method
			//

			// All controls are disabled
			borderButton.disabled = 
			controlForm.width.disabled = 
			controlForm.height.disabled = 
			controlForm.percent.disabled = 
			controlForm.halign.disabled = 
			controlForm.valign.disabled = true;

			// The thumbnail frame is invisible
			self.toggleFrame('none');
		}

		drawBypass = saveDrawBypass;

		self.draw();
	}

	/**
	 * Set the width of the thumbnail
	 *
	 * @param  void
	 * @result void
	 * @access public
	 * @see	ThumbnailControl.setHalign()
	 */
	self.setWidth = function()
	{
//		if ( controlForm.width.disabled ) {
//			return;
//		}

		width = ( percent > 0 )
			? Math.floor(W * percent / 100)
			: controlForm.width.value;
		self.setHalign();
	}

	/**
	 * Set the height of the thumbnail
	 *
	 * @param  void
	 * @result void
	 * @access public
	 * @see	ThumbnailControl.setValign()
	 */
	self.setHeight = function()
	{
//		if ( controlForm.height.disabled ) {
//			return;
//		}

		height = ( percent > 0 )
			? Math.floor(H * percent / 100)
			: controlForm.height.value;
		self.setValign();
	}

	/**
	 * Set the width and height of the thumbnail
	 *
	 * @param  void
	 * @result void
	 * @access public
	 * @see	ThumbnailControl.setWidth(), ThumbnailControl.setHeight()
	 */
	self.setPercent = function()
	{
		if ( controlForm.percent.disabled ) {
			return;
		}

		percent = controlForm.percent.value;

		if ( percent > 0 ) {
			controlForm.width.disabled = 
			controlForm.height.disabled = true;
		} else {
			controlForm.width.disabled = 
			controlForm.height.disabled = false;
		}

		var saveDrawBypass = drawBypass;
		drawBypass = true;

		self.setWidth();
		self.setHeight();

		drawBypass = saveDrawBypass;

		self.draw();
	}

	/**
	 * Set the horizontal align of the thumbnail
	 *
	 * @param  void
	 * @result void
	 * @access public
	 * @see	ThumbnailControl.draw()
	 */
	self.setHalign = function()
	{
		hAlign = controlForm.halign.options[controlForm.halign.selectedIndex].value;
		if ( hAlign < 0 ) {
			X = 0;
		} else if ( hAlign > 0 ) {
			X = W - width;
		} else {
			X = Math.floor((W - width) / 2);
		}
		self.draw();
	}

	/**
	 * Set the vertical align of the thumbnail
	 *
	 * @param  void
	 * @result void
	 * @access public
	 * @see	ThumbnailControl.draw()
	 */
	self.setValign = function()
	{
		vAlign = controlForm.valign.options[controlForm.valign.selectedIndex].value;
		if ( vAlign < 0 ) {
			Y = 0;
		} else if ( vAlign > 0 ) {
			Y = H - height;
		} else {
			Y = Math.floor((H - height) / 2);
		}
		self.draw();
	}

	/**
	 * Draws the border color of the thumbnail frame and the appropriate control button
	 *
	 * @param  string  value The value of the border color in the format #RRGGBB
	 * @result void
	 * @access private
	 * @see	ThumbnailControl.draw()
	 */
	self.drawBorderColorButton = function(value, closeSelect)
	{
		borderButton.style.color = (value.replace(/^#/, '0x') & 0x00ff00) >> 8 > 0x80 ? '#000' : '#fff';
		//borderButton.style.color = 
		borderColor = 
		borderButton.style.backgroundColor = 
		borderButton.value = value;
		self.draw();

		if ( closeSelect ) {
			self.setBorder();
		}
	}

	/**
	 * Sets the border color selector for the thumbnail frame
	 *
	 * @param  void
	 * @result void
	 * @access public
	 * @see
	 */
	self.setBorder = function()
	{
		if ( ! colorSelect.innerHTML ) {
//			colorSelect.innerHTML = colorSelectHtml;
		}
		colorSelect.style.display = colorSelect.style.display != '' ? '' : 'none';
	}

	/**
	 * Set the preview thumbnail
	 *
	 * @param  void
	 * @result void
	 * @access public
	 */
	self.setPreview = function()
	{
		var href = thumbnailImage.src;
		href = href.replace(/(img|borderColor|method|width|height|percent|halign|valign)=[\+\-]?[0-9a-fA-F]+/g, '').replace(/&{2,}/, '');

		var params = [];
		for (var i = 0; i < controlForm.elements.length; i++) {
			if ( ! controlForm.elements[i].name || controlForm.elements[i].disabled ) {
				continue;
			}
			params[params.length] = controlForm.elements[i].name + '=' + controlForm.elements[i].value.replace(/[^\+\-0-9a-fA-F]/, '');
		}

		href = href + ( href.indexOf('?') == -1 ? '?' : '&' ) + params.join('&');
		thumbnailImage.src = href;
	}

	//form.innerHTML  = __ITC_HTML_CTRL;
	//image.innerHTML = __ITC_HTML_IMAGE;
	//thumb.innerHTML = __ITC_HTML_THUMB;
	//window[__ITC_CTRLID] = self;

	// Control form
	controlForm = null;
	//controlForm = document.getElementById(__ITC_CTRLID + '_form');
	//initializeSpinbutton('width',   self.setWidth);
	//initializeSpinbutton('height',  self.setHeight);
	//initializeSpinbutton('percent', self.setPercent);

	// Thumbnail frame
	//thumbnailFrame = document.getElementById(__ITC_CTRLID + '_frame');

	// Original image
	//originalImage = document.getElementById(__ITC_CTRLID + '_image');
	//W = parseInt(originalImage.style.width);
	//H = parseInt(originalImage.style.height);
//	W = originalImage.style.width.substr(0, originalImage.style.width.length - 2);
//	H = originalImage.style.height.substr(0, originalImage.style.height.length - 2);

	// Color select
	//borderButton = document.getElementById(__ITC_CTRLID + '_border');
	//colorSelect = document.getElementById(__ITC_CTRLID + '_select');
	//colorSelect.innerHTML = __ITC_HTML_SELECT;

	// Thumbnail image
	//thumbnailImage = document.getElementById(__ITC_CTRLID + '_thumb');

	// Output control
	drawBypass = true;

	self.drawBorderColorButton('#ffffff');
	self.setMethod();

	drawBypass = false;

	self.draw();
}

<?php

	}

	// }}}
	// {{{

	function outputJsApi()
	{
		header('Content-Type: application/x-javascript');

		$src = $_SERVER['SCRIPT_NAME'] . '?action=apihelp';
		foreach ($this->_values as $name => $value) {
			$src .= '&' . $name . '=' . $value . '';
		}

/*
document.writeln('<' + 'script type="text/javascript" src="<?=$_SERVER['SCRIPT_NAME']?>?action=apihelp"><' + '/script>');
*/
?>

document.writeln('<' + 'link type="text/css" href="<?=$_SERVER['SCRIPT_NAME']?>?action=css" rel="stylesheet" /' + '>');
document.writeln('<' + 'script type="text/javascript" src="<?=$_SERVER['SCRIPT_NAME']?>?action=apihelp"><' + '/script>');
document.writeln('<' + 'script type="text/javascript" src="<?=$_SERVER['SCRIPT_NAME']?>?action=js"><' + '/script>');

<?php

		exit;
	}

	// }}}
	// {{{

	function outputJsApiHelp()
	{
		$gmt = gmdate('D, d M Y H:i:s') . ' GMT';
		header('Content-Type: application/x-javascript');
		header('Expires: ' . $gmt);
		header('Last-Modified: ' . $gmt);
		header('Cache-Control: no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0');
		header('Cache-Control: max-age=0');
		header('Pragma: no-cache');

		$htmls = array();

		ob_start();
		$this->outputHtmlControl();
		$htmls['CTRL'] = ob_get_clean();

		ob_start();
		$this->outputHtmlImage();
		$htmls['IMAGE'] = ob_get_clean();

		ob_start();
		$this->outputHtmlThumbnail();
		$htmls['THUMB'] = ob_get_clean();

		foreach ($htmls as $name => $value) {
			$value = str_replace('"', '\"', $value);
			$value = str_replace('<script', '<" + "script', $value);
			$value = str_replace('/script', '/" + "script', $value);
			$value = preg_replace('/\r|\n/', '', $value);

?>
var __ITC_HTML_<?=$name?> = "<?=$value?>";
<?php

		}

?>
var __ITC_CTRLID = "<?=$this->_control?>";
var __ITC_HTML_SELECT = 
(function(){
	var result = '<table border="0" cellpadding="0" cellspacing="0" style="font-family: \'Courier New\', Verdana, Tahoma, sans-serif; font-size: 10px; width: 100%;">';
	for (var r = 0x00; r < 0x100; r += 0x33) {
		for (var g = 0x00; g < 0x100; g += 0x33) {
			result += '<tr>';
			for (var b = 0x00; b < 0x100; b += 0x33) {
				var rgb;
				rgb = '000000' + ((0x100 * r + g) * 0x100 + b).toString(0x10);
				rgb = rgb.substr(rgb.length - 6);
				result += '<td '
					+ 'style="background-color: #' + rgb + '; color: ' + (g > 0x80 ? '#000' : '#fff') + '; cursor: pointer; cursor: hand;" '
					+ 'onclick="' + __ITC_CTRLID + '.drawBorderColorButton(' + "'#" + rgb + "'" + ', true)" '
					+ '>&nbsp;</td>';
			}
			result += '</tr>';
		}
	}
	result += '</table>';
	return result;
})();
<?php

		exit;
	}

	// }}}
	// {{{

	function prepareHtmlForm()
	{
		$itcImage = array(
			/**
			 *
			 * Image ID
			 *
			 */
			array(
				'element'	=> 'input',
				'attrs'	=> array(
					'type'	=> 'hidden',
					'name'	=> 'image',
				),
				'meta'	=> array(
					'submit'	=> 'GET COOKIE',
				),
			),

			array(
				'element'	=> 'text',
				'attrs'	=> array(
					'kind'	=> 'fieldset',
					'text'	=> 'Usage',
				),
			/**
			 *
			 * Method
			 *
			 */
			array(
				'element'	=> 'select',
				'attrs'	=> array(
					'name'	=> 'method',
					'options'	=> array(
						Image_Thumbnail::METHOD_SCALE_MAX => 'Max Scale',
						Image_Thumbnail::METHOD_SCALE_MIN => 'Min Scale',
						Image_Thumbnail::METHOD_CROP	  => 'Crop',
					),
				),
				'meta'	=> array(
					'label'	=> 'Method:',
					'submit'	=> 'GET COOKIE',
					'validator'	=> 'integer eigenvalue',
				),
			),
			),

			array(
				'element'	=> 'text',
				'attrs'	=> array(
					'kind'	=> 'fieldset',
					'text'	=> 'Limits',
				),
			/**
			 *
			 * Width
			 *
			 */
			array(
				'element'	=> 'spinbox',
				'attrs'	=> array(
					'name'	=> 'width',
				),
				'meta'	=> array(
					'label'	=> 'Width:',
					'submit'	=> 'GET COOKIE',
					'validator'	=> 'required integer range(' . $this->_defaultValues['width'][0] . ' ' . $this->_defaultValues['width'][1] . ')',
				),
			),

			/**
			 *
			 * Height
			 *
			 */
			array(
				'element'	=> 'spinbox',
				'attrs'	=> array(
					'name'	=> 'height',
				),
				'meta'	=> array(
					'label'	=> 'Height:',
					'submit'	=> 'GET COOKIE',
					'validator'	=> 'required integer range(' . $this->_defaultValues['height'][0] . ' ' . $this->_defaultValues['height'][1] . ')',
				),
			),
                
			/**
			 *
			 * Percent
			 *
			 */
			array(
				'element'	=> 'spinbox',
				'attrs'	=> array(
					'name'	=> 'percent',
				),
				'meta'	=> array(
					'label'	=> 'Percent:',
					'submit'	=> 'GET COOKIE',
					'validator'	=> 'required integer range(0 100)',
				),
			),
			),

			array(
				'element'	=> 'text',
				'attrs'	=> array(
					'kind'	=> 'fieldset',
					'text'	=> 'Alignment',
				),
			/**
			 *
			 * Horizontal
			 *
			 */
			array(
				'element'	=> 'select',
				'attrs'	=> array(
					'name'	=> 'halign',
					'value'	=> Image_Thumbnail::ALIGN_CENTER,
					'options'	=> array(
						Image_Thumbnail::ALIGN_LEFT   => 'Left',
						Image_Thumbnail::ALIGN_CENTER => 'Center',
						Image_Thumbnail::ALIGN_RIGHT  => 'Right',
					),
				),
				'meta'	=> array(
					'label'	=> 'Horizontal:',
					'submit'	=> 'GET COOKIE',
					'validator'	=> 'integer eigenvalue',
				),
			),

			/**
			 *
			 * Vertical
			 *
			 */
			array(
				'element'	=> 'select',
				'attrs'	=> array(
					'name'	=> 'valign',
					'value'	=> Image_Thumbnail::ALIGN_CENTER,
					'options'	=> array(
						Image_Thumbnail::ALIGN_TOP    => 'Top',
						Image_Thumbnail::ALIGN_CENTER => 'Center',
						Image_Thumbnail::ALIGN_BOTTOM => 'Bottom',
					),
				),
				'meta'	=> array(
					'label'	=> 'Vertical:',
					'submit'	=> 'GET COOKIE',
					'validator'	=> 'integer eigenvalue',
				),
			),
			),

			/**
			 *
			 * Border color button
			 *
			 */
			array(
				'element'	=> 'input',
				'attrs'	=> array(
					'type'	=> 'button',
					'class'	=> 'button',
					'value'	=> 'Border',
					'style'	=> 'background-color: #fff; color: #000; font-family: Verdana, \'Courier New\', Tahoma, sans-serif; font-size: 11px;',
				),
			),

			/**
			 *
			 * Preview button
			 *
			 */
			array(
				'element'	=> 'input',
				'attrs'	=> array(
					'type'	=> 'button',
					'class'	=> 'button',
					'value'	=> 'Preview',
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
					'class'	=> 'button',
					'value'	=> 'Save',
				),
			),

			/**
			 *
			 * Form attributes
			 *
			 */
			'element'	=> 'form',
			'attrs'	=> array(
				'method'	=> 'get',
				'class'	=> 'imageThumbnailControlForm',
			),
		);

		$this->_htmlForm =& XHTML::loadFromArray($itcImage);
	}

	// }}}
	// {{{

	function prepareHtmlImage()
	{
	}

	// }}}
	// {{{

	function prepareHtmlThumb()
	{
	}

	// }}}

}

// }}}

?>