<?php

/**
 * This is an extension class for XHTML group controls 
 * such as list of checkboxes, radios or selects
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
require_once 'XHTML/Input.php';
require_once 'XHTML/Select.php';

// {{{

class XHTML_Listbox extends XHTML_Group
{

	// {{{ properties

	/**
	 * Type of the listbox group
	 *
	 * @var	string
	 * @access	private
	 */
	var $_type;

	/**
	 * Type of the listbox group
	 *
	 * @var	boolean
	 * @access	private
	 */
	var $_isSelect;

	/**
	 * Label of the listbox
	 *
	 * @var	string
	 * @access	private
	 */
	var $_label;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an group of XHTML controls of '<input type="checkbox" />' and '<input type="radio" />'
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Listbox
	 * @access public
	 */
	function __construct($attrs=array())
	{
		static $lists = array(
			'select',
			'multiple',
			'checkbox',
			'radio',
		);

		$this->_type = strtolower(@$attrs['type']);
		if ( empty($this->_type) || ! in_array($this->_type, $lists) ) {
			$this->_type = 'select';
		}

		parent::__construct($attrs);

		switch ($this->_type) {
		case 'select':
			$this->_isSelect = true;
			break;
		case 'multiple':
			$this->_isSelect = true;
			$attrs['multiple'] = 'multiple';
			break;
		case 'checkbox':
		case 'radio':
			$this->_isSelect = false;
			break;
		}

		if ( ! empty($attrs['name']) ) {
			if ( $this->_type == 'radio' || $this->_type == 'select' ) {
				$attrs['name'] = preg_replace('/\s*\[\s*\]\s*$/', '', $attrs['name']);
			} elseif ( ! preg_match('/\[\s*\]\s*$/', $attrs['name']) ) {
				$attrs['name'] .= '[]';
			}
		}

		if ( $this->_isSelect ) {
			$object =& new XHTML_Select($attrs);
			$this->appendChild($object);
			return;
		}

		$options = (array)@$attrs['options'];
		$valueList = (array)@$attrs['value'];
		$index = -1;
		foreach ($options as $value => $label) {
			if ( is_scalar($label) ) {
				$this->_makeAppend($attrs, $value, $label, $valueList);
				continue;
			}
			foreach ($label as $v => $t) {
				$this->_makeAppend($attrs, $v, $t, $valueList);
			}
		}
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
		$childTagName = $child->getTagName();
		return (   $this->_isSelect && $childTagName == 'select' && count($this->getElementsByTagName()) == 0 ) 
			|| ( ! $this->_isSelect && $childTagName == 'input'  && $child->getType() == $this->_type );
	}

	// }}}
	// {{{

	function getOptions()
	{
		static $options = null;

		if ( $options ) {
			return $options;
		}

		$elements = $this->getElementsByTagName();

		if ( $this->_isSelect ) {
			$options = $elements[0]->getOptions();
			return $options;
		}

		foreach ($elements as $element) {
			$v = $element->getAttribute('value');
			$l = $element->getMetas('label');
			$options[$v] = $l;
		}
		return $options;
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
		$html = parent::outerHtml();
		if ( ! $this->_isSelect && $this->_label ) {
			$html = '<fieldset><legend>' . htmlspecialchars($this->_label) . '</legend>' . $html . '</fieldset>';
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
		// Setting of label
		if ( ! $this->_isSelect && array_key_exists('label', (array)$metas) ) {
			$this->_label = $metas['label'];
			unset($metas['label']);
		}

/*
		// Setting of source
		if ( isset($metas['source']) ) {
			XHTML_Validator_Common::isMetaSource($metas['source'], $source);
			if ( empty($source) ) {
				unset($metas['source']);
			} else {
				$metas['source'] = $source;
				call_user_func($source, $this);
			}
		}
*/

		parent::setMetas($metas);
	}

	// }}}
	// {{{

/*
	function setOptions($options)
	{
		if ( $this->_isSelect ) {
			$object =& new XHTML_Select($attrs);
			$this->appendChild($object);
			return;
		}

		$options = (array)@$attrs['options'];
		$valueList = (array)@$attrs['value'];
		$index = -1;
		foreach ($options as $value => $label) {
			if ( is_scalar($label) ) {
				$this->_makeAppend($attrs, $value, $label, $valueList);
				continue;
			}
			foreach ($label as $v => $t) {
				$this->_makeAppend($attrs, $v, $t, $valueList);
			}
		}
	}
*/

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();

		if ( $this->_isSelect ) {
			$result = $result[0];
			$result['element'] = 'listbox';
			$result['attrs']['type'] = $this->_type;
		} else {
			$value = array();
			$attrs = array();
			$metas = array();
			foreach ($result as $k => $v) {
				if ( is_string($k) ) {
					continue;
				}
				if ( isset($v['attrs']['checked']) ) {
					$value[] = $v['attrs']['value'];
				}
				$attrs = array_merge_recursive($attrs, $v['attrs']);
				$metas = array_merge_recursive($metas, $v['meta']);
				unset($result[$k]);
			}

			Core_Utils::unique($attrs);

			unset($attrs['value']);
			$result['attrs'] = $attrs;
			if ( $value ) {
				$result['attrs']['value'] = $value;
			}
			$result['attrs']['options'] = $this->getOptions();

			Core_Utils::unique($metas);

			unset($metas['label']);
			$result['meta'] = $metas;
			if ( $this->_label ) {
				$result['meta']['label'] = $this->_label;
			}
		}

		if ( isset($result['attrs']['name']) ) {
			$result['attrs']['name'] = preg_replace('/\s*\[\s*\]\s*$/', '', $result['attrs']['name']);
		}

		return $result;
	}

	// }}}
	// {{{ privates

	function _makeAppend($attrs, $value, $label, $valueList)
	{
		if ( in_array($value, $valueList) ) {
			$attrs['checked'] = 'checked';
		} else {
			unset($attrs['checked']);
		}

		$attrs['value'] = $value;

		$object =& new XHTML_Input($attrs);
		$object->setMetas(array(
			'label'	=> $label,
		));

		$this->appendChild($object);
	}

	// }}}

}

// }}}

?>