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
 * Include file Image/Graph/Font.php
 */
require_once 'Image/Graph/Font.php';

/**
 * A vertical font
 */
class Image_Graph_Font_Vertical extends Image_Graph_Font 
{

    /**
     * Get the width of the text specified in pixels
     * @param string $text The text to calc the width for 
     * @return int The width of the text using the specified font 
     */
    function width($text)
    {
        return ImageFontHeight(IMAGE_GRAPH_FONT);
    }

    /**
     * Get the height of the text specified in pixels
     * @param string $text The text to calc the height for 
     * @return int The height of the text using the specified font 
     */
    function height($text)
    {
        return ImageFontWidth(IMAGE_GRAPH_FONT) * strlen($text);
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
        ImageStringUp($this->_canvas(), IMAGE_GRAPH_FONT, $x, $y + $this->height($text), $text, $this->_getColor());
    }

}

?>