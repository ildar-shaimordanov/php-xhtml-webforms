<?php

/**
 * This is an extension class for XHTML complex control 
 * such as a peer of confirm form fields
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

require_once 'XHTML/Group.php';

// {{{

class XHTML_Comparebox extends XHTML_Group
{

	// {{{

	/**
	 * Constructor.
	 * Creates two XHTML controls
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Comparebox
	 * @access public
	 */
	function __construct($attrs=array())
	{
		parent::__construct($attrs);
	}

	// }}}
	// {{{

	/**
	 * Evaluates the child for validity before appending into the owner
	 *
	 * @param	HTML_Common	$child
	 * @return	boolean
	 * @access	public
	 */
	function appendingChild($child)
	{
		return parent::appendingChild($child);
	}

	// }}}

}

// }}}

?>