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
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file PEAR/ErrorStack.php - for error handling
 */
require_once 'PEAR/ErrorStack.php';

/**
 * Include file Image/Graph/Config.php
 */
require_once 'Image/Graph/Config.php';

/**
 * Include file Image/Graph/Element.php
 */
require_once 'Image/Graph/Element.php';

/**
 * Include file Image/Graph/Constants.php
 */
require_once 'Image/Graph/Constants.php';

/**
 * Include file Image/Graph/Color.php
 */
require_once 'Image/Graph/Color.php';

/**
 * Main class for the graph creation.
 * This is the main class, it holds the canvas and performs the final
 * output by sending the http headers and making sure the elements are outputted.
 */
class Image_Graph extends Image_Graph_Element 
{

    /**
     * The GD Image resource.     
     * @var resource
     * @access private
     */
    var $_canvas = null;

    /**
     * Number of degress to rotate the canvas, counter-clockwise
     * @var int
     * @access private
     */
    var $_rotation = 0;

    /**
     * Show generation time on graph
     * @var bool
     * @access private
     */
    var $_showTime = false;

    /**
     * Filename of output, if it will be saved to a file
     * @var string
     * @access private
     */
    var $_fileName = '';

    /**
     * Filename of a possible thumbnail
     * @var string
     * @access private
     */
    var $_thumbFileName = '';

    /**
     * Width of a possible thumbnail
     * @var int
     * @access private
     */
    var $_thumbWidth = 0;

    /**
     * Height of a possible thumbnail
     * @var string
     * @access private
     */
    var $_thumbHeight = 0;

    /**
     * Output the image to the browser
     * @var bool
     * @access private
     */
    var $_outputImage = true;

    /**
     * Antialiasing percentage
     * @var int
     * @access private
     */
    var $_antialias = 0;

    /**
     * Specifies whether the logo should be displayed or not
     * @var boolean
     * @access private
     */
    var $_hideLogo = false;

    /**
     * Display errors on the canvas
     * @var boolean
     * @access private
     */
    var $_displayErrors = false;

    /**
     * Should caching be employed or not. If yes it holds the directory of the cache. If not
     * then false.
     * @var mixed.
     * @access private
     */
    var $_cache = false;

    /**
     * Image_Graph [Constructor].
     * If passing an array as a single parameter, the following set of indexes can be used:
     * 1) Create a new image
     * 'width' - The width of the graph in pixels
     * 'height' - The height of the graph in pixels
     * 'transparent' - If set the graph is created with a transparent background
     * 2) Use an "existing" GD image
     * 'image' - A gd resource to add the graph to
     * 'left' - The left most pixel where the graphs starts on the image
     * 'top' - The top most pixel where the graph starts on the image
     * 'width' and 'height' as above
     * 3) Create from a file
     * 'filename' - The filename of the image to add the graph to (either PNG or JPEG)   
     * 'left', 'top', 'width' and 'height' as above
     * @param mixed $params A set of parameters in an indexed array or the width of the graph in pixels.	 
     * @param int $height The height of the graph in pixels
     * @param bool $createTransparent Specify whether 	 
     */
    function &Image_Graph($params, $height = false, $createTransparent = false)
    {        
        $stack =& PEAR_ErrorStack::singleton('Image_Graph');
        
        include_once('Log.php');        
        $log =& Log::factory('file', 'image_graph.log', 'Image_Graph Error Log');
        $stack->setLogger($log);        
               
        parent::Image_Graph_Element();
        
        if (is_array($params)) {
            if (isset($params['left'])) {
                $left = $params['left'];
            } else {
                $left = 0;
            }

            if (isset($params['top'])) {
                $top = $params['top'];
            } else {
                $top = 0;
            }

            if (isset($params['width'])) {
                $width = $params['width'];
            } else {
                $width = 0;
            }
            
            if (isset($params['height'])) {
                $height = $params['height'];
            } else {
                $height = 0;
            }
            
            if ((isset($params['image'])) and (is_resource($params['image']))) {
                $this->_canvas =& $params['image'];
            } elseif ((isset($params['filename'])) and (file_exists($params['filename']))) {
                $filename = $params['filename'];
                if (strtolower(substr($filename, -4)) == '.png') {
                    $this->_canvas = ImageCreateFromPNG($filename);
                } else {
                    $this->_canvas = ImageCreateFromJPEG($filename);
                }            
            }                        
            
            if ($this->_canvas != null) {
                $this->_outputImage = false;
                if ($width == 0) {
                    $width = max(0, ImageSX($this->_canvas) - $left);
                } 
                if ($height == 0) {
                    $height = max(0, ImageSY($this->_canvas) - $top);
                } 
                $this->_setCoords($left, $top, $left + $width, $top + $height);
            }            
        }
        
        if ($this->_canvas == null) {
            if (!is_array($params)) {
                $width = $params;
            }            
            $this->_setCoords(0, 0, $width-1, $height-1);
    
            if (isset($GLOBALS['_Image_Graph_gd2'])) {
                $this->_canvas = ImageCreateTrueColor($width, $height);
                ImageAlphaBlending($this->_canvas(), true);
            } else {
                $this->_canvas = ImageCreate($width, $height);
            }

            ImageColorTransparent($this->_canvas(), Image_Graph_Color::allocateColor($this->_canvas, array(0xab, 0xe1, 0x23)));        

            if ($createTransparent) {
                ImageFilledRectangle($this->_canvas(), 0, 0, $width -1, $height -1, $this->_color());
            } else {
                ImageFilledRectangle($this->_canvas(), 0, 0, $width -1, $height -1, $this->_color('white'));
            }
        }

        $this->add($GLOBALS['_Image_Graph_font']);
        $this->add($GLOBALS['_Image_Graph_verticalFont']);
    }

