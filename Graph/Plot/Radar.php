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
 * Radar chart
 */
class Image_Graph_Plot_Radar extends Image_Graph_Plot 
{

    /**
     * Output the plot
     * @access private
     */
    function _done()
    {
        if (is_a($this->_parent, 'Image_Graph_Plotarea_Radar')) {
            $centerX = (int) (($this->_left + $this->_right) / 2);
            $centerY = (int) (($this->_top + $this->_bottom) / 2);
            $radius = min($this->height(), $this->width()) * 0.40;                       
            
            $keys = array_keys($this->_dataset);
        
            while (list ($ID, $key) = each($keys)) {
                $dataset = & $this->_dataset[$key];                    
                $maxY = $dataset->maximumY();
                $count = $dataset->count();

                $dataset->_reset();
                while ($point = $dataset->_next()) {
                    $radarPolygon[] = $this->_pointX($point);
                    $radarPolygon[] = $this->_pointY($point);
                }
                ImageFilledPolygon($this->_canvas(), $radarPolygon, count($radarPolygon) / 2, $this->_getFillStyle());
                ImagePolygon($this->_canvas(), $radarPolygon, count($radarPolygon) / 2, $this->_getLineStyle());
            }
        }
        $this->_drawMarker();
        parent::_done();
    }

}

?>