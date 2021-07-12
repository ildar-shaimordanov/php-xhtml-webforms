<?php

/**
 * This is a common class of the XHTML package
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

// {{{

class XHTML_Common
{

	// {{{ properties

	/**
	 * Tag name
	 *
	 * @var		string
	 * @access	private
	 */
	var $_tagName;

	/**
	 * Available attribute list
	 *
	 * @var		array
	 * @access	private
	 */
	var $_availAttrs = array();

	/**
	 * Actual attribute list
	 *
	 * @var		array
	 * @access	private
	 */
	var $_attrs = array();

	/**
	 * Inner html element list
	 *
	 * @var		array
	 * @access	private
	 */
	var $_elements = array();

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates a HTML element using tag name, and attributes
	 *
	 * @param	string	$tagName	Tag name of the HTML element
	 * @param	array	$attrs		Assoc.array of attributes for the element
	 * @return	XHTML_Common
	 * @access	public
	 */
	function __construct($tagName, $attrs=array())
	{
		// Tag name
		$this->_tagName = strtolower($tagName);

/*
		// List of available attributes
		if ( $this->availAttributesReplacing() ) {
			$this->_availAttrs = $this->availAttributes($this);
		} else {
			$class = get_class($this);
			do {
				$availAttrs = call_user_func(array($class, 'availAttributes'), $this);
				$this->_availAttrs = array_merge($this->_availAttrs, $availAttrs);
			} while ( $class = get_parent_class($class) );
		}
		sort($this->_availAttrs, SORT_STRING);
*/
		// List of available attributes
		$this->_availAttrs = $this->availAttributesReplacing()
			? $this->availAttributes()
			: array_merge($this->_availAttrs, $this->availAttributes());
		sort($this->_availAttrs, SORT_STRING);

		// Attributes
		$this->setAttributes($attrs);
	}

	// }}}
	// {{{

	/**
	 * Evaluates the child and appends it to the end of list
	 *
	 * @param	HTML_Element	$child
	 * @return	HTML_Element	Appended item or 'null'
	 * @access	public
	 */
	function appendChild($child)
	{
		if ( ! is_a($child, 'XHTML_Common') ) {
			return null;
		}
		if ( ! $this->appendingChild($child) ) {
			return null;
		}
		$this->_elements[] = $child;
		return $child;
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
		return true;
	}

	// }}}
	// {{{

	/**
	 * Associates stylesheets with an object or a class
	 *
	 * @param	void
	 * @return	String
	 * @access	public
	 */
	function assocCss()
	{
		return '';
	}

	// }}}
	// {{{

	/**
	 * Associates client-side scripts with an object or a class
	 *
	 * @param	void
	 * @return	String
	 * @access	public
	 */
	function assocJs()
	{
		return '';
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
		return array();
	}

	// }}}
	// {{{

	/**
	 * Flag indicates how to create a list of available attibutes
	 * If it returns 'true' then the list will be overwritten
	 * There is no reason to call it immediately
	 *
	 * @param	void
	 * @return	boolean
	 * @access	public
	 */
	function availAttributesReplacing()
	{
		return false;
	}

	// }}}
	// {{{

	/**
	 * Returns an attribute value specified by name
	 *
	 * @param	string	$name	Attribute name
	 * @return	string
	 * @access	public
	 */
	function getAttribute($name)
	{
		$name = strtolower($name);
		if ( ! $this->hasAttribute($name) ) {
			return null;
		}
		return @(string)$this->_attrs[$name];
	}

	// }}}
	// {{{

	/**
	 * Returns all attributes as array or string
	 *
	 * @param	$asString	If it is 'true' result is string presentation of attributes
	 * @return	mixed
	 * @access	public
	 */
	function getAttributes($asString=false)
	{
		if ( ! $asString ) {
			return $this->_attrs;
		}

		$attrs = '';
		foreach ($this->_attrs as $name => $value) {
			if ( ! isset($value) ) {
				continue;
			}
			$attrs .= ' ' . $name . '="' . htmlspecialchars($value) . '"';
		}
		return $attrs;
	}

	// }}}
	// {{{

	/**
	 * Returns the list of tags specified by tag name
	 * If $tagName is empty then the method will return all children
	 *
	 * @param	mixed	$tagName The name of tag
	 * @return	array	The list of tags
	 * @access	public
	 */
	function getElementsByTagName($tagName=false)
	{
		if ( empty($tagName) ) {
			return $this->_elements;
		}

		$tagName = strtolower($tagName);
		$result = array();
		foreach ($this->_elements as $element) {
			if ( $element->getTagName() != $tagName ) {
				continue;
			}
			$result[] = $element;
		}
		return $result;
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
		return null;
	}

	// }}}
	// {{{

	/**
	 * Returns a tag name
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function getTagName()
	{
		return $this->_tagName;
	}

	// }}}
	// {{{

	/**
	 * Returns 'true' if the attribute exists
	 *
	 * @param	string	$name	Name of an attribute
	 * @return	boolean
	 * @access	public
	 */
	function hasAttribute($name)
	{
		return in_array(strtolower($name), $this->_availAttrs);
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
		$elements = $this->getElementsByTagName();
		$html = '';
		foreach ($elements as $element) {
			$html .= "\n" . $element->outerHtml();
		}
		return $html;
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
		return $this->innerHtml();
	}

	// }}}
	// {{{

	/**
	 * Converts HTML element to string and returns one
	 *
	 * @param	boolean	$return	If this parameter is set to 'true', 
	 *			the method will return its output, 
	 *			instead of printing it (which it does by default). 
	 * @return	string
	 * @access	public
	 */
	function output($return=false)
	{
		$html = $this->outerHtml();
		if ( $return ) {
			return $html;
		}
		echo $html;
	}

	// }}}
	// {{{

	/**
	 * Sets an attribute value specified by name
	 *
	 * @param	string	$name		Attribute name
	 * @param	string	$value		Attribute value
	 * @return	void
	 * @access	public
	 */
	function setAttribute($name, $value)
	{
		$name = strtolower($name);
		if ( ! $this->hasAttribute($name) ) {
			return null;
		}
			$this->_attrs[$name] = $value;
	}

	// }}}
	// {{{

	/**
	 * Sets a list of attributes
	 *
	 * @param	array	$attrs	Assoc.array with keys as attribute name
	 * @return	void
	 * @access	public
	 */
	function setAttributes($attrs=array())
	{
		$attrs = (array)$attrs;
		foreach ($attrs as $name => $value) {
			if ( is_numeric($name) ) {
				$name = $value = strtolower($value);
			} else {
				$name = strtolower($name);
			}

			if ( ! $this->hasAttribute($name) ) {
				continue;
			}
			$this->_attrs[$name] = $value;
		}
	}

	// }}}
	// {{{

	/**
	 * Sets additional behavor of the HTML element through meta-tags
	 *
	 * @param	array	$metas
	 * @return	void
	 * @access	public
	 */
	function setMetas($metas=array())
	{
	}

	// }}}
	// {{{

	/**
	 * Sets extended options for this object
	 *
	 * @param	mixed	$options
	 * @return	void
	 * @access	public
	 */
	function setXOptions($options)
	{
		$this->setMetas($options);
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = array();
		$elements = $this->getElementsByTagName();
		if ( $elements ) {
			foreach ($elements as $element) {
				$result[] = $element->toArray();
			}
		}
		$result['element'] = substr(get_class($this), 6);
		$attrs = $this->getAttributes();
		if ( $attrs ) {
			$result['attrs'] = $attrs;
		}
		return $result;
	}

	// }}}

}

// }}}

?>