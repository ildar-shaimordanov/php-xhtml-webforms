<?php

/**
 * This is a driver for the thumbnail creating
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

class Image_Thumbnail extends Image
{

    // {{{

    /**
     * Draw thumbnail result to resource.
     *
     * @param  array   $options Thumbnail options
     *                 There are available options for Image_Thumbnail:
     *                 - width   - width of thumbnail (in pixels)
     *                 - height  - height of thumbnail (in pixels)
     *                 - method  - thumbnail creating method (maximal and minimal scaling or crop)
     *                 - percent - ratio of scaling
     *                 - halign  - horizontal align within the loaded image
     *                 - valign  - vertical align within the loaded image
     *
     * @return void
     * @access public
     */
    function render($options=array())
    {
        static $defOptions = array(
            'width'   => 150,
            'height'  => 150,
            'method'  => Image::METHOD_SCALE_MAX,
            'percent' => 0,
            'halign'  => Image::ALIGN_CENTER,
            'valign'  => Image::ALIGN_CENTER,
        );
		$options = array_merge($defOptions, $options);

		switch ($options['method']) {
		case Image::METHOD_SCALE_MAX:
			$this->fit($options['width'], $options['height'], $options);
			break;
		case Image::METHOD_SCALE_MIN:
			$bounds = array();
			$bounds['width']  = min($this->height() / $options['height'] * $options['width'],  $this->width());
			$bounds['height'] = min($this->width()  / $options['width']  * $options['height'], $this->height());
			$bounds['left']   = Image::imageCoord($options['halign'], $this->width(),  $bounds['width']);
			$bounds['top']    = Image::imageCoord($options['valign'], $this->height(), $bounds['height']);

			$this->resizePart($options['width'], $options['height'], $bounds, $options);
			break;
		case Image::METHOD_CROP:
			$w = $this->width();
			$h = $this->height();
			if ( $options['percent'] ) {
				$options['percent'] > 1 && $options['percent'] /= 100;
				$w = floor($options['percent'] * $w);
				$h = floor($options['percent'] * $h);
			} else {
				$w = $options['width'];
				$h = $options['height'];
			}
			$x = Image::imageCoord($options['halign'], $this->width(), $w);
			$y = Image::imageCoord($options['valign'], $this->height(), $h);

			$this->crop($w, $h, $x, $y, $options);
			$this->fit($options['width'], $options['height'], $options);
			break;
		}
    }

    // }}}

}

// }}}

?>