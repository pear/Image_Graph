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
 * @subpackage Axis
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */

/**
 * Include file Image/Graph/Axis.php
 */
require_once 'Image/Graph/Axis.php';

/**
 * A normal axis thats displays labels with a 'interval' of 1.
 * This is basically a normal axis where the range is
 * the number of labels defined, that is the range is explicitly defined
 * when constructing the axis.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Axis
 */
class Image_Graph_Axis_Category extends Image_Graph_Axis
{

    /**
     * The labels shown on the axis
     * @var array
     * @access private
     */
    var $_labels = false;

    /**
     * Image_Graph_Axis_Category [Constructor].
     *
     * @param int $type The type (direction) of the Axis
     */
    function &Image_Graph_Axis_Category($type = IMAGE_GRAPH_AXIS_X)
    {
        parent::Image_Graph_Axis($type);
        $this->_labels = array();
        $this->setlabelInterval(1);
    }

    /**
     * Gets the minimum value the axis will show.
     *
     * This is always 0
     *
     * @return double The minumum value
     * @access private
     */
    function _getMinimum()
    {
        return 0;
    }

    /**
     * Gets the maximum value the axis will show.
     *
     * This is always the number of labels passed to the constructor.
     *
     * @return double The maximum value
     * @access private
     */
    function _getMaximum()
    {
        return count($this->_labels) - ($this->_pushValues ? 0 : 1);
    }

    /**
     * Sets the minimum value the axis will show.
     *
     * A minimum cannot be set on a SequentialAxis, it is always 0.
     *
     * @param double Minimum The minumum value to use on the axis
     * @access private
     */
    function _setMinimum($minimum)
    {
    }

    /**
     * Sets the maximum value the axis will show
     *
     * A maximum cannot be set on a SequentialAxis, it is always the number
     * of labels passed to the constructor.
     *
     * @param double Maximum The maximum value to use on the axis
     * @access private
     */
    function _setMaximum($maximum)
    {
    }

    /**
     * Forces the minimum value of the axis
     *
     * A minimum cannot be set on a SequentialAxis, it is always 0.
     *
     * @param double $minimum The minumum value to use on the axis
     */
    function forceMinimum($minimum)
    {
    }

    /**
     * Forces the maximum value of the axis
     *
     * A maximum cannot be set on a SequentialAxis, it is always the number
     * of labels passed to the constructor.
     *
     * @param double $maximum The maximum value to use on the axis
     */
    function forceMaximum($maximum)
    {
    }

    /**
     * Sets an interval for where labels are shown on the axis.
     *
     * The label interval is rounded to nearest integer value
     *
     * @param double $labelInterval The interval with which labels are shown
     */
    function setLabelInterval($labelInterval = 'auto')
    {
        if ($labelInterval == 'auto') {
            parent::setLabelInterval(1);
        } else {
            parent::setLabelInterval(round($labelInterval));
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
        $the_value = array_search($value, $this->_labels);
        if ($the_value !== false) {
            return $the_value + ($this->_pushValues ? 0.5 : 0);
        } else {
            return 0;
        }
    }


    /**
     * Get the minor label interval with which axis label ticks are drawn.
     *
     * For a sequential axis this is always disabled (i.e false)
     *
     * @return double The minor label interval, always false
     * @access private
     */
    function _minorLabelInterval()
    {
        return false;
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
        $this->_driver->setFont($this->_getFont());

        $maxSize = 0;
        foreach($this->_labels as $label) {
            $labelPosition = $this->_point($label);

            if (is_object($this->_dataPreProcessor)) {
                $labelText = $this->_dataPreProcessor->_process($label);
            } else {
                $labelText = $label;
            }

            if ($this->_type == IMAGE_GRAPH_AXIS_Y) {
                $maxSize = max($maxSize, $this->_driver->textWidth($labelText));
            } else {
                $maxSize = max($maxSize, $this->_driver->textHeight($labelText));
            }
        }

        if ($this->_title) {
            $this->_driver->setFont($this->_getTitleFont());

            if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                $maxSize += $this->_driver->textHeight($this->_title);
            } else {
                $maxSize += $this->_driver->textWidth($this->_title);
            }
            $maxSize += 10;
        }
        return $maxSize +3;
    }

