<?php

/**
 * This is a driver for the watermark creating
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
require_once 'Image/Exception.php';

// {{{

class Image_Watermark extends Image
{

    // {{{

    /**
     * Draw the logo result to resource.
     *
     * @param  array   $options Logo options
     *                 There are available options for Image_Watermark:
     *                 - image   - Image source (filename, image string, image resource, image object, or text to be printed)
     *                 - width   - width of thumbnail (in pixels)
     *                 - height  - height of thumbnail (in pixels)
     *                 - halign  - horizontal align within the loaded image
     *                 - valign  - vertical align within the loaded image
     *
     * @return void
     * @access public
     * @see Image->addText(), Image->addImage()
     */
    function render($options=array())
    {
		static $defOptions = array(
			'image'  => IMAGE_COPYRIGHT,
			'halign' => Image::ALIGN_LEFT,
			'valign' => Image::ALIGN_TOP,
		);
		$options = array_merge($defOptions, $options);

		if ( ! is_string($options['image']) || is_file($options['image']) ) {
			// The image option presents image within a string, a file, a resource or an Image object
			$image = Image::create($options['image'], null, null, true);

			// Evaluate the left-top corner of the image
			$options['left'] = Image::imageCoord($options['halign'], $this->width(),  imagesx($image));
			$options['top']  = Image::imageCoord($options['valign'], $this->height(), imagesy($image));

			$this->addImage($image, $options);
		} else {
			// Try the width and the height of the original image
			if ( empty($options['width']) ) {
				$options['width'] = $this->width();
			}
			if ( empty($options['height']) ) {
				$options['height'] = $this->height();
			}

			// Evaluate the left-top corner of the text
			$options['left'] = Image::imageCoord($options['halign'], $this->width(),  $options['width']);
			$options['top']  = Image::imageCoord($options['valign'], $this->height(), $options['height']);

			$this->addText($options['image'], $options);
		}
	}

    // }}}

}

// }}}

?>