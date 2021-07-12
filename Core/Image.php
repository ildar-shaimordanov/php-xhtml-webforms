<?php

/**
 * This is a driver for the images handling
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
 * @category    Image
 * @package     Image
 * @author      Ildar Shaimordanov <ildar-sh@mail.ru>
 * @copyright   2006-2007 Ildar Shaimordanov
 * @license     http://www.opensource.org/licenses/gpl-license.php   GPL
 */

require_once 'Image/Exception.php';

define('IMAGE_COPYRIGHT', $_SERVER['HTTP_HOST']);

// {{{

class Image
{

    // {{{ constants

	/**
	 * Default copyright
	 *
	 * @const
	 */

	const COPYRIGHT = IMAGE_COPYRIGHT;

    /**
     * Alignments
     *
     * @const
     */
    const ALIGN_CENTER = 0;
    const ALIGN_LEFT   = -1;
    const ALIGN_RIGHT  = +1;
    const ALIGN_TOP    = -1;
    const ALIGN_BOTTOM = +1;

    /**
     * Rotations
     *
     * @const
     */
    const ROTATE_LEFT    = 'left';
    const ROTATE_RIGHT   = 'right';
    const ROTATE_REVERSE = 'reverse';

    /**
     * Mirroring
     *
     * @const
     */
    const ROTATE_MIRROR = 'mirror';
    const ROTATE_FLIP   = 'flip';

    /**
     * Maximal scaling
     *
     * @const
     */
    const METHOD_SCALE_MAX = 0;

    /**
     * Minimal scaling
     *
     * @const
     */
    const METHOD_SCALE_MIN = 1;

    /**
     * Cropping of fragment
     *
     * @const
     */
    const METHOD_CROP      = 2;

    // }}}
	// {{{ variables

	private $_image  = null;
	private $_w = null;
	private $_h = null;

	// }}}
	// {{{

	function __construct($input, $width=null, $height=null)
	{
		$this->load($input, $width, $height);
	}

	// }}}
	// {{{

	function __destruct()
	{
		$this->free();
	}

	// }}}
	// {{{

	/**
	 * Adds a background color with specified transparency
	 *
	 * @param  array  $options Various options as follow:
	 *                         - bgcolor  - The background color. 
	 *                         - alpha-bgcolor - A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent. 
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function addBackground($options=array())
	{
		static $defOptions = array(
			'bgcolor' => 0xffffff,
			'alpha-bgcolor' => 0,
		);
		$options = array_merge($defOptions, $options);

		$color = Image::imageColor($options['bgcolor'], $options['alpha-bgcolor'], $this->image());

		$result = imagefilledrectangle(
			$this->image(), 
			0, 0, 
			$this->width() - 1, $this->height() - 1, 
			$color);

		return $result;
	}

	// }}}
	// {{{

	/**
	 * Adds border with specified color, margins and thickness
	 *
	 * @param  array  $options Various options as follow:
	 *                         - margin - The margin thickness (in pixels). 
	 *                         - border - The border line thickness (in pixels). 
	 *                         - color  - The color of border. 
	 *                         - alpha-color - A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent. 
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function addBorder($options=array())
	{
		static $defOptions = array(
			'margin' => 5,
			'border' => 1,
			'color'  => 0xffffff,
			'alpha-color' => 0,
		);
		$options = array_merge($defOptions, $options);

		// Setting of thickness
		$result = imagesetthickness($this->image(), $options['border']);
		if ( ! $result ) {
			Image::user_error('Thickness setting is failed');
			return false;
		}

		// Create the border color
		$color = Image::imageColor($options['color'], $options['alpha-color'], $this->image());

		// Draw a rectangle
		$delta = $options['margin'] + ($options['border'] >> 1);
		$result = imagerectangle(
			$this->image(), 
			$delta, 
			$delta, 
			$this->width() - $delta - 1, 
			$this->height() - $delta - 1, 
			$color);

		return $result;
	}

	// }}}
	// {{{

	/**
	 * Adds another image
	 *
	 * @param  mixed  $input   Source image, a filename or an image string data or a GD image resource
	 * @param  array  $options Various options as follow:
	 *                There are available options for Image:
	 *                - left - X-coordinate
	 *                - top  - Y-coordinate
	 *                - transparent - value is ranged from 0 to 127 and implements alpha transparency for true colour images
	 *                - gray-scale  - if set and non-zero, copies and merges part of an image with gray scale
	 *
	 * @return boolean TRUE on success or FALSE on failure.
	 * @access public
	 */
	function addImage($input, $options=array())
	{
		// Set default options
		static $defOptions = array(
			'left' => 0,
			'top'  => 0,
			'transparent' => 0,
			'gray-scale'  => 0,
		);
		$options = array_merge($defOptions, $options);

		// Loads image resource only
		$image = Image::create($input, null, null, true);

		// Evaluates bounds
		$bounds = $this->intersects(imagesx($image), imagesy($image), $options['left'], $options['top']);
		if ( ! $bounds ) {
			return false;
		}

		$pct = 100 - round($options['transparent'] * 100 / 127, 0);

		if ( $options['gray-scale'] ) {
			$result = imagecopymergegray(
				$this->image(), $image, 
				$bounds['left'], $bounds['top'], 
				0, 0, 
				$bounds['width'], $bounds['height'], 
				$pct);
		} else {
			$result = imagecopymerge(
				$this->image(), $image, 
				$bounds['left'], $bounds['top'], 
				0, 0, 
				$bounds['width'], $bounds['height'], 
				$pct);
		}

		return $result;
	}

