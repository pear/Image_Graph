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
 * @subpackage Plot     
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
 * Framework for a chart
 * @abstract 
 */
class Image_Graph_Plot extends Image_Graph_Plotarea_Element 
{

    /**    
     * The dataset to plot
     * @var Dataset
     * @access private
     */
    var $_dataset;

    /**
     * The marker to plot the data set as
     * @var Marker
     * @access private
     */
    var $_marker = null;

    /**
     * The dataselector to use for data marking
     * @var DataSelector
     * @access private
     */
    var $_dataSelector = null;

    /**
     * The Y axis to associate the plot with
     * @var int
     * @access private
     */
    var $_axisY = IMAGE_GRAPH_AXIS_Y;

    /**
     * The type of the plot if multiple datasets are used
     * @var string
     * @access private
     */
    var $_multiType = 'normal'; 

    /**
     * PlotType [Constructor]
     * Possible values for multiType are:
     * 'normal' Plot is normal, multiple datasets are displayes next to one another
     * 'stacked' Datasets are stacked on top of each other
     * 'stacked100pct' Datasets are stacked and displayed as percentages of the total sum    
     * @param Dataset $dataset The data set (value containter) to plot
     * @param string $multiType The type of the plot
     * @param string $title The title of the plot (used for legends, {@see Image_Graph_Legend})
     */
    function &Image_Graph_Plot(& $dataset, $multiType = 'normal', $title = '')
    {
        parent::Image_Graph_Common();
        if ($dataset) {
            if (is_array($dataset)) {            
                $this->_dataset = & $dataset;
            } else {
                $this->_dataset = array(&$dataset);
            }
        }
        if ($title) {
            $this->_title = $title;
        }
        $multiType = strtolower($multiType);
        if (($multiType == 'normal') or ($multiType == 'stacked') or ($multiType == 'stacked100pct')) {
            $this->_multiType = $multiType;
        } else {
            $this->_multiType = 'normal';
        }
    }

    /**
     * Sets the title of the plot, used for legend
     * @param string $title The title of the plot
     */
    function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Sets the Y axis to plot the data
     * @param int $axisY The Y axis (either IMAGE_GRAPH_AXIS_Y or IMAGE_GRAPH_AXIS_Y_SECONDARY (defaults to IMAGE_GRAPH_AXIS_Y))
     * @access private
     */
    function _setAxisY($axisY)
    {
        $this->_axisY = $axisY;
    }

    /**
     * Sets the marker to 'display' data points on the graph
     * @param Marker $marker The marker
     */
    function &setMarker(& $marker)
    {
        $this->add($marker);
        $this->_marker = & $marker;
        return $marker;
    }

    /**
     * Sets the dataselector to specify which data should be displayed on the plot as markers and which are not
     * @param DataSelector $dataSelector The dataselector
     */
    function setDataSelector(& $dataSelector)
    {
        $this->_dataSelector = & $dataSelector;
    }

