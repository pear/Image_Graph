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
 *
 * The plotarea consists of an x-axis and an y-axis, the plotarea can plot multiple
 * charts within one plotares, by simply adding them (the axis' will scale to the
 * plots automatically). A graph can consist of more plotareas
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plotarea
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
     *
     * @param string $axisX The class of the X axis (if omitted a std. axis is created)
     * @param string $axisY The class of the Y axis (if omitted a std. axis is created)
     */
    function &Image_Graph_Plotarea($axisX = 'Image_Graph_Axis_Category', $axisY = 'Image_Graph_Axis')
    {
        parent::Image_Graph_Layout();

        $this->_padding = 5;

        include_once 'Image/Graph.php';

        $this->_axisX =& Image_Graph::factory($axisX, IMAGE_GRAPH_AXIS_X);
        $this->_axisX->_setParent($this);

        $this->_axisY =& Image_Graph::factory($axisY, IMAGE_GRAPH_AXIS_Y);
        $this->_axisY->_setParent($this);
        $this->_axisY->_setMinimum(0);

        $this->_fillStyle = false;
    }

    /**
     * Sets the parent. The parent chain should ultimately be a GraPHP object
     *
     * @see Image_Graph_Common
     * @param Image_Graph_Common $parent The parent
     * @access private
     */
    function _setParent(& $parent)
    {
        parent::_setParent($parent);
        if ($this->_axisX !== null) {
            $this->_axisX->_setParent($this);
        }
        if ($this->_axisY !== null) {
            $this->_axisY->_setParent($this);
        }
        if ($this->_axisYSecondary !== null) {
            $this->_axisYSecondary->_setParent($this);
        }
    }

    /**
     * Sets the plot border line style of the element.
     *
     * @param Image_Graph_Line $lineStyle The line style of the border
     * @deprecated 0.3.0dev2 - 2004-12-16
     */
    function setPlotBorderStyle(& $plotBorderStyle)
    {        
    }

    /**
     * Adds an element to the plotarea
     *
     * @param Image_Graph_Element $element The element to add
     * @param int $axis The axis to associate the element with, either
     * IMAGE_GRAPH_AXIS_X, IMAGE_GRAPH_AXIS_Y or IMAGE_GRAPH_AXIS_Y_SECONDARY
     * (defaults to IMAGE_GRAPH_AXIS_Y)
     * @return Image_Graph_Element The added element
     * @see Image_Graph_Common::add()
     */
    function &add(& $element, $axis = IMAGE_GRAPH_AXIS_Y)
    {
        if (($axis == IMAGE_GRAPH_AXIS_Y_SECONDARY) &&
            ($this->_axisYSecondary == null))
        {
            $this->_axisYSecondary =& Image_Graph::factory('axis', IMAGE_GRAPH_AXIS_Y_SECONDARY);
            $this->_axisYSecondary->_setMinimum(0);
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
            case IMAGE_GRAPH_AXIS_X:
                $this->_axisX =& $element;
                break;

            case IMAGE_GRAPH_AXIS_Y:
                $this->_axisY =& $element;
                break;

            case IMAGE_GRAPH_AXIS_Y_SECONDARY:
                $this->_axisYSecondary =& $element;
                break;

            }
            $element->_setMinimum(0);
            $element->_setMaximum(1);
        }
        return $element;
    }

    /**
     * Get the width of the 'real' plotarea
     *
     * @return int The width of the 'real' plotarea, ie not including space occupied by padding and axis
     * @access private
     */
    function _plotWidth()
    {
        return abs($this->_plotRight - $this->_plotLeft);
    }

    /**
     * Get the height of the 'real' plotarea
     *
     * @return int The height of the 'real' plotarea, ie not including space
     *   occupied by padding and axis
     * @access private
     */
    function _plotHeight()
    {
        return abs($this->_plotBottom - $this->_plotTop);
    }

    /**
     * Set the extrema of the axis
     *
     * @param Image_Graph_Plot $plot The plot that 'hold' the values
     * @access private
     */
    function _setExtrema(& $plot)
    {
        if (($this->_axisX != null) && ($this->_axisX->_isNumeric())) {
            $this->_axisX->_setMinimum($plot->_minimumX());
            $this->_axisX->_setMaximum($plot->_maximumX());
        }

        if (($plot->_axisY == IMAGE_GRAPH_AXIS_Y_SECONDARY) &&
            ($this->_axisYSecondary !== null) &&
            ($this->_axisYSecondary->_isNumeric()))
        {
            $this->_axisYSecondary->_setMinimum($plot->_minimumY());
            $this->_axisYSecondary->_setMaximum($plot->_maximumY());
        } elseif (($this->_axisY != null) && ($this->_axisY->_isNumeric())) {
            $this->_axisY->_setMinimum($plot->_minimumY());
            $this->_axisY->_setMaximum($plot->_maximumY());
        }

        $datasets =& $plot->dataset();
        if (!is_array($datasets)) {
            $datasets = array($datasets);
        }
        $keys = array_keys($datasets);
        foreach ($keys as $key) {
            $dataset =& $datasets[$key];
            if (is_a($dataset, 'Image_Graph_Dataset')) {
                if (($this->_axisX != null) && (!$this->_axisX->_isNumeric())) {
                    $this->_axisX->_applyDataset($dataset);
                }

                if (($plot->_axisY == IMAGE_GRAPH_AXIS_Y_SECONDARY) &&
                    ($this->_axisYSecondary !== null) &&
                    (!$this->_axisYSecondary->_isNumeric()))
                {
                    $this->_axisYSecondary->_applyDataset($dataset);
                } elseif (($this->_axisY != null) && (!$this->_axisY->_isNumeric())) {
                    $this->_axisY->_applyDataset($dataset);
                }
            }
        }
        unset($keys);
    }

    /**
     * Left boundary of the background fill area
     *
     * @return int Leftmost position on the canvas
     * @access private
     */
    function _fillLeft()
    {
        return $this->_plotLeft;
    }

    /**
     * Top boundary of the background fill area
     *
     * @return int Topmost position on the canvas
     * @access private
     */
    function _fillTop()
    {
        return $this->_plotTop;
    }

    /**
     * Right boundary of the background fill area
     *
     * @return int Rightmost position on the canvas
     * @access private
     */
    function _fillRight()
    {
        return $this->_plotRight;
    }

    /**
     * Bottom boundary of the background fill area
     *
     * @return int Bottommost position on the canvas
     * @access private
     */
    function _fillBottom()
    {
        return $this->_plotBottom;
    }

    /**
     * Get the X pixel position represented by a value
     *
     * @param double Value the value to get the pixel-point for
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointX($value)
    {
        if (($this->_axisX == null) || (!isset($value['X']))) {
            return false;
        }

        if ($value['X'] === '#min#') {
            return $this->_plotLeft;
        }
        if ($value['X'] === '#max#') {
            return $this->_plotRight;
        }

        return $this->_axisX->_point($value['X']);
    }

    /**
     * Get the Y pixel position represented by a value
     *
     * @param double Value the value to get the pixel-point for
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointY($value)
    {
        if (!isset($value['Y'])) {
            return false;
        }

        if (($value['Y'] === '#min_pos#') || ($value['Y'] === '#max_nex#')) {
            // return the minimum (bottom) position or if negative then zero
            // or the maxmum (top) position or if positive then zero
            if ((isset($value['AXIS_Y'])) &&
                ($value['AXIS_Y'] == IMAGE_GRAPH_AXIS_Y_SECONDARY) &&
                ($this->_axisYSecondary !== null)
            ) {
                $axisY =& $this->_axisYSecondary;
            } else {
                $axisY =& $this->_axisY;
            }
            if ($value['Y'] === '#min_pos#') {
                return $axisY->_point(max(0, $axisY->_getMinimum()));
            } else {
                return $axisY->_point(min(0, $axisY->_getMaximum()));
            }
        }

        if ($value['Y'] === '#min#') {
            return $this->_plotBottom;
        }
        if ($value['Y'] === '#max#') {
            return $this->_plotTop;
        }

        if ((isset($value['AXIS_Y'])) &&
            ($value['AXIS_Y'] == IMAGE_GRAPH_AXIS_Y_SECONDARY)
        ) {
            if ($this->_axisYSecondary !== null) {
                return $this->_axisYSecondary->_point($value['Y']);
            }
        } else {
            if ($this->_axisY !== null) {
                return $this->_axisY->_point($value['Y']);
            }
        }
        return false;
    }

    /**
     * Return the minimum value of the specified axis
     *
     * @param int $axis The axis to return the minimum value of
     * @return double The minimum value of the axis
     * @access private
     */
    function _getMinimum($axis = IMAGE_GRAPH_AXIS_Y)
    {
        $axis =& $this->getAxis($axis);
        if ($axis !== null) {
            return $axis->_getMinimum();
        } else {
            return 0;
        }
    }

    /**
     * Return the maximum value of the specified axis
     *
     * @param int $axis The axis to return the maximum value of
     * @return double The maximum value of the axis
     * @access private
     */
    function _getMaximum($axis = IMAGE_GRAPH_AXIS_Y)
    {
        $axis =& $this->getAxis($axis);
        if ($axis !== null) {
            return $axis->_getMaximum();
        } else {
            return 0;
        }
    }

    /**
     * Return the label distance for the specified axis.
     *
     * @param int $axis The axis to return the label distance for
     * @return int The distance between 2 adjacent labels
     * @access private
     */
    function _labelDistance($axis)
    {
        if (($axis == IMAGE_GRAPH_AXIS_Y_SECONDARY) &&
            ($this->_axisYSecondary != null))
        {
            return $this->_axisYSecondary->_labelDistance();
        } elseif (($axis == IMAGE_GRAPH_AXIS_Y) && ($this->_axisY != null)) {
            return $this->_axisY->_labelDistance();
        } elseif (($axis == IMAGE_GRAPH_AXIS_X) && ($this->_axisX != null)) {
            return $this->_axisX->_labelDistance();
        }
        return false;
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
     *
     * @param int $Axis The axis to return
     * @return Image_Graph_Axis The axis
     */
    function &getAxis($Axis = IMAGE_GRAPH_AXIS_X)
    {
        switch ($Axis) {
        case IMAGE_GRAPH_AXIS_X:
            return $this->_axisX;
            break;

        case IMAGE_GRAPH_AXIS_Y:
            return $this->_axisY;
            break;

        case IMAGE_GRAPH_AXIS_Y_SECONDARY:
            return $this->_axisYSecondary;
            break;

        }
    }

    /**
     * Update coordinates
     *
     * @access private
     */
    function _updateCoords()
    {
        
        // TODO There is an issue when the axis partly exceeds the boundaries of the plot
        // TODO There is an issue when secondary axis minimum value is greater that zero
        
        if (is_array($this->_elements)) {
            $keys = array_keys($this->_elements);
            foreach ($keys as $key) {
                $element =& $this->_elements[$key];
                if (is_a($element, 'Image_Graph_Plot')) {
                    if (((is_a($element, 'Image_Graph_Plot_Bar')) ||
                        (is_a($element, 'Image_Graph_Plot_Step')) ||
                        (is_a($element, 'Image_Graph_Plot_Dot')) ||
                        (is_a($element, 'Image_Graph_Plot_CandleStick')) ||
                        (is_a($element, 'Image_Graph_Plot_BoxWhisker')) ||
                        (is_a($element, 'Image_Graph_Plot_Impulse'))) &&
                        ($this->_axisX != null))
                    {
                       $this->_axisX->_pushValues();
                    }
                    $this->_setExtrema($element);
                }
            }
            unset($keys);
        }

        $this->_calcEdges();

        $pctWidth = (int) ($this->width() * 0.05);
        $pctHeight = (int) ($this->height() * 0.05);

        $left = $this->_left + $this->_padding;
        $top = $this->_top + $this->_padding;
        $right = $this->_right - $this->_padding;
        $bottom = $this->_bottom - $this->_padding;
        
        // temporary place holder for axis point calculations
        $axisPoints['x'] = array($left, $top, $right, $bottom);
        $axisPoints['y'] = $axisPoints['x'];
        $axisPoints['y2'] = $axisPoints['x'];
                
        if ($this->_axisX !== null) {
            $intersectX = $this->_axisX->_getAxisIntersection();
            $sizeX = $this->_axisX->_size();
            $this->_axisX->_setCoords($left, $top, $right, $bottom);
        }
        
        if ($this->_axisY !== null) {
            $intersectY = $this->_axisY->_getAxisIntersection();
            $sizeY = $this->_axisY->_size();
            $this->_axisY->_setCoords($left, $top, $right, $bottom);
        }

        if ($this->_axisYSecondary !== null) {
            $intersectYsec = $this->_axisYSecondary->_getAxisIntersection();
            $sizeYsec = $this->_axisYSecondary->_size();
            $this->_axisYSecondary->_setCoords($left, $top, $right, $bottom);
        }   
        
        $axisCoordAdd = array('left' => 0, 'right' => 0, 'top' => 0, 'bottom' => 0);
             
        
        if ($this->_axisY != null) {
            if ($this->_axisX != null) {                
                $pos = $this->_axisX->_intersectPoint($intersectY['value']);                
            } else {
                $pos = $left;
            }

            if (($pos - $sizeY) < $left) {
                $axisCoordAdd['left'] = $left - ($pos - $sizeY);
                // the y-axis position needs to be recalculated!                
            } else {            
                // top & bottom may need to be adjusted when the x-axis has been
                // calculated!
                $this->_axisY->_setCoords(
                    $pos - $sizeY,
                    $top,
                    $pos,
                    $bottom
                );
            }      
        }      

        if ($this->_axisYSecondary != null) {
            // TODO There seems to be some kind of problem here - see tests/plotarea_secondary_axis_intersect.php            
            if ($this->_axisX != null) {
                $pos = $this->_axisX->_intersectPoint($intersectYsec['value']);
            } else {
                $pos = $right;
            }
            if (($pos + $sizeYsec) > $right) {
                $axisCoordAdd['right'] = ($pos + $sizeYsec) - $right;
                // the secondary y-axis position need to be recalculated
            } else {
                // top & bottom may need to be adjusted when the x-axis has been
                // calculated!
                $this->_axisYSecondary->_setCoords(
                    $pos,
                    $top,
                    $pos + $sizeY,
                    $bottom
                );
            }      
        }      
        
        if ($this->_axisX != null) {
            if (($intersectX['axis'] == IMAGE_GRAPH_AXIS_Y_SECONDARY) &&
                ($this->_axisYSecondary !== null)
            ) {
                $axis =& $this->_axisYSecondary;
            } elseif ($this->_axisY !== null) {
                $axis =& $this->_axisY;
            } else {
                $axis = false;
            }
            
            if ($axis !== false) {
                $pos = $axis->_intersectPoint($intersectX['value']);
            } else {
                $pos = $bottom;
            }
             
            if (($pos + $sizeX) > $bottom) {
                $axisCoordAdd['bottom'] = ($pos + $sizeX) - $bottom;
                $pos = $bottom - $sizeX;
            }
                      
            $this->_axisX->_setCoords(
                $left + $axisCoordAdd['left'],
                $pos,
                $right - $axisCoordAdd['right'],
                $pos + $sizeX
            );
        }
        
        if (($this->_axisX !== null) && 
            (($axisCoordAdd['left'] != 0) ||
            ($axisCoordAdd['right'] != 0))
        ) {
            // readjust y-axis for better estimate of position
            if ($this->_axisY !== null) {
                $pos = $this->_axisX->_intersectPoint($intersectY['value']);
                $this->_axisY->_setCoords(
                    $pos - $sizeY,
                    false,
                    $pos,
                    false
                );
            }

            if ($this->_axisYSecondary !== null) {
                $pos = $this->_axisX->_intersectPoint($intersectYsec['value']);
                $this->_axisYSecondary->_setCoords(
                    $pos,
                    false,
                    $pos + $sizeYsec,
                    false
                );
            }
        }        
        
        // adjust top and bottom of y-axis
        if ($this->_axisY !== null) {
            $this->_axisY->_setCoords(
                false, 
                $top + $axisCoordAdd['top'], 
                false, 
                $bottom - $axisCoordAdd['bottom']
            );
        } 

        // adjust top and bottom of y-axis
        if ($this->_axisYSecondary !== null) {
            $this->_axisYSecondary->_setCoords(
                false, 
                $top + $axisCoordAdd['top'], 
                false, 
                $bottom - $axisCoordAdd['bottom']
            );
        } 
    
        if ($this->_axisX !== null) {
            $this->_plotLeft = $this->_axisX->_left;
            $this->_plotRight = $this->_axisX->_right;
        } else {
            $this->_plotLeft = $left;
            $this->_plotRight = $right;
        }
        
        if ($this->_axisY !== null) {
            $this->_plotTop = $this->_axisY->_top;
            $this->_plotBottom = $this->_axisY->_bottom;
        } elseif ($this->_axisYSecondary !== null) {
            $this->_plotTop = $this->_axisYSecondary->_top;
            $this->_plotBottom = $this->_axisYSecondary->_bottom;
        } else {
            $this->_plotTop = $this->_top;
            $this->_plotBottom = $this->_bottom;
        }

        Image_Graph_Element::_updateCoords();
    }

    /**
     * Output the plotarea to the canvas
     *
     * @return bool Was the output 'good' (true) or 'bad' (false).
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

        $this->_getFillStyle();
        $this->_driver->rectangle(
            $this->_plotLeft,
            $this->_plotTop,
            $this->_plotRight,
            $this->_plotBottom
        );

        return parent::_done();
    }

}

?>