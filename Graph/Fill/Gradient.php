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
 * Include file Image/Graph/Fill/Image.php
 */
require_once 'Image/Graph/Fill/Image.php';

/**
 * Fill using a gradient color.
 * This creates a scaled fillstyle with colors flowing gradiently between 2 specified 
 * RGB values. Several directions are supported:
 * 0. Vertically (IMAGE_GRAPH_GRAD_VERTICAL)
 * 1. Horizontally (IMAGE_GRAPH_GRAD_HORIZONTAL)
 * 2. Mirrored vertically (the color grades from a-b-a vertically) (IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED) 
 * 3. Mirrored horizontally (the color grades from a-b-a horizontally) IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED 
 * 4. Diagonally from top-left to right-bottom (IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR) 
 * 5. Diagonally from bottom-left to top-right (IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR) 
 * 6. Radially (concentric circles in the center) (IMAGE_GRAPH_GRAD_RADIAL)
 */
// TODO Make gradient fills be dependent upon the coordinates of the fill element (fx. a bar) not the entire area (fx. the plotarea)
class Image_Graph_Fill_Gradient extends Image_Graph_Fill_Image 
{

    /**
     * The direction of the gradient
     * @var int
     * @access private
     */
    var $_direction;

    /**
     * The first color to gradient
     * @var mixed
     * @access private
     */
    var $_startColor;

    /**
     * The last color to gradient
     * @var mixed
     * @access private
     */
    var $_endColor;

    /**
     * The alpha channel
     * @var int
     * @access private
     */
    var $_alpha;

    /**
     * Image_Graph_GradientFill [Constructor]
     * @param int $direction The direction of the gradient
     * @param mixed $startColor The value of the starting color
     * @param mixed $endColor The value of the ending color
     * @param mixed $alpha The alpha channel (not supported!)
     */
    function &Image_Graph_Fill_Gradient($direction, $startColor, $endColor, $alpha = false)
    {
        parent::Image_Graph_Fill_Image('');
        $this->_direction = $direction;
        $this->_startColor = Image_Graph_Color::color2RGB($startColor);
        $this->_endColor = Image_Graph_Color::color2RGB($endColor);
        $this->_alpha = false;
    }
    
