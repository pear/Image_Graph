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
 * Area Chart plot.
 * An area chart plots all data points similar to a {@see Image_Graph_Plot_Line}, but the area beneath the
 * line is filled and the whole area 'the-line', 'the right edge', 'the x-axis' and 
 * 'the left edge' is bounded.
 * Smoothed charts are only supported with non-stacked types 
 */
class Image_Graph_Plot_Area extends Image_Graph_Plot 
{

    /**
     * Output the plot
     * @access private
     */
    function _done()
    {
        parent::_done();

        reset($this->_dataset);
        $keys = array_keys($this->_dataset);
        if ($this->_multiType == 'stacked') {
            list ($ID, $key) = each($keys);
            $dataset = & $this->_dataset[$key];
    
            $point = array ('X' => $dataset->minimumX(), 'Y' => 0);
            $base[] = $this->_pointY($point);
            $base[] = $this->_pointX($point);
    
            $point = array ('X' => $dataset->maximumX(), 'Y' => 0);
            $base[] = $this->_pointY($point);
            $base[] = $this->_pointX($point);
            $current = array();            
        }

        reset($keys);
        while (list ($ID, $key) = each($keys)) {
            $dataset = & $this->_dataset[$key];
            $dataset->_reset();
            if ($this->_multiType == 'stacked') {
                $plotarea = array_reverse($base);
                unset ($base);
                while ($point = $dataset->_next()) {
                    $x = $point['X'];
                    $p = $point;
                    if (isset($current[$x])) {
                        $p['Y'] += $current[$x];
                    } else {
                        $current[$x] = 0;
                    }
                    $x1 = $this->_pointX($p);
                    $y1 = $this->_pointY($p);
                    $plotarea[] = $x1;
                    $plotarea[] = $y1;
                    $base[] = $y1;
                    $base[] = $x1;
                    $current[$x] += $point['Y'];
                }
            } else {           
                $point = array ('X' => $dataset->minimumX(), 'Y' => 0);             
                $plotarea[] = $this->_pointX($point);
                $plotarea[] = $this->_pointY($point);
                while ($point = $dataset->_next()) {
                    $plotarea[] = $this->_pointX($point);
                    $plotarea[] = $this->_pointY($point);
                    $lastPoint = $point;
                }
                $endPoint['X'] = $lastPoint['X'];
                $endPoint['Y'] = 0;
                $plotarea[] = $this->_pointX($endPoint);
                $plotarea[] = $this->_pointY($endPoint);
            }

            ImageFilledPolygon($this->_canvas(), $plotarea, count($plotarea) / 2, $this->_getFillStyle());
            ImagePolygon($this->_canvas(), $plotarea, count($plotarea) / 2, $this->_getLineStyle());
        }
        
        $this->_drawMarker();
    }

}

?>