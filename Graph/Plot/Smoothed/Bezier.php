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
 * Bezier smoothed plottype.
 *
 * The framework for calculating the Bezier smoothed curve from the dataset.
 * Used in {@link Image_Graph_Plot_Smoothed_Line} and {@link
 * Image_Graph_Plot_Smoothed_Area}. Smoothed charts are only supported with non-
 * stacked types
 * @link http://homepages.borland.com/efg2lab/Graphics/Jean-
 * YvesQueinecBezierCurves.htm efg computer lab - description of bezier curves
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 * @abstract
 */
class Image_Graph_Plot_Smoothed_Bezier extends Image_Graph_Plot
{

    /**
     * The Bezier smooth factor, varying from 0 (straight line) to 1,5 default:
     * 0,75, 1,05 produces a circle from a square
     * @var int
     * @access private
     */
    var $_smoothFactor = 0.75;

    /**
     * Number of points to calculate between any two points on the Bezier
     * smoothed curve, the higher the slower but more smoothed, default: 50
     * @var int
     * @access private
     */
    var $_bezierCurvePoints = 50;

    /**
     * Image_Graph_Plot_Smoothed_Bezier [Constructor]
     *
     * Only 'normal' multitype supported
     *
     * @param Dataset $dataset The data set (value containter) to plot
     * @param string $title The title of the plot (used for legends, {@link
     *   Image_Graph_Legend})
     */
    function &Image_Graph_Plot_Smoothed_Bezier(& $dataset, $title = '')
    {
        parent::Image_Graph_Plot($dataset, 'normal', $title);
    }

    /**
     * Return the minimum Y point
     *
     * @return double The minumum Y point
     * @access private
     */
    function _minimumY()
    {
        return 1.05 * parent::_minimumY();
    }

    /**
     * Return the maximum Y point
     *
     * @return double The maximum Y point
     * @access private
     */
    function _maximumY()
    {
        return 1.05 * parent::_maximumY();
    }

    /**
     * Return the average of 2 points
     *
     * @param double P1 1st point
     * @param double P2 2nd point
     * @return double The average of P1 and P2
     * @access private
     */
    function _mid($p1, $p2)
    {
        return ($p1 + $p2) / 2;
    }

    /**
     * Mirrors P1 in P2 by a amount of Factor
     *
     * @param double P1 1st point, point to mirror
     * @param double P2 2nd point, mirror point
     * @param double Factor Mirror factor, 0 returns P2, 1 returns a pure
     *   mirror, ie P1 on the exact other side of P2
     * @return double P1 mirrored in P2 by Factor
     * @access private
     */
    function _mirror($p1, $p2, $factor = 1)
    {
        return $p2 + $factor * ($p2 - $p1);
    }

    /**
     * Calculates a Bezier control point, this function must be called for BOTH
     * X and Y coordinates (will it work for 3D coordinates!?)
     *
     * @param double $p1 1st point
     * @param double $p2 Point to
     * @param double $factor Mirror factor, 0 returns P2, 1 returns a pure
     *   mirror, i.e. P1 on the exact other side of P2
     * @return double P1 mirrored in P2 by Factor
     * @access private
     */
    function _controlPoint($p1, $p2, $factor)
    {
        $sa = $this->_mirror($p1, $p2, $this->_smoothFactor);
        $sb = $this->_mid($p2, $sa);

        $m = $this->_mid($p2, $factor);

        $pC = $this->_mid($sb, $m);

        return $pC;
    }

    /**
     * Calculates a Bezier point, this function must be called for BOTH X and Y
     * coordinates (will it work for 3D coordinates!?)
     *
     * @param double t A position between P2 and P3, value between 0 and 1
     * @param double P1 Point to use for calculating control points
     * @param double P2 Point 1 to calculate bezier curve between
     * @param double P3 Point 2 to calculate bezier curve between
     * @param double P4 Point to use for calculating control points
     * @return double The bezier value of the point t between P2 and P3 using P1
     *   and P4 to calculate control points
     * @access private
     */
    function _bezier($t, $p1, $p2, $p3, $p4)
    {
        return pow(1 - $t, 3) * $p1 +
            3 * pow(1 - $t, 2) * $t * $p2 +
            3 * (1 - $t) * pow($t, 2) * $p3 +
            pow($t, 3) * $p4;
    }

