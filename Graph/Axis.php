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
 * @subpackage Axis     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Plotarea/Element.php
 */
require_once 'Image/Graph/Plotarea/Element.php';

/**
 * Diplays a normal linear axis (either X- or Y-axis). 
 */
// TODO Make a better method for axis titles that using layouts
// TODO Axis-tick customizability (major + minor) 
class Image_Graph_Axis extends Image_Graph_Plotarea_Element 
{

    /** 
     * The type of the axis, possible values are:
     * <ul><li>IMAGE_GRAPH_AXIS_X / IMAGE_GRAPH_AXIS_HORIZONTAL<li>IMAGE_GRAPH_AXIS_Y / IMAGE_GRAPH_AXIS_VERTICAL / IMAGE_GRAPH_AXIS_Y_SECONDARY</ul>
     * @var int
     * @access private
     */
    var $_type;

    /** 
     * The minimum value the axis displays
     * @var int
     * @access private
     */
    var $_minimum = false;

    /** 
     * The maximum value the axis displays
     * @var int
     * @access private
     */
    var $_maximum = false;

    /** 
     * Specify if the axis should label the minimum value
     * @var bool
     * @access private
     */
    var $_showLabelMinimum = true;

    /** 
     * Specify if the axis should label 0 (zero)
     * @var bool
     * @access private
     */
    var $_showLabelZero = false;

    /** 
     * Specify if the axis should label the maximum value
     * @var bool
     * @access private
     */
    var $_showLabelMaximum = true;

    /** 
     * Show arrow heads at the 'end' of the axis, default: false
     * @var bool
     * @access private
     */
    var $_showArrow = false;

    /** 
     * The interval at which labels are drawn at the axis, default: AXIS_INTERVAL_AUTO
     * @var double
     * @access private
     */
    var $_labelInterval = 'auto';

    /** 
     * Has the label interval been explicitly set?
     * @var bool
     * @access private
     */
    var $_labelIntervalSet = false;

    /** 
     * A data preprocessor for formatting labels, fx showing dates as a standard date instead of Unix time stamp
     * @var Image_Graph_DatePreProcessor
     * @access private
     * @see Image_Graph_DataPreProcessor
     */
    var $_dataPreProcessor = null;

    /** 
     * Point marked in the axis
     * @var array
     * @access private
     */
    var $_marks = array();

    /** 
     * Specifies whether the values should be 'pushed' by 0.5
     * @var bool
     * @access private
     */
    var $_pushValues = false;

    /**
     * Image_Graph_Axis [Constructor].
     * Normally a manual creation should not be necessary, axis are created automatically
     * by the {@see Image_Graph_Plotarea} constructor unless explicitly defined otherwise
     * @param int $type The type (direction) of the Axis, use IMAGE_GRAPH_AXIS_X for an X-axis (default, may be omitted) and IMAGE_GRAPH_AXIS_Y for Y-axis)
     */
    function &Image_Graph_Axis($type = IMAGE_GRAPH_AXIS_X)
    {
        parent::Image_Graph_Element();
        $this->_type = $type;        
    }

    /** 
     * Push the values by 0.5 (for bar and step chart)
     * @access private
     */
    function _pushValues() {
        $this->_pushValues = true;
    }

    /**
     * Shows a label for the the specified values.
     * Allowed values are  combinations of:
     * <ul>
     * <li>IMAGE_GRAPH_LABEL_MINIMUM
     * <li>IMAGE_GRAPH_LABEL_ZERO     
     * <li>IMAGE_GRAPH_LABEL_MAXIMUM
     * </ul>
     * By default none of these are shows on the axis
     * @param int $value The values to show labels for
     */
    function showLabel($value)
    {
        $this->_showLabelMinimum = ($value & IMAGE_GRAPH_LABEL_MINIMUM);
        $this->_showLabelZero = ($value & IMAGE_GRAPH_LABEL_ZERO);
        $this->_showLabelMaximum = ($value & IMAGE_GRAPH_LABEL_MAXIMUM);
    }

