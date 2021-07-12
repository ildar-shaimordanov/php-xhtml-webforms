<?php

/**
*
* Structure presentation as stack and queue
*
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category   PHP
* @package    Structure
* @author     Ildar N. Shaimordanov <ildar-sh@mail.ru>
* @license    http://www.php.net/license/3_0.txt  PHP License 3.0
* @version    $Id: Structure.php,v 1.00 2005/11/16 Exp $
*
*/

// {{{

class Structure
{

    var $_struc;

    function Structure()
    {
        $this->__construct();
    }

    function __construct()
    {
        $this->clear();
    }

    function clear()
    {
        $this->_struc = array();
        return null;
    }

    function copy()
    {
        return ($this->isEmpty()) ? null : $this->get();
    }

    function get()
    {
        return null;
    }

    function isEmpty()
    {
        return (bool)!count($this->_struc);
    }

    function pop()
    {
        return null;
    }

    function push($value)
    {
        $this->_struc[] = $value;
        return $value;
    }

}

// }}}
// {{{

class Stack extends Structure
{

    function get()
    {
        return end($this->_struc);
    }

    function pop()
    {
        return array_pop($this->_struc);
    }

}

// }}}
// {{{

class Queue extends Structure
{

    function get()
    {
        return reset($this->_struc);
    }

    function pop()
    {
        return array_shift($this->_struc);
    }

}

// }}}

?>