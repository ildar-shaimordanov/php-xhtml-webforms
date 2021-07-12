<?php

/**
 * This is a package for the password generation
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
 * @category    Text
 * @package     Text_Password
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'Text/Transform.php';

// {{{

class Text_Password
{

    // {{{

    function create($options=array())
    {
        static $defOptions = array(
            'length'  => 8,
            'method'  => 'pronounceable',
            'charset' => 'uppercase',
            'count'   => 1,
            'phrase'  => '',
        );
        $options = array_merge($defOptions, $options);

        if ( $options['count'] > 1 ) {
            return Text_Password::createMultiple($options);
        }

        if ( $options['phrase'] ) {
            return Text_Password::createFromPhrase($options);
        }

        if ( $options['method'] == 'pronounceable' ) {
            return Text_Password::createPronounceable($options);
        }

        if ( $options['method'] == 'unpronounceable' ) {
            return Text_Password::createUnpronounceable($options);
        }
        return Text_Password::createFromPhrase($options);
    }

    // }}}
    // {{{

    function createFromPhrase($options)
    {
        $method = array('Text_Transform', 'str' . ucfirst($options['method']));
        $phrase = $options['phrase'];
        if ( ! is_callable($method) ) {
            return $phrase;
        }
        return call_user_func($method, $phrase);
    }

    // }}}
    // {{{

    function createMultiple($options)
    {
        $count = $options['count'];
        $options['count'] = 1;

        $result = array();
        while ( $count --> 0 ) {
            while (true) {
                $password = Text_Password::create($options);
                if ( ! in_array($password, $result) ) {
                    $result[] = $password;
                    break;
                }
            }
        }
        return $result;
    }

    // }}}
    // {{{

    function createPronounceable($options)
    {
        // List of vowels and vowel sounds
        static $v = array('a', 'e', 'i', 'o', 'u', 'ae', 'ou', 'io', 'ea', 'ou', 'ia', 'ai');

        // List of consonants and consonant sounds
        static $c = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'fr', 'dr', 'wr', 'pr', 'th', 'ch', 'ph', 'st', 'sl', 'cl');

        $v_count = count($v);
        $c_count = count($c);

        $result = '';
        for ($i = 0; $i < $options['length']; $i++) {
            $result .= $c[mt_rand(0, $c_count - 1)] . $v[mt_rand(0, $v_count - 1)];
        }

        $result = substr($result, 0, $options['length']);
        if ( $options['charset'] == 'uppercase' ) {
            $result = strtoupper($result);
        }
        return $result;
    }

    // }}}
    // {{{

    function createUnpronounceable($options)
    {
        static $charsets = array(
            'alphanumeric' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
            'alphabetical' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            'uppercase'    => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'lowercase'    => 'abcdefghijklmnopqrstuvwxyz',
            'numeric'      => '0123456789',
            ''             => '_#@%&ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        );

        $charset = $options['charset'];
        $chars = array_key_exists($charset, $charsets) 
            ? $charsets[$charset] 
            : str_replace(array('+', '|', '$', '^', '/', '\\', ','), '', trim($charset));
        $count = strlen($chars);

        $result = '';
        for ($i = 0; $i < $options['length']; $i++) {
            $num = mt_rand(0, $count - 1);
            $result .= $chars[$num];
        }

        return $result;
    }

    // }}}

}

// }}}

?>