    /**
     * Sets a data preprocessor for formatting the axis labels
     * @param Image_Graph_DataPreprocessor $dataPreProcessor The data preprocessor
     * @see Image_Graph_DataPreprocessor
     */
    function setDataPreProcessor(& $dataPreProcessor)
    {
        $this->_dataPreProcessor = & $dataPreProcessor;
    }

    /**
     * Gets the minimum value the axis will show
     * @return double The minumum value
     * @access private
     */
    function _getMinimum()
    {
        if ($this->_labelInterval) {
            return $this->_minimum;
        }

        $labelInterval = $this->_labelInterval();
        if ($labelInterval != 0) {
            $result = (int) ($this->_minimum / $labelInterval) * $labelInterval;
            if ($result != $this->_minimum) {
                $result -= $labelInterval;
            }
            return $result;
        }
        return $this->_minimum;
    }

    /**
     * Gets the maximum value the axis will show
     * @return double The maximum value
     * @access private
     */
    function _getMaximum()
    {
        if ($this->_labelInterval) {
            return $this->_maximum;
        }

        $labelInterval = $this->_labelInterval();
        if ($labelInterval != 0) {
            $result = (int) ($this->_maximum / $labelInterval) * $labelInterval;
            if ($result != $this->_maximum) {
                $result += $labelInterval;
            }
            return $result;
        }
        return $this->_maximum;
    }

    /**
     * Sets the minimum value the axis will show
     * @param double $minimum The minumum value to use on the axis
     * @access private
     */
    function _setMinimum($minimum)
    {
        if ($this->_minimum === false) {
            $this->forceMinimum($minimum);
        } else {
            $this->forceMinimum(min($this->_minimum, $minimum));
        }
    }

    /**
     * Sets the maximum value the axis will show
     * @param double $maximum The maximum value to use on the axis
     * @access private
     */
    function _setMaximum($maximum)
    {
        if ($this->_maximum === false) {
            $this->forceMaximum($maximum);
        } else {
            $this->forceMaximum(max($this->_maximum, $maximum));
        }
    }

    /**
     * Forces the minimum value of the axis
     * @param double $minimum The minumum value to use on the axis
     */
    function forceMinimum($minimum)
    {
        $this->_minimum = $minimum;
        $this->_labelInterval = $this->_calcLabelInterval();        
    }

    /**
     * Forces the maximum value of the axis
     * @param double $maximum The maximum value to use on the axis
     */
    function forceMaximum($maximum)
    {
        $this->_maximum = $maximum;
        $this->_labelInterval = $this->_calcLabelInterval();        
    }

    /**
     * Show an arrow head on the 'end' of the axis
     */
    function showArrow()
    {
        $this->_showArrow = true;
    }

    /**
     * Do not show an arrow head on the 'end' of the axis (default)
     */
    function hideArrow()
    {
        $this->_showArrow = false;
    }

    /**
     * Sets an interval for when labels are shown on the axis.
     * By default AXIS_INTERVAL_AUTO is used, forcing the axis to calculate a
     * approximate best label interval to be used
     * @param double $labelInterval The interval with which labels are shown
     */
    function setLabelInterval($labelInterval = 'auto')
    {
        if ($labelInterval == 'auto') {
            $this->_labelInterval = 0;        
            $this->_labelIntervalSet = false;
            $this->_calcLabelInterval();            
        } else {
            $this->_labelInterval = $labelInterval;        
            $this->_labelIntervalSet = true;
        }
    }

    /**
     * Axis value span     
     * @return double The span of the axis (i.e. Max-Min)
     * @access private
     */
    function _axisValueSpan()
    {
        return $this->_axisSpan();
    }

    /**
     * Axis span     
     * @return double The span of the axis (i.e. Max-Min)
     * @access private
     */
    function _axisSpan()
    {
        return abs($this->_getMaximum() - $this->_getMinimum());
    }
    
