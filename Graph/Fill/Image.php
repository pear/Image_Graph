<?php
// +--------------------------------------------------------------------------+
// | Image_Graph                                                              |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2003, 2004 Jesper Veggerby                                 |
// | Email         pear.nosey@veggerby.dk                                     |
// | Web           http://pear.veggerby.dk                                    |
// | PEAR          http://pear.php.net/package/Image_Graph                    |
// +--------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU Lesser General Public               |
// | License as published by the Free Software Foundation; either             |
// | version 2.1 of the License, or (at your option) any later version.       |
// |                                                                          |
// | This library is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU        |
// | Lesser General Public License for more details.                          |
// |                                                                          |
// | You should have received a copy of the GNU Lesser General Public         |
// | License along with this library; if not, write to the Free Software      |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA |
// +--------------------------------------------------------------------------+

/**
 * Image_Graph - PEAR PHP OO Graph Rendering Utility.
 * @package Image_Graph
 * @subpackage Fill     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Fill.php
 */
require_once 'Image/Graph/Fill.php';

/**
 * Fill using an image.
 */
class Image_Graph_Fill_Image extends Image_Graph_Fill 
{

    /**
     * The file name
     * @var stirng
     * @access private
     */
    var $_fileName;

    /**
     * The GD Image resource
     * @var resource
     * @access private
     */
    var $_image;

    /**
     * Resize the image to the bounding box of the area to fill
     * @var bool
     * @access private
     */
    var $_resize = true;

    /**
     * Image_Graph_ImageFill [Constructor]
     * @param string $filename The filename and path of the image to use for filling 
     */
    function &Image_Graph_Fill_Image($fileName)
    {
        parent::Image_Graph_Fill();
        if (file_exists($fileName)) {
            if (strtolower(substr($fileName, -4)) == '.png') {
                $this->_image = ImageCreateFromPNG($this->_fileName = $fileName);
            } else {
                $this->_image = ImageCreateFromJPEG($this->_fileName = $fileName);
            }
        } else {
            $this->_image = false;
        }
        if (($this->_image) and (isset($GLOBALS['_Image_Graph_gd2']))) {
            ImageAlphaBlending($this->_image, true);
        }
    }

    /**
     * Return the fillstyle
     * @return int A GD fillstyle 
     * @access private 
     */
    function _getFillStyle($ID = false)
    {
        if (!$this->_image) {
            return $this->_color->_index;
        }

        if (($this->_resize) and ((ImageSX($this->_image) != $this->_graphWidth()) or (ImageSY($this->_image) != $this->_graphHeight()))) {
            if (isset($GLOBALS['_Image_Graph_gd2'])) {
                $image = ImageCreateTrueColor($this->_graphWidth(), $this->_graphHeight());
                ImageCopyResampled($image, $this->_image, $this->_left, $this->_top, 0, 0, $this->width() + 1, $this->height() + 1, ImageSX($this->_image), ImageSY($this->_image));
            } else {
                $image = ImageCreate($this->_graphWidth(), $this->_graphHeight());
                ImageCopyResized($image, $this->_image, $this->_left, $this->_top, 0, 0, $this->width() + 1, $this->height() + 1, ImageSX($this->_image), ImageSY($this->_image));
            }

            ImageDestroy($this->_image);
            $this->_image = $image;
        }

        ImageSetTile($this->_canvas(), $this->_image);
        return IMG_COLOR_TILED;
    }

    /**
    * Return the fillstyle at positions X, Y 
    * @param int $x The X position
    * @param int $y The Y position
    * @param int $w The Width
    * @param int $h The Height
    * @return int A GD fillstyle 
    * @access private
    */
    function _getFillStyleAt($x, $y, $w, $h)
    {
        $this->_getFillStyle();
        if (isset($GLOBALS['_Image_Graph_gd2'])) {
            ImageCopyResampled($this->_image, $this->_image, $x, $y, $this->_left, $this->_top, $w, $h, $this->width() + 1, $this->height() + 1);
        } else {
            ImageCopyResized($this->_image, $this->_image, $x, $y, $this->_left, $this->_top, $w, $h, $this->width() + 1, $this->height() + 1);
        }

        ImageSetTile($this->_canvas(), $this->_image);
        return IMG_COLOR_TILED;
    }
}

?>