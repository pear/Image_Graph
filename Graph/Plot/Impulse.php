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
 * Impulse chart
 */
class Image_Graph_Plot_Impulse extends Image_Graph_Plot 
{

    /**
     * Output the plot
     * @access private
     */
    function _done()
    {
        parent::_done();

        if (!is_array($this->_dataset)) {
            return false;
        }
        
        reset($this->_dataset);

        if ($this->_multiType == 'stacked100pct') {
            $total = $this->_getTotals();
        }
        $current = array();
        $keys = array_keys($this->_dataset);
        $number = 0;        
        while (list ($ID, $key) = each($keys)) {
            $dataset =& $this->_dataset[$key];
            $dataset->_reset();            
            while ($point = $dataset->_next()) {
                $x0 = $this->_pointX($point);
                if (($this->_multiType == 'stacked') or ($this->_multiType == 'stacked100pct')) {
                    $x = $point['X'];                    
                    if (!isset($current[$x])) {
                        $current[$x] = 0;
                    }
                    if ($this->_multiType == 'stacked') {
                        $P1 = array('X' => $x, 'Y' => $current[$x]);
                        $P2 = array('X' => $x, 'Y' => $current[$x] + $point['Y']); 
                    } else {
                        $P1 = array('X' => $x, 'Y' => 100 * $current[$x] / $total['TOTAL_Y'][$x]);
                        $P2 = array('X' => $x, 'Y' => 100 * ($current[$x] + $point['Y']) / $total['TOTAL_Y'][$x]);  
                    }
                    $current[$x] += $point['Y'];
                } else {
                    $P1 = array('X' => $point['X'], 'Y' => 0);
                    $P2 = $point;
                }

                $x1 = $this->_pointX($P1);
                $y1 = $this->_pointY($P1);
                
                $x2 = $this->_pointX($P2);
                $y2 = $this->_pointY($P2);
                
                if ($this->_multiType == 'normal') {
                    $offset = 5*$number;                    
                    $x1 += $offset;
                    $x2 += $offset;
                }
        
                ImageLine($this->_canvas(), $x1, $y1, $x2, $y2, $this->_getLineStyle());
            }
            $number++;
        }
        $this->_drawMarker();
    }

}

?>