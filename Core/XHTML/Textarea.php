<?php

/**
 * This is a effective class for XHTML textarea control
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

class XHTML_Textarea extends XHTML_Control
{

	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML control of an element '<textarea>'
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Textarea
	 * @access public
	 */
	function __construct($attrs=array())
	{
		parent::__construct('textarea', $attrs);
	}

	// }}}
	// {{{

	/**
	 * Disables children for 'textarea'
	 *
	 * @param	HTML_Element	$child
	 * @return	HTML_Element	Appended item or 'null'
	 * @access	public
	 */
	function appendChild($child)
	{
		return null;
	}

	// }}}
	// {{{

	/**
	 * List of available attributes
	 *
	 * @param  void
	 * @return array
	 * @access public
	 */
	function availAttributes()
	{
		static $defaultAttrs = array(
			//'disabled',
			//'name',
			'rows',
			'cols',
			'readonly',
			'accesskey',
			'tabindex',
			'onblur',
			'onchange',
			'onfocus',
			'onselect',
			'wrap',
		);
		return array_merge(parent::availAttributes(), $defaultAttrs);
	}

	// }}}
	// {{{

	/**
	 * Overriden method for representing of '<textarea>' value
	 *
	 * @param  void
	 * @return string
	 * @access public
	 */
	function innerHtml()
	{
		$html = $this->getValue();
		if ( is_null($html) || $this->getMetas('no-overwrite') ) {
			$html = $this->getDefaultValue();
		}
		return htmlspecialchars($html);
	}

	// }}}

}

// }}}

?>