    /**
     * Get a very precise timestamp
     * @return The number of seconds to a lot of decimals
     * @access private 
     */
    function _getMicroTime()
    {
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        return ($mtime);
    }
    
    /**
     * Returns the graph's canvas. 
     * @return resource A GD image representing the graph's canvas 
     * @access private
     */
    function &_canvas()
    {
        return $this->_canvas;
    }

    /**
     * Hides the logo from the output 
     */
    function hideLogo()
    {
        $this->_hideLogo = true;
    }

    /**
     * Get a new color. 
     * This method allocates a color to the graph. {@see Image_Graph_Color::allocateColor()}.
     * @param int $red The red part or the whole part
     * @param int $green The green part (or nothing), or the alpha channel
     * @param int $blue The blue part (or nothing)
     * @param int $alpha The alpha channel (or nothing)
     */
    function newColor($red, $green = false, $blue = false, $alpha = false)
    {
        if (($green !== false) and ($blue !== false) and (is_numeric($green)) and (is_numeric($blue))) {
            $color = array($red, $green, $blue);
        } else {
            $color = array(($red >> 16) & 0xff, ($red >> 8) & 0xff, $red & 0xff);
            $alpha = $green;            
        }
        if ($alpha !== false) {
            $color[] = $alpha;
        }
        $canvas = $this->_canvas();
        return Image_Graph_Color::allocateColor($canvas, $color);
    }

    /**
     * The width of the graph 
     * @return int Number of pixels representing the width of the graph
     */
    function width()
    {
        return ImageSX($this->_canvas());
    }

    /**
     * The height of the graph 
     * @return int Number of pixels representing the height of the graph
     */
    function height()
    {
        return ImageSY($this->_canvas());
    }

    /**
     * Rotate the final graph 
     * @param int $Rotation Number of degrees to rotate the canvas counter-clockwise
     */
    function rotate($rotation)
    {
        $this->_rotation = $rotation;
    }

    /**
     * The width of the graph
     * @see Image_Graph::width() 
     * @return int Number of pixels representing the width of the graph
     * @access private
     */
    function _graphWidth()
    {
        return $this->width();
    }

    /**
     * The height of the graph
     * @see Image_Graph::height() 
     * @return int Number of pixels representing the height of the graph
     * @access private
     */
    function _graphHeight()
    {
        return $this->height();
    }