	// }}}
	// {{{

	/**
	 * Adds noise to the image
	 *
	 * @param  array  $options Various options as follow:
	 *                There are available options for Image:
	 *                - noise - noise level
	 * @return boolean TRUE on success or FALSE on failure.
	 * @access public
	 */
	function addNoise($options=array())
	{
		static $defOptions = array(
			'noise' => 'auto',
		);
		$options = array_merge($defOptions, $options);

		if ( empty($options['noise']) ) {
			return false;
		}

		if ( $options['noise'] == 'auto' ) {
			$options['noise'] = floor(sqrt($this->width() * $this->height()) / 4);
		}

		// Draw noise
		for ($i = 0; $i < $options['noise']; $i++) {
			$color = mt_rand(0, 0xffffff);
			if ( mt_rand(0, 1) ) {
				// lines
				$x1 = mt_rand(0, $this->width() - 1);
				$y1 = mt_rand(0, $this->height() >> 1);
				$x2 = mt_rand(0, $this->width() >> 1);
				if ( mt_rand(0, 1) ) {
					$y1 = $this->height() - $y1;
				}
				if ( mt_rand(0, 1) ) {
					$x2 = $this->width() - $x2;
				}
				$y2 = mt_rand(0, $this->height() - 1);
				$result = imageline($this->image(), $x1, $y1, $x2, $y2, $color);
			} else {
				// hollow and filled arcs
				$cx = mt_rand(0, $this->width() - 1);
				$cy = mt_rand(0, $this->height() - 1);
				$w = mt_rand(1, round($this->width() / 7));
				$h = mt_rand(1, round($this->height() / 7));
				$s = mt_rand(0, 360);
				$e = mt_rand(0, 360);
				$style = mt_rand(0, 4);
				if ( mt_rand(0, 1) ) {
					$result = imagefilledarc(
						$this->image(), 
						$cx, $cy, 
						$w, $h, 
						$s, $e, 
						$color, mt_rand(0, 4));
				} else {
					$result = imagearc(
						$this->image(), 
						$cx, $cy, 
						$w, $h, 
						$s, $e, 
						$color);
				}
			}
			if ( ! $result ) {
				return false;
			}
		}

		return true;
	}

	// }}}
	// {{{

