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
 * 2D Piechart.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 */
class Image_Graph_Plot_Pie extends Image_Graph_Plot
{

    /**
     * The radius of the 'pie' spacing
     * @access private
     * @var int
     */
    var $_radius = 3;

    /**
     * Explode pie slices.
     * @access private
     * @var mixed
     */
    var $_explode = false;

    /**
     * Perform the actual drawing on the legend.
     *
     * @param int $x0 The top-left x-coordinate
     * @param int $y0 The top-left y-coordinate
     * @param int $x1 The bottom-right x-coordinate
     * @param int $y1 The bottom-right y-coordinate
     * @access private
     */
    function _drawLegendSample($x0, $y0, $x1, $y1)
    {
        $y = ($y0 + $y1) / 2;
        $this->_driver->pieSlice($x1, $y, abs($x1 - $x0) / 2, abs($y1 - $y0) / 2, 45, 315);
    }

    /**
     * Calculate marker point data
     *
     * @param array $point The point to calculate data for
     * @param array $nextPoint The next point
     * @param array $prevPoint The previous point
     * @param array $totals The pre-calculated totals, if needed
     * @return array An array containing marker point data
     * @access private
     */
    function _getMarkerData($point, $nextPoint, $prevPoint, &$totals)
    {
        $point = parent::_getMarkerData($point, $nextPoint, $prevPoint, &$totals);

        $point['ANGLE'] = 360 * (($totals['CURRENT_Y'] +
            ($point['Y'] / 2)) / $totals['ALL_SUM_Y']);
        $totals['CURRENT_Y'] += $point['Y'];

        $point['ANG_X'] = cos(deg2rad($point['ANGLE']));
        $point['ANG_Y'] = sin(deg2rad($point['ANGLE']));

        $point['AX'] = -10 * $point['ANG_X'];
        $point['AY'] = -10 * $point['ANG_Y'];

        if ((isset($totals['ALL_SUM_Y'])) && ($totals['ALL_SUM_Y'] != 0)) {
            $point['PCT_MIN_Y'] = $point['PCT_MAX_Y'] = (100 * $point['Y'] / $totals['ALL_SUM_Y']);
        }

        $point['LENGTH'] = 10; //$radius;

        $x = $point['X'];
        $explodeRadius = 0;
        if ((is_array($this->_explode)) && (isset($this->_explode[$x]))) {
            $explodeRadius = $this->_explode[$x];
        } elseif (is_numeric($this->_explode)) {
            $explodeRadius = $this->_explode;
        }

        $point['MARKER_X'] = $totals['CENTER_X'] +
            ($totals['RADIUS'] + $explodeRadius) * $point['ANG_X'];
        $point['MARKER_Y'] = $totals['CENTER_Y'] +
            ($totals['RADIUS'] + $explodeRadius) * $point['ANG_Y'];

        return $point;
    }

    /**
     * Draws markers on the canvas
     *
     * @access private
     */
    function _drawMarker()
    {

        if ($this->_marker) {
            $totals = $this->_getTotals();

            $totals['CENTER_X'] = (int) (($this->_left + $this->_right) / 2);
            $totals['CENTER_Y'] = (int) (($this->_top + $this->_bottom) / 2);

            $totals['CURRENT_Y'] = 0;
            $number = 0;
            $diameter = min($this->height(), $this->width()) * 0.75;
            $keys = array_keys($this->_dataset);
            foreach ($keys as $key) {
                $dataset = & $this->_dataset[$key];

                if (count($this->_dataset) == 1) {
                    $totals['RADIUS0'] = false;
                    $totals['RADIUS'] = $diameter / 2;
                } else {
                    $dr = $diameter / (2 * count($this->_dataset));

                    $totals['RADIUS0'] = $number * $dr + ($number > 0 ? $this->_radius : 0);
                    $totals['RADIUS'] = ($number + 1) * $dr;
                }

                $totals['ALL_SUM_Y'] = 0;
                $totals['CURRENT_Y'] = 0;
                $dataset->_reset();
                while ($point = $dataset->_next()) {
                    $totals['ALL_SUM_Y'] += $point['Y'];
                }

                $dataset->_reset();
                $currentY = 0;
                while ($point = $dataset->_next()) {
                    if ((!is_object($this->_dataSelector)) ||
                         ($this->_dataSelector->select($point))
                    ) {
                        $point = $this->_getMarkerData(
                            $point,
                            false,
                            false,
                            $totals
                        );
                        if (is_array($point)) {
                            $this->_marker->_drawMarker(
                                $point['MARKER_X'],
                                $point['MARKER_Y'],
                                $point
                            );
                        }
                    }
                }
                $number++;
            }
            unset($keys);
        }
    }

    /**
     * Explodes a piece of this pie chart
     *
     * @param int $explode Radius to explode with (or an array)
     * @param string $x The 'x' value to explode or omitted
     */
    function explode($explode, $x = false)
    {
        if ($x === false) {
            $this->_explode = $explode;
        } else {
            $this->_explode[$x] = $explode;
        }
    }

