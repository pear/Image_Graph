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
 * @subpackage Plotarea     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Layout.php
 */
require_once 'Image/Graph/Layout.php';

/**
 * Plot area used for drawing plots.
 * The plotarea consists of an x-axis and an y-axis, the plotarea can plot multiple
 * charts within one plotares, by simply adding them (the axis' will scale to the
 * plots automatically). A graph can consist of more plotareas
 */
class Image_Graph_Plotarea extends Image_Graph_Layout 
{

    /**
     * The left most pixel of the 'real' plot area on the canvas
     * @var int
     * @access private
     */
    var $_plotLeft = 0;

    /**
     * The top most pixel of the 'real' plot area on the canvas
     * @var int
     * @access private
     */
    var $_plotTop = 0;

    /**
     * The right most pixel of the 'real' plot area on the canvas
     * @var int
     * @access private
     */
    var $_plotRight = 0;

    /**
     * The bottom most pixel of the 'real' plot area on the canvas
     * @var int
     * @access private
     */
    var $_plotBottom = 0;

    /**
     * The X axis
     * @var Axis
     * @access private
     */
    var $_axisX = null;

    /**
     * The Y axis
     * @var Axis
     * @access private
     */
    var $_axisY = null;

    /**
     * The secondary Y axis
     * @var Axis
     * @access private
     */
    var $_axisYSecondary = null;

    /**
     * The border style of the 'real' plot area
     * @var LineStyle
     * @access private
     */
    var $_plotBorderStyle = null;

    /**
     * Image_Graph_Plotarea [Constructor]
     * @param string $axisX The class of the X axis (if omitted a std. axis is created)
     * @param string $axisY The class of the Y axis (if omitted a std. axis is created)
     */
    function &Image_Graph_Plotarea($axisX = 'Image_Graph_Axis_Category', $axisY = 'Image_Graph_Axis')
    {
        parent::Image_Graph_Layout();       
        
        $this->_padding = 10;
    
        include_once 'Image/Graph.php';
                        
        $this->_axisX = & Image_Graph::factory($axisX, IMAGE_GRAPH_AXIS_X);
        if (is_object($this->_axisX)) {
            $this->_axisX->_setParent($this);
        }

        $this->_axisY = & Image_Graph::factory($axisY, IMAGE_GRAPH_AXIS_Y);

        if (is_object($this->_axisY)) {
            $this->_axisY->_setParent($this);
            $this->_axisY->_setMinimum(0);
        }
    }

    /**
     * Sets the plot border line style of the element	 
     * @param Image_Graph_Line $lineStyle The line style of the border 
     */
    function setPlotBorderStyle(& $plotBorderStyle)
    {        
        $this->_plotBorderStyle = & $plotBorderStyle;
        $this->add($plotBorderStyle);
    }

    /**
     * Adds an element to the plotarea    
     * @param Image_Graph_Element $element The element to add
     * @param int $axis The axis to associate the element with, either IMAGE_GRAPH_AXIS_X, IMAGE_GRAPH_AXIS_Y or IMAGE_GRAPH_AXIS_Y_SECONDARY (defaults to IMAGE_GRAPH_AXIS_Y)
     * @return Image_Graph_Element The added element
     * @see Image_Graph_Common::add() 
     */
    function &add(& $element, $axis = IMAGE_GRAPH_AXIS_Y)
    {
        if (($axis == IMAGE_GRAPH_AXIS_Y_SECONDARY) and ($this->_axisYSecondary == null)) {
            $this->add(Image_Graph::factory('Image_Graph_Axis', IMAGE_GRAPH_AXIS_Y_SECONDARY));
        }   

        parent::add($element);

        if (is_a($element, 'Image_Graph_Plot')) {            
            $element->_setAxisY($axis);                          
            // postpone extrema calculation until we calculate coordinates
            //$this->_setExtrema($element);            
        } elseif (is_a($element, 'Image_Graph_Grid')) {
            switch ($axis) {
            case IMAGE_GRAPH_AXIS_X: 
                if ($this->_axisX != null) {
                    $element->_setPrimaryAxis($this->_axisX);
                    if ($this->_axisY != null) { 
                        $element->_setSecondaryAxis($this->_axisY);
                    }
                }
                break;
            case IMAGE_GRAPH_AXIS_Y: 
                if ($this->_axisY != null) {
                    $element->_setPrimaryAxis($this->_axisY);
                    if ($this->_axisX != null) { 
                        $element->_setSecondaryAxis($this->_axisX);
                    }
                }
                break;
            case IMAGE_GRAPH_AXIS_Y_SECONDARY: 
                if ($this->_axisYSecondary != null) {
                    $element->_setPrimaryAxis($this->_axisYSecondary);
                    if ($this->_axisX != null) { 
                        $element->_setSecondaryAxis($this->_axisX);
                    }
                }
                break;
            }                
        } elseif (is_a($element, 'Image_Graph_Axis')) {
            switch ($element->_type) {
            case IMAGE_GRAPH_AXIS_X: $this->_axisX =& $element; break;
            case IMAGE_GRAPH_AXIS_Y: $this->_axisY =& $element; break;
            case IMAGE_GRAPH_AXIS_Y_SECONDARY: $this->_axisYSecondary =& $element; break;
            }
            $element->_setMinimum(0);
            $element->_setMaximum(1);
        }
        return $element;
    }