	/**
	 * Adds text to the images using specified font
	 *
	 * @param  string $text
	 * @param  mixed  $font    A font number or a font filename
	 * @param  array  $options Various options as follow:
	 *                - left - X-coordinate
	 *                - top  - Y-coordinate
	 *                - width  - width of the text
	 *                - height - height of the text
	 *                         - font  - Font identifier or font filename. 
	 *                         - angle - The angle in degrees, with 0 degrees being left-to-right reading text. 
	 *                                   Higher values represent a counter-clockwise rotation. 
	 *                                   For example, a value of 90 would result in bottom-to-top reading text.
	 *                         - size  - The font size as integer or 'auto' for evaluate fontsize to fit text into the image. 
	 *                         - color - The color of text. 
	 *                         - alpha-color - A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent. 
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function addText($text, $options=array())
	{
		static $defOptions = array(
			'left' => 0,
			'top'  => 0,

			'font'  => 0,
			'angle' => 0,
			'size'  => 'auto',
			'color' => 0,
			'alpha-color' => 0,
		);
		$options = array_merge($defOptions, $options);

		if ( empty($options['width']) ) {
			$options['width'] = $this->width() - $options['left'];
		}
		if ( empty($options['height']) ) {
			$options['height'] = $this->height() - $options['top'];
		}

		if ( $options['width'] <= 0 || $options['height'] <= 0 ) {
			return false;
		}

		$color = Image::imageColor($options['color'], $options['alpha-color'], $this->image());

		if ( is_int($options['font']) || ! preg_match('/\.ttf$/i', $options['font']) ) {
			if ( is_file($options['font']) ) {
				$options['font'] = imageloadfont($options['font']);
			}
			if ( ! is_int($options['font']) ) {
				Image::user_error('Font loading is failed');
				return false;
			}
			$x = $options['left'] + ($options['width']  - imagefontwidth($options['font']) * strlen($text)) >> 1;
			$y = $options['top']  + ($options['height'] - imagefontheight($options['font'])) >> 1;
			$result = imagestring($this->image(), 
				$options['font'], 
				$x, 
				$y, 
				$text, 
				$color);
		} else {
			// Estimate font size
			if ( $options['size'] === 'auto' ) {
				$size = Image::ttfMaxSize($options['angle'], $options['font'], $text, $options['width'], $options['height']);
			} else {
				$size = $options['size'];
			}

			// Estimate text coordinates
			$sz = Image::ttfSize($size, $options['angle'], $options['font'], $text);
			$x = $options['left'] + ($options['width']  - $sz[0]) / 2 + $sz[2];
			$y = $options['top']  + ($options['height'] - $sz[1]) / 2 + $sz[3];

			// Draw the text
			$result = imagettftext(
				$this->image(), 
				$size, 
				$options['angle'], 
				$x, 
				$y, 
				$color, 
				$options['font'], 
				$text);
		}

		return (bool)$result;
	}

	// }}}
    // {{{

    /**
     * Create a GD image resource from given input.
     *
     * This method tried to detect what the input:
     * - if it is a file the Image::createImageFromFile will be called
     * - if it is a string the Image::createImageFromString() will be called
     * - another image object or another GD resource
     * - if the $input is null and the $width and the $height are integer 
     *   the new image will be created with the specified sizes
     *
     * @param  mixed   $input  The input for creating an image resource. The value
     *                         may a string of filename, string of image data or
     *                         GD image resource.
     * @param  integer $width  The width of the new image
     * @param  integer $height The height of the new image
     *
     * @return resource     An GD image resource on success or false
     * @access public
     * @static
     * @see    Image::createFromFile(), Image::createFromString()
     */
    function create($input, $width=null, $height=null, $instanceResource=false)
    {
        if ( $input === null && (int)$width > 0 && (int)$height > 0 ) {
			return Image::createEmpty($width, $height);
		}

        if ( is_string($input) && is_file($input) ) {
            return Image::createFromFile($input);
        }

        if ( is_string($input) ) {
            return Image::createFromString($input);
        }

		if ( $input instanceof Image) {
			$object = $input;
			if ( $instanceResource ) {
				$input = $object->image();
			} else {
				$input = Image::createEmpty($object->width(), $object->height());
				imagecopy(
					$input, $object->image(), 
					0, 0, 
					0, 0, 
					$object->width(), $object->height());
			}
		}

		if ( get_resource_type($input) != 'gd' ) {
			Image::user_error('Unknown image resource');
			return false;
		}

        return $input;
    }

    // }}}
	// {{{

	/**
	 * Creates new images with specified width and height
	 *
	 * @param  integer $width  The width of the new image
	 * @param  integer $height The height of the new image
	 *
	 * @return resource     An GD image resource on success or false
	 * @access public
	 * @static
	 */
	function createEmpty($width, $height)
	{
		if ( function_exists('imagecreatetruecolor') ) {
			$result = imagecreatetruecolor($width, $height);
		} else {
			$result = imagecreate($width, $height);
		}

		if ( ! $result ) {
			Image::user_error('Image create error');
			return false;
		}

		return $result;
	}

	// }}}
    // {{{

    /**
     * Create a GD image resource from file (JPEG, PNG support).
     *
     * @param  string $filename The image filename.
     *
     * @return mixed            GD image resource on success, FALSE on failure.
     * @access public
     * @static
     */
    function createFromFile($filename)
    {
        if ( ! is_file($filename) || ! is_readable($filename) ) {
            Image::user_error('Unable to open file "' . $filename . '"');
            return false;
        }

        // determine image format
        list( , , $type) = getimagesize($filename);

        switch ($type) {
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($filename);
            break;
        case IMAGETYPE_PNG:
            return imagecreatefrompng($filename);
            break;
        case IMAGETYPE_GIF:
            return imagecreatefromgif($filename);
            break;
        }
        Image::user_error('Unsupport image type');
        return false;
    }

    // }}}
    // {{{

    /**
     * Create a GD image resource from a string data.
     *
     * @param  string $string The string image data.
     *
     * @return mixed          GD image resource on success, FALSE on failure.
     * @access public
     * @static
     */
    function createFromString($string)
    {
        if ( ! is_string($string) || empty($string) ) {
            Image::user_error('Invalid image value in string');
            return false;
        }

        $result = @imagecreatefromstring($string);

		if ( ! $result ) {
			Image::user_error('Image cannot be loaded from the string');
			return false;
		}

		return $result;
    }

    // }}}
	// {{{