    /**
     * Save the output as a file
     * @param string $fileName The filename and path of the file to save output in
     * @param bool $outputImage Output the image to the browser as well
     */
    function saveAs($fileName, $outputImage = false)
    {
        $this->_fileName = $fileName;
        $this->_outputImage = $outputImage;
    }

    /**
     * Create the output as a thumbnail
     * @param int $width The width of the thumbnail
     * @param int $height The height of the thumbnail
     * @param string $fileName The filename and path of the file to save the thumbnail in, if specified the thumbnail will be saved and the output will be the normal graph
     */
    function thumbnail($width = 80, $height = 60, $fileName = '')
    {
        $this->_thumbFileName = $fileName;
        $this->_thumbWidth = $width;
        $this->_thumbHeight = $height;
    }

    /**
     * Antialias the a single pixel in the graph
     * @param int $x1 X-coordinate of the first pixel to antialias
     * @param int $y1 Y-coordinate of the first pixel to antialias
     * @param int $x2 X-coordinate of the second pixel to antialias
     * @param int $y2 Y-coordinate of the second pixel to antialias
     * @access private
     */
    function _antialiasPixel($x1, $y1, $x2, $y2)
    {
        $rgb = ImageColorAt($this->_canvas(), $x1, $y1);
        $r1 = ($rgb >> 16) & 0xFF;
        $g1 = ($rgb >> 8) & 0xFF;
        $b1 = $rgb & 0xFF;

        $rgb = ImageColorAt($this->_canvas(), $x2, $y2);
        $r2 = ($rgb >> 16) & 0xFF;
        $g2 = ($rgb >> 8) & 0xFF;
        $b2 = $rgb & 0xFF;

        if (($r1 <> $r2) or ($g1 <> $g2) or ($b1 <> $b2)) {
            $r = round($r1 + ($r2 - $r1) * 50 / ($this->_antialias + 50));
            $g = round($g1 + ($g2 - $g1) * 50 / ($this->_antialias + 50));
            $b = round($b1 + ($b2 - $b1) * 50 / ($this->_antialias + 50));
            
            $rgb = Image_Graph_Color::allocateColor($this->_canvas, array($r, $g, $b));
            ImageSetPixel($this->_canvas(), $x2, $y2, $rgb);
        }
    }

    /**
     * Perform the antialias on the graph
     * @param int $percetage The percentage 'to' antialias
     * @access private
     */
    function _performAntialias()
    {
        for ($l = 0; $l < $this->height(); $l ++) {
            for ($p = 0; $p < $this->width(); $p ++) {
                // fix pixel to the left
                if ($p > 0) {
                    $this->_antialiasPixel($p, $l, $p -1, $l);
                }

                // fix pixel to the right
                if ($p < $this->width() - 1) {
                    $this->_antialiasPixel($p, $l, $p +1, $l);
                }

                // fix pixel above
                if ($l > 0) {
                    $this->_antialiasPixel($p, $l, $p, $l -1);
                }

                // fix pixel below
                if ($l < $this->height() - 1) {
                    $this->_antialiasPixel($p, $l, $p, $l +1);
                }
            }
        }
    }

    /**
     * Antialias on the graph
     * @param int $percent The percentage 'to' antialias
     */
    function antialias($percent = 5)
    {
        $this->_antialias = $percent;
    }

    /**
     * Displays errors on canvas
     */
    function displayErrors()
    {
        $this->_displayErrors = true;
        set_error_handler(array(&$this, '_default_error_handler'));
    }

    /**
     * Output to the canvas
     * @param int $type The type of image to output, i.e. IMG_PNG (default) and IMG_JPEG
     * @return mixed If the scripts outputs the image to the browser true is returned, if no output, the GD image is returned 
     */
    function done($type = IMG_PNG)
    {
        return $this->_done($type);
    }
    