    /**
     * Calculate marker point data
     * @param Array Point The point to calculate data for
     * @param Array NextPoint The next point
     * @param Array PrevPoint The previous point
     * @param Array Totals The pre-calculated totals, if needed
     * @return Array An array containing marker point data
     * @access private
     */
    function _getMarkerData($point, $nextPoint, $prevPoint, & $totals)
    {
        if (is_array($this->_dataset)) {   
            if ($this->_multiType == 'stacked') {
                if (!isset($totals['SUM_Y'])) {
                    $totals['SUM_Y'] = array();
                }
                $x = $point['X'];
                if (!isset($totals['SUM_Y'][$x])) {
                    $totals['SUM_Y'][$x] = 0;
                }
            } elseif ($this->_multiType == 'stacked100pct') {
                $x = $point['X'];
                if ($totals['TOTAL_Y'][$x] != 0) {
                    if (!isset($totals['SUM_Y'])) {
                        $totals['SUM_Y'] = array();
                    }
                    if (!isset($totals['SUM_Y'][$x])) {
                        $totals['SUM_Y'][$x] = 0;
                    }
                }
            }

            if (!$prevPoint) {
                $point['AX'] = -5;
                $point['AY'] = 5;
                $point['PPX'] = 0;
                $point['PPY'] = 0;
                $point['NPX'] = $nextPoint['X'];
                $point['NPY'] = $nextPoint['Y'];
            }
            elseif (!$nextPoint) {
                $point['AX'] = 5;
                $point['AY'] = 5;
                $point['PPX'] = $prevPoint['X'];
                $point['PPY'] = $prevPoint['Y'];
                $point['NPX'] = 0;
                $point['NPY'] = 0;
            } else {
                $point['AX'] = $this->_pointY($prevPoint) - $this->_pointY($nextPoint);
                $point['AY'] = $this->_pointX($nextPoint) - $this->_pointX($prevPoint);
                $point['PPX'] = $prevPoint['X'];
                $point['PPY'] = $prevPoint['Y'];
                $point['NPX'] = $nextPoint['X'];
                $point['NPY'] = $nextPoint['Y'];
            }
            
            $point['APX'] = $point['X'];
            $point['APY'] = $point['Y'];
    
            if ($totals['MINIMUM_X'] != 0) {
                $point['PCT_MIN_X'] = 100 * $point['X'] / $totals['MINIMUM_X'];
            }
            if ($totals['MAXIMUM_X'] != 0) {
                $point['PCT_MAX_X'] = 100 * $point['X'] / $totals['MAXIMUM_X'];
            }
    
            if ($totals['MINIMUM_Y'] != 0) {
                $point['PCT_MIN_Y'] = 100 * $point['Y'] / $totals['MINIMUM_Y'];
            }
            if ($totals['MAXIMUM_Y'] != 0) {
                $point['PCT_MAX_Y'] = 100 * $point['Y'] / $totals['MAXIMUM_Y'];
            }
    
            $point['LENGTH'] = sqrt($point['AX'] * $point['AX'] + $point['AY'] * $point['AY']);
            if ((isset($point['LENGTH'])) and ($point['LENGTH'] != 0)) {
                $point['ANGLE'] = asin($point['AY'] / $point['LENGTH']);
            }
    
            if ((isset($point['AX'])) and ($point['AX'] > 0)) {
                $point['ANGLE'] = pi() - $point['ANGLE'];
            }
    
            $point['MARKER_X1'] = $this->_pointX($point) - $totals['WIDTH'] + $this->_space;
            $point['MARKER_X2'] = $this->_pointX($point) + $totals['WIDTH'] - $this->_space;
            $point['COLUMN_WIDTH'] = abs($point['MARKER_X2'] - $point['MARKER_X1']) / count($this->_dataset);
            $point['MARKER_X'] = $point['MARKER_X1'] + ($totals['NUMBER'] + 0.5) * $point['COLUMN_WIDTH'];
            $point['MARKER_Y'] = $this->_pointY($point);
   
            if ($this->_multiType == 'stacked') {
                $point['MARKER_X'] = ($point['MARKER_X1'] + $point['MARKER_X2']) / 2;
                // TODO How about the x-values here?
                $P1 = array('Y' => $totals['SUM_Y'][$x]);
                $P2 = array('Y' => $totals['SUM_Y'][$x] + $point['Y']);
                $point['MARKER_Y'] = ($this->_pointY($P1) + $this->_pointY($P2)) / 2;
            } elseif ($this->_multiType == 'stacked100pct') {
                $x = $point['X'];
                if ($totals['TOTAL_Y'][$x] != 0) {
                    $point['MARKER_X'] = ($point['MARKER_X1'] + $point['MARKER_X2']) / 2;
                    $P1 = array('Y' => 100 * $totals['SUM_Y'][$x] / $totals['TOTAL_Y'][$x]);
                    $P2 = array('Y' => 100 * ($totals['SUM_Y'][$x] + $point['Y']) / $totals['TOTAL_Y'][$x]);
                    $point['MARKER_Y'] = ($this->_pointY($P1) + $this->_pointY($P2)) / 2;
                } else {
                    $point = false;
                }
            }    
            return $point;            
        }
    }

