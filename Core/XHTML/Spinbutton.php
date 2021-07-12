<?php

/**
 * This is an extension class for XHTML input control working as spinbutton
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

require_once 'XHTML.php';
require_once 'XHTML/Input.php';

// {{{

class XHTML_Spinbutton extends XHTML_Input
{

	// {{{ properties

	var $_min = null;
	var $_max = null;
	var $_delta = null;
	var $_rotate = null;
	var $_behavior = null;
	var $_customArrows = null;

	// }}}
	// {{{

	/**
	 * Constructor.
	 * Creates a spinned input
	 *
	 * @param  array   $attrs	Assoc.array of attributes for the element
	 * @return XHTML_Select
	 * @access public
	 */
	function __construct($attrs=array())
	{
		$attrs['type'] = 'text';
		$attrs['autocomplete'] = 'off';
		if ( ! array_key_exists('value', $attrs) ) {
			$attrs['value'] = 0;
		}
		if ( empty($attrs['id']) ) {
			$id =& Core_Object::getStaticProperty('XHTML_Spinbutton', 'spinbuttonNumber');
			$id++;
			$attrs['id'] = 'xf_spinbutton_' . $id;
		}
		if ( empty($attrs['class']) ) {
			$attrs['class'] = 'xf_spinbuttonvalue';
		} else {
			$attrs['class'] .= ' xf_spinbuttonvalue';
		}

		if ( ! empty($attrs['behavior']) ) {
			$this->_behavior = $attrs['behavior'];
		}

		parent::__construct($attrs);

		XHTML::assocRegister('css', 'XHTML_Spinbutton');
		XHTML::assocRegister('js',  'XHTML_Spinbutton');
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
		return <<<CSS

div.xf_spinbutton_frame {
	display: inline;
	padding-right: 12px;
	position: relative;
}

div.xf_spinbutton_frame input.xf_spinbutton_value {
	text-align: right;
}

div.xf_spinbutton_frame input.xf_spinbutton_up,
div.xf_spinbutton_frame input.xf_spinbutton_down {
	font-size: 4px;
	margin: 0;
	padding: 0;
	position: absolute;
	width: 12px;
}

div.xf_spinbutton_frame input[type="button"]	{
	right: 0;
}

div.xf_spinbutton_frame input.xf_spinbutton_up {
	margin-bottom: 10px;
}

div.xf_spinbutton_frame input.xf_spinbutton_down {
	margin-top: 11px !important;
	margin-top: 12px;
}

CSS;

	}

	// }}}
	// {{{

	/**
	 * Associates client-side scripts an object or a class
	 *
	 * @param	void
	 * @return	String
	 * @access	public
	 * @author	Ilya Lebedev aka WingedFox
	 * @see	http://debugger.ru/blog/spin_button
	 */
	function assocJs()
	{
		return <<<JS

//
// JavaScript unit
// DOM extensions
//
// Copyright (c) 2009 by Ildar Shaimordanov
//

if ( ! window.XHTMLDOM ) {

window.XHTMLDOM = {};

}

if ( ! XHTMLDOM.utils ) {

XHTMLDOM.utils = {};

}

if ( ! XHTMLDOM.utils.SpinButton ) {

/**
 * Creates the spin button from the existing text input. 
 *
 * @param	HTMLElement	el
 * @param	Object		options
 * There are avaliable properties of the options:
 * -- value
 * -- min
 * -- max
 * -- delta
 * -- rotate
 * -- behavior
 * -- customArrows
 *
 * For better view use the next CSS sheet
 * <pre>

div.xf_spinbutton_frame {
	display: inline;
	padding-right: 12px;
	position: relative;
}

div.xf_spinbutton_frame input.xf_spinbutton_value {
	text-align: right;
}

div.xf_spinbutton_frame input.xf_spinbutton_up,
div.xf_spinbutton_frame input.xf_spinbutton_down {
	font-size: 4px;
	margin: 0;
	padding: 0;
	position: absolute;
	width: 12px;
}

div.xf_spinbutton_frame input[type="button"]	{
	right: 0;
}

div.xf_spinbutton_frame input.xf_spinbutton_up {
	margin-bottom: 10px;
}

div.xf_spinbutton_frame input.xf_spinbutton_down {
	margin-top: 11px !important;
	margin-top: 12px;
}

 * </pre>
 *
 * @return	void
 * @access	static
 */
XHTMLDOM.utils.Spinbutton = function(el, options)
{
	if ( 'string' == typeof el ) {
		el = document.getElementById(el);
	}

	if ( ! el || el.tagName.toLowerCase() != 'input' || el.type.toLowerCase() != 'text' ) {
		throw new TypeError();
	}

	options = options || {};

	//
	// create a holder for the spin button
	//
	var holder = document.createElement('div');
	holder.className = 'xf_spinbutton_frame';
	el.parentNode.insertBefore(holder, el);

	//
	// move the input into this holder
	//
	el.className += ' xf_spinbutton_value';
	holder.appendChild(el);

	//
	// add the down arrow
	//
	var dn = document.createElement('input');
	dn.type = 'button';
	dn.className = 'xf_spinbutton_down';
	dn.tabIndex = 32767;
	if ( ! options.customArrows ) {
		dn.value = String.fromCharCode(1639);
		dn.title = 'Down';
	}
	holder.appendChild(dn);

	//
	// add the up arrow
	//
	var up = document.createElement('input');
	up.type = 'button';
	up.className = 'xf_spinbutton_up';
	up.tabIndex = 32767;
	if ( ! options.customArrows ) {
		up.value = String.fromCharCode(1640);
		up.title = 'Up';
	}
	holder.appendChild(up);

	//
	// find the nearest value for delta != 1
	//
	function setNearest()
	{
		var r = (el.value - options.min) % options.delta;
		if ( r > 0 ) {
			el.value -= r;
		}
	};

	//
	// estimate the mediana of the interval
	//
	function setMediana()
	{
		// Preventing of 'Infinity' value appearance
		var n = options.min + Number.MAX_VALUE <= options.max 
			? options.max / 2 - options.min / 2 
			: (options.max - options.min) / 2;
		el.value = options.min + options.delta * Math.floor(n / options.delta);

		setNearest();
	};

	var self = this;

	/**
	 * Gets the options of the spin button
	 */
	self.getOptions = function()
	{
		return options;
	};

	/**
	 * Gets the actual value of the control
	 */
	self.getValue = function()
	{
		return el.value;
	};

	/**
	 * Updates the rotate options
	 */
	self.updateRotate = function(rotate)
	{
		options.rotate = !! rotate;
	};

	/**
	 * Updates the actual value of the element
	 */
	self.updateValue = function(value)
	{
		if ( /^\s*min\s*$/.test(value) ) {
			el.value = options.min;
		} else if ( /^\s*max\s*$/.test(value) ) {
			el.value = options.max;
		} else if ( isFinite(value) && Number(value) >= options.min && Number(value) <= options.max ) {
			el.value = Number(value);
		} else {
			setMediana();
		}
	};

	/**
	 * Updates the next options
	 * -- min
	 * -- max
	 * -- delta
	 */
	self.updateOptions = function(opts)
	{
		if ( ! opts ) {
			return;
		}

		var numOpts = 'min max delta'.split(/\s+/);
		for (var i = 0; i < numOpts.length; i++) {
			var p = numOpts[i];
			if ( opts.hasOwnProperty(p) && isFinite(opts[p]) ) {
				continue;
			}
			opts[p] = options[p];
		}

		if ( opts.min < opts.max && opts.min + opts.delta <= opts.max ) {
			options.min = opts.min;
			options.max = opts.max;
			options.delta = opts.delta;
		}

		el.value = Math.max(Math.min(el.value, options.max), options.min);

		setNearest();
	};

	//
	// correct options
	//
	if ( ! options.hasOwnProperty('rotate') ) {
		options.rotate = false;
	}

	if ( ! options.hasOwnProperty('behavior') ) {
		options.behavior = null;
	}

	// -2^32+1 .. +2^32-1 is enough
	if ( ! isFinite(options.min) ) {
		options.min = -0xFFFFFFFF;
	}

	if ( ! isFinite(options.max) ) {
		options.max = +0xFFFFFFFF;
	}

	options.delta = Math.abs(options.delta);
	if ( ! isFinite(options.delta) ) {
		options.delta = 1;
	}

	self.updateValue(options.value);

	//
	// initialize the main handler
	//
	setInterval(function()
	{
		if ( arguments.callee.value == el.value ) {
			return;
		}

		var value = Number(el.value);

		if ( isNaN(value) ) {
			return;
		}

		arguments.callee.value = value;

		if ( 'function' == typeof options.behavior ) {
			options.behavior(el, options);
		}
	}, 50);

	//
	// assign handlers
	//
	var interval;

	//
	// up arrow mousedown
	//
	up.onmousedown = function()
	{
		if ( el.disabled ) {
			return;
		}

		interval = setInterval(function()
		{
			if ( Number(el.value) + options.delta <= options.max ) {
				el.value = Math.max(options.min, el.value);
				el.value -= -options.delta;
			} else if ( options.rotate ) {
				el.value = options.min;
			}
			setNearest();
		}, 100);
	};

	//
	// down arrow mousedown
	//
	dn.onmousedown = function()
	{
		if ( el.disabled ) {
			return;
		}

		interval = setInterval(function()
		{
			if ( Number(el.value) - options.delta >= options.min ) {
				el.value = Math.min(options.max, el.value);
				el.value -= +options.delta;
			} else if ( options.rotate ) {
				el.value = options.max;
			}
			setNearest();
		}, 100);
	};

	//
	// up/down arrow mouseup/mouseout
	//
	up.onmouseup = 
	dn.onmouseup = 
	up.onmouseout = 
	dn.onmouseout = function()
	{
		clearInterval(interval);
	};

	//
	// up/down arrow focus
	//
	up.onfocus = 
	dn.onfocus = function()
	{
		if ( el.disabled ) {
			return;
		}
		el.focus();
	};

/*
	el.onkeydown = function(e)
	{
		e = e || window.event;
		// UP
		if ( e.keyCode == 0x26 ) {
			if ( Number(el.value) + options.delta <= options.max ) {
				el.value = Math.max(options.min, el.value);
				el.value -= -options.delta;
			} else if ( options.rotate ) {
				el.value = options.min;
			}
			setNearest();
			return e.returnValue = false;
		}
		// DOWN
		if ( e.keyCode == 0x28 ) {
			if ( Number(el.value) - options.delta >= options.min ) {
				el.value = Math.min(options.max, el.value);
				el.value -= +options.delta;
			} else if ( options.rotate ) {
				el.value = options.max;
			}
			setNearest();
			return e.returnValue = false;
		}
		// HOME
		if ( e.keyCode == 0x24 ) {
			el.value = options.max;
			setNearest();
			return e.returnValue = false;
		}
		// END
		if ( e.keyCode == 0x23 ) {
			el.value = options.min;
			setNearest();
			return e.returnValue = false;
		}
	};
*/

	return self;
};

}

JS;

	}

	// }}}
	// {{{

	/**
	 * Converts HTML element to string and returns one
	 * For spinbutton this looks as follow (XX means the number of the actual spinbutton to prevent confuse of several spinbuttones):
	 * <label for="xf_spinbutton_XX">label</label>
	 * <div>
	 * <input type="hidden" id="xf_spinbutton_XX_mn" value="min" />
	 * <input type="hidden" id="xf_spinbutton_XX_mx" value="max" />
	 * <input type="hidden" id="xf_spinbutton_XX_dx" value="delta" />
	 * <input type="text"   id="xf_spinbutton_XX"    value="value" name="name" />
	 * <input type="button" id="xf_spinbutton_XX_up" value="&#1640;" />
	 * <input type="button" id="xf_spinbutton_XX_dn" value="&#1639;" />
	 * <div>
	 *
	 * @param	void
	 * @return	string
	 * @access	public
	 */
	function outerHtml()
	{
		$id = htmlspecialchars($this->getAttribute('id'));
		$value = $this->getAttribute('value');

		$html = parent::outerHtml();
		$html .= '<script type="text/javascript"><!--//--><![CDATA[//><!--' . "\n";
		$html .= 'window.XHTMLDOM && XHTMLDOM.utils && XHTMLDOM.utils.Spinbutton && XHTMLDOM.utils.Spinbutton("' . $id . '", {' . "\n";
		$html .= '    value: ' . ( ! $value ? 'null' : $value ) . ', ' . "\n";
		$html .= '    min: ' . ( $this->_min === null ? 'null' : $this->_min ) . ', ' . "\n";
		$html .= '    max: ' . ( $this->_max === null ? 'null' : $this->_max ) . ', ' . "\n";
		$html .= '    delta: ' . ( $this->_delta === null ? 'null' : $this->_delta ) . ', ' . "\n";
		$html .= '    rotate: ' . ( $this->_rotate === null ? 'null' : $this->_rotate ) . ', ' . "\n";
		$html .= '    behavior: ' . ( $this->_behavior === null ? 'null' : $this->_behavior ) . ', ' . "\n";
		$html .= '    customArrows: ' . ( $this->_customArrows === null ? 'null' : $this->_customArrows ) . "\n";
		$html .= '});' . "\n";
		$html .= '//--><!]]></script>';

		return $html;
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
		if ( empty($metas['validator']) ) {
			$metas['validator'] = 'integer';
		} elseif ( is_array($metas['validator']) ) {
			$list = array('integer');
			foreach ($metas['validator'] as $validator => $arguments) {
				$list[] .= $validator . ( empty($arguments) ? '' : '(' . implode(' ', $arguments) . ')' );
			}
			$metas['validator'] = implode(' ', $list);
		} else {
			$metas['validator'] = 'integer ' . $metas['validator'];
		}

		// The 'value' attribute is fixed as the mediana of the 'range(start stop step)' validator
		if ( ! empty($metas['validator']) && preg_match('/range\s*\(\s*([+-]?\d+)\s+([+-]?\d+)(?:\s+([+-]?\d+))?\s*\)/i', $metas['validator'], $matches) ) {
			$this->_min = $matches[1];
			$this->_max = $matches[2];
			$this->_delta = empty($matches[3]) 
				? 1 
				: $matches[3];
			$value = $this->getAttribute('value');
			if ( ! isset($value) || ! XHTML_Validator_Common::isValidRange($value, $value, array($this->_min, $this->_max, $this->_delta)) ) {
				$value = $this->_min + $this->_delta * floor(($this->_max - $this->_min) / ($this->_delta * 2));
				$this->setDefault($value);
			}
		}

		parent::setMetas($metas);
	}

	// }}}
	// {{{

	function toArray()
	{
		$result = parent::toArray();

		if ( isset($result['attrs']['id']) && preg_match('/xf_spinbutton_\d+/', $result['attrs']['id']) ) {
			unset($result['attrs']['id']);
		}

		$result['attrs']['class'] = trim(preg_replace('/\bxf_spinbuttonvalue\b/', '', @$result['attrs']['class']));
		if ( empty($result['attrs']['class']) ) {
			unset($result['attrs']['class']);
		}

		if ( $this->_behavior ) {
			$result['attrs']['behavior'] = $this->_behavior;
		}

		return $result;
	}

	// }}}

}

// }}}

?>