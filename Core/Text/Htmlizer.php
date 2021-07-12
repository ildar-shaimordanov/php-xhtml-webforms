<?php

/**
 * This is a package for the html text transformation
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
 * @category    Text
 * @package     Text_Htmlizer
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

// {{{

class Text_Htmlizer
{

	// {{{

	/**
	 * Colorizes a text string as a gradient for each non-blank character.
	 * If string is empty it does nothing. If start and end colors are equal than 
	 * the string will be colored in the single color as whole source.
	 * When colorizing it estimates ranges of the each part of the RGB presentation.
	 *
	 * @example
	 * <code>
	 * // Colorize from 0x000000 (black) to 0xffffff (white)
	 * echo Text_Htmlizer::gradient('0123456789ABCDEF');
	 * </code>
	 *
	 * @param	string	$txt	The string that should be colorized
	 * @param	integer	$from	The color of the left-hand side
	 * @param	integer	$to	The color of the right-hand side
	 * @return	string
	 * @access	public
	 */
	function gradient($txt, $from=0x000000, $to=0xffffff)
	{
		// count of all characters
		$strlen = strlen($txt);

		// count of nonblank characters
		$grdlen = $strlen - preg_match_all('/\s/', $txt, $null) - 1;

		// skip a colorizing of whitespace line
		if ( ! $grdlen ) {
			return $txt;
		}

		// skip a single coloring
		if ( $from == $to ) {
			return sprintf('<span style="color: #%06x">%s</span>', $from, $txt);
		}

		// represent the start and the end colors as arrays
		$f = array(
			floor($from / 0x10000),
			floor(($from & 0xffff) / 0x100),
			$from & 0xff,
		);
		$t = array(
			floor($to / 0x10000),
			floor(($to & 0xffff) / 0x100),
			$to & 0xff,
		);

		// current value of red, green and blue colors separately
		$r = $f[0];
		$g = $f[1];
		$b = $f[2];

		// deltas per each color
		$rd = ($t[0] - $f[0]) / $grdlen;
		$gd = ($t[1] - $f[1]) / $grdlen;
		$bd = ($t[2] - $f[2]) / $grdlen;

		$result = '';
		for ($i = 0; $i < $strlen; $i++) {
			// skip colorizing of whitespaces
			if ( preg_match('/\s/', $txt{$i}) ) {
				$result .= $txt{$i};
				continue;
			}

			// colorize one character
			$result .= sprintf('<span style="color: #%02x%02x%02x">%s</span>', $r, $g, $b, $txt{$i});

			// next color
			$r += $rd;
			$g += $gd;
			$b += $bd;
		}

		return $result;
	}

	// }}}
	// {{{

	/**
	 * Cuts string with the HTML tags by the specified number of chars and strips empty HTML tags from the output.
	 *
	 * @param	string	$text	text to cut
	 * @param	int	$len	number of chars to keep in the resulting string
	 * @param	string	$delim	optional regexp string of the stop-chars, used to split the text when limit reached in the middle of the current word
	 * @return	string
	 * @author	Ilya Lebedev
	 * @see		http://debugger.ru/blog/prevju_teksta_s_html_tegami
	 */
	function preview($text, $len, $delim='')
	{
		if ( empty($delim) ) {
			$delim = '\s;,.!?:#';
		}

		// Store for an using within callback method
		$GLOBALS['_' . $_SERVER['REQUEST_TIME']]['Text_Htmlizer'] = array(
			'l' => $len,
			'd' => $delim,
		);

		// Limit the input text about the $len amount of characters
		$result = preg_replace_callback(
			'/(<\/?[a-z]+(?:>|\s[^>]*>)|[^<]+)/mi', 
			array('Text_Htmlizer', '_preview'), 
			$text);

		// Destroy after an using
		unset($GLOBALS['_' . $_SERVER['REQUEST_TIME']]['Text_Htmlizer']);

		// Skip empty tags
		while ( preg_match('/<([a-z]+)[^>]*>\s*<\/\\1>/mi', $result) ) {
			$result = preg_replace('/<([a-z]+)[^>]*>\s*<\/\\1>/mi', '', $result);
		}

		return $result;
	}

	// }}}
	// {{{

	function _preview($matches)
	{
		$l =& $GLOBALS['_' . $_SERVER['REQUEST_TIME']]['Text_Htmlizer']['l'];
		$d =& $GLOBALS['_' . $_SERVER['REQUEST_TIME']]['Text_Htmlizer']['d'];

		// This is tag
		if ( $matches[0]{0} == '<' ) {
			return $matches[0];
		}

		// Empty line
		if ( $l <= 0 ) {
			return '';
		}

		// Split by any delimiters
		$l1 = $l - 1;
		$result = preg_split("/(.{0,$l1}+(?=[$d]))|(.{0,$l}[^$d]*)/ms", 
			$matches[0], 
			2, 
			PREG_SPLIT_DELIM_CAPTURE);

		// Obtain the valid tail of string
		if ( $result[1] ) {
			$l -= strlen($result[1]) + 1;
			$result = $result[1];
		} else {
			$l -= strlen($result[2]);
			$result = $result[2];
		}

		//$result = rtrim($result);

		return $result;
	}

	// }}}

}

// }}}

?>