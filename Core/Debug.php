<?php

/**
 *
 * Debugging wrappers for standard PHP functions
 * such as var_dump(), print_r() and debug_backtrace()
 *
 * PHP versions 4 and 5
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
 * @category    PHP
 * @package     Debug
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 * @version     $Id: Debug.php,v 1.1 2007/06/19 01:08:28 shaimord Exp $
 *
 */

// {{{

class Debug
{

    // {{{ properties

    /**
     * Internally used property
     *
     * @var
     * @access private
     */
    var $_enabled = true;

    /**
     * Internally used property
     *
     * @var
     * @access private
     */
    var $_stack = array();

    // }}}
    // {{{

    /**
     * Creates and returns the single instance for working with a timer.
     * Can not be called statically
     *
     * @param  void
     * @return Debug instance
     * @access public
     */
    function & getTimer()
    {
        static $timer = null;
        if ( null === $timer ) {
            $timer =& new Debug();
            $timer->clear();
        }
        return $timer;
    }

    // }}}
    // {{{

    /**
     * Clears internal variables of timer
     *
     * @param  void
     * @return void
     * @access public
     */
    function clear()
    {
        $this->_stack = array();
    }

    // }}}
    // {{{

    /**
     * Starts new timer countdown and returns current timer value
     *
     * @param  void
     * @return float
     * @access public
     */
    function start()
    {
        list($msec, $sec) = explode(' ', microtime());
        $now = (float)$sec + (float)$msec;
        array_push($this->_stack, $now);
        return $now;
    }

    // }}}
    // {{{

    /**
     * Stops the last timer countdown.
     * Returns the difference between the current time and the last start countdown
     * If there is no any start countdown it will return the NULL value
     *
     * @param  void
     * @return float
     * @access public
     */
    function stop()
    {
        $now = array_pop($this->_stack);
        if ( null === $now ) {
            return null;
        }
        list($msec, $sec) = explode(' ', microtime());
        return (float)$sec + (float)$msec - $now;
    }

    // }}}
    // {{{

    /**
     * Prints out or return a syntax highlighted version of the code or a fragment of the code 
     * contained in $filename using the colors defined in the built-in syntax highlighter for PHP.
     * It is possible to highlight any part of the code using special labels as follow:
     *
     * //HLB::label
     * the code to be highlighted
     * //HLE::label
     *
     * The 'label' is a non-empty string marking the begin and the end of the highlighted fragment.
     *
     * @param  string   $filename
     * @param  string   $label
     * @param  boolean  $return
     * @param  boolean  $skip
     * @return float
     * @access public
     */
    function highlight($filename=null, $label='', $return=false, $skip=false)
    {
        if ( ! $filename ) {
            $filename = $_SERVER['SCRIPT_FILENAME'];
        }
        $contents = file_get_contents($filename);

        $label = preg_quote($label);
        $result = empty($label) || ! preg_match_all('|//\s*HLB::' . $label . '\s+(((?!//\s*HLE::' . $label . '\s+).)*)//\s*HLE::' . $label . '\s+|s', $contents, $matches) 
            ? (array)$contents 
            : $matches[1];

        if ( $skip ) {
            return $result;
        }

        foreach ($result as $k => $v) {
            $v = preg_replace('/^\s*\?\>\s*/', '', $v);
            $v = preg_replace('/\s*$/', "\n", $v);
            if ( ! preg_match('/^<\?/', $v) ) {
                $v = '<' . "?\n" . $v;
            }
            $result[$k] = highlight_string($v, true);
        }
        $result = implode('<span style="color: #000; font-weight: bold;">&nbsp;.&nbsp;.&nbsp;.&nbsp;</span><br />', $result);

        if ( $return ) {
            return $result;
        }

        echo $result;
    }

    // }}}
    // {{{

