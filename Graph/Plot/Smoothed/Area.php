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
 * Bezier smoothed area chart
 * 
 * Similar to an {@link Image_Graph_Plot_Area}, but the interconnecting lines
 * between two datapoints are smoothed using a Bezier curve, which enables the
 * chart to appear as a nice curved plot instead of the sharp edges of a
 * conventional {@link Image_Graph_Plot_Area}. Smoothed charts are only supported
 * with non-stacked types
 *              
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 */
class Image_Graph_Plot_Smoothed_Area extends Image_Graph_Plot_Smoothed_Bezier 
{

    /**
     * Perform the actual drawing on the legend.
     * @param int $x0 The top-left x-coordinate
     * @param int $y0 The top-left y-coordinate
     * @param int $x1 The bottom-right x-coordinate
     * @param int $y1 The bottom-right y-coordinate
     */
    function _drawLegendSample($x0, $y0, $x1, $y1)
    {
        
        $this->_driver->polygonAdd($x0, $y1);
        $this->_addSamplePoints($x0, $y0, $x1, $y1);
        $this->_driver->polygonAdd($x1, $y1);
        $this->_driver->polygonEnd();
    }

    /**
     * Output the Bezier smoothed plot as an Area Chart
     * @access private
     */
    function _done()
    {
        if (parent::_done() === false) {
            return false;
        }

        $keys = array_keys($this->_dataset);
        foreach ($keys as $key) {
            $dataset = & $this->_dataset[$key];
            $dataset->_reset();
            $first = true;
            while ($p1 = $dataset->_next()) {
                $p0 = $dataset->_nearby(-2);
                $p2 = $dataset->_nearby(0);
                $p3 = $dataset->_nearby(1);
                if ($first) {
                    $p = $p1;
                    $p['Y'] = '#min_pos#';
                    $x = $this->_pointX($p);
                    $y = $this->_pointY($p);
                    $this->_driver->polygonAdd($x, $y);
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
                $lastPoint = $p1;
                $first = false;
            }
            $lastPoint['Y'] = '#min_pos#';
            $x = $this->_pointX($lastPoint);
            $y = $this->_pointY($lastPoint);
            $this->_driver->polygonAdd($x, $y);

            $this->_getFillStyle($key);
            $this->_getLineStyle($key);
            $this->_driver->splineEnd(true);            
        }
        unset($keys);
        $this->_drawMarker();
    }

}

?>