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
 *
 * @package Image_Graph
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */

/**
 * Include file Common.php
 */
require_once 'Image/Graph/Common.php';

/**
 * Representation of a element.
 *
 * The Image_Graph_Element can be drawn on the canvas, ie it has coordinates,
 * {@link Image_Graph_Line}, {@link Image_Graph_Fill}, border and background -
 * although not all of these may apply to all children.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @abstract
 */
class Image_Graph_Element extends Image_Graph_Common
{

    /**
     * The leftmost pixel of the element on the canvas
     * @var int
     * @access private
     */
    var $_left = 0;

    /**
     * The topmost pixel of the element on the canvas
     * @var int
     * @access private
     */
    var $_top = 0;

    /**
     * The rightmost pixel of the element on the canvas
     * @var int
     * @access private
     */
    var $_right = 0;

    /**
     * The bottommost pixel of the element on the canvas
     * @var int
     * @access private
     */
    var $_bottom = 0;

    /**
     * Background of the element. Default: None
     * @var FillStyle
     * @access private
     */
    var $_background = null;

    /**
     * Borderstyle of the element. Default: None
     * @var LineStyle
     * @access private
     */
    var $_borderStyle = null;

    /**
     * Line style of the element. Default: None
     * @var LineStyle
     * @access private
     */
    var $_lineStyle = 'black';

    /**
     * Fill style of the element. Default: None
     * @var FillStyle
     * @access private
     */
    var $_fillStyle = 'white';

    /**
     * Font of the element. Default: Standard font - FONT
     * @var Font
     * @access private
     * @see $IMAGE_GRAPH_FONT
     */
    var $_font = null;

    /**
     * Font options
     * @var array
     * @access private
     */
    var $_fontOptions = array();

    /**
     * Enable shadows on the element
     * @var bool
     * @access private
     */
    var $_shadow = false;

    /**
     * The padding displayed on the element
     * @var int
     * @access private
     */
    var $_padding = 0;

    /**
     * Resets the elements
     *
     * @access private
     */
    function _reset()
    {
        parent::_reset();
    }

    /**
     * Sets the background fill style of the element
     *
     * @param Image_Graph_Fill $background The background
     * @see Image_Graph_Fill
     */
    function setBackground(& $background)
    {
        if (!is_a($background, 'Image_Graph_Fill')) {
            $this->_error(
                'Could not set background for ' . get_class($this) . ': ' .
                get_class($background), array('background' => &$background)
            );
        } else {
            $this->_background = & $background;
            $this->add($background);
        }
    }

    /**
     * Shows shadow on the element
     */
    function showShadow()
    {
        $this->_shadow = true;
    }

    /**
     * Sets the background color of the element.
     *
     * See colors.txt in the docs/ folder for a list of available named colors.
     *
     * @param mixed $color The color
     */
    function setBackgroundColor($color)
    {
        $this->_background = $color;
    }

     /**
     * Gets the background fill style of the element
     *
     * @return int A GD fillstyle representing the background style
     * @see Image_Graph_Fill
     * @access private
     */
    function _getBackground()
    {
        if (is_object($this->_background)) {
            $this->_driver->setFill($this->_background->_getFillStyle());
        } elseif ($this->_background != null) {
            $this->_driver->setFill($this->_background);
        } else {
            return false;
        }
        return true;
    }

    /**
     * Sets the border line style of the element
     *
     * @param Image_Graph_Line $borderStyle The line style of the border
     * @see Image_Graph_Line
     */
    function setBorderStyle(& $borderStyle)
    {
        if (!is_a($borderStyle, 'Image_Graph_Line')) {
            $this->_error(
                'Could not set border style for ' . get_class($this) . ': ' .
                get_class($borderStyle), array('borderstyle' => &$borderStyle)
            );
        } else {
            $this->_borderStyle = & $borderStyle;
            $this->add($borderStyle);
        }
    }

    /**
     * Sets the border color of the element.
     *
     * See colors.txt in the docs/ folder for a list of available named colors.
     * @param mixed $color The color
     */
    function setBorderColor($color)
    {
        $this->_borderStyle = $color;
    }

    /**
     * Gets the border line style of the element
     *
     * @return int A GD linestyle representing the borders line style
     * @see Image_Graph_Line
     * @access private
     */
    function _getBorderStyle()
    {
        if (is_object($this->_borderStyle)) {
            $result = $this->_borderStyle->_getLineStyle();
            $this->_driver->setLineThickness($result['thickness']);
            $this->_driver->setLineColor($result['color']);
        } elseif ($this->_borderStyle != null) {
            $this->_driver->setLineThickness(1);
            $this->_driver->setLineColor($this->_borderStyle);
        } else {
            return false;
        }
        return true;
    }

