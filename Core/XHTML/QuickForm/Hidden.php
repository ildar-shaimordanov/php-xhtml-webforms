<?php

/**
 * This is part of the XHTML library
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

require_once 'XHTML/Input.php';

// {{{

class XHTML_QuickForm_Hidden extends XHTML_Input
{

	// {{{

	/**
	 * Generates an XHTML control.
	 * Accepts variable number of parameters.
	 *
	 * @access	public
	 * @dynamic
	 */
	function & factory($name, $value='', $attrs=array(), $metas=array())
	{
		$attrs['type'] = 'hidden';
		$attrs['name'] = $name;
		$attrs['value'] = $value;

		$class = __CLASS__;
		$object = new $class($attrs);

		if ( $metas ) {
			$object->setMetas($metas);
		}

		return $object;
	}

	// }}}

}

// }}}

?>