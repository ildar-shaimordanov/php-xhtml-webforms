<?php

// {{{

class Template
{

	// {{{ constants

	const SINGLE_VAR = '/\{(\w[^\{\}]+\w)\}/';
	const MULTI_VAR  = '/
		<!--\s+BEGIN\s+(\w+)\s+-->
		(
			(?: (?! <!--\s+END\s+\1\s+--> ). )* 
		)
		<!--\s+END\s+\1\s+-->
	/x';

	// }}}
	// {{{ properties

	var $_root;
	var $_lang;

	// }}}
	// {{{

	function __construct($root='./', $lang='en')
	{
		$this->setRoot($root);
		$this->setLang($lang);
	}

	// }}}
	// {{{

	function parseMulti($text)
	{
		return preg_replace_callback(Template::MULTI_VAR, array(&$this, '_cb_parseMulti'), $text);
	}

	// }}}
	// {{{

	function parseSingle($text)
	{
		return preg_replace_callback(Template::SINGLE_VAR, array(&$this, '_cb_parseSingle'), $text);
	}

	// }}}
	// {{{

	function setLang($lang)
	{
		if ( ! is_dir($lang) ) {
			return;
		}
		$this->_lang = $lang;
	}

	// }}}
	// {{{

	function setRoot($root)
	{
		if ( ! is_dir($root) ) {
			return;
		}
		$this->_root = $root;
	}

	// }}}
	// {{{ privates

	function _cb_parseMulti($matches)
	{
		return '';
	}

	// }}}
	// {{{

	function _cb_parseSingle($matches)
	{
		return $matches[1];
	}

	// }}}

}

// }}}

?>