    /**
     * Sets the line style of the element
     *
     * @param Image_Graph_Line $lineStyle The line style of the element
     * @see Image_Graph_Line
     */
    function setLineStyle(& $lineStyle)
    {
        if (!is_object($lineStyle)) {
            $this->_error(
                'Could not set line style for ' . get_class($this) . ': ' .
                get_class($lineStyle), array('linestyle' => &$lineStyle)
            );
        } else {
            $this->_lineStyle = & $lineStyle;
            $this->add($lineStyle);
        }
    }

    /**
     * Sets the line color of the element.
     *
     * See colors.txt in the docs/ folder for a list of available named colors.
     *
     * @param mixed $color The color
     */
    function setLineColor($color)
    {
        $this->_lineStyle = $color;
    }

    /**
     * Gets the line style of the element
     *
     * @return int A GD linestyle representing the line style
     * @see Image_Graph_Line
     * @access private
     */
    function _getLineStyle()
    {
        if (is_object($this->_lineStyle)) {
            $result = $this->_lineStyle->_getLineStyle();
            $this->_driver->setLineThickness($result['thickness']);
            $this->_driver->setLineColor($result['color']);
        } elseif ($this->_lineStyle != null) {
            $this->_driver->setLineThickness(1);
            $this->_driver->setLineColor($this->_lineStyle);
        } else {
            return false;
        }
        return true;
    }

    /**
     * Sets the fill style of the element
     *
     * @param Image_Graph_Fill $fillStyle The fill style of the element
     * @see Image_Graph_Fill
     */
    function setFillStyle(& $fillStyle)
    {
        if (!is_a($fillStyle, 'Image_Graph_Fill')) {
            $this->_error(
                'Could not set fill style for ' . get_class($this) . ': ' .
                get_class($fillStyle), array('fillstyle' => &$fillStyle)
            );
        } else {
            $this->_fillStyle = & $fillStyle;
            $this->add($fillStyle);
        }
    }

    /**
     * Sets the fill color of the element.
     *
     * See colors.txt in the docs/ folder for a list of available named colors.
     *
     * @param mixed $color The color
     */
    function setFillColor($color)
    {
        $this->_fillStyle = $color;
    }


    /**
     * Gets the fill style of the element
     *
     * @return int A GD filestyle representing the fill style
     * @see Image_Graph_Fill
     * @access private
     */
    function _getFillStyle($ID = false)
    {
        if (is_object($this->_fillStyle)) {
            $this->_driver->setFill($this->_fillStyle->_getFillStyle($ID));
        } elseif ($this->_fillStyle != null) {
            $this->_driver->setFill($this->_fillStyle);
        } else {
            return false;
        }
        return true;
    }

    /**
     * Gets the font of the element.
     *
     * If not font has been set, the parent font is propagated through it's
     * children.
     *
     * @return array An associated array used for driver
     * @access private
     */
    function _getFont($options = false)
    {
        // TODO Avoid calling this multiple times
        if ($options === false) {
            $options = $this->_fontOptions;
        } else {
            $options = array_merge($this->_fontOptions, $options);
        }

        if ($this->_font == null) {
            $result = $this->_parent->_getFont($options);
        } else {
            $result = $this->_font->_getFont($options);
        }

        if ((isset($result['size'])) && (isset($result['size_rel']))) {
            $result['size'] += $result['size_rel'];
            unset($result['size_rel']);
        }
        return $result;
    }

    /**
     * Sets the font of the element
     *
     * @param Image_Graph_Font $font The font of the element
     * @see Image_Graph_Font
     */
    function setFont(& $font)
    {
        if (!is_a($font, 'Image_Graph_Font')) {
            $this->_error('Invalid font set on ' . get_class($this));
        } else {
            $this->_font = & $font;
            $this->add($font);
        }
    }

    /**
     * Sets the font size
     *
     * @param int $size The size of the font
     */
    function setFontSize($size) {
        $this->_fontOptions['size'] = $size;
    }

    /**
     * Sets the font angle
     *
     * @param int $angle The angle of the font
     */
    function setFontAngle($angle) {
        if ($angle == 'vertical') {
            $this->_fontOptions['vertical'] = true;
            $this->_fontOptions['angle'] = 90;
        } else {
            $this->_fontOptions['angle'] = $angle;
        }
    }

    /**
     * Sets the font color
     *
     * @param mixed $color The color of the font
     */
    function setFontColor($color) {
        $this->_fontOptions['color'] = $color;
    }