	/**
	 * Crops the portion of an image starting at coordinates with a width and a height
	 *
	 * @param  integer $width
	 * @param  integer $height
	 * @param  integer $left
	 * @param  integer $top
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function crop($width, $height, $left=0, $top=0, $options=array())
	{
		$bounds = $this->intersected($width, $height, $left, $top);
		if ( ! $bounds ) {
			return false;
		}

		if ( $bounds['width'] - $bounds['left'] == $this->width() && $bounds['height'] - $bounds['top'] == $this->height() ) {
			// Do nothing
			return true;
		}

		$image = Image::createEmpty($bounds['width'], $bounds['height']);
		$result = imagecopy(
			$image, $this->image(), 
			0, 0, 
			$bounds['left'], $bounds['top'], 
			$bounds['width'], $bounds['height']);

		$this->load($image);

		return $result;
	}

	// }}}
    // {{{

    /**
     * Applies a filter to an image
     *
     * @param  integer $filter   The filter type
     * @param  integer $argument The argument to be used for some filters:
     *                           - level for brightness, contrast, smoothness
     *                           - color for colorize filter in format as for Image::imageColor()
     *
     * @return TRUE on success or FALSE on failure
     * @access public
     * @see    http://www.php.net/manual/en/function.imagefilter.php
     */
    function filter($filter, $argument=null)
    {
		$filter = (int)$filter;

        // Apply filter
        if ( $filter == IMG_FILTER_COLORIZE ) {
            $color = Image::imageColor($argument);
            $result = imagefilter($this->image(), $filter, $color['red'], $color['green'], $color['blue']);
        } else {
            $result = imagefilter($this->image(), $filter, (int)$argument);
        }

        return $result;
    }

    // }}}
	// {{{

	/**
	 * Fits the image in the specified sizes
	 *
	 * @param  integer $width
	 * @param  integer $height
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function fit($width, $height, $options=array())
	{
		$wW = $width  / $this->width();
		$hH = $height / $this->height();
		if ( $wW > $hH ) {
			$width  = round($hH * $this->width(), 0);
		} else {
			$height = round($wW * $this->height(), 0);
		}
		return $this->resize($width, $height, $options);
	}

	// }}}
	// {{{

	/**
	 * Fits the image by the specified factor in the range 0.0 to 1.0
	 *
	 * @param  float $factor
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function fitFactor($factor, $options=array())
	{
		$width  = round($factor * $this->width(), 0);
		$height = round($factor * $this->height(), 0);
		return $this->resize($width, $height, $options);
	}

	// }}}
	// {{{

	/**
	 * Fits the image in the specified height
	 *
	 * @param  integer $height
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function fitHeight($height, $options=array())
	{
		$width = round($height / $this->height() * $this->width(), 0);
		return $this->resize($width, $height, $options);
	}

	// }}}
	// {{{

	/**
	 * Fits the image by the specified percentage
	 *
	 * @param  integer $percent
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function fitPercent($percent, $options=array())
	{
		return $this->fitFactor($percent / 100, $options);
	}

	// }}}
	// {{{

	/**
	 * Fits the image in the specified size
	 * This is the shorthand for Image->fit($size, $size)
	 *
	 * @param  integer $size
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function fitSize($size, $options=array())
	{
		return $this->fit($size, $size, $options);
	}

	// }}}
	// {{{

	/**
	 * Fits the image in the specified width
	 *
	 * @param  integer $width
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function fitWidth($width, $options=array())
	{
		$height = round($width / $this->width() * $this->height(), 0);
		return $this->resize($width, $height, $options);
	}

	// }}}
    // {{{

    /**
     * Vertical mirroring
     *
     * @return TRUE on success or FALSE on failure
     * @access public
     */
    function flip()
    {
        $sx = $this->width();
        $sy = $this->height();

        $buf = Image::createEmpty($sx, 1);
        if ( ! $buf ) {
            Image::user_error('Error during vertical mirroring');
            return false;
        }

        $s1 = $sy >> 1;
        for ($y = 0; $y < $s1; $y++) {
            $sy--;
            imagecopy($buf, $this->image(), 
                0,   0, 
                0,   $y, 
                $sx, 1);
            imagecopy($this->image(), $this->image(), 
                0,   $y, 
                0,   $sy, 
                $sx, 1);
            imagecopy($this->image(), $buf, 
                0,   $sy, 
                0,   0, 
                $sx, 1);
        }

        imagedestroy($buf);

        return true;
    }

    // }}}
	// {{{

	/**
	 * Destroys the image
	 *
	 * @param  void
	 * @return void
	 * @access public
	 */
	function free()
	{
		if ( is_resource($this->_image) ) {
			imagedestroy($this->_image);
		}
		$this->_image = null;
		$this->_w = null;
		$this->_h = null;
	}

	// }}}
	// {{{

	/**
	 * Applies a gamma correction to the image
	 *
	 * @param  float $gamma The output gamma 
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 * @see    http://www.php.net/manual/en/function.imagefilter.php
	 */
	function gamma($gamma=1)
	{
		if ( $gamma == 1 ) {
			return false;
		}
		return imagegammacorrect($this->image(), 1, $gamma);
	}

	// }}}
	// {{{

	/**
	 * Applies grayscale filter to the image. 
	 * This is alias to the Image->filter(IMG_FILTER_GRAYSCALE)
	 *
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function grayscale()
	{
		return $this->filter(IMG_FILTER_GRAYSCALE);
	}

	// }}}
	// {{{

	/**
	 * Returns the actual height of the image
	 *
	 * @return integer
	 * @access public
	 */
	function height()
	{
		return $this->_h;
	}

	// }}}
    // {{{

	function image()
	{
		return $this->_image;
	}

	// }}}
    // {{{

