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
 * @subpackage Plot     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Plot.php
 */
require_once 'Image/Graph/Plot.php';

/**
 * A bar chart.
 *               
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot 
 */
class Image_Graph_Plot_Bar extends Image_Graph_Plot 
{

    /**
     * The space between 2 bars (should be a multipla of 2)
     * @var int
     * @access private
     */
    var $_space = 4;

    /**
     * The width of the bars
     * @var array
     * @access private
     */
    var $_width = 'auto';
    
    /**
     * Perform the actual drawing on the legend.
     * @param int $x0 The top-left x-coordinate
     * @param int $y0 The top-left y-coordinate
     * @param int $x1 The bottom-right x-coordinate
     * @param int $y1 The bottom-right y-coordinate
     * @access private
     */
    function _drawLegendSample($x0, $y0, $x1, $y1)
    {
        $dx = abs($x1 - $x0) / 7;
        $this->_driver->rectangle($x0 + $dx, $y0, $x1 - $dx, $y1);
    }
    
    /**
     * Set the spacing between 2 neighbouring bars
     * @param int $space The number of pixels between 2 bars, should be a
     *   multipla of 2 (ie an even number)
     */
    function spacing($space)
    {
        $this->_space = (int) ($space / 2);
    }

    /**
     * Set the width of a bars.
     * 
     * Specify 'auto' to auto calculate the width based on the positions on the
     * x-axis.
     * 
     * Supported units are:
     * 
     * '%' The width is specified in percentage of the total plot width
     * 
     * 'px' The width specified in pixels
     * 
     * @param string $width The width of any bar
     * @param string $unit The unit of the width
     */
    function barWidth($width, $unit = false)
    {
        if ($width == 'auto') {
            $this->_width = $width;
        } else {
            $this->_width = array(
                'width' => $width,
                'unit' => $unit
            );
        }
    }

    /**
     * Output the plot
     * @access private
     */
    function _done()
    {
        if (parent::_done() === false) {
            return false;
        }

        if (!is_array($this->_dataset)) {
            return false;
        }
        
        if ($this->_width == 'auto') {
            $width = $this->_parent->_labelDistance(IMAGE_GRAPH_AXIS_X) / 2;
        } elseif ($this->_width['unit'] == '%') {
            $width = $this->_width['width'] * $this->width() / 200;
        } elseif ($this->_width['unit'] == 'px') {
            $width = $this->_width['width'] / 2;
        }
        
        if ($this->_multiType == 'stacked100pct') {
            $total = $this->_getTotals();
        }

        $minYaxis = $this->_parent->_getMinimum($this->_axisY);
        $maxYaxis = $this->_parent->_getMaximum($this->_axisY);

        $number = 0;
        $keys = array_keys($this->_dataset);
        foreach ($keys as $key) {
            $dataset = & $this->_dataset[$key];
            $dataset->_reset();
            while ($point = $dataset->_next()) {                                
                
                $x1 = $this->_pointX($point) - $width + $this->_space;
                $x2 = $this->_pointX($point) + $width - $this->_space;

                if (($this->_multiType == 'stacked') || 
                    ($this->_multiType == 'stacked100pct')) 
                {                        
                    $x = $point['X'];
                                                            
                    if ($point['Y'] >= 0) {
                        if (!isset($current[$x])) {
                            $current[$x] = 0;
                        }
                        
                        if ($this->_multiType == 'stacked') {                        
                            $p0 = array(
                                'X' => $point['X'], 
                                'Y' => $current[$x]
                            );
                            $p1 = array(
                                'X' => $point['X'], 
                                'Y' => $current[$x] + $point['Y']
                            );
                        } else {
                            $p0 = array(
                                'X' => $point['X'], 
                                'Y' => 100 * $current[$x] / $total['TOTAL_Y'][$x]
                            );
                            $p1 = array(
                                'X' => $point['X'], 
                                'Y' => 100 * ($current[$x] + $point['Y']) / $total['TOTAL_Y'][$x]
                            );
                        }
                        $current[$x] += $point['Y'];
                    } else {
                        if (!isset($currentNegative[$x])) {
                            $currentNegative[$x] = 0;                        
                        }

                        $p0 = array(
                                'X' => $point['X'], 
                                'Y' => $currentNegative[$x]
                            );
                        $p1 = array(
                                'X' => $point['X'], 
                                'Y' => $currentNegative[$x] + $point['Y']
                            );
                        $currentNegative[$x] += $point['Y'];
                    }
                } else {
                    if (count($this->_dataset) > 1) {
                        $w = $width / count($this->_dataset);
                        $x2 = ($x1 = ($x1 + $x2 - $width) / 2 + $number * $w) + $w;
                    }
                    $p0 = array('X' => $point['X'], 'Y' => 0);
                    $p1 = $point;
                }
                
                if ((($minY = min($p0['Y'], $p1['Y'])) < $maxYaxis) && 
                    (($maxY = max($p0['Y'], $p1['Y'])) > $minYaxis)
                ) {
                    $p0['Y'] = $minY;
                    $p1['Y'] = $maxY;          
                              
                    if ($p0['Y'] < $minYaxis) {
                        $p0['Y'] = '#min_pos#';
                    }
                    if ($p1['Y'] > $maxYaxis) {
                        $p1['Y'] = '#max_neg#';
                    }                        
                    
                    $y1 = $this->_pointY($p0);
                    $y2 = $this->_pointY($p1);
    
                    if ($y1 != $y2) {
                        $ID = $point['ID'];
                        if (($ID === false) && (count($this->_dataset) > 1)) {
                            $ID = $key;
                        }              
                        $this->_getFillStyle($ID);
                        $this->_getLineStyle($ID);
                        $this->_driver->rectangle(
                            min($x1, $x2), 
                            min($y1, $y2), 
                            max($x1, $x2), 
                            max($y1, $y2)
                        );
                    }
                }
            }
            $number ++;
        }
        unset($keys);

        $this->_drawMarker();
    }
}

?>