    /**
     * Draws markers on the canvas
     * @access private
     */
    function _drawMarker()
    {
        if (($this->_marker) and (is_array($this->_dataset))) {
            reset($this->_dataset);
            
            $totals = $this->_getTotals();
            $totals['WIDTH'] = $this->width() / ($this->_maximumX() + 2) / 2;

            $keys = array_keys($this->_dataset);
            $number = 0;
            while (list ($ID, $key) = each($keys)) {
                $dataset = & $this->_dataset[$key];
                $totals['MINIMUM_X'] = $dataset->minimumX();
                $totals['MAXIMUM_X'] = $dataset->maximumX();
                $totals['MINIMUM_Y'] = $dataset->minimumY();
                $totals['MAXIMUM_Y'] = $dataset->maximumY();
                $totals['NUMBER'] = $number ++;
                $dataset->_reset();
                while ($point = $dataset->_next()) {
                    $prevPoint = $dataset->_nearby(-2);
                    $nextPoint = $dataset->_nearby();
                    
                    $x = $point['X'];
                    $y = $point['Y'];
                    if ((!is_object($this->_dataSelector)) or ($this->_dataSelector->select($point))) {
                        $point = $this->_getMarkerData($point, $nextPoint, $prevPoint, $totals);
                        if (is_array($point)) {
                            $this->_marker->_drawMarker($point['MARKER_X'], $point['MARKER_Y'], $point);
                        }
                    }
                    if (!isset($totals['SUM_Y'])) {
                        $totals['SUM_Y'] = array();
                    }
                    if (isset($totals['SUM_Y'][$x])) {
                        $totals['SUM_Y'][$x] += $y;
                    } else {
                        $totals['SUM_Y'][$x] = $y;
                    }
                }
            }
        }
    }

    /**
     * Get the minimum X value from the dataset
     * @return double The minimum X value
     * @access private
     */
    function _minimumX()
    {
        if (!is_array($this->_dataset)) {
            return 0;
        }

        $min = false;
        if (is_array($this->_dataset)) {
            $keys = array_keys($this->_dataset);
            while (list ($ID, $key) = each($keys)) {
                if ($min === false) {
                    $min = $this->_dataset[$key]->minimumX();
                } else {
                    $min = min($min, $this->_dataset[$key]->minimumX());
                }
            }
        }
        return $min;
    }

    /**
     * Get the maximum X value from the dataset
     * @return double The maximum X value
     * @access private
     */
    function _maximumX()
    {
        if (!is_array($this->_dataset)) {
            return 0;
        }

        $max = 0;
        if (is_array($this->_dataset)) {
            $keys = array_keys($this->_dataset);
            while (list ($ID, $key) = each($keys)) {
                $max = max($max, $this->_dataset[$key]->maximumX());
            }
        }
        return $max;
    }

    /**
     * Get the minimum Y value from the dataset
     * @return double The minimum Y value
     * @access private
     */
    function _minimumY()
    {
        if (!is_array($this->_dataset)) {
            return 0;
        }

        $min = false;
        if (is_array($this->_dataset)) {
            $keys = array_keys($this->_dataset);
            while (list ($ID, $key) = each($keys)) {
                if ($min === false) {
                    $min = $this->_dataset[$key]->minimumY();
                } else {
                    $min = min($min, $this->_dataset[$key]->minimumY());
                }
            }
        }
        return $min;

    }

    /**
     * Get the maximum Y value from the dataset
     * @return double The maximum Y value
     * @access private
     */
    function _maximumY()
    {
        if ($this->_multiType == 'stacked100pct') {
            return 100;
        }
        
        $maxY = 0;
        if (is_array($this->_dataset)) {
            reset($this->_dataset);            

            $keys = array_keys($this->_dataset);
            while (list ($ID, $key) = each($keys)) {
                $dataset = & $this->_dataset[$key];

                $dataset->_reset();
                while ($point = $dataset->_next()) {
                    if ($this->_multiType == 'stacked') {
                        $x = $point['X'];
                        if ((!isset($total)) or (!isset($total[$x]))) {
                            $maxY = ($total[$x] = $point['Y']);
                        } else {
                            $maxY = max($maxY, $total[$x] += $point['Y']);
                        }
                    } else {
                        $maxY = max($maxY, $point['Y']);
                    }
                }
            }
        }
        return $maxY;        
    }

    /**
     * Get the X pixel position represented by a value
     * @param double $point the value to get the pixel-point for  
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointX($point)
    {
        $point['AXIS_Y'] = $this->_axisY;
        return parent::_pointX($point);
    }

    /**
     * Get the Y pixel position represented by a value
     * @param double $point the value to get the pixel-point for  
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointY($point)
    {
        $point['AXIS_Y'] = $this->_axisY;
        return parent::_pointY($point);
    }    

    /**
     * Update coordinates
     * @access private
     */
    function _updateCoords()
    {
        $this->_setCoords($this->_parent->_plotLeft, $this->_parent->_plotTop, $this->_parent->_plotRight, $this->_parent->_plotBottom);
        parent::_updateCoords();
    }
    