    /**
     * Calculates all Bezier points, for the curve
     *
     * @param bool IncludeEdges Specifies if the edges should be calculated as
     *   well, default: false
     * @return array Array of Bezier points
     * @access private
     */
    function _getPoints(&$dataset, $includeEdges = false)
    {
        if ($includeEdges) {
            $dataset->_reset();
            $point = $dataset->_next();
            //$point['X'] = $this->_dataset->minimumX();
            $point['Y'] = 0;
            $plotarea[] = $this->_pointX($point);
            $bottomY = $plotarea[] = $this->_pointY($point);
        }

        $maxX = 0;
        $dataset->_reset();
        while ($p1 = $dataset->_next()) {
            $p0 = $dataset->_nearby(-2);
            $p2 = $dataset->_nearby(0);
            $p3 = $dataset->_nearby(1);

            $p1 = $this->_pointXY($p1);
            $maxX = max($p1['X'], $maxX);
            if ($p2) {
                $p2 = $this->_pointXY($p2);
                $maxX = max($p2['X'], $maxX);
            }
            if (!$p0) {
                $p0['X'] = $p1['X'] - abs($p2['X'] - $p1['X']);
                $p0['Y'] = $p1['Y']; //-($p2['Y']-$p1['Y']);
            } else {
                $p0 = $this->_pointXY($p0);
                $maxX = max($p0['X'], $maxX);
            }
            if (!$p3) {
                $p3['X'] = $p1['X'] + 2*abs($p1['X'] - $p0['X']);
                $p3['Y'] = $p1['Y'];
            } else {
                $p3 = $this->_pointXY($p3);
                $maxX = max($p3['X'], $maxX);
            }

            if (!$p2) {
                $p2['X'] = $p1['X'] + abs($p1['X'] - $p0['X']);
                $p2['Y'] = $p1['Y'];
            }

            $pC1['X'] = $this->_controlPoint($p0['X'], $p1['X'], $p2['X']);
            $pC1['Y'] = $this->_controlPoint($p0['Y'], $p1['Y'], $p2['Y']);
            $pC2['X'] = $this->_controlPoint($p3['X'], $p2['X'], $p1['X']);
            $pC2['Y'] = $this->_controlPoint($p3['Y'], $p2['Y'], $p1['Y']);

            $interval = 1 / $this->_bezierCurvePoints;
            for ($t = 0; $t <= 1; $t = $t + $interval) {
                $b0['X'] = $this->_bezier(
                    $t,
                    $p1['X'],
                    $pC1['X'],
                    $pC2['X'],
                    $p2['X']
                );

                $b0['Y'] = $this->_bezier(
                    $t,
                    $p1['Y'],
                    $pC1['Y'],
                    $pC2['Y'],
                    $p2['Y']
                );

                $b = $b0;

                if (($b['X'] >= $this->_left) && ($b['X'] <= $this->_right) &&
                    ($b['Y'] >= $this->_top) && ($b['Y'] <= $this->_bottom) &&
                    ($b['X'] <= $maxX))
                {
                    $finalX = $b['X'];
                    $plotarea[] = $b['X'];
                    $plotarea[] = $b['Y'];
                }
            }
        }

        if ($includeEdges) {
            $plotarea[] = $finalX;
            $plotarea[] = $bottomY;
        }

        return $plotarea;
    }

    /**
     * Calculates all Bezier points, for the curve
     *
     * @param bool IncludeEdges Specifies if the edges should be calculated as
     *   well, default: false
     * @return array Array of Bezier points
     * @access private
     */
    function _getControlPoints($p1, $p0, $p2, $p3)
    {
        $p1 = $this->_pointXY($p1);
        if ($p2) {
            $p2 = $this->_pointXY($p2);
        }
        if (!$p0) {
            $p0['X'] = $p1['X'] - abs($p2['X'] - $p1['X']);
            $p0['Y'] = $p1['Y']; //-($p2['Y']-$p1['Y']);
        } else {
            $p0 = $this->_pointXY($p0);
        }
        if (!$p3) {
            $p3['X'] = $p1['X'] + 2*abs($p1['X'] - $p0['X']);
            $p3['Y'] = $p1['Y'];
        } else {
            $p3 = $this->_pointXY($p3);
        }

        if (!$p2) {
            $p2['X'] = $p1['X'] + abs($p1['X'] - $p0['X']);
            $p2['Y'] = $p1['Y'];
        }

        $pC1['X'] = $this->_controlPoint($p0['X'], $p1['X'], $p2['X']);
        $pC1['Y'] = $this->_controlPoint($p0['Y'], $p1['Y'], $p2['Y']);
        $pC2['X'] = $this->_controlPoint($p3['X'], $p2['X'], $p1['X']);
        $pC2['Y'] = $this->_controlPoint($p3['Y'], $p2['Y'], $p1['Y']);

        return array(
            'X' => $p1['X'],
            'Y' => $p1['Y'],
            'P1X' => $pC1['X'],
            'P1Y' => $pC1['Y'],
            'P2X' => $pC2['X'],
            'P2Y' => $pC2['Y']
        );
    }

    /**
     * Create legend sample data for the driver.
     *
     * Common for all smoothed plots
     *
     * @access private
     */
    function _addSamplePoints($x0, $y0, $x1, $y1)
    {
        $p = abs($x1 - $x0);
        $cy = ($y0 + $y1) / 2;
        $h = abs($y1 - $y0);
        $dy = $h / 4;
        $dw = abs($x1 - $x0) / $p;
        for ($i = 0; $i < $p; $i++) {
            $v = 2 * pi() * $i / $p;
            $x = $x0 + $i * $dw;
            $y = $cy + 2 * $v * sin($v);
            $this->_driver->polygonAdd($x, $y);
        }
    }

}

?>