    /**
     * The Wrapper for the standard functions 'imagecolorallocate' and  'imagecolorallocatealpha'
     *
     * @param  mixed    $color The color (string as #ffffff, integer or array)
     * @param  integer  $alpha A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent
     * @param  resource $image resource to image
     * @return mixed    An assoc.array with keys as 'red', 'green', 'blue' and 'alpha' for the transparency
     *                  or integer value if the '$image has been specified
     *
     * NOTE:
     * Deprecated fo use within cyclic algorothms.
     * Use 'imagecolorallocate' or 'imagecolorallocatealpha' instead of Image::imageColor
     *
     * @access public
     */
    function imageColor($color, $alpha=0, $image=null)
    {
        if ( is_string($color) && preg_match('/#[0-9a-f]{6}/i', $color, $matches) ) {
            $color = sscanf($color, '#%2x%2x%2x');
        } elseif ( is_string($color) && preg_match('/#[0-9a-f]{3}/i', $color, $matches) ) {
            $color = sscanf($color, '#%1x%1x%1x');
            for ($i = 0; $i < count($color); $i++) {
                $color[$i] *= 0x11;
            }
        } elseif ( is_numeric($color) ) {
            $color = array(
                floor($color / 0x10000),
                floor($color / 0x100) & 0xff,
                $color & 0xff,
            );
        }

        $result['red']   = isset($color['red'])   ? $color['red']   : isset($color[0]) ? $color[0] : 0;
        $result['green'] = isset($color['green']) ? $color['green'] : isset($color[1]) ? $color[1] : 0;
        $result['blue']  = isset($color['blue'])  ? $color['blue']  : isset($color[2]) ? $color[2] : 0;
        $result['alpha'] = isset($color['alpha']) ? $color['alpha'] : isset($color[3]) ? $color[3] : $alpha;

        if ( $image ) {
            $result = imagecolorallocatealpha(
                $image, 
                $result['red'], 
                $result['green'], 
                $result['blue'], 
                $result['alpha']);
            if ( $result === false || $result == -1 ) {
				Image::user_error('Color allocation failed');
                return false;
            }
        }

        return $result;
    }

    // }}}
    // {{{

    /**
     * Estumates coordinate (X or Y) for the actual alignment and size
     *
     * @param  integer $align The actual alignment (vertical or horizontal)
     * @param  integer $param The bounded size (width or height)
     * @param  integer $src   The size to be aligned
     * @return integer
     *
     * @access public
     */
    function imageCoord($align, $param, $src)
    {
        if ( $align < Image::ALIGN_CENTER ) {
            $result = 0;
        } elseif ( $align > Image::ALIGN_CENTER ) {
            $result = $param - $src;
        } else {
            $result = ($param - $src) >> 1;
        }
        return $result;
    }

    // }}}
	// {{{

	/**
	 * Evaluates bounds of the new rectangle as intersection of the old rectangle and the image
	 * The special parameter $source indicates the source to be intersected
	 *
	 * @param  integer $width
	 * @param  integer $height
	 * @param  integer $left
	 * @param  integer $top
	 * @param  integer $source  The source to be intersected (0 means the image and 1 means the rectangle)
	 * @return array   New bounds or FALSE
	 * @access public
	 */
	function intersect($width, $height, $left, $top, $source)
	{
		if ( $left < 0 ) {
			$width = min($this->width(), $width + $left);
			$left = $source ? -$left : 0;
		} else {
			$width = min($width, $this->width() - $left);
		}

		if ( $top < 0 ) {
			$height = min($this->height(), $height + $top);
			$top = $source ? -$top : 0;
		} else {
			$height = min($height, $this->height() - $top);
		}

		if ( $width <= 0 || $height <= 0 ) {
			return false;
		}

		return array(
			'left'   => $left,
			'top'    => $top,
			'width'  => $width,
			'height' => $height,
		);
	}

	// }}}
	// {{{

	/**
	 * Evaluates new properties of a rectangle within the image
	 *
	 * @param  integer $width
	 * @param  integer $height
	 * @param  integer $left
	 * @param  integer $top
	 * @return array   New bounds or FALSE
	 * @access public
	 */
	function intersected($width, $height, $left, $top)
	{
		return $this->intersect($width, $height, $left, $top, 0);
	}

	// }}}
	// {{{

	/**
	 * Evaluates new properties of a rectangle within bounds
	 *
	 * @param  integer $width
	 * @param  integer $height
	 * @param  integer $left
	 * @param  integer $top
	 * @return array   New bounds or FALSE
	 * @access public
	 */
	function intersects($width, $height, $left, $top)
	{
		return $this->intersect($width, $height, $left, $top, 1);
	}

	// }}}
	// {{{

	/**
	 * Loads and initializes the image object.
	 * The same as Image::create() but there is dynamic method
	 *
	 * @param  mixed   $input  The input for creating an image resource. The value
	 *                         may a string of filename, string of image data or
	 *                         GD image resource.
	 * @param  integer $width  The width of the new image
	 * @param  integer $height The height of the new image
	 * @dynamic
	 * @see    Image::create()
	 */
	function load($input, $width=null, $height=null)
	{
		$this->free();
		$this->_image = Image::create($input, $width, $height);
		$this->_w = imagesx($this->_image);
		$this->_h = imagesy($this->_image);
	}

