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
class Image_Graph_Plot_Band extends Image_Graph_Plot 
{

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
        $h = abs($y1 - $y0) / 6;
        $w = round(abs($x1 - $x0) / 5);
        $y = ($y0 + $y1) / 2;
        
        $this->_driver->polygonAdd($x0, $y - $h * 3); 
        $this->_driver->polygonAdd($x0 + $w, $y - 4 * $h); 
        $this->_driver->polygonAdd($x0 + 2 * $w, $y - $h * 2); 
        $this->_driver->polygonAdd($x0 + 3 * $w, $y - $h * 4); 
        $this->_driver->polygonAdd($x0 + 4 * $w, $y - $h * 3); 
        $this->_driver->polygonAdd($x1, $y - $h * 2); 
        $this->_driver->polygonAdd($x1, $y + $h * 3); 
        $this->_driver->polygonAdd($x0 + 4 * $w, $y + $h); 
        $this->_driver->polygonAdd($x0 + 3 * $w, $y + 2 * $h); 
        $this->_driver->polygonAdd($x0 + 2 * $w, $y + 1 * $h); 
        $this->_driver->polygonAdd($x0 + 1 * $w, $y); 
        $this->_driver->polygonAdd($x0, $y + $h); 
        
        $this->_getLineStyle();
        $this->_getFillStyle();
        $this->_driver->polygonEnd();
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
        
        $current = array();

        $keys = array_keys($this->_dataset);
        foreach ($keys as $key) {
            $dataset =& $this->_dataset[$key];
            $dataset->_reset();
            $upperBand = array();
            $lowerBand = array();            
            while ($data = $dataset->_next()) {
                $point['X'] = $data['X'];                
                $y = $data['Y'];
                
                $point['Y'] = $data['Y']['high'];
                $x = $this->_pointX($point);                
                $y_high = $this->_pointY($point);                 

                $point['Y'] = $data['Y']['low'];
                $y_low = $this->_pointY($point);
                
                $upperBand[] = array('X' => $x, 'Y' => $y_high);
                $lowerBand[] = array('X' => $x, 'Y' => $y_low);                
            }
            $lowerBand = array_reverse($lowerBand);
            foreach ($lowerBand as $point) {
                $this->_driver->polygonAdd($point['X'], $point['Y']);
            }
            foreach ($upperBand as $point) {
                $this->_driver->polygonAdd($point['X'], $point['Y']);
            }
            unset($upperBand);
            unset($lowerBand);
            
            $this->_getLineStyle();
            $this->_getFillStyle($key);
            $this->_driver->polygonEnd();
        }
        unset($keys);
        $this->_drawMarker();
    }

}

?>