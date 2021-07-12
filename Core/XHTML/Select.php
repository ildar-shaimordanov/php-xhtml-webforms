<?php

/**
 * This is a effective class for XHTML select control
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

class XHTML_Select extends XHTML_Control
{

	// {{{ properties

	/**
	 * Options list
	 *
	 * @var	array
	 * @access	private
	 */
	var $_options = array();

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML control of elements '<select>', <optgroup>', and '<option>'
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Select
	 * @access public
	 */
	function __construct($attrs=array())
	{
		if ( isset($attrs['name']) && in_array('multiple', $attrs, true) && ! preg_match('/\[\s*\]\s*$/', $attrs['name']) ) {
			$attrs['name'] .= '[]';
		}
		$this->setOptions((array)@$attrs['options']);
		parent::__construct('select', $attrs);
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
		$options = $this->getOptions();
		return empty($options) && ( get_class($child) == 'XHTML_Option' || get_class($child) == 'XHTML_Optgroup' );
	}

	// }}}
	// {{{

	/**
	 * List of attributes for any types of '<select>'
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
			'size',
			'multiple',
			'tabindex',
			'onblur',
			'onchange',
			'onfocus',
		);
		return array_merge(parent::availAttributes(), $defaultAttrs);
	}

	// }}}
	// {{{

	/**
	 * Returns the parsed name of the control
	 *
	 * @param	boolean	$fullParsed
	 * @return	array
	 * @access	public
	 */
	function getNameParsed($fullParsed=false)
	{
		$nameParsed = parent::getNameParsed();
		if ( $this->getAttribute('multiple') ) {
			array_pop($nameParsed);
		}
		return $nameParsed;
	}

	// }}}
	// {{{

	function getOptions()
	{
		return $this->_options;
	}

	// }}}
	// {{{

	/**
	 * Overriden method for representing of '<select>' value
	 *
	 * @param  void
	 * @return string
	 * @access public
	 */
	function innerHtml()
	{
		$options = $this->getOptions();
		$valueList = (array)$this->getValue();
		if ( empty($valueList) || $this->getMetas('no-overwrite') ) {
			$valueList = (array)$this->getDefaultValue();
		}

		$html = '';
		$index = -1;
		foreach ($options as $value => $text) {
			if ( is_scalar($text) ) {
				$html .= XHTML_Select::_makeOption($index, $value, $text, $valueList);
				continue;
			}
			$html .= "\n" . '<optgroup label="' . htmlspecialchars($value) . '">';
			//$html .= "\n" . '<optgroup label="' . ( is_string($value) ? htmlspecialchars($value) : 'Label' ) . '">';
			foreach ($text as $v => $t) {
				$html .= XHTML_Select::_makeOption($index, $v, $t, $valueList);
			}
			$html .= '</optgroup>';
		}
		return $html;
	}

	// }}}
	// {{{

	function setOptions($options=null)
	{
		$this->_options = $options;
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();
		$options = $this->getOptions();
		if ( $options && ! $this->getMetas('source') ) {
			$result['attrs']['options'] = $options;
		}
		if ( isset($result['attrs']['name']) ) {
			$result['attrs']['name'] = preg_replace('/\s*\[\s*\]\s*$/', '', $result['attrs']['name']);
		}
		return $result;
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
		$options = $this->getOptions();
		$result = array();
/*
		$index = -1;
		foreach ($options as $val => $text) {
			if ( is_scalar($text) ) {
				XHTML_Select::_makeOption($index, null, null, null, true);
				$result[] = $index;
				continue;
			}
			foreach ($text as $v => $t) {
				XHTML_Select::_makeOption($index, null, null, null, true);
				$result[] = $index;
			}
		}
*/
		foreach ($options as $val => $text) {
			if ( is_scalar($text) ) {
				$result[] = $val;
				continue;
			}
			foreach ($text as $v => $t) {
				$result[] = $v;
			}
		}
		return array_intersect((array)$value, $result);
	}

	// }}}
	// {{{ privates

	function _makeOption( & $index, $value, $text, $submitted, $valueOnly=false)
	{
/*
		if ( ! is_string($value) ) {
			$index++;
			$value = $index;
		}
		if ( $valueOnly ) {
			return;
		}
*/
		return sprintf("\n" . '<option value="%s"%s>%s</option>', 
			htmlspecialchars($value), 
			in_array((string)$value, $submitted) ? ' selected="selected"' : '', 
			htmlspecialchars($text));
	}

	// }}}

}

// }}}

?>