	// }}}
    // {{{

    /**
     * Horizontal mirroring
     *
     * @return TRUE on success or FALSE on failure
     * @access public
     */
    function mirror()
    {
        $sx = $this->width();
        $sy = $this->height();

        $buf = Image::createEmpty(1, $sy);
        if ( ! $buf ) {
            Image::user_error('Error during horizontal mirroring');
            return false;
        }

        $s1 = $sx >> 1;
        for ($x = 0; $x < $s1; $x++) {
            $sx--;
            imagecopy($buf, $this->image(), 
                0,   0, 
                $x,  0, 
                1,   $sy);
            imagecopy($this->image(), $this->image(), 
                $x,  0, 
                $sx, 0, 
                1,   $sy);
            imagecopy($this->image(), $buf, 
                $sx, 0, 
                0,   0, 
                1,   $sy);
        }

        imagedestroy($buf);

        return true;
    }

    // }}}
	// {{{

	/**
	 * Applies multiwave filter to the image
	 *
	 * @return boolean TRUE on success or FALSE on failure.
	 * @access public
	 * @see    http://captcha.ru/captchas/multiwave/
	 */
	function multiwave()
	{
		// Create the target image
		$image = Image::createEmpty($this->width(), $this->height());

		// frequency
		$freq1 = $this->multiwaveFreq();
		$freq2 = $this->multiwaveFreq();
		$freq3 = $this->multiwaveFreq();
		$freq4 = $this->multiwaveFreq();

		// phase
		$phase1 = $this->multiwavePhase();
		$phase2 = $this->multiwavePhase();
		$phase3 = $this->multiwavePhase();
		$phase4 = $this->multiwavePhase();

		// amplitude
		$ampl1 = $this->multiwaveAmplitude();
		$ampl2 = $this->multiwaveAmplitude();

		for ($x = 0; $x < $this->width(); $x++) {
			for ($y = 0; $y < $this->height(); $y++) {
				// coordinatess of the pixel-prototype
				$sx = $x + ( sin($x * $freq1 + $phase1) + sin($y * $freq3 + $phase3) ) * $ampl1;
				$sy = $y + ( sin($x * $freq2 + $phase2) + sin($y * $freq4 + $phase4) ) * $ampl2;

				if ($sx < 0 || $sy < 0 || $sx >= $this->width() - 1 || $sy >= $this->height() - 1 ) {
					// prototype beyond the image
					$color    = 255;
					$color_x  = 255;
					$color_y  = 255;
					$color_xy = 255;
				} else {
					// colors of the main pixel and 3 neighbors for the best antialiasing
					$color    = (imagecolorat($this->image(), $sx,     $sy)     >> 16) & 0xFF;
					$color_x  = (imagecolorat($this->image(), $sx + 1, $sy)     >> 16) & 0xFF;
					$color_y  = (imagecolorat($this->image(), $sx,     $sy + 1) >> 16) & 0xFF;
					$color_xy = (imagecolorat($this->image(), $sx + 1, $sy + 1) >> 16) & 0xFF;
				}

				// smooth 3 points only, colors of whose neighbors are differ
				if ( $color == $color_x && $color == $color_y && $color == $color_xy ) {
					$newcolor = $color;
				} else {
					// deviation of coordinates of prototype from integer
					$frsx  = $sx - floor($sx);
					$frsy  = $sy - floor($sy);
					$frsx1 = 1 - $frsx;
					$frsy1 = 1 - $frsy;
					// evaluate a color of the main pixel as ratio of colors of the main pixel and its neighbors
					$newcolor = floor(
						$color    * $frsx1 * $frsy1 +
						$color_x  * $frsx  * $frsy1 +
						$color_y  * $frsx1 * $frsy  +
						$color_xy * $frsx  * $frsy );
				}

				// allocate pixel to the target image
				// the usage of Image::imageColor is deprecated because of it is much slow
				$result = imagesetpixel(
					$image, 
					$x, $y, 
					imagecolorallocate($image, $newcolor, $newcolor, $newcolor));

				if ( ! $result ) {
					imagedestroy($image);
					return false;
				}
			}
		}

		// Free a memory from the source image
		$this->load($image);

		// Save the resulting thumbnail
		return true;
	}

	private function multiwaveAmplitude()
	{
		return mt_rand(300, 700) / 100;
	}

	private function multiwaveFreq()
	{
		return mt_rand(700000, 1000000) / 15000000;
	}

	private function multiwavePhase()
	{
		return mt_rand(0, 3141592) / 1000000;
	}

	// }}}
    // {{{