    /**
     * Nice wrapper for PHP debug_backtrace().
     * Prints human-readable information about a backtrace.
     *
     * @param   array   $trace    If the $trace argument is sets it will be returned
     * @param   boolean $return   If this parameter is set to TRUE, the method will return
     *          its output, instead of printing it (which it does by default)
     *
     * @return  boolean If $return argument is set to TRUE, method will return its output
     *
     * @access  public
     */
    function backtrace($trace=false, $return=false)
    {
        if (empty($trace)) {
            $trace = debug_backtrace();
            array_shift($trace);
        }

        $result = '';
        $i = count($trace);
        while ($i--) {
            $func = '<span class="dbg_trace_function">';
            if (isset($trace[$i]['class'])) {
                $func .= $trace[$i]['class'] . $trace[$i]['type'];
            }
            $func .= htmlspecialchars($trace[$i]['function']) . '</span>';

            $args = array();
            foreach ($trace[$i]['args'] as $arg) {
                $args[] = '<span class="dbg_trace_arg">' . Debug::var_dump(null, $arg, true) . '</span>';
            }
            if ($args) {
                $func .= '<span class="dbg_trace_args_left">(</span>';
                $func .= '<span class="dbg_trace_args">' . implode('<span class="dbg_trace_args_separator">,</span>', $args) . '</span>';
                $func .= '<span class="dbg_trace_args_right">)</span>';
            } else {
                $func .= '<span class="dbg_trace_args_empty">()</span>';
            }

            $result .= '<tr class="dbg_trace_row">'
                . '<td class="dbg_trace_td1">'
                . '<span class="dbg_trace_file">' . @$trace[$i]['file'] . '</span>'
                . '<span class="dbg_trace_line">' . @$trace[$i]['line'] . '</span>'
                . '</td>'
                . '<td class="dbg_trace_td2">' . $func . '</td>'
                . '</tr>';
        }

        // Non-empty backtrace
        if ($result) {
            $result = '<table class="dbg_trace">' . $result . '</table>';
        }

        // Return or output immediately
        if ($return) {
            return $result;
        }

        // Using of output buffering
        $debug =& Debug::useConsole(false, true);
        if ( null !== $debug && $debug->_enabled ) {
            $debug->_stack[] = $result;
            return;
        }

        echo $result;
    }

    // }}}
    // {{{

    /**
     * Nice wrapper for PHP print_r() and var_dump().
     * Its behavior is equals to Debug::dump() but it makes windowable output.
     *
     * @param   mixed   $var
     * @param   boolean $return   If this parameter is set to TRUE, the method will return
     *          its output, instead of printing it (which it does by default)
     * @param   boolean $collapse If this parameter is set to TRUE, the presented value will be collapsed
     *
     * @return  boolean
     *
     * @access  public
     *
     * @see     For detail see Debug::dump()
     */
    function display($value, $return=false, $collapse=false)
    {
        $result = Debug::var_dump(null, $value, $collapse);
        $result = '<div class="dbg_display"><div class="dbg_display_frame"><div class="dbg_display_view">' . $result . '</div></div></div>';

        // Return or output immediately
        if ( $return ) {
            return $result;
        }

        // Using of output buffering
        $debug =& Debug::useConsole(false, true);
        if ( null !== $debug && $debug->_enabled ) {
            $debug->_stack[] = $result;
            return;
        }

        echo $result;
    }

    // }}}
    // {{{

    /**
     * Nice wrapper for PHP print_r() and var_dump().
     * Prints human-readable information about variables.
     *
     * @param   mixed   $var      Variable to be outputted
     * @param   boolean $return   If this parameter is set to TRUE, the method will return
     *          its output, instead of printing it (which it does by default)
     * @param   boolean $collapse If this parameter is set to TRUE, the presented value will be collapsed
     *
     * @return  boolean If $return argument is set to TRUE, method will return its output
     *
     * @access  public
     */
    function dump($value, $return=false, $collapse=false)
    {
        $result = Debug::var_dump(null, $value, $collapse);
        $result = '<div class="dbg_var_dump">' . $result . '</div>';

        // Return or output immediately
        if ( $return ) {
            return $result;
        }

        // Using of output buffering
        $debug =& Debug::useConsole(false, true);
        if ( null !== $debug && $debug->_enabled ) {
            $debug->_stack[] = $result;
            return;
        }

        echo $result;
    }

    // }}}
    // {{{

    function var_dump($name, $value, $collapse=false)
    {
        if ( Debug::useNice(true) ) {
            Debug::useNice();
        }

        $type = gettype($value);
        switch ($type) {
        case 'string':
            $str = $value;
            $str = str_replace(array("\n", "\t", "\r"), array('\\n', '\\t', '\\r'), $str);
            $str = htmlspecialchars($str);
            $str = '&quot;' . $str . '&quot;';
            break;
        case 'integer':
        case 'float':
        case 'double':
            $str = $value;
            break;
        case 'boolean':
            $str = $value ? 'TRUE' : 'FALSE';
            break;
        case 'array':
            $str = 'ARRAY[' . count($value) . ']';
            $isComplex = true;
            break;
        case 'object':
            $str = 'OBJECT ' . get_class($value);
            $isComplex = true;
            break;
        case 'NULL':
            $str = 'NULL';
            break;
        case 'resource':
            $str = get_resource_type($value) . preg_replace('/Resource id (#\d+)/', ' [\1]', $value);
            break;
        }

        $str = '<span class="dbg_var dbg_var_' . strtolower($type) . '" title="' . $type . '">' . $str . '</span>';
        if ( null !== $name ) {
            $str = '<span class="dbg_var_name">' . htmlspecialchars($name) . '</span> = ' . $str;
        }

        if ( empty($isComplex) ) {
            return $str;
        }

        static $refs = array();

        if ( $refs && $value ) {
            $found = false;
            for ($i = count($refs) - 1; $i >= 0; $i--) {
                if ( $type != gettype($refs[$i]) ) {
                    continue;
                }
                if ( $type == 'object' && $value == $refs[$i] || $type == 'array' && ! array_diff($value, $refs[$i]) ) {
                    $found = true;
                    break;
                }
            }
            if ( $found ) {
                $str .= '<span class="dbg_var_recurse">*RECURSION*</span>';
                return $str;
            }
        }

        $result = '';
        foreach ($value as $k => $v) {
            if ( $k === 'GLOBALS' ) {
                continue;
            }
            array_push($refs, &$value);
            $result .= '<li>' . Debug::var_dump($k, $v, $collapse) . '</li>';
            array_pop($refs);
        }

        if ( $result ) {
            $str = '<div class="dbg_var_complex">' . $str . '<ul' . ( $collapse ? ' style="display: none;"' : '' ) . '>' . $result . '</ul></div>';
        }

        return $str;
    }

