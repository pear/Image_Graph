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
 * @subpackage Text     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Common.php
 */
require_once 'Image/Graph/Common.php';

/**
 * A font.
 */
class Image_Graph_Font extends Image_Graph_Common 
{

    /**
     * The color of the font
     * @var Color
     * @access private
     */
    var $_color = 'black';

    /**
     * Image_Graph_Font [Constructor]
     */
    function &Image_Graph_Font()
    {
        parent::Image_Graph_Common();
    }

    /**
     * Set the color of the font
     * @param mixed $color The color object of the Font 
     */
    function setColor($color)
    {
        $this->_color = $color;
    }

    /**
     * Get the color of the font
     * @return int The color of the Font 
     * @access private 
     */
    function _getColor()
    {
        return $this->_color($this->_color);
    }

    /**
     * Get the width of the text specified in pixels
     * @param string $text The text to calc the width for 
     * @return int The width of the text using the specified font 
     */
    function width($text)
    {
        return ImageFontWidth(IMAGE_GRAPH_FONT) * strlen($text);
    }

    /**
     * Get the height of the text specified in pixels
     * @param string $text The text to calc the height for 
     * @return int The height of the text using the specified font 
     */
    function height($text)
    {
        return ImageFontHeight(IMAGE_GRAPH_FONT);
    }

    /**
     * Get the center width of the text specified in pixels
     * @param string $text The text to calc the width for 
     * @return int The center width of the text using the specified font 
     * @access private 
     */
    function _centerWidth($text)
    {
        return (int) ($this->width($text) / 2);
    }

    /**
     * Get the center height of the text specified in pixels
     * @param string $text The text to calc the height for 
     * @return int The center height of the text using the specified font
     * @access private 
     */
    function _centerHeight($text)
    {
        return (int) ($this->height($text) / 2);
    }

    /**
     * Write a text on the canvas
     * @param int $x The X (horizontal) position of the text 
     * @param int $y The Y (vertical) position of the text 
     * @param string $text The text to write on the canvas 
     * @access private 
     */
    function _write($x, $y, $text)
    {
        ImageString($this->_canvas(), IMAGE_GRAPH_FONT, $x, $y, $text, $this->_getColor());
    }

}

?>