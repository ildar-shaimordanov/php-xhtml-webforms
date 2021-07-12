<?php

/**
 * This is a simple static XHTML text
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

require_once 'XHTML/Element.php';

// {{{

class XHTML_Text extends XHTML_Element
{

	// {{{ properties

	/**
	 * Inner text
	 *
	 * @var		string
	 * @access	private
	 */
	var $_text;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML text-container of an element using tag name, and attributes
	 * Available containers are
	 * - a (anchor)
	 * - div, span, p (block-oriented, text-oriented elements and paragraph)
	 * - pre (preformatted text)
	 * - h1-h6 (headers)
	 * - label (text label used in couple with form-controls)
	 * - fieldset, legend (group of form-controls)
	 *
	 * @param	string	$tagName	Tag name of the HTML element
	 * @param	array	$attrs	Assoc.array of attributes for the element
	 * @return	XHTML_Text
	 * @access	public
	 */
	function __construct($attrs=array())
	{
		static $containers = array(
			'a',
			'div',
			'fieldset',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'label',
			'legend',
			'p',
			'pre',
			'span',
		);
		$tagName = strtolower(@$attrs['kind']);
		if ( empty($tagName) || ! in_array($tagName, $containers) ) {
			$tagName = 'div';
		}

		$this->_text = @$attrs['text'];

		parent::__construct($tagName, $attrs);
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
		static $sameTag = array(
			'a',
			'label',
			'legend',
		);

		$tagName = $this->getTagName();

		if ( $this->_text && $tagName != 'fieldset' ) {
			return false;
		}
		$childTagName = $child->getTagName();
		if ( $tagName == $childTagName && in_array($tagName, $sameTag) ) {
			return false;
		}
		return true;
	}

	// }}}
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
		static $attrs = array(
			'a' => array(
				'name',
				'href',
				'rel',
				'rev',
				'type',
				'target',
				'hreflang',
				'charset',
				'accesskey',
				'tabindex',
				'shape',
				'coords',
				'onfocus',
				'onblur',
			),
			'label' => array(
				'accesskey',
				'for',
				'onfocus',
				'onblur',
			),
			'legend'	=> array(
				'accesskey',
			),
		);
		$tagName = $this->getTagName();
		return array_key_exists($tagName, $attrs) 
			? array_merge(parent::availAttributes(), $attrs[$tagName]) 
			: parent::availAttributes();
	}

	// }}}
	// {{{

	/**
	 * Returns an inner text of a container or inner html-elements elsewhere
	 *
	 * @param  void
	 * @return string
	 * @access public
	 */
	function innerHtml()
	{
		$text = $this->_text;
		if ( $text ) {
			$text = htmlspecialchars($text);
			if ( $this->getTagName() != 'fieldset' ) {
				return $text;
			}
			$text = '<legend>' . $text . '</legend>';
		}
		return $text . parent::innerHtml();
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
	// {{{

	function toArray()
	{
		$result = parent::toArray();
		$result['attrs']['kind'] = $this->getTagName();
		if ( $this->_text ) {
			$result['attrs']['text'] = $this->_text;
		}
		return $result;
	}

	// }}}

}

// }}}

?>