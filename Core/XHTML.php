<?php

/**
 * This is a wrapper for handling of XHTML elements
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
 * @package     XHTML
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'Object.php';

require_once 'XHTML/Exception.php';
require_once 'XHTML/Validator/Common.php';

// {{{

class XHTML
{

	// {{{ constants

	/**
	 * Save to an array
	 *
	 * @constant
	 */
	const AS_ARRAY	= 0;

	/**
	 * Save to a string as serialized object
	 *
	 * @constant
	 */
	const AS_STRING	= 1;

	/**
	 * Save to a string as HTML
	 *
	 * @constant
	 */
	const AS_HTML	= 2;

	// }}}
	// {{{

	/**
	 * Handler for ob_start().
	 *
	 * @param	string	$html	Input html string
	 * @return	string	Html string with all substitutions
	 * @access	static
	 */
	function assocBuffer($html)
	{
		static $called = false;
		if ( $called ) {
			return $html;
		}

		$called = true;

		$assoc = XHTML::assocOutput(null, true);
		if ( preg_match('/<head[^>]*?>.*?<\/head>/ms', $html) ) {
			return preg_replace('/(?=<\/head>)/', $assoc, $html);
		}
		return $assoc . $html;
	}

	// }}}
	// {{{

	/**
	 * Outputs client-side scripts and stylesheets associated with a view
	 *
	 * @param	string	$assoc	An association (css or js)
	 * @return	string
	 * @access	public
	 */
	function assocOutput($assoc=null, $return=false)
	{
		static $called = false;
		if ( $called ) {
			return '';
		}

		$called = true;

		$assoc = strtoupper($assoc);
		$ref =& Core_Object::getStaticProperty('XHTML', 'assoc');

		$result = '';
		if ( $assoc == 'CSS' || $assoc === '' ) {
			$result .= '<style type="text/css">' . "\n";
			foreach ($ref['CSS'] as $k => $v) {
				$result .= call_user_func(array($v, 'assocCss'));
			}
			$result .= "\n" . '</style>';
		}

		if ( $assoc == 'JS' || $assoc === '' ) {
			$result .= '<script type="text/javascript"><!--//--><![CDATA[//><!--' . "\n";
			foreach ($ref['JS'] as $k => $v) {
				$result .= call_user_func(array($v, 'assocJs'));
			}
			$result .= "\n" . '//--><!]]></script>';
		}

		if ( $return ) {
			return $result;
		}
		echo $result;
	}

	// }}}
	// {{{

	/**
	 * Register for output of client-side scripts or stylesheets associated with a view
	 *
	 * @param	string	$assoc	An association (css or js)
	 * @param	mixed	$object	A class name or an object instance
	 * @return	array    The internal index of the association
	 * @access	public
	 * @throws	XHTML_Exception
	 */
	function assocRegister($assoc, $object)
	{
		if ( ! is_subclass_of($object, 'XHTML_Common') ) {
			throw new XHTML_Exception('You try to register non-associated object/class');
		}

		$assoc = strtoupper($assoc);
		if ( $assoc != 'CSS' && $assoc != 'JS' ) {
			throw new XHTML_Exception('Unknown association ' . $assoc);
		}

		$ref =& Core_Object::getStaticProperty('XHTML', 'assoc');

		if ( is_object($object) ) {
			// Class instance
			$ref[$assoc][] = $object;
		} elseif ( empty($ref[$assoc][$object]) ) {
			// Class name
			$ref[$assoc][$object] = $object;
		}
	}

	// }}}
	// {{{

	/**
	 * Creates the standard forms from array:
	 * - login form with 'login', 'password' textboxes and 'submit' button
	 * - guestbook form with 'username', 'e-mail', 'message' textboxes and 'submit' button
	 * - search form with 'search' textbox and 'submit' button
	 * - upload form with 'file', 'description' textboxes and 'submit' button
	 *
	 * @param	string	$formType	Specifies one of the standard form types (login, guestbook, search, upload)
	 * @param	string	$action		Specifies the script handling posted data
	 * @return	XHTML_Form instance
	 * @access	public
	 */
	function & createForm($formType, $action=null)
	{
		static $formImages = array(
			//
			// Login form
			//
			'login' => array(
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'name'	=> 'login',
						'type'	=> 'text',
						'class'	=> 'loginTextbox',
					),
					'meta'	=>	array(
						'submit'	=> 'POST',
						'label'	=> 'Username:',
					),
				),
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'name'	=> 'password',
						'type'	=> 'password',
						'class'	=> 'loginTextbox',
					),
					'meta'	=>	array(
						'submit'	=> 'POST',
						'label'	=> 'Password:',
					),
				),
				array(
					'element'	=> 'Togglebox',
					'attrs'	=> array(
						'name'	=> 'remember',
					),
					'meta'	=> array(
						'submit'	=> 'POST',
						'label'	=> 'Remember Me',
						'validator'	=> 'eigenvalue',
					),
				),
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'value'	=> 'Log In',
						'type'	=> 'submit',
						'class'	=> 'loginButton',
					),
				),
				'element'	=> 'Form',
				'attrs'	=> array(
					'method'	=> 'post',
					'class'	=> 'loginForm',
				),
			),
			//
			// Guestbook form
			//
			'guestbook' => array(
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'name'	=> 'gb_username',
						'type'	=> 'text',
						'class'	=> 'guestbookTextbox',
					),
					'meta'	=>	array(
						'submit'	=> 'POST',
						'label'	=> 'User name:',
					),
				),
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'name'	=> 'gb_email',
						'type'	=> 'text',
						'class'	=> 'guestbookTextbox',
					),
					'meta'	=>	array(
						'submit'	=> 'POST',
						'label'	=> 'E-mail:',
					),
				),
				array(
					'element'	=> 'Textarea',
					'attrs'	=> array(
						'name'	=> 'gb_message',
						'class'	=> 'guestbookTextbox',
					),
					'meta'	=>	array(
						'submit'	=> 'POST',
						'label'	=> 'Message:',
					),
				),
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'value'	=> 'Enter',
						'type'	=> 'submit',
						'class'	=> 'guestbookButton',
					),
				),
				'element'	=> 'Form',
				'attrs'	=> array(
					'method'	=> 'post',
					'class'	=> 'guestbookForm',
				),
			),
			//
			// Search form
			//
			'search' => array(
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'name'	=> 'search_string',
						'value'	=> 'Search...',
						'onfocus'	=> 'if ( ! this.filled ) this.value = ""; this.filled = 1',
						'type'	=> 'text',
						'class'	=> 'searchTextbox',
					),
					'meta'	=> array(
						'filter'	=> 'trim',
						'validator'	=> 'required',
					),
				),
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'value'	=> 'Search',
						'type'	=> 'submit',
						'class'	=> 'searchButton',
					),
				),
				'element'	=> 'Form',
				'attrs'	=> array(
					'method'	=> 'post',
					'class'	=> 'searchForm',
				),
			),
			//
			// Upload form
			//
			'upload' => array(
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'name'	=> 'upload_file',
						'type'	=> 'file',
						'class'	=> 'uploadTextbox',
					),
					'meta'	=> array(
						'label'	=> 'File:',
					),
				),
				array(
					'element'	=> 'Textarea',
					'attrs'	=> array(
						'name'	=> 'upload_desc',
						'class'	=> 'uploadTextbox',
					),
					'meta'	=>	array(
						'submit'	=> 'POST',
						'label'	=> 'Description:',
					),
				),
				array(
					'element'	=> 'Input',
					'attrs'	=> array(
						'value'	=> 'Upload',
						'type'	=> 'submit',
						'class'	=> 'uploadButton',
					),
				),
				'element'	=> 'Form',
				'attrs'	=> array(
					'method'	=> 'post',
					'class'	=> 'uploadForm',
				),
			),
		);

		$formType = strtolower($formType);
		if ( ! array_key_exists($formType, $formImages) ) {
			static $null = null;
			return $null;
		}

		$formImages[$formType]['attrs']['action'] = $action;
		return XHTML::loadFromArray($formImages[$formType]);
	}

	// }}}
	// {{{

	/**
	 * Loads the HTML element from some images (such as array, serialized string, and html string)
	 *
	 * @param	mixed	$image
	 * @param	mixed	$imageType
	 * @return	XHTML_Element	instance
	 * @access	public
	 */
	function & load($image, $imageType=XHTML::AS_STRING)
	{
		if ( is_array($image) ) {
			return XHTML::loadFromArray($image);
		} elseif ( $imageType == XHTML::AS_STRING ) {
			return XHTML::loadFromString($image);
		} else {
			return XHTML::loadFromHtml($image);
		}
	}

	// }}}
	// {{{

	/**
	 * Loads the HTML element from an array
	 *
	 * @param	mixed	$image
	 * @return	XHTML_Element	instance
	 * @access	public
	 */
	function & loadFromArray($image)
	{
		$element = (string)@$image['element'];
		if ( ! preg_match('/^XHTML_/', $element) ) {
			$element = 'XHTML_' . ucfirst(strtolower($element));
		}

		$attrs = @$image['attrs'];
		$metas = @$image['meta'];

		$object =& Core_Object::factory($element, $attrs, $metas);

		unset($image['element'], $image['attrs'], $image['meta']);

		foreach ($image as $element) {
			$item =& XHTML::loadFromArray($element);
			if ( empty($item) ) {
				continue;
			}
			$object->appendChild($item);
		}
		return $object;
	}

	// }}}
	// {{{

	/**
	 * Loads the HTML element from an HTML string
	 *
	 * @param	mixed	$image
	 * @return	XHTML_Element	instance
	 * @access	public
	 */
	function & loadFromHtml($image)
	{
		preg_match_all('/<form([^>]*?)>(.*?)<\/form>/ms', $image, $matches, PREG_SET_ORDER);
		if ( ! $matches ) {
			return null;
		}

		$attrs = array();
		$elements = array();
		foreach ($matches as $match) {
			XHTML_Validator_Common::isValidAttr($match[1], $attrs);
			if ( ! $attrs ) {
			}
		}
	}

	// }}}
	// {{{

	/**
	 * Loads the HTML element from a string
	 *
	 * @param	mixed	$image
	 * @return	XHTML_Element	instance
	 * @access	public
	 */
	function & loadFromString($image)
	{
		$object = @unserialize($image);
		return $object;
	}

	// }}}
	// {{{

	/**
	 * Registers a callback function as a filter of submitted data
	 *
	 * @param	string	$callback	Callback function
	 * @return	boolean
	 * @access	public
	 * @static
	 */
	function registerFilter($callback)
	{
		return XHTML_Validator_Common::registerCallback('filter', $callback);
	}

	// }}}
	// {{{

	/**
	 * Registers a callback function as a source of XHTML-control value
	 *
	 * @param	string	$callback	Callback function
	 * @return	boolean
	 * @access	public
	 * @static
	 */
	function registerSource($callback)
	{
		return XHTML_Validator_Common::registerCallback('source', $callback);
	}

	// }}}
	// {{{

	/**
	 * Registers a callback function as a validator of submitted data
	 *
	 * @param	string	$callback	Callback function
	 * @return	boolean
	 * @access	public
	 * @static
	 */
	function registerValidator($callback)
	{
		return XHTML_Validator_Common::registerCallback('validator', $callback);
	}

	// }}}
	// {{{

	/**
	 * Saves the HTML element to some images (such as array, serialized string, and html string)
	 *
	 * @param	mixed	$object
	 * @param	mixed	$imageType
	 * @return	mixed
	 * @access	public
	 */
	function save($object, $imageType=XHTML::AS_STRING)
	{
		switch ($imageType) {
		case XHTML::AS_ARRAY:
			return XHTML::saveToArray($object);
		case XHTML::AS_STRING:
			return XHTML::saveToString($object);
		case XHTML::AS_HTML:
			return XHTML::saveToHtml($object);
		}
	}

	// }}}
	// {{{

	/**
	 * Saves the HTML element to an array
	 *
	 * @param	mixed	$object
	 * @return	mixed
	 * @access	public
	 */
	function saveToArray($object)
	{
		return $object->toArray();
	}

	// }}}
	// {{{

	/**
	 * Saves the HTML element to an HTML string
	 *
	 * @param	mixed	$object
	 * @return	mixed
	 * @access	public
	 */
	function saveToHtml($object)
	{
		return $object->outerHtml();
	}

	// }}}
	// {{{

	/**
	 * Saves the HTML element to a string
	 *
	 * @param	mixed	$object
	 * @return	mixed
	 * @access	public
	 */
	function saveToString($object)
	{
		return serialize($object);
	}

	// }}}

}

// }}}

?>