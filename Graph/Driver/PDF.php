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
 * Class for handling output in PDF format.
 * Requires PEAR::File_PDF
 * 
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
 * Include file File/PDF.php
 */
require_once 'File/PDF.php';

/**
 * Include file Image/Graph/Color.php
 */
require_once 'Image/Graph/Color.php';
 
/**
 * PDF Driver class.
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Driver
 * @since 0.3.0dev2
 */
class Image_Graph_Driver_PDF extends Image_Graph_Driver 
{

    /**
     * The PDF document
     * @var File_PDF
     * @access private
     */
    var $_pdf;

    /**
     * Create a driver
     * @param array $param Parameter array
     */
    function &Image_Graph_Driver_PDF($param)
    {
        parent::Image_Graph_Driver($param);

        $this->_pdf =& File_PDF::factory();
        $this->_pdf->open();
        $this->_pdf->addPage();
    }

    /**
     * Get the color index for the RGB color
     * @param int $color The color
     * @return int The GD image index of the color
     * @access private
     */
    function _color($color = false)
    {
        if ($color === false) {
            return false;
        } else {
            $color = Image_Graph_Color::color2RGB($color);
            $color[0] = $color[0]/255;
            $color[1] = $color[1]/255;
            $color[2] = $color[2]/255;
            return $color;
        }
    }

    /**
     * Get the GD applicable linestyle
     * @param mixed $lineStyle The line style to return, false if the one
     * explicitly set
     * @return mixed A GD compatible linestyle
     * @access private
     */
    function _getLineStyle($lineStyle = false)
    {        
        if ($lineStyle === false) {
            $lineStyle = $this->_lineStyle;
        }

        if ($lineStyle == 'transparent') {
            return false;
        } 
        
        $color = $this->_color($lineStyle);
        $this->_pdf->setLineWidth($this->_thickness); 
        $this->_pdf->setDrawColor('rgb', $color[0], $color[1], $color[2]);
        return true;
    }

    /**
     * Get the PDF fill style
     * @param mixed $fillStyle The fillstyle to return, false if the one
     * explicitly set
     * @return bool True if set (so that a line should be drawn)
     * @access private
     */
    function _getFillStyle($fillStyle = false)
    {
        if ($fillStyle === false) {
            $fillStyle = $this->_fillStyle;
        }
        
        if ($fillStyle == 'transparent') {
            return false;
        } 

        $color = $this->_color($fillStyle);
        $this->_pdf->setFillColor('rgb', $color[0], $color[1], $color[2]);
        return true;
    }
    
    /**
     * Sets the font options.
     * The $font array may have the following entries:
     * 'type' = 'ttf' (TrueType) or omitted for default<br>
     * If 'type' = 'ttf' then the following can be specified<br>
     * 'size' = size in pixels<br>
     * 'angle' = the angle with which to write the text
     * 'file' = the .ttf file (either the basename, filename or full path)
     * @param array $font The font options.
     */
    function setFont($fontOptions)
    {
        parent::setFont($fontOptions);
        if (isset($this->_font['file'])) {
            $this->_pdf->setFont(
                $this->_font['file'], 
                '', 
                (isset($this->_font['size']) ? $this->_font['size'] : 'null')
            );
        } else {
            $this->_pdf->setFont('Arial');
        }
    }
    
    /**
     * Get the width of the canvas
     * @return int The width
     */
    function getWidth()
    {
    }

    /**
     * Get the height of the canvas
     * @return int The height
     */
    function getHeight()
    {
    }

    /**
     * Sets an image that should be used for filling
     * @param string $filename The filename of the image to fill with
     */
    function setFillImage($filename)
    {
    }

    /**
     * Resets the driver.
     * Include fillstyle, linestyle, thickness and polygon
     * @access private
     */
    function _reset()
    {
        parent::_reset();
    }
        
    
    /**
     * Draw a line
     * @param int $x0 X start point 
     * @param int $y0 X start point 
     * @param int $x1 X end point 
     * @param int $y1 Y end point
     * @param mixed $color The line color, can be omitted
     */
    function line($x0, $y0, $x1, $y1, $color = false)
    {
        if ($this->_getLineStyle($color) !== false) {
            $this->_pdf->line($x0, $y0, $x1, $y1);
        }        
        $this->_reset();
    }    

