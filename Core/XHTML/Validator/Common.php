<?php

/**
 * This is the validation class
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
 * @package     XHTML_Forms_Validator
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'Object.php';

// {{{

class XHTML_Validator_Common
{

	// {{{ constants

	/**
	 * Regex for html-attributes validation
	 *
	 * @constant
	 */
	const RE_ATTR = '/
		\s*
		(
			([a-z_][a-z0-9_-]*) (?: :([a-z_][a-z0-9_-]*) )?
		)
		(?:
			\s*=\s*
			(?: \"([^\"]*)\" | \'([^\']*)\' | ([^\s]+) )
		)?
		\s*
	/ix';

	/**
	 * Regex for e-mail validation
	 *
	 * @constant
	 */
	const RE_EMAIL = '/^ \s* ( [^@]+ ) @ ( (?:[a-z0-9_-]+\.)+[a-z]{2,4} ) \s* $/ix';

	/**
	 * Regex for IP validation
	 *
	 * @constant
	 */
	const RE_IP = '/^
		\s*
		(?: ( [01]?\d\d? | 2[0-4]\d | 25[0-5] ) \. )
		(?: ( [01]?\d\d? | 2[0-4]\d | 25[0-5] ) \. )
		(?: ( [01]?\d\d? | 2[0-4]\d | 25[0-5] ) \. )
		( [01]?\d\d? | 2[0-4]\d | 25[0-5] )
		\s*
	$/ix';

	/**
	 * Regex for URL validation
	 *
	 * @constant
	 */
	const RE_URL = '/^
		\s*
		(?:
			# scheme:\/\/
			([a-z]+) :\/\/
		)?
		(?:
			# username:password@
			( [^:@]+ ) (?: : ([^:@]+) )? @
		)?
		(
			# hostname|localhost|IP
			(?: [a-z0-9_-]+ \. )+ [a-z]{2,4}
			|
			localhost
			|
			(?: (?: [01]?\d\d? | 2[0-4]\d | 25[0-5] ) \. ){3}
			(?: (?: [01]?\d\d? | 2[0-4]\d | 25[0-5] ) )
		)
		(?:
			# :port
			: (\d+)
		)?
		(?:
			# \/path
			([^:\?\#]+)
		)?
		(?:
			# ?query
			\? ([^\#]+)
		)?
		(?:
			# #fragment
			\# ([^\s]+)
		)?
		\s*
	$/ix';

	/**
	 * Regex for variable name validation
	 *
	 * @constant
	 */
	const RE_VARNAME = '/^
		\s*
		(
			# name
			[a-z_][a-z0-9_]*
		)
		\s*
		(
			# indexes
			(?: \s* \[ \s* [^\s\[\]]* \s* \] \s* )*
		)
	$/ix';

	/**
	 * Regex for variable indexes validation and splitting
	 *
	 * @constant
	 */
	const RE_VARNAME_INDEXES = '/ \s*\[\s* ([^\s\[\]]*) \s*\]\s* /ix';
	const RE_VARNAME_SPLIT = '/ (?: ^\s*\[\s* ) | (?: \s*\]\s*\[\s* ) | (?: \s*\]\s*$ ) /x';

	/**
	 * Regex for parsing of validators assumed as follow:
	 * name(arguments)
	 *
	 * @constant
	 */
	const RE_VALIDATOR = '/ 
		\s* 
		([a-z]+) 
		\s* 
		(?: 
			\s* \( \s* 
			(.*?) 
			\s* \) \s* 
		)? 
		\s* 
	/x';

	// }}}
	// {{{

	function isMetaFilter($value, & $result=null)
	{
		$value = is_scalar($value) 
			? preg_split('/\s+/', trim($value)) 
			: array_map('trim', (array)$value);

		$filters = XHTML_Validator_Common::registerCallback('filter', null);

		$result = array();
		foreach ($value as $filter) {
			if ( in_array($filter, $filters) ) {
				$result[] = $filter;
			}
		}
		return ! empty($result);
	}

	// }}}
	// {{{

	function isMetaSource($value, & $result=null)
	{
		$value = trim($value);
		$sources = XHTML_Validator_Common::registerCallback('source', null);

		if ( ! in_array($value, $sources) ) {
			return false;
		}

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	function isMetaSubmit($value, & $result=null)
	{
		static $noneSubmit = array(
			'NONE',
		);
		static $availSubmits = array(
			'POST',
			'GET',
			'COOKIE',
		);
		static $defaultSubmits = array(
			'POST',
			'GET',
		);

		$value = is_scalar($value) 
			? preg_split('/\s+/', trim($value)) 
			: array_map('trim', (array)$value);
		$value = array_map('strtoupper', $value);
		$value = array_unique($value);

		if ( array_intersect($value, $noneSubmit) ) {
			$result = array();
			return true;
		}

		$result = array_intersect($value, $availSubmits);
		if ( empty($result) ) {
			$result = $defaultSubmits;
		}
		return true;
	}

	// }}}
	// {{{

	function isMetaValidator($value, & $result=null)
	{
		if ( is_string($value) ) {
			if ( ! preg_match_all(XHTML_Validator_Common::RE_VALIDATOR, $value, $matches, PREG_SET_ORDER) ) {
				return false;
			}

			$value = array();
			foreach ($matches as $match) {
				$name = $match[1];
				$args = trim(@$match[2]);
				$args = empty($args) 
					? array() 
					: preg_split('/\s+/', $args);
				$value[$name] = $args;
			}
		}

		$validators = XHTML_Validator_Common::registerCallback('validator', null);

		$result = array();
		foreach ($value as $name => $args) {
			$name = strtolower($name);
			if ( $name != 'Eigenvalue' && ! is_callable(array('XHTML_Validator_Common', 'isValid' . ucfirst($name))) ) {
				continue;
			}

			switch ($name) {
			case 'range':
				// range(min max delta) omitted delta equals 1
				if ( count($args) < 2 || count($args) > 3 
				|| ! XHTML_Validator_Common::isValidInteger($args[0]) 
				|| ! XHTML_Validator_Common::isValidInteger($args[1]) 
				|| ! empty($args[2]) && ! XHTML_Validator_Common::isValidInteger($args[2]) 
				) {
					continue 2;
				}
				$min = min($args[0], $args[1]);
				$max = max($args[0], $args[1]);
				$args[0] = $min;
				$args[1] = $max;
				if ( empty($args[2]) || ! XHTML_Validator_Common::isValidInteger($args[2]) ) {
					$args[2] = 1;
				}
				break;
			case 'list':
			case 'mimetype':
				// list(value ...)
				// mimetype(value ...)
				if ( ! count($args) ) {
					continue 2;
				}
				break;
			case 'length':
			case 'minlength':
			case 'maxlength':
			case 'minfilesize':
			case 'maxfilesize':
				// length(len)
				// minlength(len)
				// maxlength(len)
				// minfilesize(size)
				// maxfilesize(size)
				if ( count($args) != 1 || ! XHTML_Validator_Common::isValidInteger($args[0]) ) {
					continue 2;
				}
				break;
			case 'rangefilesize':
			case 'rangelength':
				// rangefilesize(min max)
				// rangelength(min max)
				if ( count($args) != 2 
				|| ! XHTML_Validator_Common::isValidInteger($args[0]) 
				|| ! XHTML_Validator_Common::isValidInteger($args[1]) 
				) {
					continue 2;
				}
				$min = min($args[0], $args[1]);
				$max = max($args[0], $args[1]);
				$args[0] = $min;
				$args[1] = $max;
				break;
			case 'callback':
				// callback(function)
				if ( count($args) != 1 || ! in_array($args[0], $validators) ) {
					continue 2;
				}
				break;
			default:
				$args = array();
			}
			$result[$name] = $args;
		}

		return ! empty($result);
	}

	// }}}
	// {{{

	/**
	 * Validates a value is alphabetical
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidAlphabetical($value, & $result=null)
	{
		if ( preg_match('/[^a-zA-Z]/', $value) ) {
			return false;
		}
		
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value is alphanumeric
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidAlphanumeric($value, & $result=null)
	{
		if ( preg_match('/[^a-zA-Z0-9]/', $value) ) {
			return false;
		}
		
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates an html-attributes and returns it's parts
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidAttr($value, & $result=null)
	{
		if ( ! preg_match_all(XHTML_Validator_Common::RE_ATTR, $value, $matches, PREG_SET_ORDER) ) {
			return false;
		}
		$result = array();
		foreach ($matches as $match) {
			if ( empty($match[3]) ) {
				$result[strtolower($match[1])] = trim(end($match));
			} else {
				$result[strtolower($match[2])][strtolower($match[3])] = trim(end($match));
			}
		}
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value by callback
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @param	array	$args	The callback name
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidCallback($value, & $result=null, $args=array())
	{
		if ( ! call_user_func($args[0], $value) ) {
			return false;
		}

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates an e-mail and returns it's parts
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidEmail($value, & $result=null)
	{
		// special case for URL
		// username@hostname
		return XHTML_Validator_Common::isValidUrl($value, $result);
	}

	// }}}
	// {{{

	/**
	 * Validates an float value
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidFloat($value, & $result=null)
	{
		if ( is_numeric($value) && (float)$value == $value ) {
			$result = $value;
			return true;
		}
		return false;
	}

	// }}}
	// {{{

	/**
	 * Validates an integer value
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidInteger($value, & $result=null)
	{
		if ( is_numeric($value) && (int)$value == $value ) {
			$result = $value;
			return true;
		}
		return false;
	}

	// }}}
	// {{{

	/**
	 * Validates an IP and returns it's parts
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidIp($value, & $result=null)
	{
		// (0-255).(0-255).(0-255).(0-255)
		$valid = (bool)preg_match(XHTML_Validator_Common::RE_IP, $value, $matches);
		if ( $valid ) {
			$result = array_slice($matches, 1);
		}
		return $valid;
	}

	// }}}
	// {{{

	/**
	 * Validates a value
	 * Value must have exact number of characters
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The value of the available length
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidLength($value, & $result=null, $args=array())
	{
		$len = strlen($value);
		if ( $len != $args[0] ) {
			return false;
		}
		
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value
	 * value must be one item of the list
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The list of available values
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidList($value, & $result=null, $args=array())
	{
		if ( ! in_array($value, $args) ) {
			return false;
		}
		
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates an uploaded file
	 * The file size must not exceed the given number of bytes
	 *
	 * @param	array	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The available maximal length
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidMaxfilesize($value, & $result=null, $args=array())
	{
		if ( ! XHTML_Validator_Common::isValidUploaded($value) || @filesize($value['tmp_name']) > $args[0] ) {
			return false;
		}

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value
	 * Value must not exceed given number of characters
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The available maximal length
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidMaxlength($value, & $result=null, $args=array())
	{
		$len = strlen($value);
		if ( $len > $args[0] ) {
			return false;
		}
		
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value is one of the list
	 * The file must have a correct MIME type
	 *
	 * @param	array	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The list of available values
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidMimetype($value, & $result=null, $args=array())
	{
		if ( ! XHTML_Validator_Common::isValidUploaded($value) ) {
			return false;
		}

		@list($fileMime, $fileSubMime) = explode('/', $value['type']);

		foreach ($args as $arg) {
			@list($mime, $subMime) = explode('/', $arg);
			if ( empty($mime) || $mime == '*' 
			|| ( $fileMime == $mime 
			&& ( empty($subMime) || $subMime == '*' || $fileSubMime == $subMime ) ) ) {
				$result = $value;
				return true;
			}
		}

		return false;
	}

	// }}}
	// {{{

	/**
	 * Validates an uploaded file
	 * The file size must be equals or exceed the given number of bytes
	 *
	 * @param	array	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The available maximal length
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidMinfilesize($value, & $result=null, $args=array())
	{
		if ( ! XHTML_Validator_Common::isValidUploaded($value) || @filesize($value['tmp_name']) < $args[0] ) {
			return false;
		}

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value
	 * Value must have more than given number of characters
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The available minimal length
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidMinlength($value, & $result=null, $args=array())
	{
		$len = strlen($value);
		if ( $len < $args[0] ) {
			return false;
		}
		
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value
	 * Value must be a number not starting with 0
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidNonzero($value, & $result=null)
	{
		if ( is_array($value) ) {
			$value = array_filter($value);
		}

		if ( empty($value) ) {
			return false;
		}

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates any value
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidOptional($value, & $result=null)
	{
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value is in the range of numeric values
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The range of numeric values (in this order: min max delta)
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidRange($value, & $result=null, $args=array())
	{
		if ( $value < $args[0] || $value > $args[1] || ($value - $args[0]) % $args[2] ) {
			return false;
		}

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates an uploaded file
	 * The file size must not exceed the maximal number of bytes and have not to be less than minimal ones
	 *
	 * @param	array	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The available maximal length
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidRangefilesize($value, & $result=null, $args=array())
	{
		if ( ! XHTML_Validator_Common::isValidUploaded($value) || @filesize($value['tmp_name']) < $args[0] || @filesize($value['tmp_name']) > $args[1] ) {
			return false;
		}

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value
	 * Value must have between min and max characters
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @param	array	$args	The minimal and maximal length of string (in this order: min max)
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidRangelength($value, & $result=null, $args=array())
	{
		$len = strlen($value);
		if ( $len < $args[0] || $len > $args[1] ) {
			return false;
		}
		
		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value is not empty
	 *
	 * @param	mixed	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidRequired($value, & $result=null)
	{
		if ( empty($value) ) {
			return false;
		}
		if ( ! isset($value) ) {
			return false;
		}
/*
		if ( is_array($value) ) {
			return XHTML_Validator_Common::isValidUploaded($value, $result);
		}

		if ( (string)$value == '' ) {
			return false;
		}
*/

		$result = $value;
		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a value is uploaded file
	 *
	 * @param	array	$value	The value to be parsed
	 * @param	string	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidUploaded($value, & $result=null)
	{
		if ( ( isset($value['error']) && $value['error'] == 0 
		|| ! empty($value['tmp_name']) && $value['tmp_name'] != 'none' ) 
		&& is_uploaded_file($value['tmp_name']) ) {
			$result = $value;
			return true;
		}
		return false;
	}

	// }}}
	// {{{

	/**
	 * Validates an URL and returns it's parts
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidUrl($value, & $result=null)
	{
		// scheme://username:password@hostname:port/path?query#fragment
		// scheme://username:password@localhost:port/path?query#fragment
		// scheme://username:password@IP:port/path?query#fragment
		static $partNames = array(
			1 => 'scheme',
			'user',
			'pass',
			'host',
			'port',
			'path',
			'query',
			'fragment',
		);

		$valid = (bool)preg_match(XHTML_Validator_Common::RE_URL, $value, $matches);
		if ( ! $valid ) {
			return false;
		}

		// emulating of array_combine from PHP 5
		$result = array();
		foreach ($partNames as $k => $v) {
			$result[$v] = isset($matches[$k]) ? $matches[$k] : '';
		}
/*
		// array_combine requires both arrays should have equal number of elements
		$result = array_slice($matches, 1);
		while ( count($result) < count($partNames) ) {
			$result[] = '';
		}
		$result = array_combine($partNames, $result);
*/

		return true;
	}

	// }}}
	// {{{

	/**
	 * Validates a variable name and returns it's parts
	 *
	 * @param	string	$value	The value to be parsed
	 * @param	array	$result	The reference to the resulting array
	 * @return	boolean	It is 'true' if the argument is valid
	 * @access	public
	 */
	function isValidVarname($value, & $result=null)
	{
		// name[first][second][][fourth]
		if ( ! preg_match(XHTML_Validator_Common::RE_VARNAME, $value, $matches) ) {
			return false;
		}
		$result = preg_split(XHTML_Validator_Common::RE_VARNAME_SPLIT, $matches[2]);
		$result[0] = $matches[1];
		if ( count($result) > 1 ) {
			array_pop($result);
		}
		return true;
	}

	// }}}
	// {{{

	function registerCallback($type, $callback)
	{
		static $callbacks = array(
			'source'	=> array(),
			'filter'	=> array(),
			'validator'	=> array(),
		);

		$type = strtolower($type);
		if ( ! array_key_exists($type, $callbacks) ) {
			return false;
		}

		if ( $callback === null ) {
			return $callbacks[$type];
		}

		if ( ! is_callable($callback) ) {
			return false;
		}

		$callbacks[$type][] = $callback;
		return true;
	}

	// }}}

}

// }}}

?>