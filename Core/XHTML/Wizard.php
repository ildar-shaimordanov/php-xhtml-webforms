<?php

/**
 * This is an extension class for XHTML controls groupped by pages or tabs
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

require_once 'Utils.php';

require_once 'XHTML/Group.php';

// {{{

class XHTML_Wizard extends XHTML_Group
{

	// {{{ properties

	/**
	 * Type of wizard (page or tab, by default)
	 *
	 * @var	string
	 * @access	private
	 */
	var $_type = 'tab';

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates a HTML element using tag name, and attributes
	 *
	 * @param	string	$tagName	Tag name of the HTML element
	 * @param	array	$attrs		Assoc.array of attributes for the element
	 * @return	XHTML_Group
	 * @access	public
	 */
	function __construct($attrs=array())
	{
		static $types = array(
			'tab',
			'page',
		);

		if ( isset($attrs['type']) && in_array($attrs['type'], $types) ) {
			$this->_type = $attrs['type'];
		}

		parent::__construct($attrs);
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
		// NOTE!!! add support of pages
		return parent::outerHtml();
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();
		$result['type'] = $this->_type;
		return $result;
	}

	// }}}

}

// }}}
// {{{

class XHTML_Tab extends XHTML_Group
{

	// {{{ properties

	/**
	 * Label for this tab
	 *
	 * @var	string
	 * @access	private
	 */
	var $_label = '';

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates a HTML element using tag name, and attributes
	 *
	 * @param	string	$tagName	Tag name of the HTML element
	 * @param	array	$attrs		Assoc.array of attributes for the element
	 * @return	XHTML_Group
	 * @access	public
	 */
	function __construct($attrs=array())
	{
		if ( isset($attrs['label']) ) {
			$this->_label = $attrs['label'];
		}

		parent::__construct($attrs);
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
		// NOTE!!! add support of pages
		$html .= '<div class="label">' . htmlspecialchars($this->_label) . '</div>';
		$html .= '<div class="xf_tab_inner">' . parent::outerHtml() . '</div>';
		return '<div class="xf_tab">' . $html . '</div>';
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();
		$result['label'] = $this->_label;
		return $result;
	}

	// }}}

}

// }}}

?>