    /**
     * Get the step each pixel on the canvas will represent on the axis.     
     * @return double The step a pixel represents
     * @access private
     */
    function _delta()
    {
        if (($span = $this->_axisValueSpan()) == 0) {
            return 0;
        }

        if ($this->_type == IMAGE_GRAPH_AXIS_X) {
            return $this->width() / $span;
        } else {
            return $this->height() / $span;
        }
    }

    /**
     * Preprocessor for values, ie for using logarithmic axis
     * @param double $value The value to preprocess
     * @return double The preprocessed value
     * @access private
     */
    function _value($value)
    {
        return $value - $this->_getMinimum() + ($this->_pushValues ? 0.5 : 0);
    }

    /**
     * Apply the dataset tot he axis
     * @param Image_Graph_Dataset $dataset The dataset
     * @access private
     */
    function _applyDataset(&$dataset)
    {
        if ($this->_type == IMAGE_GRAPH_AXIS_X) {
            $this->_setMinimum($dataset->minimumX());
            $this->_setMaximum($dataset->maximumX());
        } else {
            $this->_setMinimum($dataset->minimumY());
            $this->_setMaximum($dataset->maximumY());
        }
    }
    
    /**
     * Get the pixel position represented by a value on the canvas
     * @param double $value the value to get the pixel-point for 
     * @return double The pixel position along the axis
     * @access private
     */
    function _point($value)
    {
        if ($this->_type == IMAGE_GRAPH_AXIS_X) {
            return $this->_left + $this->_delta() * $this->_value($value);
        } else {
            return $this->_bottom - $this->_delta() * $this->_value($value);
        }
    }

    /**
     * Calculate the label interval
     * If explicitly defined this will be calucated to an approximate best.
     * @return double The label interval
     * @access private
     */
    function _calcLabelInterval()
    {
        if ($this->_labelIntervalSet) {
             return $this->_labelInterval;
        } 
               
        if ($this->_getMinimum() > $this->_getMaximum()) {
            return 1;
        }        
        
        $span = $this->_axisValueSpan();

        $interval = pow(10, floor(log10($span)));

        if ($interval == 0) {
            $interval = 1;
        }

        if ((($span) / $interval) < 3) {
            $interval = $interval / 4;
        }
        elseif ((($span) / $interval) < 5) {
            $interval = $interval / 2;
        }
        elseif ((($span) / $interval) > 10) {
            $interval = $interval * 2;
        }

        if (($interval -floor($interval) == 0.5) and ($interval != 0.5)) {
            $interval = floor($interval);
        }

        // just to be 100% sure that an interval of 0 is not returned som additional checks are performed
        if ($interval == 0) {
            $interval = ($span) / 5;
        }

        if ($interval == 0) {
            $interval = 1;
        }

        return $interval;
    }

    /**
     * Get next label point
     * @param doubt $point The current point, if omitted or false, the first is returned
     * @return double The next label point
     * @access private
     */
    function _getNextLabel($currentLabel = false)
    {
        if (($this->_axisSpan() == 0) or ($this->_axisValueSpan() == 0) or ($this->_labelInterval() == 0)) {
            return false;
        }
        
        $labelInterval = $this->_axisSpan()/($this->_axisValueSpan()/$this->_labelInterval());
        
        if ($labelInterval == 0) {
            return false;
        }
        
        if ($currentLabel === false) {            
            return((int) ($this->_getMinimum() / $labelInterval)) * $labelInterval - $labelInterval;
        } else {
            return $currentLabel + $labelInterval;
        }
    }        
    
    /**
     * Get the interval with which labels are shown on the axis.
     * If explicitly defined this will be calucated to an approximate best.
     * @return double The label interval
     * @access private
     */
    function _labelInterval()
    {
        return $this->_labelInterval;
    }

    /** 
     * Get the minor label interval with which axis label ticks are drawn.
     * @return double The minor label interval, default: 1/5 of the LabelInterval
     * @access private
     */
    // TODO employ (better) method for calculating minor label interval
    function _minorLabelInterval()
    {    
        if ($this->_labelInterval) {
            return false;
        }
        return $this->_labelInterval() / 5;
    }