    /**
     * Get the width of the 'real' plotarea	 
     * @return int The width of the 'real' plotarea, ie not including space occupied by padding and axis 
     * @access private
     */
    function _plotWidth()
    {
        return abs($this->_plotRight - $this->_plotLeft);
    }

    /**
     * Get the height of the 'real' plotarea	 
     * @return int The height of the 'real' plotarea, ie not including space occupied by padding and axis 
     * @access private
     */
    function _plotHeight()
    {
        return abs($this->_plotBottom - $this->_plotTop);
    }

    /**
     * Set the extrema of the axis	 
     * @param Image_Graph_Plot $plot The plot that 'hold' the values 
     * @access private
     */
    function _setExtrema(& $plot)
    {               
        if (($this->_axisX != null) and ($this->_axisX->_isNumeric())) {
            $this->_axisX->_setMinimum($plot->_minimumX());            
            $this->_axisX->_setMaximum($plot->_maximumX());            
        }
    
        if (($plot->_axisY == IMAGE_GRAPH_AXIS_Y_SECONDARY) and ($this->_axisYSecondary !== null) and ($this->_axisYSecondary->_isNumeric())) {
            $this->_axisYSecondary->_setMinimum($plot->_minimumY());            
            $this->_axisYSecondary->_setMaximum($plot->_maximumY());            
        } elseif (($this->_axisY != null) and ($this->_axisY->_isNumeric())) {
            $this->_axisY->_setMinimum($plot->_minimumY());            
            $this->_axisY->_setMaximum($plot->_maximumY());            
        }

        $datasets =& $plot->dataset();
        if (!is_array($datasets)) {
            $keys = array(false);
        } else {        
            $keys = array_keys($datasets);
        }
        while (list($id, $key) = each($keys)) {
            if ($key === false) {
                $dataset =& $datasets;
            } else {
                $dataset =& $datasets[$key];
            }
            if (is_a($dataset, 'Image_Graph_Dataset')) {            
                if (($this->_axisX != null) and (!$this->_axisX->_isNumeric())) {
                    $this->_axisX->_applyDataset($dataset);            
                }
            
                if (($plot->_axisY == IMAGE_GRAPH_AXIS_Y_SECONDARY) and ($this->_axisYSecondary !== null) and (!$this->_axisYSecondary->_isNumeric())) {
                    $this->_axisYSecondary->_applyDataset($dataset);            
                } elseif (($this->_axisY != null) and (!$this->_axisY->_isNumeric())) {
                    $this->_axisY->_applyDataset($dataset);            
                }
            }
        }
    }

    /**
     * Left boundary of the background fill area 
     * @return int Leftmost position on the canvas
     * @access private
     */
    function _fillLeft()
    {
        return $this->_plotLeft;
    }

    /**
     * Top boundary of the background fill area 
     * @return int Topmost position on the canvas
     * @access private
     */
    function _fillTop()
    {
        return $this->_plotTop;
    }

