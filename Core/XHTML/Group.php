<?php

/**
 * This is a semi-abstract class for XHTML groupped control
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

require_once 'XHTML/Common.php';

// {{{

class XHTML_Group extends XHTML_Common
{

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
		parent::__construct('', $attrs);
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
		$elements = $this->getElementsByTagName();
		$result = array();
		foreach ($elements as $element) {
			$result = array_merge_recursive($result, $element->getMetas($name));
		}

		Core_Utils::unique($result);

		return $result;
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
		$elements = $this->getElementsByTagName();
		foreach ($elements as $element) {
			$element->setMetas($metas);
		}
	}

	// }}}

}

// }}}

?>