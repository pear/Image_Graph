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
 * @since 0.3.0dev2
 */ 

/**
 * Include file Image/Graph/Plot.php
 */
require_once 'Image/Graph/Plot.php';

/**
 * Impulse chart.
 *               
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 * @since 0.3.0dev2
 */
class Image_Graph_Plot_BoxWhisker extends Image_Graph_Plot 
{
    
    /**
     * Draws a box & whisker
     *
     * @param int $x The x position
     * @param int $w The width of the box
     * @param int $r The radius of the circle markers
     * @param int $y_min The Y position of the minimum value
     * @param int $y_q1 The Y position of the median of the first quartile
     * @param int $y_med The Y position of the median
     * @param int $y_q3 The Y position of the median of the third quartile
     * @param int $y_max The Y position of the maximum value
     * @param int $key The ID tag
     * @access private
     */
    function _drawBoxWhisker($x, $w, $r, $y_min, $y_q1, $y_med, $y_q3, $y_max, $key = false)
    {
        // draw circles                
        $this->_getLineStyle();
        $this->_getFillStyle('min');
        $this->_driver->ellipse($x, $y_min, $r, $r);

        $this->_getLineStyle();
        $this->_getFillStyle('quartile1');
        $this->_driver->ellipse($x, $y_q1, $r, $r);

        $this->_getLineStyle();
        $this->_getFillStyle('median');
        $this->_driver->ellipse($x, $y_med, $r, $r);

        $this->_getLineStyle();
        $this->_getFillStyle('quartile3');
        $this->_driver->ellipse($x, $y_q3, $r, $r);

        $this->_getLineStyle();
        $this->_getFillStyle('max');
        $this->_driver->ellipse($x, $y_max, $r, $r);
        
        // draw box and lines
        
        $this->_getLineStyle();
        $this->_driver->line($x, $y_min, $x, $y_q1);
        $this->_getLineStyle();
        $this->_driver->line($x, $y_q3, $x, $y_max);
        
        $this->_getLineStyle();
        $this->_getFillStyle('box');
        $this->_driver->rectangle($x - $w, $y_q1, $x + $w, $y_q3);

        $this->_getLineStyle();
        $this->_driver->line($x - $w, $y_med, $x + $w, $y_med);
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
        $x = round(($x0 + $x1) / 2);        
        $h = abs($y1 - $y0) / 9;
        $w = round(abs($x1 - $x0) / 5);
        $r = 2;//round(abs($x1 - $x0) / 13);
        $this->_drawBoxWhisker($x, $w, $r, $y1, $y1 - 2 * $h, $y1 - 4 * $h, $y0 + 3 * $h, $y0);
    }

    /**
     * Output the plot
     *
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
        
        if ($this->_multiType == 'stacked100pct') {
            $total = $this->_getTotals();
        }
        $current = array();
        $number = 0;        
        $width = floor(0.5 * $this->_parent->_labelDistance(IMAGE_GRAPH_AXIS_X) / 2);
        
        $keys = array_keys($this->_dataset);
        foreach ($keys as $key) {
            $dataset =& $this->_dataset[$key];
            $dataset->_reset();            
            while ($data = $dataset->_next()) {
                $point['X'] = $data['X'];                
                $y = $data['Y'];
                
                $min = min($y);
                $max = max($y);
                $q1 = $dataset->_median($y, 'first');
                $med = $dataset->_median($y, 'second');
                $q3 = $dataset->_median($y, 'third');
                
                $point['Y'] = $min;
                $x = $this->_pointX($point);                
                $y_min = $this->_pointY($point);                 

                $point['Y'] = $max;
                $y_max = $this->_pointY($point);                 

                $point['Y'] = $q1;
                $y_q1 = $this->_pointY($point);                 

                $point['Y'] = $med;
                $y_med = $this->_pointY($point);                 

                $point['Y'] = $q3;
                $y_q3 = $this->_pointY($point);
                
                $r = min(5, $width / 10);
                $this->_drawBoxWhisker($x, $width, $r, $y_min, $y_q1, $y_med, $y_q3, $y_max, $key);
            }
        }
        unset($keys);
        $this->_drawMarker();
    }

}

?>