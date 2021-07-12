<?php

/**
 * This is a mime type class
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
 * @category    Mime
 * @package     Mime_Types
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

// {{{

class Mime_Types
{

	// {{{ properties

	var $_file;

	var $_mimes;

	// }}}
	// {{{

	function __construct($file=false)
	{
		$this->_file = $this->setFile($file);
		$lines = $this->loadFile($this->_file);
		$this->_mimes = $this->parse($lines);
	}

	// }}}
	// {{{

	function getMime($mime=false, $asIs=false)
	{
		@list($mimeType, $mimeSubType) = explode('/', $mime);

		// */*
		if ( empty($mimeType) || $mimeType == '*' ) {
			if ( $asIs ) {
				return $this->_mimes;
			}

			$result = array();
			foreach ($this->_mimes as $k => $v) {
				$result[$v[1]][$k] = $k;
			}
			return $result;
		}

		// type/subType
		if ( $mimeSubType && $mimeSubType != '*' && array_key_exists($mime, $this->_mimes) ) {
			return $asIs 
				? $this->_mimes[$mime]
				: $this->_mimes[$mime][0];
		}

		// type/*
		$result = array();
		foreach ($this->_mimes as $k => $v) {
			if ( $v[1] != $mimeType ) {
				continue;
			}
			$result[] = $asIs 
				? $v 
				: $k;
		}
		return $result;
	}

	// }}}
	// {{{

	function loadFile($file)
	{
		$mimes = @file($file);
		return $mimes;
	}

	// }}}
	// {{{

	function parse($lines)
	{
		$result = array();
		foreach ((array)$lines as $line) {
			$line = trim($line);
			if ( empty($line) || preg_match('/^\s*#/', $line) ) {
				continue;
			}
			preg_match('|^([^/]+)\s*/\s*([^/\s]+)\s*(.+)?$|', $line, $matches);
			$type = trim($matches[1]);
			$subType = trim($matches[2]);
			$extensions = empty($matches[3]) 
				? array()
				: preg_split('/\s+/', trim($matches[3]));
			$mime = $type . '/' . $subType;
			$result[$mime] = array(
				$mime,
				$type,
				$subType,
				$extensions,
			);
		}
		return $result;
	}

	// }}}
	// {{{

	function setFile($file)
	{
		if ( empty($file) ) {
			$file = dirname(__FILE__) . '/Types/mime.types';
		}
		return file_exists($file) && is_readable($file) 
			? $file 
			: false;
	}

	// }}}

}

// }}}

?>