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
 * Include file Image/Graph/Plot/Smoothed/Bezier.php
 */
require_once 'Image/Graph/Plot/Smoothed/Bezier.php';

/**
 * Smoothed radar chart.
 *               
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 * @since 0.3.0dev2
 */
class Image_Graph_Plot_Smoothed_Radar extends Image_Graph_Plot_Smoothed_Bezier 
{
    
    // TODO Create legend sample for smoothed radar chart
    
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
                if ($dataset->count() >= 3) {
                    $dataset->_reset();                
                    $p1_ = $dataset->_next();
                    $p2_ = $dataset->_next();
                    $p3_ = $dataset->_next();
                    $plast_ = false;
                    if ($p3_) {
                        while ($p = $dataset->_next()) {
                            $plast_ = $p;
                        }
                    }
                    
                    if ($plast_ === false) {
                        $plast_ = $p3_;
                    }                
                    $dataset->_reset();
                    while ($p1 = $dataset->_next()) {
                        $p0 = $dataset->_nearby(-2);                    
                        $p2 = $dataset->_nearby(0);
                        $p3 = $dataset->_nearby(1);
                        
                        if ($p0 === false) {
                            $p0 = $plast_;
                        }
                        
                        if ($p2 === false) {
                            $p2 = $p1_;
                            $p3 = $p2_;
                        } elseif ($p3 === false) {
                            $p3 = $p1_;
                        }
    
                                                
                        $cp = $this->_getControlPoints($p1, $p0, $p2, $p3);
                        $this->_driver->splineAdd(
                            $cp['X'], 
                            $cp['Y'], 
                            $cp['P1X'], 
                            $cp['P1Y'], 
                            $cp['P2X'], 
                            $cp['P2Y']
                        );
                        
                        $next2last = $p0;
                        $last = $p1;
                    }
                    
                    $cp = $this->_getControlPoints($p1_, $plast_, $p2_, $p3_);
                    $this->_driver->splineAdd(
                        $cp['X'], 
                        $cp['Y'], 
                        $cp['P1X'], 
                        $cp['P1Y'], 
                        $cp['P2X'], 
                        $cp['P2Y']
                    );                
                    $this->_getFillStyle($key);
                    $this->_getLineStyle($key);
                    $this->_driver->splineEnd(true);
                }
            }
            unset($keys);
        }
        $this->_drawMarker();                 
        return parent::_done();
    }

}

?>