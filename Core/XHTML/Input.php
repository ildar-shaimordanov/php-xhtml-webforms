<?php

/**
 * This is a effective class for XHTML input control
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

class XHTML_Input extends XHTML_Control
{

	// {{{

	/**
	 * Type of 'input' tag
	 *
	 * @var	 string
	 * @access  private
	 */
	var $_type;

	/**
	 * Default checked attribute for the 'radio' and 'checkbox'
	 *
	 * @var	 string
	 * @access  private
	 */
	var $_isDefaultChecked = false;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML control of an element '<input />'
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Input
	 * @access public
	 */
	function __construct($attrs=array())
	{
		static $types = array(
			'text',
			'password',
			'checkbox',
			'radio',
			'submit',
			'reset',
			'button',
			'image',
			'file',
			'hidden',
		);
		static $withoutValueAttr = array(
			'file',
			'image',
		);
		static $checkable = array(
			'checkbox',
			'radio',
		);

		// Default is 'text'
		$attrs['type'] = strtolower(@$attrs['type']);
		if ( empty($attrs['type']) || ! in_array($attrs['type'], $types) ) {
			$attrs['type'] = 'text';
		}
		$this->_type = strtolower($attrs['type']);

		// The 'file' and the 'image' are not have the 'value' attribute
		if ( in_array($attrs['type'], $withoutValueAttr) ) {
			unset($attrs['value']);
		}

		// Default values of checkable
		if ( ! array_key_exists('value', $attrs) && in_array($attrs['type'], $checkable) ) {
			$attrs['value'] = 'on';
		}

		parent::__construct('input', $attrs);

		// Default checked flag
		$this->_isDefaultChecked = in_array($this->getType(), $checkable) && $this->getAttribute('checked');
	}

	// }}}
	// {{{

	/**
	 * Disables children for 'input'
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
	 * List of attributes for any types of '<input />'
	 *
	 * @param  void
	 * @return array
	 * @access public
	 * @see	   http://www.w3.org/TR/web-forms-2/#summary
	 */
	function availAttributes()
	{
		static $attrs = array(
			'text' => array(
				'accesskey',
				'autocomplete',
				'autofocus',
				//'disabled',
				'maxlength',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'readonly',
				'required',
				//'size', // Deprecated
				'tabindex',
				'type',
				'value',
			),
			'password' => array(
				'accesskey',
				'autocomplete',
				'autofocus',
				//'disabled',
				'maxlength',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'readonly',
				'required',
				//'size', // Deprecated
				'tabindex',
				'type',
				'value',
			),
			'checkbox' => array(
				'accesskey',
				'autofocus',
				'checked',
				//'disabled',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'required',
				'tabindex',
				'type',
				'value',
			),
			'radio' => array(
				'accesskey',
				'autofocus',
				'checked',
				//'disabled',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'required',
				'tabindex',
				'type',
				'value',
			),
			'button' => array(
				'accesskey',
				'autofocus',
				//'disabled',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'tabindex',
				'type',
				'value',
			),
			'submit' => array(
				'accesskey',
				'autofocus',
				//'disabled',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'tabindex',
				'type',
				'value',
			),
			'reset' => array(
				'accesskey',
				'autofocus',
				//'disabled',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'tabindex',
				'type',
				'value',
			),
			'file' => array(
				'accept',
				'accesskey',
				'autofocus',
				//'disabled',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'required',
				'type',
				'tabindex',
			),
			'hidden' => array(
				'disabled',
				'name',
				'type',
				'value',
			),
			'image' => array(
				'accesskey',
				'alt',
				'autofocus',
				//'disabled',
				'ismap',
				//'name',
				'onblur',
				'onchange',
				'onfocus',
				'onselect',
				'src',
				'tabindex',
				'type',
				'usemap',
			),
		);
		$type = $this->getType();
		return $type == 'hidden' 
			? $attrs[$type] 
			: array_merge(parent::availAttributes(), $attrs[$this->getType()]);
	}

	// }}}
	// {{{

	function availAttributesReplacing()
	{
		return $this->getType() == 'hidden';
	}

	// }}}
	// {{{

	/**
	 * List of available submits
	 * For '<input type="file" />' it returns array('FILES')
	 *
	 * @param  array  $submits	List of available submits
	 * @return array  List of available submits
	 * @access public
	 */
	function availSubmits($submits)
	{
		static $availSubmits = array(
			'FILES',
		);
		return $this->getType() == 'file' 
			? $availSubmits
			: parent::availSubmits($submits);
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
		static $checkable = array(
			'checkbox',
			'radio',
		);

		$nameParsed = parent::getNameParsed();
		if ( ! $fullParsed && in_array($this->getType(), $checkable) && preg_match('/\[\s*\]\s*$/', $this->getName()) ) {
			array_pop($nameParsed);
		}
		return $nameParsed;
	}

	// }}}
	// {{{

	/**
	 * Returns value of 'type' attribute
	 *
	 * @param  void
	 * @return string
	 * @access public
	 */
	function getType()
	{
		return $this->_type;
	}

	// }}}
	// {{{

	/**
	 * Converts inner HTML elements to string and returns one
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function innerHtml()
	{
		return '';
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
		static $prefixed = array(
			'text',
			'password',
			'file',
		);
		static $postfixed = array(
			'checkbox',
			'radio',
		);

		$label = $this->getLabel();
		$type = $this->getType();

		$html = '';
		if ( $label && in_array($type, $prefixed) ) {
			$html .= $label;
		}
		$html .= '<' . $this->getTagName() . $this->getAttributes(true) . ' />';
		if ( $label && in_array($type, $postfixed) ) {
			$html .= $label;
		}
		return $html;
	}

	// }}}
	// {{{

	/**
	 * Sets additional behavor of the HTML control through meta-tags
	 *
	 * @param	array	$metas
	 * @return	void
	 * @access	public
	 */
	function setMetas($metas=array())
	{
		static $notOverwritten = array(
			'file',
			'image',
			'password',
		);
		static $availSubmits = array(
			'FILES',
		);

		$type = $this->getType();

		if ( in_array($type, $notOverwritten) ) {
			$metas['no-overwrite'] = 'no-overwrite';
		}

		if ( $type == 'password' ) {
			$metas['validator'] = 'required ' . @$metas['validator'];
		}

		$metas['submit'] = $type == 'file' 
			? $availSubmits 
			: $this->availSubmits(@(array)$metas['submit']);

		if ( $type == 'file' ) {
			unset($metas['filter']);
		}

		parent::setMetas($metas);
	}

	// }}}
	// {{{

	/**
	 * Sets the actual value of a control
	 *
	 * @param	mixed	$value
	 * @return	void
	 * @access	public
	 */
	function setValue($value)
	{
		static $checkable = array(
			'checkbox',
			'radio',
		);

		$defaultValue = $this->getDefaultValue();
		$type = $this->getType();

		$isCheckable = in_array($type, $checkable);
		$isArrayLike = preg_match('/\[\s*\]\s*$/', $this->getName());

		$hasBeenSubmitted = true;
		if ( null === $value ) {
			$value = $defaultValue;
			$hasBeenSubmitted = false;
		}

		if ( $isCheckable && $isArrayLike ) {
			if ( ! is_array($value) ) {
				return;
			}
			if ( $hasBeenSubmitted && in_array($defaultValue, $value) ) {
				$this->setAttribute('checked', 'checked');
				parent::setValue($defaultValue);
			} else {
				$this->setAttribute('checked', null);
				parent::setValue(null);
			}
		} else {
			if ( $isCheckable ) {
				if ( $value == $defaultValue ) {
					if ( $hasBeenSubmitted ) {
						$this->setAttribute('checked', 'checked');
						parent::setValue($defaultValue);
					}
				} else {
					$this->setAttribute('checked', null);
					parent::setValue(null);
				}
			} else {
				if ( ! $this->getMetas('no-overwrite') ) {
					$this->setAttribute('value', $value);
				}
				if ( $hasBeenSubmitted ) {
					parent::setValue($value);
				}
			}
		}
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();

		if ( $this->_isDefaultChecked ) {
			$result['attrs']['checked'] = 'checked';
		} else {
			unset($result['attrs']['checked']);
		}

		return $result;
	}

	// }}}

}

// }}}

?>