    /**
     * Output the plot
     *
     * @return bool Was the output 'good' (true) or 'bad' (false).
     * @access private
     */
    function _done()
    {
        if (parent::_done() === false) {
            return false;
        }

        $number = 0;

        $keys = array_keys($this->_dataset);
        foreach ($keys as $key) {
            $dataset = & $this->_dataset[$key];

            $totalY = 0;
            $dataset->_reset();
            while ($point = $dataset->_next()) {
                $totalY += $point['Y'];
            }

            $centerX = (int) (($this->_left + $this->_right) / 2);
            $centerY = (int) (($this->_top + $this->_bottom) / 2);
            $diameter = min($this->height(), $this->width()) * 0.75;
            $currentY = 0; //rand(0, 100)*$totalY/100;
            $dataset->_reset();

            if (count($this->_dataset) == 1) {
                $radius0 = false;
                $radius1 = $diameter / 2;
            } else {
                $dr = $diameter / (2 * count($this->_dataset));

                $radius0 = $number * $dr + ($number > 0 ? $this->_radius : 0);
                $radius1 = ($number + 1) * $dr;
            }

            while ($point = $dataset->_next()) {
                $angle1 = 360 * ($currentY / $totalY);
                $currentY += $point['Y'];
                $angle2 = 360 * ($currentY / $totalY);

                $x = $point['X'];
                $id = $point['ID'];

                $dX = 0;
                $dY = 0;
                $explodeRadius = 0;
                if ((is_array($this->_explode)) && (isset($this->_explode[$x]))) {
                    $explodeRadius = $this->_explode[$x];
                } elseif (is_numeric($this->_explode)) {
                    $explodeRadius = $this->_explode;
                }

                if ($explodeRadius > 0) {
                    $dX = $explodeRadius * cos(deg2rad(($angle1 + $angle2) / 2));
                    $dY = $explodeRadius * sin(deg2rad(($angle1 + $angle2) / 2));
                }

                $ID = $point['ID'];
                $this->_getFillStyle($ID);
                $this->_getLineStyle($ID);
                $this->_driver->pieSlice($centerX + $dX, $centerY + $dY, $radius1, $radius1, $angle1, $angle2, $radius0, $radius0);
            }
            $number++;
        }
        unset($keys);
        $this->_drawMarker();
        return true;
    }

    /**
     * Draw a sample for use with legend
     *
     * @param array $param The parameters for the legend
     * @access private
     */
    function _legendSample(&$param)
    {
        if (is_array($this->_dataset)) {
            $totals = $this->_getTotals();
            $totals['CENTER_X'] = (int) (($this->_left + $this->_right) / 2);
            $totals['CENTER_Y'] = (int) (($this->_top + $this->_bottom) / 2);
            $totals['RADIUS'] = min($this->height(), $this->width()) * 0.75 * 0.5;
            $totals['CURRENT_Y'] = 0;
    
            $count = 0;
            $keys = array_keys($this->_dataset);
            foreach ($keys as $key) {
                $dataset =& $this->_dataset[$key];
                $count++;
    
                $dataset->_reset();
                while ($point = $dataset->_next()) {
                    $caption = $point['X'];
    
                    $this->_driver->setFont($param['font']);
                    $x2 = $param['x'] + 20 + $param['width'] + $this->_driver->textWidth($caption);
                    $y2 = $param['y'] + $param['height']+5;
    
                    if ((($param['align'] & IMAGE_GRAPH_ALIGN_VERTICAL) != 0) && ($y2 > $param['bottom'])) {
                        $param['y'] = $param['top'];
                        $param['x'] = $x2;
                        $y2 = $param['y'] + $param['height'];
                    } elseif ((($param['align'] & IMAGE_GRAPH_ALIGN_VERTICAL) == 0) && ($x2 > $param['right'])) {
                        $param['x'] = $param['left'];
                        $param['y'] = $y2;
                        $x2 = $param['x'] + 20 + $param['width'] + $this->_driver->textWidth($caption);
                    }
    
                    $x = $x0 = $param['x'];
                    $y = $param['y'];
                    $y0 = $param['y'] - $param['height']/2;
                    $x1 = $param['x'] + $param['width'];
                    $y1 = $param['y'] + $param['height']/2;
    
                    if (!isset($param['simulate'])) {
                        $this->_getFillStyle($point['ID']);
                        $this->_getLineStyle($point['ID']);
                        $this->_drawLegendSample($x0, $y0, $x1, $y1);
    
                        if (($this->_marker) && ($dataset) && ($param['show_marker'])) {
                            $prevPoint = $dataset->_nearby(-2);
                            $nextPoint = $dataset->_nearby();
    
                            $p = $this->_getMarkerData($point, $nextPoint, $prevPoint, $totals);
                            if (is_array($point)) {
                                $p['MARKER_X'] = $x+$param['width']/2;
                                $p['MARKER_Y'] = $y;
                                unset ($p['AVERAGE_Y']);
                                $this->_marker->_drawMarker($p['MARKER_X'], $p['MARKER_Y'], $p);
                            }
                        }
                        $this->write($x + $param['width'] +10, $y, $caption, IMAGE_GRAPH_ALIGN_CENTER_Y | IMAGE_GRAPH_ALIGN_LEFT, $param['font']);
                    }
    
                    if (($param['align'] & IMAGE_GRAPH_ALIGN_VERTICAL) != 0) {
                        $param['y'] = $y2;
                    } else {
                        $param['x'] = $x2;
                    }
                }
            }
            unset($keys);
        }
    }

}

?>