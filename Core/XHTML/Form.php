<?php

/**
 * This is a effective class for XHTML form
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

require_once 'Object.php';

require_once 'XHTML/Element.php';

// {{{

class XHTML_Form extends XHTML_Element
{

	// {{{ properties

	var $_html = null;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an XHTML form
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Form
	 * @access public
	 */
	function __construct($attrs=array())
	{
		static $availMethods = array(
			'get' => array(
				'application/x-www-form-urlencoded',
			),
			'post' => array(
				'multipart/form-data',
			),
		);

		if ( empty($attrs['action']) ) {
			$attrs['action'] = $_SERVER['SCRIPT_NAME'];
		}
		if ( empty($attrs['method']) || ! in_array($attrs['method'], array_keys($availMethods)) ) {
			$attrs['method'] = 'get';
		}
		if ( empty($attrs['enctype']) || ! in_array($attrs['enctype'], $availMethods[$attrs['method']]) ) {
			$attrs['enctype'] = reset($availMethods[$attrs['method']]);
		}

		parent::__construct('form', $attrs);
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
		if ( $child->getTagName() == 'form' ) {
			return false;
		}

		if ( $child->getTagName() == 'input' && $child->getType() == 'file' ) {
			$this->setAttribute('method', 'post');
			$this->setAttribute('enctype', 'multipart/form-data');
			$fileAccept = $child->getAttribute('accept');
			if ( $fileAccept ) {
				$accept = $this->getAttribute('accept');
				$this->setAttribute('accept', $accept 
					? $accept . ', ' . $fileAccept 
					: $fileAccept);
			}
		}

#		$this->import($child);

		return true;
	}

	// }}}
	// {{{

	/**
	 * List of attributes for the 'form' XHTML element
	 *
	 * @param  void
	 * @return array
	 * @access public
	 */
	function availAttributes()
	{
		static $formAttrs = array(
			'accept',
			'accept-charset',
			'action',
			'enctype',
			'method',
			'name',
			'onreset',
			'onsubmit',
			'target',
		);
		return array_merge(parent::availAttributes(), $formAttrs);
	}

	// }}}
	// {{{

	/**
	 * Returns the list of all controls of the form
	 *
	 * @param	void
	 * @return	array	The list of controls
	 * @access	public
	 */
	function getControls()
	{
		$cacheFormCtrl =& Core_Object::getStaticProperty('XHTML_Form', 'cacheFormCtrl');
		return $cacheFormCtrl['total'];
	}

	// }}}
	// {{{

	/**
	 * Returns named or all submitted data
	 *
	 * @param	string	$name	The name of control
	 * @return	mixed
	 * @access	public
	 */
	function getValue($name=false)
	{
		$cacheFormCtrl =& Core_Object::getStaticProperty('XHTML_Form', 'cacheFormCtrl');

		$result = array();

		if ( isset($cacheFormCtrl['store']) ) {
			foreach ($cacheFormCtrl['store'] as $control) {
				$cName = $control->getName();
				$value = $control->getValue();

				if ( $cName == $name ) {
					return $value;
				}

				if ( empty($name) && $value !== null ) {
					$nameParsed = $control->getNameParsed(true);
					$ref =& $result;
					foreach ($nameParsed as $namePart) {
						$ref =& $ref[$namePart];
					}
					$ref = $value;
				}
			}
		}

		return $name 
			? null 
			: $result;
	}

	// }}}
	// {{{

	/**
	 * Overwrites value of the control with data passed from requests
	 * This method modifies the submit-meta of all controls adding 
	 * the self-owned method attribute to the list of submits of control.
	 *
	 * @param	void
	 * @return	void
	 * @access	public
	 */
	function import($child=null)
	{
		if ( $child === null ) {
			$child = $this;
		} elseif ( XHTML_Form::isControl($child) ) {
			// Modifies the submit meta of the control
			$submits = $child->getMetas('submit');
			if ( empty($submits) ) {
				$submits = array();
			}
			array_unshift($submits, $this->getAttribute('method'));
			$child->setSubmit($submits);

			// Sets the value of the control from the request
			$result = $child->setValueSubmitted();

			/**
			 * meanings of keys:
			 * - total - total number of controls
			 * - named - number of controls with established names
			 * - store - number of controls with valid submitted values
			 * - noval - number of controls with empty or invalid values
			 */
			$cacheFormCtrl =& Core_Object::getStaticProperty('XHTML_Form', 'cacheFormCtrl');
			$cacheFormCtrl['total'][] = $child;
			if ( $child->getName() && ! $child->getAttribute('disabled') ) {
				$cacheFormCtrl['named'][] = $child;
				if ( $result ) {
					$cacheFormCtrl['store'][] = $child;
				} elseif ( $result === false ) {
					$cacheFormCtrl['noval'][] = $child;
				}
			}

			return;
		}

		$elements = $child->getElementsByTagName();
		foreach ($elements as $element) {
			$this->import($element);
		}
	}

	// }}}
	// {{{

	/**
	 * Evaluates an XHTML-element as a control
	 *
	 * @param	XHTML_Common	$element
	 * @return	boolean
	 * @access	public
	 * @static
	 */
	function isControl($element)
	{
		return is_a($element, 'XHTML_Control');
		//return $element instanceof 'XHTML_Control';
	}

	// }}}
	// {{{

	/**
	 * Checks that the submitted data are exists
	 *
	 * @param	void
	 * @return	boolean
	 * @access	public
	 */
	function isSubmitted()
	{
		$cacheFormCtrl =& Core_Object::getStaticProperty('XHTML_Form', 'cacheFormCtrl');
		return isset($cacheFormCtrl['named']) 
			&& ( isset($cacheFormCtrl['store']) || isset($cacheFormCtrl['noval']) )
			&& count($cacheFormCtrl['named']) >= count(@$cacheFormCtrl['store']) + count(@$cacheFormCtrl['noval']);
	}

	// }}}
	// {{{

	/**
	 * Checks that the submitted data are valid
	 *
	 * @param	void
	 * @return	boolean
	 * @access	public
	 */
	function isValid()
	{
		$cacheFormCtrl =& Core_Object::getStaticProperty('XHTML_Form', 'cacheFormCtrl');
		return isset($cacheFormCtrl['named']) 
			&& isset($cacheFormCtrl['store']) 
			&& count($cacheFormCtrl['named']) == count($cacheFormCtrl['store']);
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
		$html .= "\n" . '<!-- BEGIN of XHTML Forms -->' . "\n";
		$html .= parent::outerHtml();
		$html .= "\n" . '<!-- END of XHTML Forms -->' . "\n";
		return $html;
	}

	// }}}

}

// }}}

?>