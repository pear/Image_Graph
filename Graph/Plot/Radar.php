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
 * Radar chart.
 *               
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 */
class Image_Graph_Plot_Radar extends Image_Graph_Plot 
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
        $p = 10;
        $rx = abs($x1 - $x0) / 2;
        $ry = abs($x1 - $x0) / 2;
        $r = min($rx, $ry);
        $cx = ($x0 + $x1) / 2;
        $cy = ($y0 + $y1) / 2;
        $max = 5;        
        for ($i = 0; $i < $p; $i++) {
            $v = 2 * pi() * $i / $p;
            $t = $r * rand(3, $max) / $max;
            $x = $cx + $t * cos($v);
            $y = $cy + $t * sin($v);
            $this->_driver->polygonAdd($x, $y);
        }
        $this->_driver->polygonEnd();
    }
    
    /**
     * Output the plot
     * @access private
     */
    function _done()
    {
        if (is_a($this->_parent, 'Image_Graph_Plotarea_Radar')) {
            $keys = array_keys($this->_dataset);
            foreach ($keys as $key) {
                $dataset = & $this->_dataset[$key];                    
                $maxY = $dataset->maximumY();
                $count = $dataset->count();

                $dataset->_reset();
                while ($point = $dataset->_next()) {
                    $this->_driver->polygonAdd(
                        $this->_pointX($point), 
                        $this->_pointY($point)
                    );
                }
                $this->_getFillStyle($key);
                $this->_getLineStyle($key);
                $this->_driver->polygonEnd();
            }
            unset($keys);
        }
        $this->_drawMarker();
        
        return parent::_done();
    }

}

?>