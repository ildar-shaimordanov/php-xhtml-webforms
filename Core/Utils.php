<?php

/**
 * This is unsorted collection of the various routines
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

// {{{

class Core_Utils
{

	// {{{

	/**
	 * Evaluates shorthand byte values to valid integer values. 
	 * The available options are 
	 * k (for Kilobytes), M (for Megabytes), G (for Gigabytes), T (for Terabytes). 
	 * These are case sensitive. Anything else assumes bytes.
	 *
	 * @param	mixed	$value
	 * @return	integer
	 * @access	public
	 * @see		http://www.php.net/en/faq.using.html#faq.using.shorthandbytes
	 */
	function getBytes($value)
	{
		static $suffixes = array(
			'k' => 1024, 
			'M' => 1048576, 
			'G' => 1073741824, 
			'T' => 1099511627776,
		);

		preg_match('/\b(\d+(?:\.\d*)?)(k|M|G|T)?\b/', $value, $matches);
		if ( empty($matches[1]) ) {
			return 0;
		}

		$result = $matches[1];
		if ( isset($matches[2]) ) {
			$result *= $suffixes[$matches[2]];
		}

		return floor($result);
	}

	// }}}
	// {{{

	/**
	 * Convert valid integer value to shorthand bytes appending suffixes as 
	 * k (for Kilobytes), M (for Megabytes), G (for Gigabytes), T (for Terabytes).
	 *
	 * @param	integer	$value
	 * @return	string
	 * @access	public
	 * @see		http://www.php.net/en/faq.using.html#faq.using.shorthandbytes
	 */
	function getBytesShorthand($value)
	{
		static $suffixes = array(
			'T' => 1099511627776, 
			'G' => 1073741824, 
			'M' => 1048576, 
			'k' => 1024,
		);

		foreach ($suffixes as $k => $v) {
			if ( $value >= $v ) {
				//return floor($value / $v) . $k;
				return round($value / $v, 2) . $k;
			}
		}

		return $value;
	}

	// }}}
	// {{{

	/**
	 * Converts PHP to JS variables
	 *
	 * @param	string	$value
	 * @return	string
	 * @access	public
	 * @see		http://dklab.ru/lib/JsHttpRequest/
	 * @author	Dmitry Koteroff
	 */
	function php2js($value=null)
	{
		if ( $value === null ) {
			return 'null';
		}
		if ( $value === false ) {
			return 'false';
		}
		if ( $value === true ) {
			return 'true';
		}
		if ( preg_match('/^([\/\|#])(.+?)\1([a-z]*)$/is', $value, $matches) ) {
			$rex = $matches[2];
			$mod = $matches[3];
			if ( preg_match('/[xX]/', $mod) ) {
				// Remove comments
				$rex = preg_replace('/\s*(?<!\\\\)#.*?$/m', '', $rex);
				// Remove multiline
				$rex = preg_replace('/(?<!\\\\)\s+/s', '', $rex);
			}
			// Remove all modifiers excluding /igm
			$mod = strtolower($mod);
			$mod = preg_replace('/[^igmIGM]/', '', $mod);
			return 'new RegExp("' . $rex . '", "' . $mod . '")';
		}
		if ( is_scalar($value) ) {
			$value = addslashes($value);
			$value = str_replace("\n", '\n', $value);
			$value = str_replace("\r", '\r', $value);
			$value = preg_replace('/(<\/)(script>)/i', '\1"+"\2', $value);
			return '"' . $value . '"';
		}
		$isList = true;
		for ($i = 0, reset($value); $i < count($value); $i++, next($value)) {
			if ( key($value) !== $i ) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if ( $isList ) {
			foreach ($value as $v) {
				$result[] = Core_Utils::php2js($v);
			}
			$result = '[ ' . join(', ', $result) . ' ]';
		} else {
			foreach ($value as $k => $v) {
				$result[] = Core_Utils::php2js($k) . ': ' . Core_Utils::php2js($v);
			}
			$result = '{ ' . join(', ', $result) . ' }';
		}
		return $result;
	}

	// }}}
	// {{{

	/**
	 * Unquotes a variable recursively.
	 *
	 * @param	mixed	$value
	 * @return	void
	 * @access	public
	 */
	function unescape( & $value)
	{
		if ( is_scalar($value) ) {
			$value = stripslashes($value);
			return;
		}

		if ( null === $value ) {
			return;
		}

		foreach ($value as $k => $v) {
			Core_Utils::unescape($value[$k]);
		}
	}

	// }}}
	// {{{

	/**
	 * Removes duplicate values from an array.
	 *
	 * @param	mixed	$array
	 * @return	void
	 * @access	public
	 */
	function unique( & $array)
	{
		foreach ($array as $k => $v) {
			if ( is_scalar($v) ) {
				continue;
			}
			$v = array_unique($v);
			if ( count($v) == 1 ) {
				$v = reset($v);
			}
			$array[$k] = $v;
		}
	}

	// }}}

}

?>