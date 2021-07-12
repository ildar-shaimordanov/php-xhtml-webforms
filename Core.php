<?php

/**
 * This is a core file
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

/**
 *
 * Common core (kernel) includes
 *
 */
require_once 'Core/Debug.php';
Debug::useNice();
Debug::useConsole();

$timer =& new Debug();
$timer->start();

/**
 *
 * OS depended path and directory separator
 *
 */
if (!defined('PATH_SEPARATOR')) {
    define('PATH_SEPARATOR', (preg_match('/WIN/i', PHP_OS)) ? ';' : ':');
}

if (!defined('DIRECTORY_SEPARATOR')) {
    define('DIRECTORY_SEPARATOR', (preg_match('/WIN/i', PHP_OS)) ? '\\' : '/');
}

/**
 *
 * Core definitions
 *
 */
if (!defined('PROJECT_PATH')) {
    define('PROJECT_PATH', dirname(__FILE__));
}

if (!defined('PROJECT_CORE_PATH')) {
    define('PROJECT_CORE_PATH', PROJECT_PATH . DIRECTORY_SEPARATOR . 'Core');
}

if (!defined('PROJECT_CORE_DEFAULTS_PATH')) {
    define('PROJECT_CORE_DEFAULTS_PATH', PROJECT_CORE_PATH . DIRECTORY_SEPARATOR . '.defaults');
}

if (!defined('PROJECT_PEAR_PATH')) {
    define('PROJECT_PEAR_PATH', PROJECT_PATH . DIRECTORY_SEPARATOR . 'PEAR');
}

/**
 *
 * Include paths definitions
 *
 */
ini_set('include_path', ( 
    defined('SKIP_INCLUDE_PATH') && SKIP_INCLUDE_PATH === true
        ? '.' . DIRECTORY_SEPARATOR 
        : ini_get('include_path') 
    )
    . PATH_SEPARATOR . PROJECT_CORE_PATH
    . PATH_SEPARATOR . PROJECT_PEAR_PATH
);

?>