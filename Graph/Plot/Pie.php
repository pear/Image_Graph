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
 * Include file Image/Graph/Plot.php
 */
require_once 'Image/Graph/Plot.php';

/**
 * 2D Piechart
 */
class Image_Graph_Plot_Pie extends Image_Graph_Plot 
{

    /**
     * The radius of the 'pie' spacing
     * @access private
     * @var int
     */
    var $_radius = 0;

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
        $point = parent::_getMarkerData($point, $nextPoint, $prevPoint, &$totals);
        
        $point['ANGLE'] = 360 * (($totals['CURRENT_Y'] + ($point['Y'] / 2)) / $totals['ALL_SUM_Y']);
        $totals['CURRENT_Y'] += $point['Y'];

        $point['ANG_X'] = cos(deg2rad($point['ANGLE']));
        $point['ANG_Y'] = sin(deg2rad($point['ANGLE']));

        $point['AX'] = -10 * $point['ANG_X'];
        $point['AY'] = -10 * $point['ANG_Y'];

//        if ((isset($totals['TOTAL_X'])) and ($totals['TOTAL_X'] != 0)) {
//            $point['PCT_MIN_X'] = $point['PCT_MAX_X'] = (100 * $point['X'] / $totals['TOTAL_X']);
//        }
        if ((isset($totals['ALL_SUM_Y'])) and ($totals['ALL_SUM_Y'] != 0)) {
            $point['PCT_MIN_Y'] = $point['PCT_MAX_Y'] = (100 * $point['Y'] / $totals['ALL_SUM_Y']);
        }

        $point['LENGTH'] = 10; //$radius;

        $point['MARKER_X'] = $totals['CENTER_X'] + ($this->_radius + $totals['RADIUS']) * $point['ANG_X'];
        $point['MARKER_Y'] = $totals['CENTER_Y'] + ($this->_radius + $totals['RADIUS']) * $point['ANG_Y'];

        return $point;
    }

    /**
     * Draws markers on the canvas
     * @access private
     */
    function _drawMarker()
    {

        if ($this->_marker) {            
            $totals = $this->_getTotals();

            $totals['CENTER_X'] = (int) (($this->_left + $this->_right) / 2);
            $totals['CENTER_Y'] = (int) (($this->_top + $this->_bottom) / 2);
            $totals['RADIUS'] = min($this->height(), $this->width()) * 0.75 * 0.5;

            
            $totals['CURRENT_Y'] = 0;
            reset($this->_dataset);
            $keys = array_keys($this->_dataset);
            while (list ($ID, $key) = each($keys)) {
                $dataset = & $this->_dataset[$key];

                $dataset->_reset();
                $currentY = 0;
                while ($point = $dataset->_next()) {
                    if ((!is_object($this->_dataSelector)) or ($this->_dataSelector->select($point))) {
                        $point = $this->_getMarkerData($point, false, false, $totals);
                        if (is_array($point)) {
                            $this->_marker->_drawMarker($point['MARKER_X'], $point['MARKER_Y'], $point);
                        }
                    }
                }
            }
        }
    }

    /**
     * Output the plot
     * @access private
     */
    function _done()
    {
        parent::_done();

        $keys = array_keys($this->_dataset);
        while (list ($ID, $key) = each($keys)) {
            $dataset = & $this->_dataset[$key];

            $dataset->_reset();
            while ($point = $dataset->_next()) {
                $totalY += $point['Y'];
            }

            $centerX = (int) (($this->_left + $this->_right) / 2);
            $centerY = (int) (($this->_top + $this->_bottom) / 2);
            $diameter = min($this->height(), $this->width()) * 0.75;
            $currentY = 0; //rand(0, 100)*$totalY/100;
            $dataset->_reset();

            while ($point = $dataset->_next()) {
                $angle1 = 360 * ($currentY / $totalY);
                $currentY += $point['Y'];
                $angle2 = 360 * ($currentY / $totalY);
                $dX = $diameter * ($this->_radius / 100) * cos(deg2rad(($angle1 + $angle2) / 2));
                $dY = $diameter * ($this->_radius / 100) * sin(deg2rad(($angle1 + $angle2) / 2));
                $dD = sqrt($dX * $dX + $dY * $dY);

                $polygon[] = $centerX;
                $polygon[] = $centerY;

                $angle = min($angle1, $angle2);
                $dA = 360 / (pi() * $diameter);
                while ($angle <= max($angle1, $angle2)) {
                    $polygon[] = ($centerX + ($diameter / 2) * cos(deg2rad($angle % 360)));
                    $polygon[] = ($centerY + ($diameter / 2) * sin(deg2rad($angle % 360)));
                    $angle += $dA;
                }
                if ($angle != max($angle1, $angle2)) {
                    $polygon[] = ($centerX + ($diameter / 2) * cos(deg2rad($angle2 % 360)));
                    $polygon[] = ($centerY + ($diameter / 2) * sin(deg2rad($angle2 % 360)));
                }
                //ImageFilledArc($this->_canvas(), $centerX+$dX, $centerY+$dY, $diameter-$dD, $diameter-$dD, $angle1 % 360, $angle2 % 360, $this->_getFillStyle(), IMG_ARC_PIE);
                //ImageFilledArc($this->_canvas(), $centerX+$dX, $centerY+$dY, $diameter-$dD, $diameter-$dD, $angle1 % 360, $angle2 % 360, $this->_getLineStyle(), IMG_ARC_NOFILL+IMG_ARC_EDGED);
                ImageFilledPolygon($this->_canvas(), $polygon, count($polygon) / 2, $this->_getFillStyle());
                ImagePolygon($this->_canvas(), $polygon, count($polygon) / 2, $this->_getLineStyle());

                unset ($polygon);
            }
        }
        //ImageEllipse($this->_canvas(), $centerX, $centerY, $diameter, $diameter, 0);
        $this->_drawMarker();
    }

}

?>