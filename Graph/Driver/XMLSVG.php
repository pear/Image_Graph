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
 * Class for handling output in SVG format.
 *
 * Outputs the graph in SVG format (Scalable Vector Graphics).
 *
 * Based on the PEAR::XML_SVG package.
 *
 * This driver is experimental, XML_SVG lacks some required functionality. Until
 * it provides this functionality use the Image_Graph_Driver_SVG driver.
 * @package Image_Graph
 * @subpackage Driver
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 * @since 0.3.0dev2
 */

/**
 * Include file Image/Graph/Driver.php
 */
require_once 'Image/Graph/Driver.php';

/**
 * Include file XML/SVG.php
 */
require_once 'XML/SVG.php';

/**
 * Include file Image/Graph/Constants.php
 */
require_once 'Image/Graph/Constants.php';

/**
 * Include file Image/Graph/Color.php
 */
require_once 'Image/Graph/Color.php';

/**
 * SVG Driver class.
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Driver
 * @since 0.3.0dev2
 */
class Image_Graph_Driver_XMLSVG extends Image_Graph_Driver
{

    /**
     * The SVG document
     * @var XML_SVG_Document
     * @access private
     */
    var $_svg = '';

    /**
     * The width of the SVG document
     * @var int
     * @access private
     */
    var $_width = 0;

    /**
     * The height of the SVG document
     * @var int
     * @access private
     */
    var $_height = 0;

    /**
     * Create the SVG driver
     *
     * @param array $param Parameter array
     */
    function &Image_Graph_Driver_XMLSVG($param)
    {
        parent::Image_Graph_Driver($param);

        if (isset($param['width'])) {
            $this->_width = $param['width'];
        }
        if (isset($param['height'])) {
            $this->_height = $param['height'];
        }

        $this->_svg =& new XML_SVG_Document(
            array(
                'width' => $this->_width,
                'height' => $this->_height
            )
        );
    }

    /**
     * Get the color index for the RGB color
     *
     * @param int $color The color
     * @return string A SVG compatible color
     * @access private
     */
    function _color($color = false)
    {
        if ($color === false) {
            return 'transparent';
        } else {
            $color = Image_Graph_Color::color2RGB($color);
            return 'rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')';
        }
    }

    /**
     * Get the opacity for the RGB color
     *
     * @param int $color The color
     * @return int A SVG compatible opacity value
     * @access private
     */
    function _opacity($color = false)
    {
        if ($color === false) {
            return false;
        } else {
            $color = Image_Graph_Color::color2RGB($color);
            if ($color[3] != 255) {
                return sprintf('%0.1f', $color[3]/255);
            } else {
                return false;
            }
        }
    }

    /**
     * Get the SVG applicable linestyle
     *
     * @param mixed $lineStyle The line style to return, false if the one
     *   explicitly set
     * @return mixed A SVG compatible linestyle
     * @access private
     */
    function _getLineStyle($lineStyle = false)
    {
        if ($lineStyle === false) {
            $lineStyle = $this->_lineStyle;
        }

        if ($lineStyle != 'transparent') {
            $result = 'stroke-width:' . $this->_thickness . ';';
            $result .= 'stroke:' .$this->_color($lineStyle) . ';';
            if ($opacity = $this->_opacity($lineStyle)) {
                $result .= 'stroke-opacity:' . $opacity . ';';
            }
        }
        return $result;
    }

    /**
     * Get the SVG applicable fillstyle
     *
     * @param mixed $fillStyle The fillstyle to return, false if the one
     *   explicitly set
     * @return mixed A SVG compatible fillstyle
     * @access private
     */
    function _getFillStyle($fillStyle = false)
    {
        if ($fillStyle === false) {
            $fillStyle = $this->_fillStyle;
        }

        if (is_array($fillStyle)) {
            if ($fillStyle['type'] == 'gradient') {
                // TODO Gradient fill's does not seem to be supported by XML_SVG, investigate
                $opacity = $this->_opacity($fillStyle['start']);
                $result = 'fill:' . $this->_color($fillStyle['start']) . ';';
                if ($opacity = $this->_opacity($fillStyle)) {
                    $result .= 'fill-opacity:' . $opacity . ';';
                }
            }
        } elseif ($fillStyle != 'transparent') {
            $result = 'fill:' . $this->_color($fillStyle) . ';';
            if ($opacity = $this->_opacity($fillStyle)) {
                $result .= 'fill-opacity:' . $opacity . ';';
            }
            return $result;
        } else {
            return '';;
        }
    }

    /**
     * Get the width of the canvas
     *
     * @return int The width
     */
    function getWidth()
    {
        return $this->_width;
    }

    /**
     * Get the height of the canvas
     *
     * @return int The height
     */
    function getHeight()
    {
        return $this->_height;
    }

    /**
     * Sets an image that should be used for filling
     *
     * @param string $filename The filename of the image to fill with
     */
    function setFillImage($filename)
    {
    }

    /**
     * Draw a line
     *
     * @param int $x0 X start point
     * @param int $y0 X start point
     * @param int $x1 X end point
     * @param int $y1 Y end point
     * @param mixed $color The line color, can be omitted
     */
    function line($x0, $y0, $x1, $y1, $color = false)
    {
        $this->_svg->addChild(
            new XML_SVG_Line(
                array(
                    'x1' => $x0,
                    'y1' => $y0,
                    'x2' => $x1,
                    'y2' => $y1,
                    'style' => $this->_getLineStyle($color)
                )
            )
        );
    }

    /**
     * Draw a rectangle
     *
     * @param int $x0 X start point
     * @param int $y0 X start point
     * @param int $x1 X end point
     * @param int $y1 Y end point
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function rectangle($x0, $y0, $x1, $y1, $fillColor = false, $lineColor = false)
    {
        $this->_svg->addChild(
            new XML_SVG_Rect(
                array(
                    'x' => $x0,
                    'y' => $y0,
                    'width' => abs($x1 - $x0),
                    'height' => abs($y1 - $y0),
                    'style' => $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor)
                )
            )
        );
    }

    /**
     * Draw an ellipse
     *
     * @param int $x Center point x-value
     * @param int $y Center point y-value
     * @param int $rx X-radius of ellipse
     * @param int $ry Y-radius of ellipse
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function ellipse($x, $y, $rx, $ry, $fillColor = false, $lineColor = false)
    {
        $this->_svg->addChild(
            new XML_SVG_Ellipse(
                array(
                    'cx' => $x,
                    'cy' => $y,
                    'rx' => $rx,
                    'ry' => $ry,
                    'style' => $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor)
                )
            )
        );
    }

    /**
     * Output the result of the driver
     *
     * @param array $param Parameter array
     * @abstract
     */
    function done($param = false)
    {
        $this->_svg->printElement();
    }

}

?>