    /** 
     * Get the size in pixels of the axis.
     * For an x-axis this is the width of the axis including labels, and for an
     * y-axis it is the corrresponding height
     * @return int The size of the axis
     * @access private 
     */
    function _size()
    {
        if ($this->_minimum < 0) {
            return 0;
        } else {
            if (!$this->_font) {
                $this->_font = $GLOBALS['_Image_Graph_font'];
            }

            $maxSize = 0;
            
            $value = $this->_getNextLabel();

            while (($value <= $this->_getMaximum()) and ($value !== false)) {
                if ((abs($value) > 0.0001) and ($value > $this->_getMinimum()) and ($value < $this->_getMaximum())) {
                    if (is_object($this->_dataPreProcessor)) {
                        $labelText = $this->_dataPreProcessor->_process($value);
                    } else {
                        $labelText = $value;
                    }
                                       
                    if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                        $maxSize = max($maxSize, $this->_font->height($labelText));
                    } else {
                        $maxSize = max($maxSize, $this->_font->width($labelText));
                    }
                }

                $value = $this->_getNextLabel($value);
            }
            return $maxSize +3;
        }
    }
    
    /**
     * Adds a mark to the axis at the specified value
     * @param double $value The value
     * @param double $value2 The second value (for a ranged mark) 
     */
    function addMark($value, $value2 = false, $text = false)
    {    
        if ($value2 === false) {
            $this->_marks[] = $value;
        } else {
            $this->_marks[] = array($value, $value2);
        }
    }
    
    /**
     * Is the axis numeric or not?
     * @return bool True if numeric, false if not
     * @access private
     */
    function _isNumeric() {
        return true;
    }

    /**
     * Output the axis
     * @access private
     */
    function _done()
    {
        parent::_done();

        if (!$this->_font) {
            $this->_font = $GLOBALS['_Image_Graph_font'];
        }

        $value = $this->_getNextLabel();
        
        $lastPosition = false;

        while (($value <= $this->_getMaximum()) and ($value !== false)) {
            if ((((abs($value) > 0.0001) or ($this->_showLabelZero)) and (($value > $this->_getMinimum()) or ($this->_showLabelMinimum)) and (($value < $this->_getMaximum()) or ($this->_showLabelMaximum))) and ($value>= $this->_getMinimum()) and ($value<= $this->_getMaximum())) {
                $labelPosition = $this->_point($value);

                if (is_object($this->_dataPreProcessor)) {
                    $labelText = $this->_dataPreProcessor->_process($value);
                } else {
                    $labelText = $value;
                }

                if ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                    $this->write($this->_right - 3, $labelPosition, $labelText, IMAGE_GRAPH_ALIGN_CENTER_Y | IMAGE_GRAPH_ALIGN_RIGHT);                   
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                    $this->write($this->_left + 3, $labelPosition, $labelText, IMAGE_GRAPH_ALIGN_CENTER_Y | IMAGE_GRAPH_ALIGN_LEFT);                   
                } else {
                    $this->write($labelPosition, $this->_top + 3, $labelText, IMAGE_GRAPH_ALIGN_CENTER_X | IMAGE_GRAPH_ALIGN_TOP);                   
                }
                
                if ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                    ImageLine($this->_canvas(), $this->_right, $labelPosition, $this->_right + 6, $labelPosition, $this->_getLineStyle());
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                    ImageLine($this->_canvas(), $this->_left, $labelPosition, $this->_left - 6, $labelPosition, $this->_getLineStyle());
                } else {
                    ImageLine($this->_canvas(), $labelPosition, $this->_top, $labelPosition, $this->_top - 6, $this->_getLineStyle());
                }
            }

            $value = $this->_getNextLabel($value);