    // }}}
    // {{{

    /**
     * Toggle a usage of console.
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function toggleConsole()
    {
        $debug =& Debug::useConsole(false, true);
        if ( null === $debug ) {
            return null;
        }
        $debug->_enabled = ! $debug->_enabled;
        return $debug->_enabled;
    }

    // }}}
    // {{{

    /**
     * Starts output buffering for 
     * Debug::backtrace(), Debug::display(), and Debug::dump().
     *
     * @param   boolean $useNice
     * @param   boolean $check
     *
     * @param  void
     * @return void
     * @access public
     */
    function & useConsole($useNice=true, $check=false)
    {
        static $debugRef = null;
        if ( $check === true ) {
            return $debugRef;
        }

        $debug =& Debug::useConsole(false, true);
        if ( null !== $debug ) {
            return;
        }
        $debug = new Debug();

        if ( $useNice ) {
            ob_start();

?>

<script type="text/javascript"><!--//--><![CDATA[//><!--

/**
 *
 * This is the part of the PHP::Debug package
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
 * @package    Debug
 * @author     Ildar N. Shaimordanov
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id: Debug.php,v 1.1 2007/06/19 01:08:28 shaimord Exp $
 *
 */

(function(){

var dbg_console = document.getElementById('dbg_console');

function dbg_console_emulate_fixed(e)
{
    dbg_console.style.top = ( document.documentElement.scrollTop || document.body.scrollTop ) + 'px';
}

if ( dbg_console && ! window.opera && window.attachEvent ) {
    dbg_console.style.position = 'absolute';
    window.attachEvent('onscroll', dbg_console_emulate_fixed);
}

function dbg_console_collapser(e)
{
    var e = e || event;
    if ( e.ctrlKey && e.shiftKey && e.altKey && dbg_console ) {
        if ( dbg_console.className == 'dbg_console_hidden' ) {
            dbg_console.className = 'dbg_console_visible';
            window.debugConsoleCollapseToggle && window.debugConsoleCollapseToggle(dbg_console, true);
        } else {
            dbg_console.className = 'dbg_console_hidden';
            window.debugConsoleCollapseToggle && window.debugConsoleCollapseToggle(dbg_console, false);
        }
    }
}

if ( document.attachEvent ) {
    document.attachEvent('onkeydown', dbg_console_collapser);
} else if ( document.addEventListener ) {
    document.addEventListener('keydown', dbg_console_collapser, true);
}

})();

//--><!]]></script>
<style type="text/css">

/**
 *
 * This is the part of the PHP::Debug package
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
 * @package    Debug
 * @author     Ildar N. Shaimordanov
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id: Debug.php,v 1.1 2007/06/19 01:08:28 shaimord Exp $
 *
 */


/**
 *
 * Debug console styles
 *
 */
#dbg_console		{ background-color: #fff; border: 1px solid #ccc; left: 0; position: fixed; right: 0; top: 0; z-index: 999999; }
.dbg_console_visible	{ display: block; }
.dbg_console_hidden	{ display: none; }
.dbg_console_frame	{ height: 250px; max-height: 250px; overflow: scroll; }

</style>

<?php

            $debug->_stack[] = ob_get_clean();
        } else {
            $debug->_stack[] = '';
        }

        ob_start(array($debug, '_ob'));

        static $null = null;
        return $null;
    }

    function _ob($text)
    {
        $console = '';
        if ( count($this->_stack) > 1 ) {
            $console = '<div id="dbg_console" class="dbg_console_hidden"><div class="dbg_console_frame">' . implode("\n", $this->_stack) . '</div></div>';
        }
        return preg_replace('/(?=<\/body[^>]*>|$)/is', $console, $text, 1);
    }

    // }}}
    // {{{