    /**
     * Sets the coordinates of the element
     *
     * @param int $left The leftmost pixel of the element on the canvas
     * @param int $top The topmost pixel of the element on the canvas
     * @param int $right The rightmost pixel of the element on the canvas
     * @param int $bottom The bottommost pixel of the element on the canvas
     * @access private
     */
    function _setCoords($left, $top, $right, $bottom)
    {
        if ($left === false) {
            $left = $this->_left;
        }
        
        if ($top === false) {
            $top = $this->_top;
        }

        if ($right === false) {
            $right = $this->_right;
        }
        
        if ($bottom === false) {
            $bottom = $this->_bottom;
        }
        
        $this->_left = min($left, $right);
        $this->_top = min($top, $bottom);
        $this->_right = max($left, $right);
        $this->_bottom = max($top, $bottom);
    }

    /**
     * Moves the element
     *
     * @param int $deltaX Number of pixels to move the element to the right
     *   (negative values move to the left)
     * @param int $deltaY Number of pixels to move the element downwards
     *   (negative values move upwards)
     * @access private
     */
    function _move($deltaX, $deltaY)
    {
        $this->_left += $deltaX;
        $this->_right += $deltaX;
        $this->_top += $deltaY;
        $this->_bottom += $deltaY;
    }

    /**
     * Sets the width of the element relative to the left side
     *
     * @param int $width Number of pixels the element should be in width
     * @access private
     */
    function _setWidth($width)
    {
        $this->_right = $this->_left + $width;
    }

    /**
     * Sets the height of the element relative to the top
     *
     * @param int $width Number of pixels the element should be in height
     * @access private
     */
    function _setHeight($height)
    {
        $this->_bottom = $this->_top + $height;
    }

    /**
     * Sets padding of the element
     *
     * @param int $padding Number of pixels the element should be padded with
     */
    function setPadding($padding)
    {
        $this->_padding = $padding;
    }

    /**
     * The width of the element on the canvas
     *
     * @return int Number of pixels representing the width of the element
     */
    function width()
    {
        return abs($this->_right - $this->_left) + 1;
    }

    /**
     * The height of the element on the canvas
     *
     * @return int Number of pixels representing the height of the element
     */
    function height()
    {
        return abs($this->_bottom - $this->_top) + 1;
    }

    /**
     * Left boundary of the background fill area
     *
     * @return int Leftmost position on the canvas
     * @access private
     */
    function _fillLeft()
    {
        return $this->_left + $this->_padding;
    }

    /**
     * Top boundary of the background fill area
     *
     * @return int Topmost position on the canvas
     * @access private
     */
    function _fillTop()
    {
        return $this->_top + $this->_padding;
    }

    /**
     * Right boundary of the background fill area
     *
     * @return int Rightmost position on the canvas
     * @access private
     */
    function _fillRight()
    {
        return $this->_right - $this->_padding;
    }

    /**
     * Bottom boundary of the background fill area
     *
     * @return int Bottommost position on the canvas
     * @access private
     */
    function _fillBottom()
    {
        return $this->_bottom - $this->_padding;
    }

    /**
     * Returns the filling width of the element on the canvas
     *
     * @return int Filling width
     * @access private
     */
    function _fillWidth()
    {
        return $this->_fillRight() - $this->_fillLeft() + 1;
    }

    /**
     * Returns the filling height of the element on the canvas
     *
     * @return int Filling height
     * @access private
     */
    function _fillHeight()
    {
        return $this->_fillBottom() - $this->_fillTop() + 1;
    }

    /**
     * Draws a shadow 'around' the element
     * 
     * Not implemented yet.
     *
     * @access private
     */
    function _displayShadow()
    {        
        // TODO Implement shadows
    }

    /**
     * Writes text to the canvas.
     *
     * @param int $x The x position relative to alignment
     * @param int $y The y position relative to alignment
     * @param string $text The text
     * @param int $alignmen The text alignment (both vertically and horizontally)
     */
    function write($x, $y, $text, $alignment = false, $font = false)
    {
        $font = $this->_getFont($font);

        if ($alignment === false) {
            $alignment = IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP;
        }

        $this->_driver->setFont($font);
        $this->_driver->write($x, $y, $text, $alignment);
    }



    /**
     * Output the element to the canvas
     *
     * @return bool Was the output 'good' (true) or 'bad' (false).
     * @see Image_Graph_Common
     * @access private
     */
    function _done()
    {
        $this->_getBackground();
        $this->_getBorderStyle();
        $this->_driver->rectangle($this->_left, $this->_top, $this->_right, $this->_bottom);

        $result = parent::_done();

        return $result;
    }

}
?>