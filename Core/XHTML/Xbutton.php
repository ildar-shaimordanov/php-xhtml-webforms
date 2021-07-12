<?php

/**
 * This is a effective class for XHTML button control
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

require_once 'XHTML/Control.php';

// {{{

class XHTML_Xbutton extends XHTML_Control
{

	// {{{ properties

	/**
	 * Type of 'button' tag
	 *
	 * @var	 string
	 * @access  private
	 */
	var $_type;

	/**
	 * Inner text
	 *
	 * @var	 string
	 * @access  private
	 */
	var $_text;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML control of an element '<button></button>'
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Xbutton
	 * @access public
	 */
	function __construct($attrs=array())
	{
		static $types = array(
			'submit',
			'reset',
			'button',
		);

		$attrs['type'] = strtolower(@$attrs['type']);
		if ( empty($attrs['type']) || ! in_array($attrs['type'], $types) ) {
			$attrs['type'] = 'submit';
		}

		$this->_text = @$attrs['text'];

		parent::__construct('button', $attrs);
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
		static $disabledTags = array(
			'a',
			'button',
			'fieldset',
			'form',
			'iframe',
			'input',
			'label',
			'select',
			'textarea',
		);
		return ! in_array($child->getTagName(), $disabledTags);
#		if ( in_array($child->getTagName(), $disabledTags) ) {
#			$child = null;
#		}
	}

	// }}}
	// {{{

	/**
	 * List of attributes for any types of '<button></button>'
	 *
	 * @param  void
	 * @return array
	 * @access public
	 */
	function availAttributes()
	{
		static $attrs = array(
			'type',
			//'name',
			'value',
			//'disabled',
			'accesskey',
			'tabindex',
			'onfocus',
			'onblur',
		);
		return array_merge(parent::availAttributes(), $attrs);
	}

	// }}}
	// {{{

	/**
	 * Overriden method for representing of '<button>' value
	 *
	 * @param  void
	 * @return string
	 * @access public
	 */
	function innerHtml()
	{
		return $this->_text 
			? htmlspecialchars($this->_text) 
			: parent::innerHtml();
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();
		if ( $this->_text ) {
			$result['attrs']['text'] = $this->_text;
		}
		return $result;
	}

	// }}}

}

// }}}

?>