    /**
     * Factory method to create Image_Graph objects.
     * Used for 'lazy including', i.e. loading only what is necessary, when it is necessary.
     * If only one parameter is required for the constructor of the class simply pass this
     * parameter as the $params parameter, unless the parameter is an array or a reference
     * to a value, in that case you must 'enclose' the parameter in an array. Similar if
     * the constructor takes more than one parameter specify the parameters in an array, i.e
     * Image_Graph::factory('MyClass', array($param1, $param2, &$param3));
     *
     * Class name can be either of the following:
     * 1 The 'real' Image_Graph class name, i.e. Image_Graph_Plotarea or Image_Graph_Plot_Line
     * 2 Short class name (leave out Image_Graph) and retain case, i.e. Plotarea, Plot_Line *not* plot_line
     * 3 Class name alias, the following are supported:
     * 'graph' = Image_Graph
     * 'plotarea' = Image_Graph_Plotarea
     * 'line' = Image_Graph_Plot_Line
     * 'area' = Image_Graph_Plot_Area
     * 'bar' = Image_Graph_Plot_Bar
     * 'stacked_bar' = Image_Graph_Plot_Bar_Stacked
     * 'dataset' = Image_Graph_Dataset_Trivial
     * 'random' = Image_Graph_Dataset_Random
     * 'axis' = Image_Graph_Axis
     * 'axis_log' = Image_Graph_Axis_Logarithmic
     * 'title' = Image_Graph_Title
     * 'line_grid' = Image_Graph_Grid_Lines
     * 'bar_grid' = Image_Graph_Grid_Bars
     * 'legend' = Image_Graph_Legend 
     * 'ttf_font' = Image_Graph_Font_TTF
     * 'gradient' = Image_Graph_Fill_Gradient
     * @param string $class The class for the object
     * @param mixed $params The paramaters to pass to the constructor
     * @return object A new object for the class 
     */
    function &factory($class, $params = null) 
    {
        if (substr($class, 0, 11) != 'Image_Graph') {
            switch ($class) {
            case 'graph': $class = 'Image_Graph'; break;
            case 'plotarea': $class = 'Image_Graph_Plotarea'; break;
            case 'line': $class = 'Image_Graph_Plot_Line'; break;
            case 'area': $class = 'Image_Graph_Plot_Area'; break;
            case 'bar': $class = 'Image_Graph_Plot_Bar'; break;
            case 'stacked_bar': $class = 'Image_Graph_Plot_Bar_Stacked'; break;
            case 'dataset': $class = 'Image_Graph_Dataset_Trivial'; break;
            case 'random': $class = 'Image_Graph_Dataset_Random'; break;
            case 'axis': $class = 'Image_Graph_Axis'; break;
            case 'axis_log': $class = 'Image_Graph_Axis_Logarithmic'; break;
            case 'title': $class = 'Image_Graph_Title'; break;
            case 'line_grid': $class = 'Image_Graph_Grid_Lines'; break;
            case 'bar_grid': $class = 'Image_Graph_Grid_Bars'; break;
            case 'legend': $class = 'Image_Graph_Legend'; break;
            case 'ttf_font': $class = 'Image_Graph_Font_TTF'; break;
            case 'gradient': $class = 'Image_Graph_Fill_Gradient'; break;
            default: $class = 'Image_Graph_' . $class; break;
            }
        }           
        
    	include_once str_replace('_', '/', $class) . '.php';        

        if (is_array($params)) {
            switch (count($params)) {
            case 1: return new $class($params[0]);
            case 2: return new $class($params[0], $params[1]);
            case 3: return new $class($params[0], $params[1], $params[2]);
            case 4: return new $class($params[0], $params[1], $params[2], $params[3]);
            case 5: return new $class($params[0], $params[1], $params[2], $params[3], $params[4]);
            case 6: return new $class($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
            case 7: return new $class($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
            case 8: return new $class($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7]);
            case 9: return new $class($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8]);
            case 10: return new $class($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9]);
            default: return new $class();
            }           
        } else {
            if ($params == null) {
                return new $class();
            } else {
                return new $class($params);
            }
    	}
    }

    /**
     * Factory method to create layouts.
     * This method is used for easy creation, since using {@see Image_Graph::factory()} does
     * not work with passing newly created objects from Image_Graph::factory() as reference,
     * this is something that is fortunately fixed in PHP5. 
     * Also used for 'lazy including', i.e. loading only what is necessary, when it is necessary.
     * @param mixed $layout The type of layout, can be either 'Vertical' or 'Horizontal'
     * @param Image_Graph_Element $part1 The 1st part of the layout
     * @param Image_Graph_Element $part2 The 2nd part of the layout
     * @param int $percentage The percentage of the layout to split at
     */
    function &layoutFactory($layout, &$part1, &$part2, $percentage = 50) {               
        include_once "Image/Graph/Layout/$layout.php";
        $class = "Image_Graph_Layout_$layout";
        return new $class($part1, $part2, $percentage);        
    }

    /**
     * Factory method to create horizontal layout.
     * @param Image_Graph_Element $part1 The 1st part of the layout
     * @param Image_Graph_Element $part2 The 2nd part of the layout
     * @param int $percentage The percentage of the layout to split at
     */
    function &horizontal(&$part1, &$part2, $percentage = 50) {                       
        return Image_Graph::layoutFactory('Horizontal', $part1, $part2, $percentage);        
    }
    
    /**
     * Factory method to create vertical layout.
     * @param Image_Graph_Element $part1 The 1st part of the layout
     * @param Image_Graph_Element $part2 The 2nd part of the layout
     * @param int $percentage The percentage of the layout to split at
     */
    function &vertical(&$part1, &$part2, $percentage = 50) {                       
        return Image_Graph::layoutFactory('Vertical', $part1, $part2, $percentage);        
    }
    
    /**
     * The error handling routine set by set_error_handler()
     * @param string $error_type The type of error being handled.
     * @param string $error_msg The error message being handled.
     * @param string $error_file The file in which the error occurred.
     * @param integer $error_line The line in which the error occurred.
     * @param string $error_context The context in which the error occurred.
     * @return Boolean
     * @access private
     */
    function _default_error_handler($error_type, $error_msg, $error_file, $error_line, $error_context)
    {
        switch( $error_type ) {
        case E_ERROR: $level = 'error'; break;
        case E_USER_ERROR: $level = 'user error'; break;
        case E_WARNING: $level = 'warning'; break;
        case E_USER_WARNING: $level = 'user warning'; break;
        case E_NOTICE: $level = 'notice'; break;
        case E_USER_NOTICE: $level = 'user notice'; break;
        default: $level = '(unknown)'; break;
        }
        
        $this->_error("PHP $level: $error_msg", 
            array(
                'type' => $error_type,
                'file' => $error_file,
                'line' => $error_line,
                'context' => $error_context
            )
        );
    }
    
    /**
     * Display errors on error stack
     */
    function _displayErrors() {
        $stack =& $this->_getErrorStack();
        if ($stack->hasErrors()) {
            $errors = $stack->getErrors();
            if (is_array($errors)) {
                $y = 0;
                while (list($id, $error) = each($errors)) {
                    $y += $this->_displayError(0, $y, $error);
                }
            }
        }
    }

    /**
     * Display an error from the error stack
     * @param int $x The horizontal position of the error box
     * @param int $y The vertical position of the error box
     * @param array $error The error context
     */
    function _displayError($x, $y, $error) {        
        $canvas =& $error['params']['canvas'];
        if (!is_resource($canvas)) {
            $canvas =& $this->_canvas();
        }
        
        $FH = ImageFontHeight(1);
        $FW = ImageFontWidth(1);

        $MAX_CHARS = floor(ImageSX($canvas)/$FW) - 20;
        $error['message'] = trim($error['message']);
        while ((strlen($error['message']) > $MAX_CHARS) and (strpos($error['message'], ' '))) {
            $string = substr($error['message'], 0, $MAX_CHARS);
            $pos = strrpos($string, ' ');
            if (($nl = strpos($string, "\n")) and ($nl<$pos)) {
                $pos = $nl;
            }
            $lines[] = substr($error['message'], 0, $pos);;
            $error['message'] = substr($error['message'], $pos+1);
        }
        if ($error['message']) {
            $strings = explode("\n", $error['message']);
            if ((isset($lines)) and (is_array($lines))) {
                $lines = array_merge($lines, $strings);
            } else {
                $lines = $strings;
            }
        }

        if ((isset($lines)) and (is_array($lines))) {
            $title = ucfirst($error['level']) . ' ' . $error['code'] . ': ' . $error['package'] . ' (' . $error['context']['class'] . ')';

            reset($lines);

            $max = strlen($title)*$FW;
            while (list($id, $text) = each($lines)) {
                $max = max($max, strlen($text)*$FW);
            }
            $height = 25+(count($lines)-1)*($FH+2)+15;
            $width = $max+10;

            $left = max(0, min($x, ImageSX($canvas)-$width));
            $top = max(0, min($y, ImageSY($canvas)-$height));

            ImageFilledRectangle($canvas, $left+5, $top+5, $left+$width+5, $top+$height+5, $this->_color('lightgray@0.5'));
            ImageFilledRectangle($canvas, $left, $top, $left+$width, $top+$height, $this->_color('white'));
            ImageRectangle($canvas, $left, $top, $left+$width, $top+$height, $this->_color('black'));
            ImageFilledRectangle($canvas, $left, $top, $left+$width, $top+20, $this->_color('blue'));
            ImageRectangle($canvas, $left, $top, $left+$width, $top+20, $this->_color('black'));

            ImageString($canvas, 1, $left+($width-$FW*strlen($title))/2, $top+(20-$FH)/2, $title, $this->_color('white'));

            reset($lines);
            $y = $top+25;
            while (list($id, $text) = each($lines)) {
                ImageString($canvas, 1, $left+5, $y, $text, $this->_color('black'));
                $y += $FH+2;
            }
            return $top+$height+10;
        }
        return 0;               
    }
    
    /**
     * Enable caching of the output. 
     * Note! Any change at all to any part of the graph makes it output again. Do *NOT*
     * use caching with plots using Image_Graph_Dataset_Random. The specified cache directory
     * must exist prior to caching.
     * Requires PEAR::Cache.
     * @param string $cacheDir The directory where cached files are put. If false caching is disabled.    
     */
    function cache($cacheDir = 'cache/') {
        $this->_cache = $cacheDir;
    }
        
    
    /**
     * Output to the canvas
     * @param int $type The type of image to output, i.e. IMG_PNG (default) and IMG_JPEG
     * @access private
     */
    function _done($type = IMG_PNG)
    {
        $useCached = false;
        $timeStart = $this->_getMicroTime();
        
        if ($this->_cache !== false) {        
            include_once 'Cache/Graphics.php';
            $cache =& new Cache_Graphics();
            $cache->setCacheDir($this->_cache);
            $cacheID = $cache->generateID(serialize($this));
            
            if ($output =& $cache->getImage($cacheID)) {                
                $this->_canvas =& ImageCreateFromString($output);
                $useCached = true;
            }
        }
        
        if ($useCached === false) {       
            if ($this->_shadow) {
                $this->setPadding(20);
                $this->_setCoords($this->_left, $this->_top, $this->_right -10, $this->_bottom-10);
            }
    
            $this->_updateCoords();
            
    
            if ($this->_background) {
                ImageFilledRectangle($this->_canvas(), $this->_left, $this->_top, $this->_right, $this->_bottom, $this->_getBackground());
            }
    
            if (!file_exists(dirname(__FILE__).'/Graph/Images/logo.png')) {
                $this->_error('Could not find Logo your installation may be incomplete');
            } else {
                parent::_done();
            }
            
            if (isset($this->_borderStyle)) {
                ImageRectangle($this->_canvas(), $this->_left, $this->_top, $this->_right, $this->_bottom, $this->_getBorderStyle());
            }
    
            if ($this->_displayErrors) {
                $this->_displayErrors();
            }
    
            if ($this->_rotation) {
                $this->_canvas = ImageRotate($this->_canvas(), $this->_rotation, $this->_getFillStyle());
            }
    
            $timeEnd = $this->_getMicroTime();
    
            if ($this->_showTime) {
                ImageString($this->_canvas(), FONT, $this->_left + $this->width() * 0.15, $this->_bottom - $this->_height * 0.1 - ImageFontHeight(IMAGE_GRAPH_FONT), 'Generated in '.sprintf('%0.3f', $timeEnd - $timeStart).' sec', $this->_color('red'));
            }
    
            if (!$this->_hideLogo) {
                include_once 'Image/Graph/Logo.php';
                $logo = Image_Graph::factory('Image_Graph_Logo', 
                    array(
                        dirname(__FILE__).'/Graph/Images/logo.png', 
                        IMAGE_GRAPH_ALIGN_TOP_RIGHT
                    )
                );
                $logo->_setParent($this);
                $logo->_done();
            }
            
            if ($this->_antialias) {
                $this->_performAntialias();
            }
        }

        if (($this->_outputImage) and (!IMAGE_GRAPH_DEBUG)) {
            header('Expires: Tue, 2 Jul 1974 17:41:00 GMT'); // Date in the past
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
            header('Pragma: no-cache');
            header('Content-type: image/'. ($type == IMG_JPG ? 'jpeg' : 'png'));            
            header('Content-Disposition: attachment; filename = \"'. (isset($_GET['thumb']) ? $_GET['thumb'] : (isset($_GET['image']) ? $_GET['image'] : '')).'\"');
        }
        
        if ($this->_fileName) {
            if (strtolower(substr($this->_fileName, -4)) == '.png') {
                ImagePNG($this->_canvas(), $this->_fileName);
            } else {
                ImageJPEG($this->_canvas(), $this->_fileName);
            }
        }
        
        if (($this->_thumbWidth) and ($this->_thumbHeight)) {
            if (isset($GLOBALS['_Image_Graph_gd2'])) {
                $thumbnail = ImageCreateTrueColor($this->_thumbWidth, $this->_thumbHeight);
                ImageCopyResampled($thumbnail, $this->_canvas(), 0, 0, 0, 0, $this->_thumbWidth, $this->_thumbHeight, $this->width(), $this->height());
            } else {
                $thumbnail = ImageCreate($this->_thumbWidth, $this->_thumbHeight);
                ImageCopyResized($thumbnail, $this->_canvas(), 0, 0, 0, 0, $this->_thumbWidth, $this->_thumbHeight, $this->width(), $this->height());
            }

            if ($this->_thumbFileName) {
                if (strtolower(substr($this->_thumbFileName, -4)) == '.png') {
                    ImagePNG($thumbnail, $this->_thumbFileName);
                } else {
                    ImageJPEG($thumbnail, $this->_thumbFileName);
                }
                ImageDestroy($thumbnail);
            } else {
                ImageDestroy($this->_canvas());
                $this->_canvas = $thumbnail;
            }
        }

        if (($this->_outputImage) and (!IMAGE_GRAPH_DEBUG)) {
            if ($type == IMG_JPG) {
                ImageJPEG($this->_canvas());
            } else {
                ImagePNG($this->_canvas());
            }
        }

        if (($this->_cache !== false) and (is_object($cache)) and ($useCached === false)) {
            $cache->cacheImage($cacheID, $this->_canvas(), ($type == IMG_JPG ? 'jpg' : 'png'));
        }

        if ($this->_outputImage) {
            ImageDestroy($this->_canvas());
            return true;
        } else {
            return $this->_canvas();
        }                
    }
}

/**
 * Default font variable
 * @global Image_Graph_Font $_Image_Graph_font
 */
$GLOBALS['_Image_Graph_font'] = & Image_Graph::factory('Image_Graph_Font');

/**
 * Default vertical font variable
 * @global Image_Graph_Font_Vertical $_Image_Graph_verticalFont
 */
$GLOBALS['_Image_Graph_verticalFont'] = & Image_Graph::factory('Image_Graph_Font_Vertical');

?>