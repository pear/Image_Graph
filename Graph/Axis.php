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
 * Class for axis handling.
 *
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
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Axis
 */
class Image_Graph_Axis extends Image_Graph_Plotarea_Element
{
    // TODO Create an option to make it user specified at which point/value the axis intersects (i.e. 0, min, max)

    /**
     * The type of the axis, possible values are:
     * <ul>
     * <li>IMAGE_GRAPH_AXIS_X / IMAGE_GRAPH_AXIS_HORIZONTAL
     * <li>IMAGE_GRAPH_AXIS_Y / IMAGE_GRAPH_AXIS_VERTICAL / IMAGE_GRAPH_AXIS_Y_SECONDARY
     * </ul>
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
     * The minimum value the axis has been explicitly set by the user
     * @var bool
     * @access private
     */
    var $_minimumSet = false;

    /**
     * The maximum value the axis displays
     * @var int
     * @access private
     */
    var $_maximum = false;

    /**
     * The maximum value the axis has been explicitly set by the user
     * @var bool
     * @access private
     */
    var $_maximumSet = false;

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
     * Intersection data of axis
     * @var array
     * @access private
     */
    var $_intersect = array('value' => 'default', 'axis' => 'default');
    
    // TODO Make labelOptions propagate to sub-classes where required
    /**
     * The label options
     *
     * Should text be shows, preferences for ticks. The indexes start at level
     * 1, which is chosen for readability
     * @var array
     * @access private
     */
    var $_labelOptions = array(
        1 => array(
            'interval' => 1,
            'type' => 'auto',
            'tick' => array('start' => -2, 'end' => 2),
            'showtext' => true,
            'showoffset' => false,
            'font' => array(),
            'offset' => 0
        )
    );

    /**
     * The labels that are shown.
     *
     * This is used to make values show only once...
     * @access private
     */
    var $_labelText = array();

    /**
     * A data preprocessor for formatting labels, fx showing dates as a standard
     * date instead of Unix time stamp
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
     * The title of this axis
     * @var string
     * @access private
     */
    var $_title = '';

    /**
     * The font used for the title of this axis
     * @var Image_Graph_Font
     * @access private
     */
    var $_titleFont = false;

    /**
     * Image_Graph_Axis [Constructor].
     * Normally a manual creation should not be necessary, axis are created
     * automatically by the {@link Image_Graph_Plotarea} constructor unless
     * explicitly defined otherwise
     *
     * @param int $type The type (direction) of the Axis, use IMAGE_GRAPH_AXIS_X
     * for an X-axis (default, may be omitted) and IMAGE_GRAPH_AXIS_Y for Y-
     * axis)
     */
    function &Image_Graph_Axis($type = IMAGE_GRAPH_AXIS_X)
    {
        parent::Image_Graph_Element();
        $this->_type = $type;
    }

    /**
     * Push the values by 0.5 (for bar and step chart)
     *
     * @access private
     */
    function _pushValues()
    {
        $this->_pushValues = true;
    }

    /**
     * Gets the font of the title.
     *
     * If not font has been set, the parent font is propagated through it's
     * children.
     *
     * @return array An associated array used for driver
     * @access private
     */
    function _getTitleFont()
    {
        if ($this->_titleFont === false) {
            return $this->_getFont();
        } else {
            if (is_object($this->_titleFont)) {
                return $this->_titleFont->_getFont();
            } elseif (is_array($this->_titleFont)) {
                return $this->_getFont($this->_titleFont);
            } elseif (is_int($this->_titleFont)) {
                return $this->_getFont(array('size' => $this->_titleFont));
            }
        }
        return array();
    }

    /**
     * Shows a label for the the specified values.
     *
     * Allowed values are  combinations of:
     * <ul>
     * <li>IMAGE_GRAPH_LABEL_MINIMUM
     * <li>IMAGE_GRAPH_LABEL_ZERO
     * <li>IMAGE_GRAPH_LABEL_MAXIMUM
     * </ul>
     * By default none of these are shows on the axis
     *
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
     *
     * @param Image_Graph_DataPreprocessor $dataPreProcessor The data preprocessor
     * @see Image_Graph_DataPreprocessor
     */
    function setDataPreProcessor(& $dataPreProcessor)
    {
        $this->_dataPreProcessor = & $dataPreProcessor;
    }

    /**
     * Gets the minimum value the axis will show
     *
     * @return double The minumum value
     * @access private
     */
    function _getMinimum()
    {
        return $this->_minimum;
    }

