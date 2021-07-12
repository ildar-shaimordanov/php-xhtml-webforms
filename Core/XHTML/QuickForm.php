<?php

/**
 * Quick Form is the part of the XHTML_Forms library
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

require_once 'XHTML/Form.php';

class XHTML_QuickForm extends XHTML_Form
{

	// {{{

	function __construct($method='', $action='', $attrs=array())
	{
		$attrs['method'] = $method;
		$attrs['action'] = $action;
		parent::__construct($attrs);
	}

	// }}}
	// {{{

	function addElement($element)
	{
		if ( XHTML_Form::isControl($element) || is_a($element, 'XHTML_Common') ) {
			$object = $element;
		} else {
			$args = func_get_args();
			$object =& call_user_func_array(array('XHTML_QuickForm', 'createElement'), $args);
		}
		return $this->appendChild($object);
	}

	// }}}
	// {{{

	function & createElement($element)
	{
		static $null = null;

		$type = ucfirst($element);
		@include_once 'XHTML/QuickForm/' . $type . '.php';
		$class = 'XHTML_QuickForm_' . $type;
		if ( ! class_exists($class) ) {
			return $null;
		}

		$args = func_get_args();
		$args = array_slice($args, 1);
		$object = call_user_func_array(array($class, 'factory'), $args);

		return $object;
	}

	// }}}

}

?>