/*            if ($minorLabelInterval = $this->_minorLabelInterval()) {
                $minorValue = $value + $minorLabelInterval;
                while (($minorValue < $nextValue) and ($minorValue < $this->_getMaximum() - $minorLabelInterval)) {
                    if ($minorValue >= $this->_getMinimum()) {
                        $position = $this->_point($minorValue);
                        if ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                            ImageLine($this->_canvas(), $this->_right, $position, $this->_right + 3, $position, $this->_getLineStyle());
                        } else {
                            ImageLine($this->_canvas(), $position, $this->_top, $position, $this->_top - 3, $this->_getLineStyle());
                        }
                    }

                    $minorValue += $minorLabelInterval;
                }
            }*/
        }
                     
        if ($this->_type == IMAGE_GRAPH_AXIS_X) {
            ImageLine($this->_canvas(), $this->_left, $this->_top, $this->_right, $this->_top, $this->_getLineStyle());
            if ($this->_showArrow) {
                $arrow[] = $this->_right - 8;
                $arrow[] = $this->_top + 5;
                $arrow[] = $this->_right;
                $arrow[] = $this->_top;
                $arrow[] = $this->_right - 8;
                $arrow[] = $this->_top - 5;
                ImageFilledPolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getFillStyle());
                ImagePolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getLineStyle());
            }
        } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
            ImageLine($this->_canvas(), $this->_left, $this->_top, $this->_left, $this->_bottom, $this->_getLineStyle());
            if ($this->_showArrow) {
                $arrow[] = $this->_left - 5;
                $arrow[] = $this->_top + 8;
                $arrow[] = $this->_left;
                $arrow[] = $this->_top;
                $arrow[] = $this->_left + 5;
                $arrow[] = $this->_top + 8;
                ImageFilledPolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getFillStyle());
                ImagePolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getLineStyle());
            }
        } else {
            ImageLine($this->_canvas(), $this->_right, $this->_top, $this->_right, $this->_bottom, $this->_getLineStyle());
            if ($this->_showArrow) {
                $arrow[] = $this->_right - 5;
                $arrow[] = $this->_top + 8;
                $arrow[] = $this->_right;
                $arrow[] = $this->_top;
                $arrow[] = $this->_right + 5;
                $arrow[] = $this->_top + 8;
                ImageFilledPolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getFillStyle());
                ImagePolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getLineStyle());
            }
        }

        reset($this->_marks);
        while (list($id, $mark) = each($this->_marks)) {
            $arrow = false;
            if (is_array($mark)) {
                if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                    $x0 = $this->_point($mark[0]);
                    $y0 = $this->_top-4;
                    $x1 = $this->_point($mark[1]);
                    $y1 = $this->_top+2;
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                    $x0 = $this->_right-2;
                    $y0 = $this->_point($mark[1]);
                    $x1 = $this->_right+4;
                    $y1 = $this->_point($mark[0]);
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                    $x0 = $this->_left-4;
                    $y0 = $this->_point($mark[1]);
                    $x1 = $this->_left+2;
                    $y1 = $this->_point($mark[0]);
                }
                ImageFilledRectangle($this->_canvas(), $x0, $y0, $x1, $y1, $this->_getFillStyle());
                ImageRectangle($this->_canvas(), $x0, $y0, $x1, $y1, $this->_getLineStyle());
            } else {                      
                if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                    $x = $this->_point($mark);                             
                    $arrow[] = $x;
                    $arrow[] = $this->_top;
                    $arrow[] = $x - 7;
                    $arrow[] = $this->_top - 7;
                    $arrow[] = $x + 7;
                    $arrow[] = $this->_top - 7;
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                    $y = $this->_point($mark);                             
                    $arrow[] = $this->_right;
                    $arrow[] = $y;
                    $arrow[] = $this->_right + 7;
                    $arrow[] = $y - 7;
                    $arrow[] = $this->_right + 7;
                    $arrow[] = $y + 7;
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                    $y = $this->_point($mark);                             
                    $arrow[] = $this->_left;
                    $arrow[] = $y;
                    $arrow[] = $this->_left - 7;
                    $arrow[] = $y - 7;
                    $arrow[] = $this->_left - 7;
                    $arrow[] = $y + 7;
                }
                ImageFilledPolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getFillStyle());
                ImagePolygon($this->_canvas(), $arrow, count($arrow) / 2, $this->_getLineStyle());
            }
            unset($arrow);        
        }
    }

}

?>