    /**
     * Gets the maximum value the axis will show
     *
     * @return double The maximum value
     * @access private
     */
    function _getMaximum()
    {
        return $this->_maximum;
    }

    /**
     * Sets the minimum value the axis will show
     *
     * @param double $minimum The minumum value to use on the axis
     * @access private
     */
    function _setMinimum($minimum)
    {
        if ($this->_minimum === false) {
            $this->forceMinimum($minimum, false);
        } else {
            $this->forceMinimum(min($this->_minimum, $minimum), false);
        }
    }

    /**
     * Sets the maximum value the axis will show
     *
     * @param double $maximum The maximum value to use on the axis
     * @access private
     */
    function _setMaximum($maximum)
    {
        if ($this->_maximum === false) {
            $this->forceMaximum($maximum, false);
        } else {
            $this->forceMaximum(max($this->_maximum, $maximum), false);
        }
    }

    /**
     * Forces the minimum value of the axis
     *
     * @param double $minimum The minumum value to use on the axis
     * @param bool $userEnforce This value should not be set, used internally
     */
    function forceMinimum($minimum, $userEnforce = true)
    {
        if (($userEnforce) || (!$this->_minimumSet)) {
            $this->_minimum = $minimum;
            $this->_minimumSet = $userEnforce;
        }
        $this->_calcLabelInterval();
    }

    /**
     * Forces the maximum value of the axis
     *
     * @param double $maximum The maximum value to use on the axis
     * @param bool $userEnforce This value should not be set, used internally
     */
    function forceMaximum($maximum, $userEnforce = true)
    {
        if (($userEnforce) || (!$this->_maximumSet)) {
            $this->_maximum = $maximum;
            $this->_maximumSet = $userEnforce;
        }
        $this->_calcLabelInterval();
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
     * Return the label distance.
     *
     * @param int $level The label level to return the distance of
     * @return int The distance between 2 adjacent labels
     * @access private
     */
    function _labelDistance($level = 1)
    {
        // TODO This will probably fail if using setLabelInterval()
        $l1 = $this->_getNextLabel(false, $level);
        $l2 = $this->_getNextLabel($l1, $level);
        return abs($this->_point($l2) - $this->_point($l1));
    }

    /**
     * Sets an interval for when labels are shown on the axis.
     *
     * By default 'auto' is used, forcing the axis to calculate a approximate
     * best label interval to be used. Specify an array to use user-defined
     * values for labels.
     *
     * @param mixed $labelInterval The interval with which labels are shown
     * @param int $level The label level to set the interval on
     */
    function setLabelInterval($labelInterval = 'auto', $level = 1)
    {
        if (!isset($this->_labelOptions[$level])) {
            $this->_labelOptions[$level] = array();
        }

        if ($labelInterval === 'auto') {
            $this->_labelOptions[$level]['type'] = 'auto';
            $this->_calcLabelInterval();
        } else {
            $this->_labelOptions[$level]['type'] = 'manual';
            $this->_labelOptions[$level]['interval'] = $labelInterval;
        }
    }

    /**
     * Sets the title of this axis.
     *
     * This is used as an alternative (maybe better) method, that using layout's
     * for axis-title generation.
     *
     * @param string $title The title of this axis
     * @param Image_Graph_Font $font The font used for the title
     * @since 0.3.0dev2
     */
    function setTitle($title, $font = false)
    {
        $this->_title = $title;
        $this->_titleFont =& $font;
    }

    /**
     * Axis value span
     *
     * @return double The span of the axis (i.e. Max-Min)
     * @access private
     */
    function _axisValueSpan()
    {
        return $this->_axisSpan();
    }

    /**
     * Axis span
     *
     * @return double The span of the axis (i.e. Max-Min)
     * @access private
     */
    function _axisSpan()
    {
        return abs($this->_getMaximum() - $this->_getMinimum());
    }

    /**
     * Get the step each pixel on the canvas will represent on the axis.
     *
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
     *
     * @param double $value The value to preprocess
     * @return double The preprocessed value
     * @access private
     */
    function _value($value)
    {
        return $value - $this->_getMinimum() + ($this->_pushValues ? 0.5 : 0);
    }

    /**
     * Apply the dataset to the axis
     *
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
     *
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
     * Get the axis intersection pixel position
     *
     * This is only to be called prior to output! I.e. between the user
     * invokation of Image_Graph::done() and any actual output is performed.
     * This is because it can change the axis range.
     *
     * @param double $value the intersection value to get the pixel-point for
     * @return double The pixel position along the axis
     * @access private
     */
    function _intersectPoint($value)
    {

        if ($value === 'min') {
            if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                return $this->_left;
            } else {
                return $this->_bottom;
            }
            //$value = $this->_getMinimum();
        } elseif ($value === 'max') {
            if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                return $this->_right;
            } else {
                return $this->_top;
            }
            //$value = $this->_getMaximum();
        } elseif ($value < $this->_getMinimum()) {
            $this->_setMinimum($value);
        } elseif ($value > $this->_getMaximum()) {
            $this->_setMaximum($value);
        }

