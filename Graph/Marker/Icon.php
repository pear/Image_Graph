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
 * @subpackage Marker     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Marker.php
 */
require_once 'Image/Graph/Marker.php';

/**
 * Data marker using an image as icon
 */
class Image_Graph_Marker_Icon extends Image_Graph_Marker 
{

    /**
     * The image representing the icon
     * @var resource
     * @access private
     */
    var $_icon;

    /**
     * Filename of the image icon
     * @var string
     * @access private
     */
    var $_fileName;

    /**
     * X Point of the icon to use as data 'center'
     * @var int
     * @access private
     */
    var $_pointX;

    /**
     * Y Point of the icon to use as data 'center'
     * @var int
     * @access private
     */
    var $_pointY;

    /**
     * Create an icon marker
     * @param string $fileName The filename of the icon
     * @param int $width The 'new' width of the icon if it is to be resized
     * @param int $height The 'new' height of the icon if it is to be resized
     */
    function &Image_Graph_Marker_Icon($fileName, $width = 0, $height = 0)
    {
        parent::Image_Graph_Marker();
        if (file_exists($fileName)) {
            if (strtolower(substr($fileName, -4)) == '.png') {
                $this->_icon = ImageCreateFromPNG($this->_fileName = $fileName);
            } else {
                $this->_icon = ImageCreateFromJPEG($this->_fileName = $fileName);
            }

            if (($width) and ($height)) {
                if (isset($GLOBALS['_Image_Graph_gd2'])) {
                    $icon = ImageCreateTrueColor($width, $height);
                    ImageCopyResampled($icon, $this->_icon, 0, 0, 0, 0, $width, $height, ImageSX($this->_icon), ImageSY($this->_icon));
                } else {
                    $icon = ImageCreate($width, $height);
                    ImageCopyResized($icon, $this->_icon, 0, 0, 0, 0, $width, $height, ImageSX($this->_icon), ImageSY($this->_icon));
                }

                ImageDestroy($this->_icon);
                $this->_icon = $icon;
            }

            $this->_pointX = ImageSX($this->_icon) / 2;
            $this->_pointY = ImageSY($this->_icon) / 2;
        } else {
            $this->_icon = false;
        }
    }

    /**
     * Set the X 'center' point of the marker
     * @param int $x The X 'center' point of the marker  
     */
    function setPointX($x)
    {
        $this->_pointX = $x;
    }

    /**
     * Set the Y 'center' point of the marker
     * @param int $y The Y 'center' point of the marker  
     */
    function setPointY($y)
    {
        $this->_pointY = $y;
    }

    /**
     * Draw the marker on the canvas
     * @param int $x The X (horizontal) position (in pixels) of the marker on the canvas 
     * @param int $y The Y (vertical) position (in pixels) of the marker on the canvas 
     * @param array $values The values representing the data the marker 'points' to 
     * @access private
     */
    function _drawMarker($x, $y, $values = false)
    {
        parent::_drawMarker($x, $y, $values);
        if ($this->_icon) {
            ImageCopy($this->_canvas(), $this->_icon, $x - $this->_pointX, $y - $this->_pointY, 0, 0, ImageSX($this->_icon), ImageSY($this->_icon));
        }
    }

}

?>