    /**
     * Display rendered image (send it to browser or to file).
     * This method is a common implementation to render and output an image.
     * The method calls the render() method automatically and outputs the
     * image to the browser or to the file.
     *
     * @param  mixed   $input   Destination image, a filename or an image string data or a GD image resource
     * @param  array   $options Options
     *         Predefined options:
     *         type    int    Type of output image (IMAGETYPE_PNG or IMAGETYPE_JPEG)
     *         quality int    Quality ranges from 0 (worst quality) to 100 (best quality) for JPEG
     *                        This value corresponds to the compress argument for PNG as follow:
     *                        quality 0 corresponds to compression 9, and quality 100 corresponds compression 0
     *         filter  int    PNG filters
     *         Others options are defined by inherited classes
     *
     * @return boolean          TRUE on success or FALSE on failure.
     * @access public
     */
    function output($output=null, $options=array())
    {
		static $defOptions = array(
			'type'     => IMAGETYPE_PNG,
			'quality'  => 75, // JPEG quality ranges from 0 (worst quality) to 100 (best quality). 
			'filter'   => PNG_NO_FILTER,  // PNG filters. 
		);
		$options = array_merge($defOptions, $options);

        // Render an image
        $this->render($options);

        // Before output to browsers send appropriate headers
        if ( empty($output) ) {
            $content_type = image_type_to_mime_type($options['type']);
            if ( ! headers_sent() ) {
                header('Content-Type: ' . $content_type);
            } else {
                Image::user_error('Headers have already been sent. Could not display image.');
                return false;
            }
        }

		$image = $this->image();

        // Define outputing function
        switch ($options['type']) {
        case IMAGETYPE_PNG:
			$compress = 9 - floor($options['quality'] / 10);
			$compress < 0 && $compress++;
            $result = imagepng($image, $output, $compress, $options['filter']);
            break;
        case IMAGETYPE_JPEG:
            $result = imagejpeg($image, $output, $options['quality']);
            break;
        case IMAGETYPE_GIF:
            $result = $output 
				? imagegif($image, $output) 
				: imagegif($image);
            break;
        default:
            Image::user_error('Image type ' . $content_type . ' not supported by PHP');
            return false;
        }

        return $result;
    }

    // }}}
    // {{{

    /**
     * Renders the image before output.
     *
     * @param  array   $options Options are defined by inherited classes
     * @return void
     * @access public
     * @see    Image->output()
     */
    function render($options=array())
    {
    }

    // }}}
	// {{{