    /**
     * Apply the dataset to the axis.
     *
     * This calculates the order of the categories, which is very important
     * for fx. line plots, so that the line does not "go backwards", consider
     * these X-sets:<p>
     * 1: (1, 2, 3, 4, 5, 6)<br>
     * 2: (0, 1, 2, 3, 4, 5, 6, 7)<p>
     * If they are not ordered, but simply appended, the categories on the axis
     * would be:<p>
     * X: (1, 2, 3, 4, 5, 6, 0, 7)<p>
     * Which would render the a line for the second plot to show incorrectly.
     * Instead this algorithm, uses and 'value- is- before' method to see that
     * the 0 is before a 1 in the second set, and that it should also be before
     * a 1 in the X set. Hence:<p>
     * X: (0, 1, 2, 3, 4, 5, 6, 7)
     *
     * @param Image_Graph_Dataset $dataset The dataset
     * @access private
     */
    function _applyDataset(&$dataset)
    {
        $newLabels = array();

        $dataset->_reset();
        while ($point = $dataset->_next()) {
            if ($this->_type == IMAGE_GRAPH_AXIS_X) {
                $data = $point['X'];
            } else {
                $data = $point['Y'];
            }
            if (!in_array($data, $this->_labels)) {
                $newLabels[] = $data;
                //$this->_labels[] = $data;
            }
            $allLabels[] = $data;
        }
        if (count($this->_labels) == 0) {
            $this->_labels = $newLabels;
        } elseif (is_array($newLabels)) {
            // get all intersecting labels
            $intersect = array_intersect($allLabels, $this->_labels);
            // traverse all new and find their relative position withing the
            // intersec, fx value X0 is before X1 in the intersection, which
            // means that X0 should be placed before X1 in the label array
            $keys = array_keys($newLabels);
            foreach($keys as $key) {
                $newLabel = $newLabels[$key];
                $key = array_search($newLabel, $allLabels);
                reset($intersect);
                $this_value = false;
                // intersect indexes are the same as in allLabels!
                $first = true;
                while ((list($id, $value) = each($intersect)) &&
                    ($this_value === false))
                {
                    if (($first) && ($id > $key)) {
                        $this_value = $value;
                    } elseif ($id >= $key) {
                        $this_value = $value;
                    }
                    $first = false;
                }
                if ($this_value === false) {
                    // the new label was not found before anything in the
                    // intersection -> append it
                    $this->_labels[] = $newLabel;
                } else {
                    // the new label was found before $this_value in the
                    // intersection, insert the label before this position in
                    // the label array
                    $key = array_search($this_value, $this->_labels);
                    $pre = array_slice($this->_labels, 0, $key);
                    $pre[] = $newLabel;
                    $post = array_slice($this->_labels, $key);
                    $this->_labels = array_merge($pre, $post);
                }
            }
            unset($keys);
        }
        $this->_labels = array_values(array_unique($this->_labels));
        $this->_calcLabelInterval();
    }

    /**
     * Return the label distance.
     *
     * @return int The distance between 2 adjacent labels
     * @access private
     */
    function _labelDistance()
    {
        reset($this->_labels);
        list(, $l1) = each($this->_labels);
        list(, $l2) = each($this->_labels);
        return abs($this->_point($l2) - $this->_point($l1));
    }

    /**
     * Get next label point
     *
     * @param doubt $point The current point, if omitted or false, the first is
     *   returned
     * @return double The next label point
     * @access private
     */
    function _getNextLabel($currentLabel = false)
    {
        if ($currentLabel === false) {
            reset($this->_labels);
        }
        $result = false;
        $count = ($currentLabel === false ? $this->_labelInterval() - 1 : 0);
        while ($count < $this->_labelInterval()) {
           $result = (list(, $label) = each($this->_labels));
           $count++;
        }
        if ($result) {
            return $label;
        } else  {
            return false;
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
        return false;
    }

    /**
     * Output the axis
     *
     * @return bool Was the output 'good' (true) or 'bad' (false).
     * @access private
     */
    function _done()
    {
        $result = true;
        if (Image_Graph_Element::_done() === false) {
            $result = false;
        }
        
        $this->_driver->startGroup(get_class($this));
        
        $this->_drawAxisLines();
        
        $this->_driver->startGroup(get_class($this) . '_ticks');
        $label = false;
        while (($label = $this->_getNextLabel($label)) !== false) {
            $this->_drawTick($label);
        }
        $this->_driver->endGroup();       

        $this->_driver->endGroup();
        return $result;
    }

}

?>