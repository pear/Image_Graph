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
 * Include file Image/Graph/Font/Extended.php
 */
require_once 'Image/Graph/Font/Extended.php';

/**
 * A truetype font.
 */
class Image_Graph_Font_TTF extends Image_Graph_Font_Extended 
{

    /**
     * The file of the font.
     * On Windows systems they will be located in %SYSTEMROOT%\FONTS, ie C:\WINDOWS\FONTS
     * @var string
     * @access private
     */
    var $_fontFile;

    /**
     * FontTTF [Constructor]
     * @param string $fontFile The filename of the TTF font file. On Windows systems they will be located in %SYSTEMROOT%\FONTS, ie C:\WINDOWS\FONTS	 
     */
    function &Image_Graph_Font_TTF($fontFile)
    {
        parent::Image_Graph_Font();
        $this->setFontFile($fontFile);
    }

    /**
     * Set another font file
     * @param string $fontFile The filename of the TTF font file. On Windows systems they will be located in %SYSTEMROOT%\FONTS, ie C:\WINDOWS\FONTS	 
     */
    function setFontFile($fontFile)
    {
        if (strtolower(substr($fontFile, -4)) != '.ttf') {
            $fontFile .= '.ttf';
        }

        if (file_exists($fontFile)) {
            $this->_fontFile = $fontFile;
        }
        elseif (file_exists(dirname(__FILE__)."/../Fonts/$fontFile")) {
            $this->_fontFile = dirname(__FILE__)."/../Fonts/$fontFile";
        }
    }

    /**
     * Get the width of the text specified in pixels
     * @param string $text The text to calc the width for
     * @param int $angle The angle to calculate the width with 
     * @return int The width of the text using the specified font 
     */
    function width($text, $angle = false)
    {
        if ($angle === false) {
            $angle = $this->_angle;
        }
        $bounds = ImageTTFBBox($this->_size, $angle, $this->_fontFile, $text);
        $x0 = min($bounds[0], $bounds[2], $bounds[4], $bounds[6]);
        $x1 = max($bounds[0], $bounds[2], $bounds[4], $bounds[6]);
        return abs($x0 - $x1);
    }

    /**
     * Get the height of the text specified in pixels
     * @param string $text The text to calc the height for 
     * @param int $angle The angle to calculate the height with 
     * @return int The height of the text using the specified font 
     */
    function height($text, $angle = false)
    {
        if ($angle === false) {
            $angle = $this->_angle;
        }
        $bounds = ImageTTFBBox($this->_size, $angle, $this->_fontFile, $text);
        $y0 = min($bounds[1], $bounds[3], $bounds[5], $bounds[7]);
        $y1 = max($bounds[1], $bounds[3], $bounds[5], $bounds[7]);
        return abs($y0 - $y1);
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
        if ($this->_angle !== false) {
            $textVerticalAngle = 360 - $this->_angle;
            $height = $this->height($text, 0);
            //$x -= ($textOffsetX = cos(deg2rad($textVerticalAngle))*$height/2);        
            $y += ($textOffsetY = sin(deg2rad($textVerticalAngle)) * $height / 2);
            if (($this->_angle <= 180) and ($this->_angle > 0)) {
                $y += $this->height($text);
            }
        } else {
            $y += $this->height($text);
        }
        ImageTTFText($this->_canvas(), $this->_size, $this->_angle, $x, $y, $this->_getColor(), $this->_fontFile, $text);
    }

}

?>