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
 * Include file Image/Graph/Plot/Smoothed/Bezier.php
 */
require_once 'Image/Graph/Plot/Smoothed/Bezier.php';

/**
 * Bezier smoothed line chart.
 * Similar to a {@see Image_Graph_Plot_Line}, but the interconnecting lines between two datapoints are
 * smoothed using a Bezier curve, which enables the chart to appear as a nice curved plot
 * instead of the sharp edges of a conventional {@see Image_Graph_Plot_Line}. 
 * Smoothed charts are only supported with non-stacked types 
 */
class Image_Graph_Plot_Smoothed_Line extends Image_Graph_Plot_Smoothed_Bezier 
{

    /**
     * Gets the fill style of the element         
     * @return int A GD filestyle representing the fill style 
     * @see Image_Graph_Fill
     * @access private
     */
    function _getFillStyle($ID = false)
    {
        return IMG_COLOR_TRANSPARENT;
    }

    /**
     * Output the Bezier smoothed plot as an Line Chart
     * @access private
     */
    function _done()
    {
        parent::_done();

       $keys = array_keys($this->_dataset);
        while (list ($ID, $key) = each($keys)) {
            $dataset = & $this->_dataset[$key];
            $plotarea = $this->_getPoints($dataset);
            reset($plotarea);
            $x0 = $y0 = false;
            if (count($plotarea) >= 6) {
                while (list ($ID, $x) = each($plotarea)) {
                    list ($ID, $y) = each($plotarea);
                    if (($x0) and ($y0)) {
                        ImageLine($this->_canvas(), $x0, $y0, $x, $y, $this->_getLineStyle());
                    }
                    $x0 = $x;
                    $y0 = $y;
                }
            }
        }
        $this->_drawMarker();
    }

}
?>