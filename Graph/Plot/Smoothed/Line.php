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
 * Include file Image/Graph/Plot/Smoothed/Bezier.php
 */
require_once 'Image/Graph/Plot/Smoothed/Bezier.php';

/**
 * Bezier smoothed line chart.
 *
 * Similar to a {@link Image_Graph_Plot_Line}, but the interconnecting lines
 * between two datapoints are smoothed using a Bezier curve, which enables the
 * chart to appear as a nice curved plot instead of the sharp edges of a
 * conventional {@link Image_Graph_Plot_Line}. Smoothed charts are only supported
 * with non-stacked types
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 */
class Image_Graph_Plot_Smoothed_Line extends Image_Graph_Plot_Smoothed_Bezier
{

    /**
     * Gets the fill style of the element
     *
     * @return int A GD filestyle representing the fill style
     * @see Image_Graph_Fill
     * @access private
     */
    function _getFillStyle($ID = false)
    {
        return IMG_COLOR_TRANSPARENT;
    }

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
        $this->_addSamplePoints($x0, $y0, $x1, $y1);
        $this->_driver->polygonEnd(false);
    }

    /**
     * Output the Bezier smoothed plot as an Line Chart
     *
     * @return bool Was the output 'good' (true) or 'bad' (false).
     * @access private
     */
    function _done()
    {
        if (parent::_done() === false) {
            return false;
        }

        $this->_driver->startGroup(get_class($this) . '_' . $this->_title);
        $keys = array_keys($this->_dataset);
        foreach ($keys as $key) {
            $dataset =& $this->_dataset[$key];
            $dataset->_reset();
            $numPoints = 0;
            while ($p1 = $dataset->_next()) {
                if ($p1['Y'] === null) {
                    if ($numPoints > 1) {
                        $this->_getLineStyle($key);
                        $this->_driver->splineEnd(false);
                    }
                    $numPoints = 0;
                } else {
                    $p0 = $dataset->_nearby(-2);
                    $p2 = $dataset->_nearby(0);
                    $p3 = $dataset->_nearby(1);

                    if (($p0) && ($p0['Y'] === null)) {
                        $p0 = false;
                    }
                    if (($p2) && ($p2['Y'] === null)) {
                        $p2 = false;
                    }
                    if (($p3) && ($p3['Y'] === null)) {
                        $p3 = false;
                    }

                    if ($p2) {
                        $cp = $this->_getControlPoints($p1, $p0, $p2, $p3);
                        $this->_driver->splineAdd(
                            $cp['X'],
                            $cp['Y'],
                            $cp['P1X'],
                            $cp['P1Y'],
                            $cp['P2X'],
                            $cp['P2Y']
                        );
                    } else {
                        $x = $this->_pointX($p1);
                        $y = $this->_pointY($p1);
                        $this->_driver->polygonAdd($x, $y);
                    }
                    $numPoints++;
                }
            }
            if ($numPoints > 1) {
                $this->_getLineStyle();
                $this->_driver->splineEnd(false);
            }
        }
        unset($keys);
        $this->_drawMarker();
        $this->_driver->endGroup();
        return true;
    }

}
?>