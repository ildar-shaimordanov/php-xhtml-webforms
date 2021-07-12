<?php

/**
 * This is a driver for the CAPTCHA
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
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'Image.php';

// {{{

define('IMAGE_CAPTCHA_WARNING', ''
	. "Attention!\n" 
	. "The message that You see, \n" 
	. "giving confirmation of the \n" 
	. "fact that the original image \n" 
	. "has been misappropriated \n" 
	. "from the site of legal \n" 
	. "owners. At this time the \n" 
	. "image is locked by owners \n" 
	. "to prevent unlawful usage.");

class Image_Captcha extends Image
{

	// {{{ constants

	const CAPTCHA_WIDTH  = 150;
	const CAPTCHA_HEIGHT = 150;

	// }}}
	// {{{ variables

	private $_text;
	private $_disabled = false;

	public  $warning = IMAGE_CAPTCHA_WARNING;

	// }}}
	// {{{

	function __construct($text, $width=Image_Captcha::CAPTCHA_WIDTH, $height=Image_Captcha::CAPTCHA_HEIGHT)
	{
		parent::__construct(null, $width, $height);
		$this->_text = $text;
	}

	// }}}
	// {{{

	/**
	 * Disables output of the CAPTCHA text. 
	 * The disabled CAPTCHA locks a normal text and output the warning text
	 *
	 * @param  void
	 * #return void
	 * @access public
	 */
	function disableCaptcha()
	{
		$this->_disabled = true;
	}

	// }}}
    // {{{

    /**
     * Draw text image to resource.
     *
     * @param  array  $options Options
     *                There are available options for Image_Captcha:
     *                - font - font filename for the main text
     *                - copyright-text - copyright text; the default value is a domain name
     *                - copyright-font - font filename for the copyright text (default value is 'auto', that means the same font as for the main text)
     *                - options for Image->addBackground()
     *                - options for Image->addNoise()
     *                - options for Image->multiwave()
     *
     * @return boolean TRUE on success or FALSE on failure.
     * @access public
     */
    function render($options=array())
    {
        static $defOptions = array(
			'noise' => 'auto',
            'font'  => 0,
            'copyright-text' => IMAGE_COPYRIGHT,
            'copyright-font' => 'auto',
        );
		$options = array_merge($defOptions, $options);

		// White background
		$this->addBackground();

		// Disable CAPTCHA and output warning text
		if ( $this->_disabled ) {
			if ( ! empty($options['warning']) ) {
				$this->warning = $options['warning'];
			}

			$this->addText($this->warning, array(
				'font' => $options['font'],
			));
			return;
		}

		// Draw the CAPTCHA text
		$angle = floor(rad2deg(atan2($this->height(), $this->width())));
		$angle = mt_rand(0, $angle) - ($angle >> 1);
		$this->addText($this->_text, array(
			'font'  => $options['font'],
			'size'  => mt_rand(14, 18),
			'angle' => $angle,
			'color' => 0,
		));

		// Modify image
		$this->multiwave($options);

		// Random background
		$bgcolor = mt_rand(0, 0xffffff);
		$this->addBackground(array(
			'bgcolor' => $bgcolor,
			'alpha-bgcolor' => 112,
		));

		// Noising
		$this->addNoise($options);

		// Copyright image
		$height = max(20, $this->height() >> 3);
		$image =& new Image(null, $this->width(), $height);

		// Background for copyright text
		$image->addBackground(array(
			'bgcolor' => $bgcolor,
			'alpha-bgcolor' => 64,
		));

		// Text for copyright
		$image->addText($options['copyright-text'], array(
			'font'    => $options['copyright-font'] != 'auto' ? $options['copyright-font'] : $options['font'],
			'size'    => 'auto',
			'color'   => 0xffffff,
		));

		// Concatenate the CAPTCHA and the copiright text
		$this->addImage($image, array(
			'left' => 0,
			'top'  => $this->height() - $height,
			'transparent' => 50,
		));
    }

    // }}}

}

// }}}

?>