    /**
     * Right boundary of the background fill area 
     * @return int Rightmost position on the canvas
     * @access private
     */
    function _fillRight()
    {
        return $this->_plotRight;
    }

    /**
     * Bottom boundary of the background fill area 
     * @return int Bottommost position on the canvas
     * @access private
     */
    function _fillBottom()
    {
        return $this->_plotBottom;
    }

    /**
     * Get the X pixel position represented by a value
     * @param double Value the value to get the pixel-point for	 
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointX($value)
    {
        if (($this->_axisX == null) or (!isset($value['X']))) {
            return false;
        }
//        return max($this->_plotLeft, min($this->_plotRight, $this->_axisX->_point($value['X'])));
        return $this->_axisX->_point($value['X']);
    }

    /**
     * Get the Y pixel position represented by a value
     * @param double Value the value to get the pixel-point for	 
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointY($value)
    {
        if ((isset($value['AXIS_Y'])) and ($value['AXIS_Y'] == IMAGE_GRAPH_AXIS_Y_SECONDARY)) {
            if (($this->_axisYSecondary == null) or (!isset($value['Y']))) {
                return false;
            }
    //        return max($this->_plotTop, min($this->_plotBottom, $this->_axisY->_point($value['Y'])));
            return $this->_axisYSecondary->_point($value['Y']);
        } else {
            if (($this->_axisY == null) or (!isset($value['Y']))) {
                return false;
            }
    //        return max($this->_plotTop, min($this->_plotBottom, $this->_axisY->_point($value['Y'])));
            return $this->_axisY->_point($value['Y']);
        }
    }

    /** 
     * Hides the axis
     */
    function hideAxis()
    {
        $this->_axisX = $this->_axisY = $this->_axisYSecondary = null;
    }

    /** 
     * Get axis
     * @param int $Axis The axis to return
     * @return Image_Graph_Axis The axis
     */
    function &getAxis($Axis = IMAGE_GRAPH_AXIS_X)
    {
        switch ($Axis) {
            case IMAGE_GRAPH_AXIS_X: return $this->_axisX; break;
            case IMAGE_GRAPH_AXIS_Y: return $this->_axisY; break;
            case IMAGE_GRAPH_AXIS_Y_SECONDARY: return $this->_axisYSecondary; break;
        }
    }
    
