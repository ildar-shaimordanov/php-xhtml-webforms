<?php

/**
 * This is a semi-abstract class of XHTML elements
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

require_once 'XHTML/Common.php';

// {{{

class XHTML_Element extends XHTML_Common
{

	// {{{

	/**
	 * List of available attributes
	 * This virtual method may be overriden by inherited class
	 * There is no reason to call it immediately
	 *
	 * @param	void
	 * @return	array
	 * @access	public
	 */
	function availAttributes()
	{
		static $defaultAttrs = array(
			// Core attributes
			'id',
			'class',
			'style',
			'title',
			// Internationalization
			'lang',
			'dir',
			// Events
			'onclick',
			'ondblclick',
			'onmousedown',
			'onmouseup',
			'onmouseover',
			'onmousemove',
			'onmouseout',
			'onkeypress',
			'onkeydown',
			'onkeyup',
		);
		return $defaultAttrs;
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
		$tagName = $this->getTagName();
		return '<' . $tagName . $this->getAttributes(true) . '>' . $this->innerHtml() . '</' . $tagName . '>';
	}

	// }}}

}

// }}}

?>