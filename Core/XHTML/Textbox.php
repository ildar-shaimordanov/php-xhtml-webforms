<?php

/**
 * This is an extension class for XHTML complex controls 
 * such as texts, passwords, hiddens and textareas
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
require_once 'XHTML/Input.php';
require_once 'XHTML/Textarea.php';

// {{{

class XHTML_Textbox extends XHTML_Group
{

	// {{{ properties

	/**
	 * Type of the textbox group
	 *
	 * @var	string
	 * @access	private
	 */
	var $_type;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML control of '<input type="text" />', '<input type="password" />', '<input type="hidden" />' or '<textarea></textarea>'
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Textbox
	 * @access public
	 */
	function __construct($attrs=array())
	{
		static $boxes = array(
			'text',
			'password',
			'hidden',
			'textarea',
		);

		$this->_type = strtolower(@$attrs['type']);
		if ( ! in_array($this->_type, $boxes) ) {
			$this->_type = 'text';
		}

		parent::__construct($attrs);

		$class = $this->_type == 'textarea' 
			? 'XHTML_Textarea' 
			: 'XHTML_Input';

		$object =& new $class($attrs);
		$this->appendChild($object);
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
		if ( count($this->getElementsByTagName()) ) {
			return false;
		}

		$childTagName = $child->getTagName();
		return ( $childTagName == 'textarea' ) 
			|| ( $childTagName == 'input' && $child->getType() == $this->_type );
	}

	// }}}
	// {{{

	function toArray()
	{
		$elements = $this->getElementsByTagName();

		$result = $elements[0]->toArray();
		$result['element'] = 'Textbox';
		$result['attrs']['type'] = $this->_type;

		return $result;
	}

	// }}}

}

// }}}

?>