    /**
     * Update coordinates
     * @access private
     */
    function _updateCoords()
    {
        if (is_array($this->_elements)) {
            reset($this->_elements);

            $keys = array_keys($this->_elements);
            while (list ($ID, $key) = each($keys)) {
                $element =& $this->_elements[$key];
                if (is_a($element, 'Image_Graph_Plot')) {
                    if ((
                        (is_a($element, 'Image_Graph_Plot_Bar')) or 
                        (is_a($element, 'Image_Graph_Plot_Step')) or 
                        (is_a($element, 'Image_Graph_Plot_Dot')) or 
                        (is_a($element, 'Image_Graph_Plot_Impulse'))
                    ) and ($this->_axisX != null)) {
                       $this->_axisX->_pushValues();
                    }  
                    $this->_setExtrema($element);
                }
            }
        }        

        $this->_calcEdges();

        $pctWidth = (int) ($this->width() * 0.05);
        $pctHeight = (int) ($this->height() * 0.05);
       
        if (($this->_axisX != null) and ($this->_axisY != null) and ($this->_axisYSecondary != null)) {
            $this->_axisX->_setCoords(
                $this->_left + $this->_axisY->_size() + $this->_padding, 
                $this->_bottom - $this->_axisX->_size() - $this->_padding, 
                $this->_right - $this->_axisYSecondary->_size() - $this->_padding, 
                $this->_bottom - $this->_padding
            );                   
            $this->_axisY->_setCoords(
                $this->_left + $this->_padding, 
                $this->_top + $this->_padding, 
                $this->_left + $this->_axisY->_size() + $this->_padding, 
                $this->_bottom - $this->_axisX->_size() - $this->_padding);
            $this->_axisYSecondary->_setCoords(
                $this->_right - $this->_axisYSecondary->_size() - $this->_padding + 1, 
                $this->_top + $this->_padding, 
                $this->_right - $this->_padding, 
                $this->_bottom - $this->_axisX->_size() - $this->_padding);

            $this->_plotLeft = $this->_axisX->_left;
            $this->_plotTop = $this->_axisY->_top;
            $this->_plotRight = $this->_axisX->_right;
            $this->_plotBottom = $this->_axisY->_bottom;
        } elseif (($this->_axisX != null) and ($this->_axisY != null)) {
            if (($this->_axisX->_minimum >= 0) and ($this->_axisY->_minimum >= 0)) {
                $this->_axisX->_setCoords(
                    $this->_left + $this->_axisY->_size() + $this->_padding, 
                    $this->_bottom - $this->_axisX->_size() - $this->_padding, 
                    $this->_right - $this->_padding, 
                    $this->_bottom - $this->_padding
                );                   
                $this->_axisY->_setCoords(
                    $this->_left + $this->_padding, 
                    $this->_top + $this->_padding, 
                    $this->_left + $this->_axisY->_size() + $this->_padding, 
                    $this->_bottom - $this->_axisX->_size() - $this->_padding);
            }
            elseif ($this->_axisX->_minimum >= 0) {
                $this->_axisY->_setCoords(
                    $this->_left, 
                    $this->_top, 
                    $this->_left + $this->_axisY->_size(), 
                    $this->_bottom
                );
                $this->_axisX->_setCoords(
                    $this->_axisY->_right, 
                    $this->_axisY->_point(0), 
                    $this->_right, 
                    $this->_axisY->_point(0) + $this->_axisX->_size()
                );
            }
            elseif ($this->_axisY->_minimum >= 0) {
                $this->_axisX->_setCoords(
                    $this->_left, 
                    $this->_bottom - $this->_axisX->_size(), 
                    $this->_right, 
                    $this->_bottom
                );
                $this->_axisY->_setCoords(
                    $this->_axisX->_point(0) - $this->_axisY->_size(), 
                    $this->_top, 
                    $this->_axisX->_point(0), 
                    $this->_axisX->_top
                );
            } else {
                $this->_axisY->_setCoords(
                    $this->_left + $this->_padding, 
                    $this->_top + $this->_padding, 
                    $this->_right - $this->_padding, 
                    $this->_bottom - $this->_padding
                );
                $this->_axisX->_setCoords(
                    $this->_left + $this->_padding, 
                    $this->_axisY->_point(0), 
                    $this->_right - $this->_padding, 
                    $this->_axisY->_point(0) + $this->_axisX->_size()
                );
                $this->_axisY->_setCoords(
                    $this->_axisX->_point(0) - $this->_axisY->_size(), 
                    $this->_top + $this->_padding, 
                    $this->_axisX->_point(0), 
                    $this->_bottom - $this->_padding);
            }

            //$this->_axisX->shrink($indent, $indent, $indent, $indent);
            //$this->_axisY->shrink($indent, $indent, $indent, $indent);

            $this->_plotLeft = $this->_axisX->_left;
            $this->_plotTop = $this->_axisY->_top;
            $this->_plotRight = $this->_axisX->_right;
            $this->_plotBottom = $this->_axisY->_bottom;
        } else {
            $this->_plotLeft = $this->_left;
            $this->_plotTop = $this->_top;
            $this->_plotRight = $this->_right;
            $this->_plotBottom = $this->_bottom;
        }

        Image_Graph_Element::_updateCoords();
    }

    /**
     * Output the plotarea to the canvas
     * @access private
     */
    function _done()
    {
        if ($this->_axisX != null) {
            $this->add($this->_axisX);
        }
        if ($this->_axisY != null) {
            $this->add($this->_axisY);
        }
        if ($this->_axisYSecondary != null) {
            $this->add($this->_axisYSecondary);
        }

        if ($this->_background) {
            ImageFilledRectangle($this->_canvas(), $this->_plotLeft, $this->_plotTop, $this->_plotRight, $this->_plotBottom, $this->_getBackground());
        }

        parent::_done();

        if ($this->_plotBorderStyle) {
            ImageRectangle($this->_canvas(), $this->_plotLeft, $this->_plotTop, $this->_plotRight, $this->_plotBottom, $this->_plotBorderStyle->_getLineStyle());
        }
    }

}

?>