<?php

/**
 * This is an extension class for XHTML input control working as CAPTCHA
 *
 * PHP versions 5
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    XHTML
 * @package     XHTML_Forms
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'XHTML/Input.php';
require_once 'Text/Password.php';

// {{{

class XHTML_Captcha extends XHTML_Input
{

	// {{{ constants

	/**
	 * Default captcha name for session storage and inputbox
	 *
	 * @const	string
	 * @access	public
	 */
	const CAPTCHA_NAME = 'XF_CAPTCHA';

	// }}}
	// {{{ properties

	/**
	 * Session storage name for captcha
	 *
	 * @var	string
	 * @access	private
	 */
	var $_storage;

	/**
	 * Captcha image options
	 *
	 * @var	string
	 * @access	private
	 */
	var $_src;
	var $_width;
	var $_height;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML control for the CAPTCHA
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Select
	 * @access public
	 */
	function __construct($attrs=array())
	{
		$attrs = self::_setDefaultAttrs($attrs);

		$this->_storage = $attrs['storage'];
		$this->_src = $attrs['src'];
		$this->_width = $attrs['width'];
		$this->_height = $attrs['height'];

		parent::__construct($attrs);
#		$this->setMetas();

		//  Create captcha storage
		if ( empty($_SESSION[$this->_storage]) ) {
			$_SESSION[$this->_storage]['captcha'] = null;
			$_SESSION[$this->_storage]['referer'] = md5($_SERVER['DOCUMENT_ROOT']);
		}
	}

	// }}}
	// {{{

	/**
	 * Captcha generation method
	 *
	 * @param	mixed	Callback that is used to generate a captcha text
	 * @param	array	Attributes like defined for the constructor to distribute the width and height of an image and the storage container in sessions
	 * @param	array	An image output controlling options
	 * @return	boolean
	 * @access	public
	 */
	function generateImage($textGenerator, $attrs, $options)
	{
		$attrs = self::_setDefaultAttrs($attrs);

		if ( false === strpos($_SERVER['REQUEST_URI'], $attrs['src']) || false === strpos($attrs['src'], $_SERVER['REQUEST_URI']) ) {
			return false;
		}

		require_once 'Image/Captcha.php';

		// Create the captcha text
		$text = call_user_func($textGenerator);
		$image =& new Image_Captcha($text, $attrs['width'], $attrs['height']);

		// Defend against a fishing
		$storage = $attrs['storage'];
		if ( empty($_SESSION[$storage]['referer']) || $_SESSION[$storage]['referer'] != md5($_SERVER['DOCUMENT_ROOT']) ) {
			$_SESSION[$storage]['referer'] = null;
			$image->disableCaptcha();
		}

		// Output of the image
		$image->output(null, $options);

		// Store captcha phrase and image status to the session
		$_SESSION[$storage]['captcha'] = $text;

		return true;
	}

	// }}}
	// {{{

	/**
	 * Converts HTML element to string and returns one
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function outerHtml()
	{
		$image = '<span class="xf_captcha">' 
			. '<a href="' . htmlspecialchars($this->_src) . '" target="_blank"'
			. ' onclick="return event.returnValue = ! (function(a){'
			.		'if ( ! document.images ) { return; }'
			.		'var img = a.firstChild;'
			.		'while ( img.tagName.toUpperCase() != \'IMG\' &amp;&amp; ( img = img.nextSibling ) ) {}'
			.		'if ( ! img ) { return; }'
			.		'img.src = a.href + ( a.href.indexOf(\'?\') == -1 ? \'?\' : \'&amp;\' ) + (new Date()).getTime();'
			.		'a.blur();'
			.		'return true;'
			.	'})(this);">'
			. '<img src="' . htmlspecialchars($this->_src) . '"'
			. ' width="' . htmlspecialchars($this->_width) . '"'
			. ' height="' . htmlspecialchars($this->_height) . '"'
			. ' title="' . htmlspecialchars($this->getAttribute('title')) . '" border="0" />'
			. '</a></span>';
		return parent::outerHtml() . $image;
#		return $image . parent::outerHtml();
	}

	// }}}
	// {{{

	/**
	 * Sets additional behavor of the HTML control through meta-tags
	 *
	 * @param	array	$metas
	 * @return	void
	 * @access	public
	 */
	function setMetas($metas=array())
	{
		static $defMetas = array(
			'no-overwrite' => 'no-overwrite',
			'validator' => 'required', 
			'submit' => array('POST'),
			'message' => 'Verification code is failed. Try again.',
		);
		$metas = array_merge($defMetas, $metas);
		unset($metas['filter']);
		parent::setMetas($metas);
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();

		$result['attrs']['storage'] = $this->_storage;
		$result['attrs']['src'] = $this->_src;
		$result['attrs']['width'] = $this->_width;
		$result['attrs']['height'] = $this->_height;

		unset($result['meta']['filter']);

		return $result;
	}

	// }}}
	// {{{

	/**
	 * Filters and validates a value before setting to the control
	 *
	 * @param	mixed	The reference to the submitted data
	 * @return	mixed	It is true if the value is valid or false elsewhere
	 * @public
	 */
	function validate( & $value)
	{
		// Filtering
		$value = trim($value);

		// Validating
		$storage = $this->_storage;
		if ( isset($_SESSION[$storage]['captcha']) && $value == $_SESSION[$storage]['captcha'] ) {
			return true;
		}
		$value = '';
		return false;
	}

	// }}}
	// {{{ private

	function _setDefaultAttrs($attrs)
	{
		if ( empty($attrs['name']) ) {
			$attrs['name'] = XHTML_Captcha::CAPTCHA_NAME;
		}
		if ( empty($attrs['storage']) ) {
			$attrs['storage'] = XHTML_Captcha::CAPTCHA_NAME;
		}
		if ( empty($attrs['src']) ) {
			$attrs['src'] = $_SERVER['REQUEST_URI'];
			if ( ! preg_match('/show_captcha=yes/', $_SERVER['REQUEST_URI']) ) {
				$attrs['src'] .= ( false === strpos($_SERVER['REQUEST_URI'], '?') ? '?' : '&' ) . 'show_captcha=yes';
			}
#			$attrs['src'] = $_SERVER['SCRIPT_NAME'] . '?show_captcha=yes';
#			$attrs['src'] = $_SERVER['SCRIPT_NAME'] . '?show_captcha=yes&storage=' . $attrs['storage'];
		}
		if ( empty($attrs['width']) ) {
			$attrs['width'] = 150;
		}
		if ( empty($attrs['height']) ) {
			$attrs['height'] = 150;
		}
		if ( empty($attrs['title']) ) {
			$attrs['title'] = 'Click on the image for update the captcha';
		}
		$attrs['type'] = 'text';
		$attrs['autocomplete'] = 'off';

		return $attrs;
	}

	// }}}

}

// }}}

?>