	/**
	 * Resizes an image with a width and a height
	 *
	 * @param  integer $width
	 * @param  integer $height
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function resize($width, $height, $options=array())
	{
		return $this->resizePart($width, $height, array(), $options);
/*
		static $defOptions = array(
			'resize' => false,
		);
		$options = array_merge($defOptions, $options);

		if ( $width <= 0 || $height <= 0 ) {
			return false;
		}

		if ( $width >= $this->width() && $height >= $this->height() ) {
			return true;
		}

		$image = Image::createEmpty($width, $height);

		if ( function_exists('imagecopyresampled') && ! $options['resize'] ) {
			$result = imagecopyresampled(
				$image, $this->image(), 
				0, 0, 
				0, 0, 
				$width, $height, 
				$this->width(), $this->height());
		} else {
			$result = imagecopyresized(
				$image, $this->image(), 
				0, 0, 
				0, 0, 
				$width, $height, 
				$this->width(), $this->height());
		}

		$this->load($image);

		return $result;
*/
	}

	// }}}
	// {{{

	/**
	 * Resizes a part of an image with a width and a height
	 *
	 * @param  integer $width
	 * @param  integer $height
	 * @param  array   $bounds
	 * @return TRUE on success or FALSE on failure
	 * @access public
	 */
	function resizePart($width, $height, $bounds=array(), $options=array())
	{
		static $defOptions = array(
			'resize' => false,
		);
		$options = array_merge($defOptions, $options);

		if ( $width <= 0 || $height <= 0 ) {
			return false;
		}

		if ( $width >= $this->width() && $height >= $this->height() ) {
			return true;
		}

		$defBounds = array(
			'left'   => 0,
			'top'    => 0,
			'width'  => $this->width(),
			'height' => $this->height(),
		);
		$bounds = array_merge($defBounds, (array)$bounds);
		$bounds = $this->intersected($bounds['width'], $bounds['height'], $bounds['left'], $bounds['top']);

		if ( ! $bounds || $bounds['width'] < $width || $bounds['height'] < $height ) {
			return false;
		}

		$image = Image::createEmpty($width, $height);

		if ( function_exists('imagecopyresampled') && ! $options['resize'] ) {
			$result = imagecopyresampled(
				$image, $this->image(), 
				0, 0, 
				$bounds['left'], $bounds['top'], 
				$width, $height, 
				$bounds['width'], $bounds['height']);
		} else {
			$result = imagecopyresized(
				$image, $this->image(), 
				0, 0, 
				$bounds['left'], $bounds['top'], 
				$width, $height, 
				$bounds['width'], $bounds['height']);
		}

		$this->load($image);

		return $result;
	}

	// }}}
    // {{{

    /**
     * Rotates an image
     *
     * @param  float   $angle   The numeric value of rotation or the word definition of rotation
     * @param  array   $options Options
     *                 There are available options for Image_Noise:
     *                 - color - specifies the color of the uncovered zone after the rotation
     *                 - ignore-transparent - if set and non-zero, transparent colors are ignored (otherwise kept)
     *
     * @return boolean TRUE on success or FALSE on failure.
     * @access public
     */
    function rotate($angle, $options=array())
    {
        static $defOptions = array(
            'color' => 0xffffff,
            'ignore-transparent' => 0,
        );
		$options = array_merge($defOptions, $options);

        if ( $angle % 360 == 0 ) {
            return true;
        }

        $color = Image::imageColor($options['color'], 0, $this->image());
        $image = imagerotate($this->image(), $angle, $color, $options['ignore-transparent']);

        if ( ! $image ) {
            Image::user_error('Rotate error');
			return false;
        }

		$this->load($image);

        return true;
    }

    // }}}
    // {{{

    /**
     * The wrapper for the standard function 'imagettfbbox'
     *
     * @param  float  $size     The font size in pixels
     * @param  float  $angle    Angle in degrees in which text will be measured
     * @param  string $fontfile The name of the TrueType font file
     * @param  string $text     The string to be measured
     * @return array  An array with 8 elements representing four points 
     *                making the bounding box of the text
     *
     * @access public
     * @see    http://www.php.net/manual/en/function.imagettfbbox.php
     * @author Dmitry Koteroff
     */
    function ttfBox($size, $angle, $fontfile, $text)
    {
        // Estimate sizes about the zero angle
        $horiz = imagettfbbox($size, 0, $fontfile, $text);

        // Angle
        $cos = cos(deg2rad($angle));
        $sin = sin(deg2rad($angle));

        $result = array();
        for ($i = 0; $i < 7; $i += 2) {
            $x = $horiz[$i];
            $y = $horiz[$i + 1];
            $result[] = round($x * $cos + $y * $sin);
            $result[] = round($y * $cos - $x * $sin);
        }
        return $result;
    }

    // }}}
    // {{{

    /**
     * Estimates the miximal size of the font for the embed text.
     * Resulting array is as follow:
     * - 0 - width
     * - 1 - height
     * - 2 - offset by X coordinates from the upper-left corner
     * - 3 - offset by Y
     *
     * @param  float   $angle    Angle in degrees in which text will be measured
     * @param  string  $fontfile The name of the TrueType font file
     * @param  string  $text     The string to be measured
     * @param  integer $width    The rectangle width
     * @param  integer $height   The rectangle height
     * @return integer
     *
     * @access public
     * @author Dmitry Koteroff
     */
    function ttfMaxSize($angle, $fontfile, $text, $width, $height)
    {
        $min = 1;
        $max = $height;
        while (true) {
            // Average
            $size = round(($min + $max) / 2);
            $sz = Image::ttfSize($size, $angle, $fontfile, $text);
            if ( $sz[0] > $width || $sz[1] > $height ) {
                // Decrease max width until the text crosses the rectangle
                $max = $size;
            } else {
                // Increase min width while text is in the bounds
                $min = $size;
            }
            // Done
            if ( abs($max - $min) < 2) {
                break;
            }
        }
        return $min;
    }

    // }}}
    // {{{

    /**
     * Estimates size of the rectangle with the embed text.
     * Resulting array is as follow:
     * - 0 - width
     * - 1 - height
     * - 2 - offset by X coordinates from the upper-left corner
     * - 3 - offset by Y
     *
     * @param  float  $size     The font size in pixels
     * @param  float  $angle    Angle in degrees in which text will be measured
     * @param  string $fontfile The name of the TrueType font file
     * @param  string $text     The string to be measured
     * @return array
     *
     * @access public
     * @author Dmitry Koteroff
     */
    function ttfSize($size, $angle, $fontfile, $text)
    {
        // Estimate the bounding box
        $box = Image::ttfBox($size, $angle, $fontfile, $text);
        $x = array(
            $box[0],
            $box[2],
            $box[4],
            $box[6],
        );
        $y = array(
            $box[1],
            $box[3],
            $box[5],
            $box[7],
        );

        // Estimate width, height and offset of the start point
        $mx = min($x);
        $my = min($y);
        $w = max($x) - $mx;
        $h = max($y) - $my;

        return array(
            $w,
            $h,
            0 - $mx,
            0 - $my,
        );
    }

    // }}}
    // {{{

    /**
     * Generates a particular response to an exception at runtime.
     *
     * @param  string  $error_msg  The designated error message for this error
     *
     * @return void
     * @access public
     */
    function user_error($error_msg)
    {
        throw new Image_Exception($error_msg);
//        user_error($error_msg, E_USER_NOTICE);
    }

    // }}}
	// {{{

	/**
	 * Returns the actual width of the image
	 *
	 * @return integer
	 * @access public
	 */
	function width()
	{
		return $this->_w;
	}

	// }}}

}

// }}}

?>