    /**
     * Draws a polygon
     * @param bool $connectEnds Specifies wether the start point should be
     * conencted to the endpoint (closed polygon) or not (connected line)
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function polygonEnd($connectEnds = true, $fillColor = false, $lineColor = false)
    {
        $prev_point = false;
        $first_point = false;
        if ($this->_getLineStyle($lineColor) !== false) {            
            foreach ($this->_polygon as $point) {
                if ($prev_point) {
                    $this->_pdf->line(
                        $prev_point['X'], 
                        $prev_point['Y'], 
                        $point['X'], 
                        $point['Y']
                    );
                }
                if ($first_point === false) {
                    $first_point = $point;
                }
                $prev_point = $point;
            }
            if ($connectEnds) {
                $this->_pdf->line(
                    $prev_point['X'], 
                    $prev_point['Y'], 
                    $first_point['X'], 
                    $first_point['Y']
                );
            }
        }
        $this->_reset();                
    }

    /**
     * Draws a polygon
     * @param bool $connectEnds Specifies wether the start point should be
     * conencted to the endpoint (closed polygon) or not (connected line)
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function splineEnd($connectEnds = true, $fillColor = false, $lineColor = false)
    {
        //$this->_reset();
        $this->polygonEnd($connectEnds, $fillColor, $lineColor);                
    }

    /**
     * Draw a rectangle
     * @param int $x0 X start point 
     * @param int $y0 X start point 
     * @param int $x1 X end point 
     * @param int $y1 Y end point
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function rectangle($x0, $y0, $x1, $y1, $fillColor = false, $lineColor = false)
    {
        $draw = '';
        if ($this->_getFillStyle($lineColor) !== false) {
            $draw .= 'F';
        }
        if ($this->_getLineStyle($lineColor) !== false) {
            $draw .= 'D';
        }
        
        if ($draw != '') {
            $this->_pdf->rect($x0, $y0, abs($x1-$x0), abs($y1-$y0), $draw);
        }
                       
        $this->_reset();
    }

    /**
     * Draw an ellipse
     * @param int $x Center point x-value 
     * @param int $y Center point y-value
     * @param int $rx X-radius of ellipse 
     * @param int $ry Y-radius of ellipse
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function ellipse($x, $y, $rx, $ry, $fillColor = false, $lineColor = false)
    {
        $draw = '';
        if ($this->_getFillStyle($lineColor) !== false) {
            $draw .= 'F';
        }
        if ($this->_getLineStyle($lineColor) !== false) {
            $draw .= 'D';
        }
        
        if ($draw != '') {
            $this->_pdf->circle($x, $y, $rx, $draw);
        }
                       
        $this->_reset();
    }

    /**
     * Get the width of a text,
     * @param string $text The text to get the width of
     * @return int The width of the text
     */ 
    function textWidth($text)
    {
        return $this->_pdf->getStringWidth($text);
    }

    /**
     * Get the height of a text,
     * @param string $text The text to get the height of
     * @return int The height of the text
     */ 
    function textHeight($text)
    {
        if (isset($this->_font['size'])) {
            return $this->_font['size'];
        } else {
            return 12;
        }
    }
    
    /**
     * Writes text
     * @param int $x X-point of text 
     * @param int $y Y-point of text
     * @param string $text The text to write
     * @param int $alignment The alignment of the text
     * @param mixed $color The color of the text
     */
    function write($x, $y, $text, $alignment, $color = false)
    {
        $this->_pdf->setXY($x, $y);
        $this->_pdf->write(5, $text);
        $this->_reset();
    }
    
    /**
     * Overlay image
     * @param int $x X-point of overlayed image 
     * @param int $y Y-point of overlayed image
     * @param string $filename The filename of the image to overlay  
     * @param int $width The width of the overlayed image (resizing if possible)
     * @param int $height The height of the overlayed image (resizing if
     * possible)
     */
    function overlayImage($x, $y, $filename, $width = false, $height = false)
    {
        //$this->_pdf->image($filename, $x, $y, $width, $height);
    }
    
    /**
     * Output the result of the driver
     * @param array $param Parameter array
     * @abstract
     */
    function done($param = false)
    {
        parent::done($param);
        $this->_pdf->output('image_graph.pdf', true);
    }
    
}

?>