    /**
     * Get the dataset
     * @return Image_Graph_Dataset The dataset(s)
     */
    function &dataset() {
        return $this->_dataset;       
    }
    
    /**
     * Calulate totals
     * return array An associated array with the totals
     */
    function _getTotals() {
        $keys = array_keys($this->_dataset);
        $total = array(
            'MINIMUM_X' => $this->_minimumX(),
            'MAXIMUM_X' => $this->_maximumX(),
            'MINIMUM_Y' => $this->_minimumY(),
            'MAXIMUM_Y' => $this->_maximumY()
        );
        $total['ALL_SUM_Y'] = 0;
        
        while (list ($ID, $key) = each($keys)) {
            $dataset = & $this->_dataset[$key];

            $dataset->_reset();
            while ($point = $dataset->_next()) {
                $x = $point['X'];
                $total['ALL_SUM_Y'] += $point['Y'];
                if (isset($total['TOTAL_Y'][$x])) {
                    $total['TOTAL_Y'][$x] += $point['Y'];
                } else {
                    $total['TOTAL_Y'][$x] = $point['Y'];
                }
                if (isset($total['TOTAL_X'][$x])) {
                    $total['TOTAL_X'][$x] += $point['X'];
                } else {
                    $total['TOTAL_X'][$x] = $point['X'];
                }
            }
        }
        return $total;
    }
        

    /**
      * Draw a sample for use with legend
      * @param int $x The x coordinate to draw the sample at
      * @param int $y The y coordinate to draw the sample at
      * @access private
      */
    function _legendSample($x, $y, &$font)
    {
        if (!is_array($this->_dataset)) {
            return false;
        }

        $size['Height'] = 0;
        $size['Width'] = $x;        
        if (is_array($this->_dataset)) {
            if (is_a($this->_fillStyle, 'Image_Graph_Fill')) {
                $this->_fillStyle->_reset();
            }

            $count = 0;
            $keys = array_keys($this->_dataset);
            while (list ($ID, $key) = each($keys)) {
                $dataset =& $this->_dataset[$key];
                $count++;
                if (is_a($this->_fillStyle, 'Image_Graph_Fill')) {
                    $fillStyle = $this->_fillStyle->_getFillStyleAt($x -5, $y -5, 10, 10, $key);
                } else {
                    $fillStyle = $this->_getFillStyle($key);
                }
                if ($fillStyle != IMG_COLOR_TRANSPARENT) {
                    ImageFilledRectangle($this->_canvas(), $x -5, $y -5, $x +5, $y +5, $fillStyle);
                    ImageRectangle($this->_canvas(), $x -5, $y -5, $x +5, $y +5, $this->_getLineStyle());
                } else {
                    ImageLine($this->_canvas(), $x -7, $y, $x +7, $y, $this->_getLineStyle());
                }
                if (($this->_marker) and ($dataset)) {
                    $dataset->_reset();
                    $point = $dataset->_next();
                    $prevPoint = $dataset->_nearby(-2);
                    $nextPoint = $dataset->_nearby();
        
                    $point = $this->_getMarkerData($point, $nextPoint, $prevPoint, $i);
                    if (is_array($point)) {
                        $point['MARKER_X'] = $x;
                        $point['MARKER_Y'] = $y;
                        unset ($point['AVERAGE_Y']);
                        $this->_marker->_drawMarker($point['MARKER_X'], $point['MARKER_Y'], $point);
                    }
                }

                $old_font =& $this->_font;
                $this->_font =& $font;              
                $caption = ($dataset->_name ? $dataset->_name : $this->_title);
                $this->write($x + 30, $y, $caption, IMAGE_GRAPH_ALIGN_CENTER_Y | IMAGE_GRAPH_ALIGN_LEFT);
                $this->_font =& $old_font;

                $x += 40+$font->width($caption);
                $size['Height'] = max($size['Height'], 10, $font->height($caption));                
            }
        }
        $size['Width'] = $x-$size['Width'];        
        return $size;
    }

}

?>