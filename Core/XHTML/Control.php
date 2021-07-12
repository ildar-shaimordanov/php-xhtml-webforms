<?php

/**
 * This is a semi-abstract class of XHTML control elements
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
require_once 'Utils.php';

require_once 'XHTML/Element.php';
require_once 'XHTML/Validator/Common.php';

// {{{

class XHTML_Control extends XHTML_Element
{

	// {{{ properties

	/**
	 * Control name
	 *
	 * @var		string
	 * @access	private
	 */
	var $_name;

	/**
	 * Control name parsed
	 *
	 * @var		array
	 * @access	private
	 */
	var $_nameParsed;

	/**
	 * Actual value
	 *
	 * @var		mixed
	 * @access	private
	 */
	var $_value = null;

	/**
	 * Default value
	 *
	 * @var		mixed
	 * @access	private
	 */
	var $_defaultValue;

	/**
	 * Meta-data
	 *
	 * @var		array
	 * @access	private
	 */
	var $_metas = array();

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates an abstract XHTML control of an element using tag name, and attributes
	 *
	 * @param	string	$tagName	Tag name of the HTML element
	 * @param	array	$attrs		Assoc.array of attributes for the element
	 * @return	XHTML_Control
	 * @access	public
	 */
	function __construct($tagName, $attrs=array())
	{
		parent::__construct($tagName, $attrs);

		$name = @(string)$attrs['name'];
		if ( $name ) {
			// Fix indexed name and parse it
			$nameCache =& Core_Object::getStaticProperty('XHTML_Control', 'nameCache');
			while ( preg_match('/((?:(?!\[\s*\]).)*?\[)\s*(\])/', $name, $matches) ) {
				$next = $matches[0];
				if ( array_key_exists($next, (array)$nameCache) ) {
					$nameCache[$next]++;
				} else {
					$nameCache[$next] = 0;
				}
				$name = str_replace($next, $matches[1] . $nameCache[$next] . $matches[2], $name);
			}
			if ( XHTML_Validator_Common::isValidVarname($name, $this->_nameParsed) ) {
				$this->_name = $attrs['name'];
			}
		}

		// Sets the default value of the control
		$this->setDefault(@$attrs['value']);

		// Sets submit methods
		$this->setSubmit('');
	}

	// }}}
	// {{{

	function __sleep()
	{
		$this->setValue(null);
		return array_keys(get_object_vars($this));
	}

	// }}}
	// {{{

	function __wakeup()
	{
		$this->setValueSubmitted();
	}
	
	// }}}
	// {{{

	/**
	 * List of common attributes for all control HTML elements ('disabled' and 'name')
	 *
	 * @param	void
	 * @return	array
	 * @access	public
	 */
	function availAttributes()
	{
		static $controlAttrs = array(
			'disabled',
			'name',
		);
		return array_merge(parent::availAttributes(), $controlAttrs);
	}

	// }}}
	// {{{

	/**
	 * List of available submits
	 * This virtual method may be overriden by inherited class
	 * There is no reason to call it immediately
	 *
	 * @param	string	$submits	List of passed submits
	 * @return	array	List of available submits
	 * @access	public
	 */
	function availSubmits($submits)
	{
		XHTML_Validator_Common::isMetaSubmit($submits, $result);
		return $result;
	}

	// }}}
	// {{{

	/**
	 * Returns the default value of the control
	 *
	 * @param	void
	 * @return	mixed
	 * @access	public
	 */
	function getDefaultValue()
	{
		return $this->_defaultValue;
	}

	// }}}
	// {{{

	/**
	 * Returns 'label' meta-data translated to '<label>'
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function getLabel()
	{
		$label = $this->getMetas('label');
		if ( $label ) {
			$label = '<label for="' . htmlspecialchars($this->getAttribute('id')) . '">' . htmlspecialchars($label) . '</label>';
		}
		return $label;
	}

	// }}}
	// {{{

	/**
	 * Returns 'message' meta-data
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function getMessage()
	{
		$message = htmlspecialchars($this->getMetas('message'));
		return $message;
	}

	// }}}
	// {{{

	/**
	 * Returns meta-data specified by their name or null
	 *
	 * @param	string	$name
	 * @return	mixed
	 * @access	public
	 */
	function getMetas($name=null)
	{
		if ( empty($name) ) {
			return $this->_metas;
		}

		if ( array_key_exists($name, $this->_metas) ) {
			return $this->_metas[$name];
		}

		return null;
	}

	// }}}
	// {{{

	/**
	 * Returns the name of the control
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function getName()
	{
		return $this->_name;
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
		return $this->_nameParsed;
	}

	// }}}
	// {{{

	function getValidator()
	{
		$validators = $this->getMetas('validator');
		return $validators;
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
		return $this->_value;
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
		return $this->getLabel() . parent::outerHtml();
	}

	// }}}
	// {{{

	function setConstant($value=null)
	{
		$this->setMetas(array(
			'no-overwrite'	=> 'no-overwrite',
		));
		$this->setDefault($value);
	}

	// }}}
	// {{{

	function setDefault($value=null)
	{
		$this->_defaultValue = $value;
	}

	// }}}
	// {{{

	function setFilter($value=null)
	{
		$this->setMetas(array(
			'filter'	=> $value,
		));
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
		$name = $this->getName();

		$metas = (array)$metas;

		// Erase 'no-overwrite', 'submit', 'filter' and 'validator' metas for unnamed controls
		if ( empty($name) ) {
			unset($metas['no-overwrite'], $metas['submit'], $metas['filter'], $metas['validator']);
		}

		// Setting of label and appropriate 'id' attribute if it is necessary
		$id = $this->getAttribute('id');
		if ( isset($metas['label']) && empty($id) ) {
			$id =& Core_Object::getStaticProperty('XHTML_Control', 'idNumber');
			$id++;
			$this->setAttribute('id', 'xf_' . $id);
		}

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

		// Setting of submits
		if ( isset($metas['submit']) ) {
			$metas['submit'] = $this->availSubmits(@(array)$metas['submit']);
		}

		// Setting of filters
		if ( isset($metas['filter']) ) {
			XHTML_Validator_Common::isMetaFilter($metas['filter'], $filters);
			if ( empty($filters) ) {
				unset($metas['filter']);
			} else {
				$metas['filter'] = $filters;
			}
		}

		// Setting of validators
		if ( isset($metas['validator']) ) {
			XHTML_Validator_Common::isMetaValidator($metas['validator'], $validators);
			if ( empty($validators) ) {
				unset($metas['validator']);
			} else {
				$metas['validator'] = array_merge((array)$this->getValidator(), $validators);
			}
		}

		$this->_metas = array_merge($this->_metas, (array)$metas);
		//$this->_metas += $metas;
	}

	// }}}
	// {{{

	function setSource($value=null)
	{
		$this->setMetas(array(
			'source'	=> $value,
		));
	}

	// }}}
	// {{{

	function setSubmit($value=null)
	{
		$this->setMetas(array(
			'submit'	=> $value,
		));
	}

	// }}}
	// {{{

	function setValidator($value=null)
	{
		$this->setMetas(array(
			'validator'	=> $value,
		));
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
		// To be overriden
		$this->_value = $value;
	}

	// }}}
	// {{{

	/**
	 * Overwrites value of the control with data passed from requests.
	 * The method returns 'true' or 'false' depending on validity of submitted data.
	 * The 'null' value means that the control has no name, submits or there is no submitting.
	 *
	 * There is no reason to call it immediately
	 *
	 * @param	void
	 * @return	mixed
	 * @access	public
	 */
	function setValueSubmitted()
	{
		static $uploads = array(
			'name',
			'type',
			'tmp_name',
			'error',
			'size',
		);

		$name = $this->getName();
		if ( empty($name) ) {
			return null;
		}

		$submits = (array)$this->getMetas('submit');
		if ( empty($submits) ) {
			return null;
		}

		$nameParsed = $this->getNameParsed();

		if ( $submits[0] == 'FILES' ) {
			$nameFirst = array_shift($nameParsed);
			if ( ! array_key_exists($nameFirst, $_FILES) ) {
				return null;
			}
			$nameLast = empty($nameParsed) 
				? '' 
				: '["' . implode('"]["', $nameParsed) . '"]';
			$eval = '$found = null;';
			foreach ($uploads as $upload) {
				$eval .= '$found["' . $upload . '"] = @$_FILES["' . $nameFirst . '"]["' . $upload . '"]' . $nameLast . ';';
			}
		} else {
			$nameLast = '["' . implode('"]["', $nameParsed) . '"]';
			$eval = '';
			foreach ($submits as $submit) {
				$eval .= 'if ( isset($_' . $submit . $nameLast . ') ) { $found = $_' . $submit . $nameLast . '; } else';
			}
			$eval .= ' { $found = null; } if ( get_magic_quotes_gpc() ) { Core_Utils::unescape($found); } ';
		}

		// Receive submitted value
		eval($eval);

		// Filter and validate
		$result = null === $found 
			? null 
			: $this->validate($found);

		// Store value to the control
		$this->setValue($found);

		return $result;
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();

		$value = $this->getDefaultValue();
		if ( $value ) {
			$result['attrs']['value'] = $value;
		} else {
			unset($result['attrs']['value']);
		}

		if ( isset($result['attrs']['id']) && preg_match('/xf_\d+/', $result['attrs']['id']) ) {
			unset($result['attrs']['id']);
		}

		$metas = $this->getMetas();
		foreach ($metas as $k => $v) {
			if ( $v ) {
				continue;
			}
			unset($metas[$k]);
		}
		if ( $metas ) {
			if ( ! empty($metas['submit']) ) {
				$metas['submit'] = implode(' ', $metas['submit']);
			}
			if ( ! empty($metas['validator']) ) {
				$list = array();
				foreach ($metas['validator'] as $validator => $arguments) {
					$list[] .= $validator . ( empty($arguments) ? '' : '(' . implode(' ', $arguments) . ')' );
				}
				$metas['validator'] = implode(' ', $list);
			}
			$result['meta'] = $metas;
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
		return $value == $this->getDefaultValue();
	}

	// }}}
	// {{{

	/**
	 * Filters and validates a value before setting to the control
	 *
	 * @param	mixed	The reference to the submitted data
	 * @return	mixed	It is true if the value is valid or false elsewhere
	 * @public
	 */
	function validate( & $value)
	{
		$filters = (array)$this->getMetas('filter');
		foreach ($filters as $filter) {
			if ( ! is_callable($filter) ) {
				continue;
			}
			$value = call_user_func($filter, $value);
		}

		$validators = (array)$this->getValidator();
		foreach ($validators as $name => $args) {
			if ( 0 == strcasecmp($name, 'eigenvalue') ) {
				if ( ! $this->validEigenvalue($value) ) {
					return false;
				}
				continue;
			}
			if ( ! is_callable(array('XHTML_Validator_Common', 'isValid' . $name)) 
			|| ! call_user_func(array('XHTML_Validator_Common', 'isValid' . $name), $value, null, $args) ) {
				return false;
			}
		}
		return true;
	}

	// }}}

}

// }}}

?>