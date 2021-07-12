<?php

/**
 * This is an extension class for XHTML input control working as toggle
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

// {{{

class XHTML_Togglebox extends XHTML_Input
{

	// {{{

	/**
	 * Constructor.
	 * Creates a checkboxed togglebox
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Listbox
	 * @access public
	 */
	function __construct($attrs=array())
	{
		$attrs['type'] = 'checkbox';
		if ( empty($attrs['value']) ) {
			$attrs['value'] = 'on';
		}

		parent::__construct($attrs);
	}

	// }}}
	// {{{

	/**
	 * Returns the value from the control
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function getValue()
	{
		$value = parent::getValue();
		if ( $value === null ) {
			$value = '';
		}
		return $value;
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
		$html = '';
		$html .= '<input type="hidden" value="" name="' . htmlspecialchars($this->getAttribute('name')) . '" />';
		$html .= parent::outerHtml();
		return $html;
	}

	// }}}
	// {{{

	/**
	 * Validates a value with the eigenvalue of the control
	 *
	 * @param	mixed	The submitted data
	 * @return	mixed	It is true if the value is valid or false elsewhere
	 * @public
	 */
	function validEigenvalue($value)
	{
		return $value == '' || parent::validEigenvalue($value);
	}

	// }}}

}

// }}}

?>