        return $this->_point($value);
    }
    
    /**
     * Calculate the label interval
     *
     * If explicitly defined this will be calucated to an approximate best.
     *
     * @return double The label interval
     * @access private
     */
    function _calcLabelInterval()
    {
        $min = $this->_getMinimum();
        $max = $this->_getMaximum();
        if ((!empty($min)) && (!empty($max)) && ($min > $max)) {
            $this->_labelOptions[1]['interval'] = 1;
            return true;
        }

        foreach($this->_labelOptions as $level => $labelOptions) {
            if ($labelOptions['type'] !== 'auto') {
                $span = false;
            } elseif ($level == 1) {
                $span = $this->_axisValueSpan();
            } else {
                $l1 = $this->_getNextLabel(false, $level - 1);
                $l2 = $this->_getNextLabel($l1, $level - 1);
                if ((!is_numeric($l1)) || (!is_numeric($l2))) {
                    $span == false;
                } else {
                    $span = $l2 - $l1;
                }
            }

            if ($span !== false) {
                $interval = pow(10, floor(log10($span)));

                if ($interval == 0) {
                    $interval = 1;
                }

                if ((($span) / $interval) < 3) {
                    $interval = $interval / 4;
                } elseif ((($span) / $interval) < 5) {
                    $interval = $interval / 2;
                } elseif ((($span) / $interval) > 10) {
                    $interval = $interval * 2;
                }

                if (($interval -floor($interval) == 0.5) && ($interval != 0.5)) {
                    $interval = floor($interval);
                }

                // just to be 100% sure that an interval of 0 is not returned some
                // additional checks are performed
                if ($interval == 0) {
                    $interval = ($span) / 5;
                }

                if ($interval == 0) {
                    $interval = 1;
                }

                $this->_labelOptions[$level]['interval'] = $interval;
            }
        }
    }

    /**
     * Get next label point
     *
     * @param doubt $currentLabel The current label, if omitted or false, the
     *   first is returned
     * @param int $level The label level to get the next label from
     * @return double The next label point
     * @access private
     */
    function _getNextLabel($currentLabel = false, $level = 1)
    {
        if (!isset($this->_labelOptions[$level])) {
            return false;
        }

        if (is_array($this->_labelOptions[$level]['interval'])) {
            if ($currentLabel === false) {
                reset($this->_labelOptions[$level]['interval']);
            }

            if (list(, $label) = each($this->_labelOptions[$level]['interval'])) {
                return $label;
            } else {
                return false;
            }
        } else {
            $li = $this->_labelInterval($level);
            if (($this->_axisSpan() == 0) || ($this->_axisValueSpan() == 0) ||
                ($li == 0)
            ) {
                return false;
            }

            $labelInterval = $this->_axisSpan() / ($this->_axisValueSpan() / $li);

            if ($labelInterval == 0) {
                return false;
            }

            if ($currentLabel === false) {
                return((int) ($this->_getMinimum() / $labelInterval)) *
                    $labelInterval - $labelInterval;
            } else {
                return $currentLabel + $labelInterval;
            }
        }
    }

    /**
     * Get the interval with which labels are shown on the axis.
     *
     * If explicitly defined this will be calucated to an approximate best.
     *
     * @param int $level The label level to get the label interval for
     * @return double The label interval
     * @access private
     */
    function _labelInterval($level = 1)
    {
        if ((!isset($this->_labelOptions[$level])) ||
            (!isset($this->_labelOptions[$level]['interval']))
        ) {
            return 1;
        }

        return (is_array($this->_labelOptions[$level]['interval'])
            ? 1
            : $this->_labelOptions[$level]['interval']
        );
    }

    /**
     * Get the size in pixels of the axis.
     *
     * For an x-axis this is the width of the axis including labels, and for an
     * y-axis it is the corrresponding height
     *
     * @return int The size of the axis
     * @access private
     */
    function _size()
    {
        krsort($this->_labelOptions);

        $totalMaxSize = 0;

        $this->_driver->setFont($this->_getFont());

        foreach ($this->_labelOptions as $level => $labelOptions) {
            if ((isset($labelOptions['showoffset'])) && ($labelOptions['showoffset'] === true)) {
                $this->_labelOptions[$level]['offset'] = $totalMaxSize;
            } else {
                $this->_labelOptions[$level]['offset'] = 0;
            }
            if ((isset($labelOptions['showtext'])) && ($labelOptions['showtext'] === true)) {

                if (isset($labelOptions['font'])) {
                    $font = $labelOptions['font'];
                } else {
                    $font = false;
                }
                $this->_driver->setFont($this->_getFont($font));

                $value = $this->_getNextLabel(false, $level);
                $maxSize = 0;
                while (($value <= $this->_getMaximum()) && ($value !== false)) {
                    if ((abs($value) > 0.0001) && ($value > $this->_getMinimum()) &&
                        ($value < $this->_getMaximum()))
                    {
                        if (is_object($this->_dataPreProcessor)) {
                            $labelText = $this->_dataPreProcessor->_process($value);
                        } else {
                            $labelText = $value;
                        }

                        if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                            $maxSize = max($maxSize, $this->_driver->textHeight($labelText));
                        } else {
                            $maxSize = max($maxSize, $this->_driver->textWidth($labelText));
                        }
                    }

                    $value = $this->_getNextLabel($value, $level);
                }
                if ((isset($labelOptions['showoffset'])) && ($labelOptions['showoffset'] === true)) {
                    $totalMaxSize += $maxSize;
                } else {
                    $totalMaxSize = max($totalMaxSize, $maxSize);
                }
            }
        }

        if ($this->_title) {
            $this->_driver->setFont($this->_getTitleFont());

            if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                $totalMaxSize += $this->_driver->textHeight($this->_title);
            } else {
                $totalMaxSize += $this->_driver->textWidth($this->_title);
            }
            $totalMaxSize += 10;
        }

        return $totalMaxSize + 3;
    }

    /**
     * Adds a mark to the axis at the specified value
     *
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
     *
     * @return bool True if numeric, false if not
     * @access private
     */
    function _isNumeric()
    {
        return true;
    }

    /**
     * Set the major tick appearance.
     *
     * The positions are specified in pixels relative to the axis, meaning that
     * a value of -1 for start will draw the major tick 'line' starting at 1
     * pixel outside (negative) value the axis (i.e. below an x-axis and to the
     * left of a normal y-axis).
     *
     * @param int $start The start position relative to the axis
     * @param int $end The end position relative to the axis
     * @param int $level The label level to set the tick options for
     * @since 0.3.0dev2
     */
    function setTickOptions($start, $end, $level = 1)
    {
        if (!isset($this->_labelOptions[$level])) {
            $this->_labelOptions[$level] = array();
        }

        $this->_labelOptions[$level]['tick'] = array(
            'start' => $start,
            'end' => $end
        );
    }

    /**
     * Set axis intersection.
     *
     * Sets the value at which the axis intersects other axis, fx. at which Y-
     * value the x-axis intersects the y-axis (normally at 0).
     * 
     * Possible values are 'default', 'min', 'max' or a number between axis min
     * and max (the value will automatically be limited to this range).
     * 
     * For a coordinate system with 2 y-axis, the x-axis can either intersect
     * the primary or the secondary y-axis. To make the x-axis intersect the
     * secondary y-axis at a given value pass IMAGE_GRAPH_AXIS_Y_SECONDARY as
     * second parameter.
     *
     * @param mixed $intersection The value at which the axis intersect the
     * 'other' axis
     * @param mixed $axis The axis to intersect. Only applies to x-axis with
     * both a primary and secondary y-axis available.
     * @since 0.3.0dev2
     */
    function setAxisIntersection($intersection, $axis = 'default')
    {
        // TODO Ensure that the intersection is within the other axis range, otherwise expand range
        $this->_intersect = array(
            'value' => $intersection,
            'axis' => $axis
        );
    }

    /**
     * Get axis intersection data.
     * 
     * @return array An array with the axis intersection data.
     * @since 0.3.0dev2
     * @access private
     */
    function _getAxisIntersection()
    {
        $value = $this->_intersect['value'];
        $axis = $this->_intersect['axis'];
        if (($this->_type == IMAGE_GRAPH_AXIS_Y) 
            || ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY)
        ) {
            $axis = IMAGE_GRAPH_AXIS_X;
        } elseif ($axis == 'default') {
            $axis = IMAGE_GRAPH_AXIS_Y;
        }
        
        if ($value === 'default') {
            switch ($this->_type) {
            case IMAGE_GRAPH_AXIS_Y:
                $value = 'min';
                break;
            case IMAGE_GRAPH_AXIS_Y_SECONDARY:
                $value = 'max';
                break;
            case IMAGE_GRAPH_AXIS_X:
                $value = 0;
                break;
            }
        }
        
        return array('value' => $value, 'axis' => $axis);
    }
    
    /**
     * Output an axis tick mark.
     *
     * @param int $value The value to output
     * @param int $level The label level to draw the tick for
     * @access private
     */
    function _drawTick($value, $level = 1)
    {
        if (isset($this->_labelOptions[$level])) {
            $labelOptions = $this->_labelOptions[$level];
            $labelPosition = $this->_point($value);

            if (isset($labelOptions['offset'])) {
                $offset = $labelOptions['offset'];
            } else {
                $offset = 0;
            }

            if ((isset($labelOptions['showtext'])) && ($labelOptions['showtext'] === true)) {
                if (is_object($this->_dataPreProcessor)) {
                    $labelText = $this->_dataPreProcessor->_process($value);
                } else {
                    $labelText = $value;
                }

                if (!in_array($labelText, $this->_labelText)) {
                    $this->_labelText[] = $labelText;

                    if (isset($labelOptions['font'])) {
                        $font = $labelOptions['font'];
                    } else {
                        $font = false;
                    }

                    $font = $this->_getFont($font);

                    if ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                        $this->write(
                            $this->_right - 3 - $offset,
                            $labelPosition,
                            $labelText,
                            IMAGE_GRAPH_ALIGN_CENTER_Y | IMAGE_GRAPH_ALIGN_RIGHT,
                            $font
                        );
                    } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                        $this->write(
                            $this->_left + 3 + $offset,
                            $labelPosition,
                            $labelText,
                            IMAGE_GRAPH_ALIGN_CENTER_Y | IMAGE_GRAPH_ALIGN_LEFT,
                            $font
                        );
                    } else {
                        $this->write(
                            $labelPosition,
                            $this->_top + 3 + $offset,
                            $labelText,
                            IMAGE_GRAPH_ALIGN_CENTER_X | IMAGE_GRAPH_ALIGN_TOP,
                            $font
                        );
                    }
                }
            }

            if (isset($this->_labelOptions[$level]['tick'])) {
                $tickStart = $this->_labelOptions[$level]['tick']['start'];
                $tickEnd = $this->_labelOptions[$level]['tick']['end'];
            } else {
                $tickStart = -2;
                $tickEnd = 2;
            }

            $this->_getLineStyle();
            if ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                if ($tickStart === 'auto') {
                    $tickStart = -$offset;
                }
                $this->_driver->line(
                    $this->_right + $tickStart,
                    $labelPosition,
                    $this->_right + $tickEnd,
                    $labelPosition
                );
            } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                if ($tickStart === 'auto') {
                    $tickStart = $offset;
                }
                $this->_driver->line(
                    $this->_left - $tickStart,
                    $labelPosition,
                    $this->_left - $tickEnd,
                    $labelPosition
                );
            } else {
                if ($tickStart === 'auto') {
                    $tickStart = $offset;
                }
                $this->_driver->line(
                    $labelPosition,
                    $this->_top - $tickStart,
                    $labelPosition,
                    $this->_top - $tickEnd
                );
            }
        }
    }

    /**
     * Draws axis lines.
     *
     * @access private
     */
    function _drawAxisLines()
    {
        if ($this->_type == IMAGE_GRAPH_AXIS_X) {
            $this->_getLineStyle();
            $this->_driver->line(
                $this->_left,
                $this->_top,
                $this->_right,
                $this->_top
            );

            if ($this->_title) {
                $y = $this->_bottom;
                $x = $this->_left + $this->width() / 2;
                $this->write($x, $y, $this->_title, IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_BOTTOM, $this->_getTitleFont());
            }

            if ($this->_showArrow) {
                $this->_getFillStyle();
                $this->_getLineStyle();
                $this->_driver->polygonAdd($this->_right - 8, $this->_top + 5);
                $this->_driver->polygonAdd($this->_right, $this->_top);
                $this->_driver->polygonAdd($this->_right - 8, $this->_top - 5);
                $this->_driver->polygonEnd();
            }
        } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
            $this->_getLineStyle();
            $this->_driver->line(
                $this->_left,
                $this->_top,
                $this->_left,
                $this->_bottom
            );

            if ($this->_title) {
                $this->_driver->setFont($this->_getTitleFont());
                $y = $this->_top + $this->height() / 2;
                $x = $this->_right;
                $this->write($x, $y, $this->_title, IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_CENTER_Y, $this->_getTitleFont());
            }

            if ($this->_showArrow) {
                $this->_getFillStyle();
                $this->_getLineStyle();
                $this->_driver->polygonAdd($this->_left - 5, $this->_top + 8);
                $this->_driver->polygonAdd($this->_left, $this->_top);
                $this->_driver->polygonAdd($this->_left + 5, $this->_top + 8);
                $this->_driver->polygonEnd();
            }
        } else {
            $this->_getLineStyle();
            $this->_driver->line(
                $this->_right,
                $this->_top,
                $this->_right,
                $this->_bottom
            );

            if ($this->_title) {
                $this->_driver->setFont($this->_getTitleFont());
                $y = $this->_top + $this->height() / 2;
                $x = $this->_left;
                $this->write($x, $y, $this->_title, IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_CENTER_Y, $this->_getTitleFont());
            }

            if ($this->_showArrow) {
                $this->_getFillStyle();
                $this->_getLineStyle();
                $this->_driver->polygonAdd($this->_right - 5, $this->_top + 8);
                $this->_driver->polygonAdd($this->_right, $this->_top);
                $this->_driver->polygonAdd($this->_right + 5, $this->_top + 8);
                $this->_driver->polygonEnd();
            }
        }
    }

    /**
     * Output the axis
     *
     * @return bool Was the output 'good' (true) or 'bad' (false).
     * @access private
     */
    function _done()
    {
        if (parent::_done() === false) {
            return false;
        }

        $this->_drawAxisLines();

        ksort($this->_labelOptions);
        foreach ($this->_labelOptions as $level => $labelOption) {
            $value = $this->_getNextLabel(false, $level);
            while (($value <= $this->_getMaximum()) && ($value !== false)) {
                if ((((abs($value) > 0.0001) || ($this->_showLabelZero)) &&
                    (($value > $this->_getMinimum()) || ($this->_showLabelMinimum)) &&
                    (($value < $this->_getMaximum()) || ($this->_showLabelMaximum))) &&
                    ($value >= $this->_getMinimum()) && ($value <= $this->_getMaximum())
                ) {
                    $this->_drawTick($value, $level);
                }
                $value = $this->_getNextLabel($value, $level);
            }
        }

        $tickStart = -3;
        $tickEnd = 2;

        foreach ($this->_marks as $mark) {
            if (is_array($mark)) {
                if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                    $x0 = $this->_point($mark[0]);
                    $y0 = $this->_top + $tickStart;
                    $x1 = $this->_point($mark[1]);
                    $y1 = $this->_top + $tickEnd;
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                    $x0 = $this->_right + $tickStart;
                    $y0 = $this->_point($mark[1]);
                    $x1 = $this->_right + $tickEnd;
                    $y1 = $this->_point($mark[0]);
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                    $x0 = $this->_left + $tickStart;
                    $y0 = $this->_point($mark[1]);
                    $x1 = $this->_left + $tickEnd;
                    $y1 = $this->_point($mark[0]);
                }
                $this->_getFillStyle();
                $this->_getLineStyle();
                $this->_driver->rectangle($x0, $y0, $x1, $y1);
            } else {
                $this->_getFillStyle();
                $this->_getLineStyle();
                if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                    $x = $this->_point($mark);
                    $this->_driver->polygonAdd($x, $this->_top);
                    $this->_driver->polygonAdd($x - 5, $this->_top + 5);
                    $this->_driver->polygonAdd($x + 5, $this->_top + 5);
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                    $y = $this->_point($mark);
                    $this->_driver->polygonAdd($this->_right, $y);
                    $this->_driver->polygonAdd($this->_right - 5, $y - 5);
                    $this->_driver->polygonAdd($this->_right - 5, $y + 5);
                } elseif ($this->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY) {
                    $y = $this->_point($mark);
                    $this->_driver->polygonAdd($this->_left, $y);
                    $this->_driver->polygonAdd($this->_left + 5, $y - 5);
                    $this->_driver->polygonAdd($this->_left + 5, $y + 5);
                }
                $this->_driver->polygonEnd();
            }
        }
        return true;
    }

}

?>