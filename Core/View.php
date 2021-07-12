<?php

/**
 * The basic class for all inherited classes of the framework
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
 * @category    XF
 * @package     Core
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'Exception.php';
require_once 'Object.php';

/**
 *
 * The basic visible class in the framework
 *
 */
class Core_View extends Core_Object
{

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

		$assoc = Core_Object::assocOutput(null, true);
		if ( preg_match('/<head[^>]*?>.*?<\/head>/ms', $html) ) {
			return preg_replace('/(?=<\/head>)/', $assoc, $html);
		}
		return $assoc . $html;
	}

	// }}}
	// {{{

	/**
	 * Associates stylesheets with an object or a class
	 *
	 * @param	void
	 * @return	String
	 * @access	public
	 */
	function assocCss()
	{
		return '';
	}

	// }}}
	// {{{

	/**
	 * Associates client-side scripts with an object or a class
	 *
	 * @param	void
	 * @return	String
	 * @access	public
	 */
	function assocJs()
	{
		return '';
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
		$ref =& Core_Object::getStaticProperty('Core_Object', 'assoc');

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
	 * !!!TODO!!!
	 *
	 * To use PEAR::Cache::Lite
	 *
	 */
	function assocOutputCache($assoc=null, $return=false)
	{
		static $called = false;
		if ( $called ) {
			return;
		}

		$called = true;

		$assoc = strtoupper($assoc);
		$ref =& Core_Object::getStaticProperty('Core_Object', 'assoc');

		$result = '';
		if ( $assoc == 'CSS' || $assoc === '' ) {
			$result .= '<style type="text/css">' . "\n";
			foreach ($ref['CSS'] as $k => $v) {
				$result .= call_user_func(array($v, 'assocCss'));
			}
			$result .= "\n" . '</style>';
		}
		file_put_content(CORE_CACHE_DIR . '/assoc/styles.css', $result);

		$result = '';
		if ( $assoc == 'JS' || $assoc === '' ) {
			$result .= '<script type="text/javascript"><!--//--><![CDATA[//><!--' . "\n";
			foreach ($ref['JS'] as $k => $v) {
				$result .= call_user_func(array($v, 'assocJs'));
			}
			$result .= "\n" . '//--><!]]></script>';
		}
		file_put_content(CORE_CACHE_DIR . '/assoc/scripts.css', $result);
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
	 * @throws	Core_Exception
	 */
	function assocRegister($assoc, $object)
	{
		if ( ! is_subclass_of($object, 'Core_Object') ) {
			throw new Core_Exception('You try to register non-associated object/class');
		}

		$assoc = strtoupper($assoc);
		if ( $assoc != 'CSS' && $assoc != 'JS' ) {
			throw new Core_Exception('Unknown association ' . $assoc);
		}

		$ref =& Core_Object::getStaticProperty('Core_Object', 'assoc');

		if ( is_object($object) ) {
			// Class instance
			$ref[$assoc][] = $object;
		} elseif ( empty($ref[$assoc][$object]) ) {
			// Class name
			$ref[$assoc][$object] = $object;
		}
	}

	// }}}

}


?>