    /**
     * Print out predefined CSS styles and JavaScript for 
     * Debug::backtrace(), Debug::display() and Debug::dump().
     *
     * @param   boolean $check
     * @retrun  void
     *
     * @access  public
     */
    function useNice($check=false)
    {
        static $useNice = 0;

        if ( $check === true ) {
            return $useNice;
        }

        // Debug::useNice() has been called
        // Embed CSS and JavaScript has been outputed
        if ( $useNice == 2 ) {
            return;
        }

        // Standalone call of Debug::useNice()
        if ( $useNice == 0 ) {
            $useNice++;
            return;
        }

        // CSS and JavaScript below will be output only time
        $useNice++;

?>

<script type="text/javascript"><!--//--><![CDATA[//><!--

/**
 *
 * This is the part of the PHP::Debug package
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
 * @package    Debug
 * @author     Ildar N. Shaimordanov
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id: Debug.php,v 1.1 2007/06/19 01:08:28 shaimord Exp $
 *
 */

(function(){

function dbg_var_collapser(e)
{
    var e = e || event;
    var target = e.srcElement || e.target;

    if ( target.className.match(/dbg_var_(array|object)/i)  ) {
        var collapsed = target;
        do {
            collapsed = collapsed.nextSibling;
        } while ( collapsed && ! ( collapsed.tagName && collapsed.tagName.toUpperCase() == 'UL' ) );
        if ( ! collapsed ) {
            return;
        }
        if ( collapsed.style.display == 'none' ) {
            collapsed.style.display = '';
            window.debugVarCollapseToggle && window.debugVarCollapseToggle(collapsed, true);
        } else {
            collapsed.style.display = 'none';
            window.debugVarCollapseToggle && window.debugVarCollapseToggle(collapsed, false);
        }
    }
}

if ( document.attachEvent ) {
    document.attachEvent('onmousedown', dbg_var_collapser);
} else if ( document.addEventListener ) {
    document.addEventListener('mousedown', dbg_var_collapser, true);
}

})();

//--><!]]></script>
<style type="text/css">

/**
 *
 * This is the part of the PHP::Debug package
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
 * @package    Debug
 * @author     Ildar N. Shaimordanov
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id: Debug.php,v 1.1 2007/06/19 01:08:28 shaimord Exp $
 *
 */

/**
 *
 * Debug::backtrace() styles
 *
 */
.dbg_trace			{ border: 1px solid #666; font-family: Verdana, sans-serif; font-size: 10px; width: 100%; }
.dbg_trace_row			{ background-color: #ccc; vertical-align: top; }
.dbg_trace_td1			{ width: 350px; }
.dbg_trace_td2			{ padding-left: 10px; }

.dbg_trace_file			{ font-style: italic; }
.dbg_trace_line			{ font-weight: bold; padding-left: 5px; width: 50px; }
.dbg_trace_function		{ font-weight: bold; }

.dbg_trace_args			{ display: block; }
.dbg_trace_args_empty,
.dbg_trace_args_left		{ display: inline-block; padding-left: 5px; }
.dbg_trace_args_right		{ clear: both; float: left; }
.dbg_trace_args_separator	{ display: none; }
.dbg_trace_arg			{ clear: both; cursor: pointer; float: left; margin-left: 30px; }


/**
 *
 * Debug::display() styles
 *
 */
.dbg_display			{ background-color: #ccc; border: 1px solid #000; height: 202px; padding: 2px; padding-top: 12px; width: 402px; }
.dbg_display_frame		{ background-color: #fff; border: 1px solid #000; height: 200px; overflow: scroll; width: 400px; }
.dbg_display_view		{ width: 1000%; }

/**
 *
 * Debug::dump() styles
 *
 */
.dbg_var_dump			{ border-bottom: 1px solid #ccc; border-right: 1px solid #ccc; }
.dbg_var_complex		{ border-left: 1px solid #ccc; border-top: 1px solid #ccc; font-size: 10px; width: auto; }
.dbg_var_complex ul		{ list-style-type: none; margin: 0; }
.dbg_var_complex ul li		{ list-style-type: none; margin: 0; padding-left: 20px; }

.dbg_var_recurse		{ font-weight: bold; padding-left: 5px; }
.dbg_var_name			{ font-family: Verdana, sans-serif; font-weight: bold; }
.dbg_var			{ font-family: Verdana, sans-serif; font-size: 10px; }

.dbg_var_string			{ color: #009900; }
.dbg_var_integer,
.dbg_var_float,
.dbg_var_double			{ color: #0000ff; }
.dbg_var_boolean,
.dbg_var_null,
.dbg_var_resource		{ color: #930; font-weight: bold; }
.dbg_var_array,
.dbg_var_object			{ cursor: pointer; }

</style>

<?php

    }

    // }}}

}

// }}}

?>