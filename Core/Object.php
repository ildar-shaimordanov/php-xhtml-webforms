<?php

/**
 * The basic class for all inherited classes of the framework
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
 * @category    XF
 * @package     Core
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'Exception.php';

/**
 *
 * The basic class in the framework
 *
 */
class Core_Object
{

    // {{{

    /**
     * Makes instance of a class spesified by a class path passed as an argument.
     * The file containing a class should be found in the include paths.
     *
     * @param  string $classPath
     * @param  mixed  $arguments
     * @param  mixed  $options
     * @return object instance
     * @access static
     */
    function & factory($classPath, $arguments, $options)
    {
		$classFile = preg_replace('|_+|', '/', $classPath);
		$className = preg_replace('|/+|', '_', $classPath);

        if ( ! ( @include_once $classFile . '.php' ) ) {
            throw new Core_Exception('Unknown class has been instantiated: ' . $classPath);
        } else {
            $object = new $className($arguments);
			$object->setXOptions($options);
        }

		return $object;
    }

    // }}}
    // {{{

    /**
     * Makes single instance of a class specified by a class path passed as an argument.
     * The file containing a class should be found in the include paths.
     *
     * @param  string $classPath
     * @param  mixed  $arguments
     * @param  mixed  $options
     * @return object instance
     * @access static
     */
    function & singleton($classPath, $arguments, $options)
    {
        static $object = null;

        if ( empty($object) ) {
            $object =& Core::factory($classPath, $arguments, $options);
        }

        return $object;
    }

    // }}}
    // {{{

    /**
     * Returns the static property for a static class.
     * This method has been scrapped from the PEAR.
     *
     * @param  string  $class The name of the class
     * @param  string  $name  The name of the property
     * @return mixed   The reference to the property
     * @access public
     * @static
     */
    function & getStaticProperty($class, $name)
    {
        static $props;
        if ( null === $class ) {
            return $props;
        }
        return $props[$class][$name];
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
	}

	// }}}

}

?>