    /**
     * Causes the object to update all sub elements coordinates (Image_Graph_Common, does not itself have coordinates, this is basically an abstract method)
     * @access private
     */
    function _updateCoords() {
        parent::_updateCoords();
        
        $width = $this->width();
        $height = $this->height();
        
        switch ($this->_direction) {
            case IMAGE_GRAPH_GRAD_HORIZONTAL :
                $count = $width;
                break;

            case IMAGE_GRAPH_GRAD_VERTICAL :
                $count = $height;
                break;

            case IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED :
                $count = $width/2;
                break;

            case IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED :
                $count = $height/2;
                break;

            case IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR :
            case IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR :
                $count = sqrt($width*$width + $height*$height);
                break;

            case IMAGE_GRAPH_GRAD_RADIAL :
                $count = max($width, $height);
                break;
        }

        if (isset($GLOBALS['_Image_Graph_gd2'])) {
            $this->_image = ImageCreateTrueColor($this->_graphWidth(), $this->_graphHeight());
            if ($this->_alpha !== false) {
                ImageAlphaBlending($this->_image, true);
                ImageColorTransparent($this->_image, $transparent = Image_Graph_Color::allocateColor($this->_image, array(0xab, 0xe1, 0x23)));        
                ImageFilledRectangle($this->_image, 0, 0, $this->_graphWidth()-1, $this->_graphHeight()-1, $transparent);
            }
        } else {
            $this->_image = ImageCreate($this->_graphWidth(), $this->_graphHeight());
        }

        $redIncrement = ($this->_endColor[0] - $this->_startColor[0]) / $count;
        $greenIncrement = ($this->_endColor[1] - $this->_startColor[1]) / $count;
        $blueIncrement = ($this->_endColor[2] - $this->_startColor[2]) / $count;

        for ($i = 0; $i <= $count; $i ++) {
            unset($color);
            if ($i == 0) {
                $color = array($this->_startColor[0], $this->_startColor[1], $this->_startColor[2]);
            } else {
                $color[0] = round(($redIncrement * $i) + $redIncrement + $this->_startColor[0]);
                $color[1] = round(($greenIncrement * $i) + $greenIncrement + $this->_startColor[1]);
                $color[2] = round(($blueIncrement * $i) + $blueIncrement + $this->_startColor[2]);
            }
            if ($this->_alpha !== false) {
                $color[3] = $this->_alpha;
            }
            $color = Image_Graph_Color::allocateColor($this->_image, $color);

            switch ($this->_direction) {
                case IMAGE_GRAPH_GRAD_HORIZONTAL :
                    ImageLine($this->_image, 
                        $this->_left + $i, 
                        $this->_top, 
                        $this->_left + $i, 
                        $this->_top + $height-1, $color);
                    break;

                case IMAGE_GRAPH_GRAD_VERTICAL :
                    ImageLine($this->_image, 
                        $this->_left, 
                        $this->_top + $height - $i, 
                        $this->_left + $width - 1, 
                        $this->_top + $height - $i, $color);
                    break;

                case IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED :
                    ImageLine($this->_image, 
                        $this->_left + $i, 
                        $this->_top, 
                        $this->_left + $i, 
                        $this->_top + $height-1, $color);
                    ImageLine($this->_image, 
                        $this->_left + $width - $i, 
                        $this->_top, 
                        $this->_left + $width - $i, 
                        $this->_top + $height-1, $color);
                    break;

                case IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED :
                    ImageLine($this->_image, 
                        $this->_left, 
                        $this->_top + $i, 
                        $this->_left + $width - 1, 
                        $this->_top + $i, $color);
                    ImageLine($this->_image, 
                        $this->_left, 
                        $this->_top + $height - $i, 
                        $this->_left + $width - 1, 
                        $this->_top + $height - $i, $color);
                    break;

                case IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR :
                    if ($i > $width) {
                        $polygon = array (
                            $this->_left + $width, 
                            $this->_top + $i - $width, 
                            $this->_left + $width, 
                            $this->_top + $height, 
                            $this->_left + $i - $width, 
                            $this->_top + $height);
                    } else {
                        $polygon = array (
                            $this->_left, 
                            $this->_top + $i, 
                            $this->_left, 
                            $this->_top + $height, 
                            $this->_left + $width, 
                            $this->_top + $height, 
                            $this->_left + $width, 
                            $this->_top, 
                            $this->_left + $i, 
                            $this->_top);
                    }
                    ImageFilledPolygon($this->_image, $polygon, count($polygon) / 2, $color);
                    break;

                case IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR :
                    if ($i > $height) {
                        $polygon = array (
                            $this->_left + $i - $height, 
                            $this->_top, 
                            $this->_left + $width, 
                            $this->_top, 
                            $this->_left + $width, 
                            $this->_top + 2 * $height - $i);
                    } else {
                        $polygon = array (
                            $this->_left, 
                            $this->_top + $height - $i, 
                            $this->_left, 
                            $this->_top, 
                            $this->_left + $width, 
                            $this->_top, 
                            $this->_left + $width, 
                            $this->_top + $height, 
                            $this->_left + $i, 
                            $this->_top + $height);
                    }
                    ImageFilledPolygon($this->_image, $polygon, count($polygon) / 2, $color);
                    break;

                case IMAGE_GRAPH_GRAD_RADIAL :
                    if (($GLOBALS['_Image_Graph_gd2']) and ($i < $count)) {
                        ImageFilledEllipse($this->_image, 
                            $this->_left + $width / 2, 
                            $this->_top + $height / 2, $count - $i, $count - $i, $color);
                    }
                    break;
            }
        }
    }
    
}

?>