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
 * @subpackage Grid     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Grid.php
 */
require_once 'Image/Graph/Grid.php';

/**
 * Display alternating bars on the plotarea.
 * {@see Image_Graph_Grid} 
 */
class Image_Graph_Grid_Bars extends Image_Graph_Grid 
{

    /**
     * Output the grid
     * @access private      
     */
    function _done()
    {
        parent::_done();

        if (!$this->_primaryAxis) {
            return false;
        }

        $i = 0;
        $value = $this->_primaryAxis->_getNextLabel();

        $secondaryPoints = $this->_getSecondaryAxisPoints();

        while (($value <= $this->_primaryAxis->_getMaximum()) and ($value !== false)) {
            if (($value > $this->_primaryAxis->_getMinimum()) and ($i == 1)) {
                reset($secondaryPoints);
                if (count($secondaryPoints) > 2) {
                    list ($id, $previousSecondaryValue) = each($secondaryPoints);
                    while (list ($id, $secondaryValue) = each($secondaryPoints)) {
                        if ($this->_primaryAxis->_type == IMAGE_GRAPH_AXIS_X) {
                            $p1 = array ('Y' => $secondaryValue, 'X' => $value);
                            $p2 = array ('Y' => $previousSecondaryValue, 'X' => $value);
                            $p3 = array ('Y' => $previousSecondaryValue, 'X' => $previousValue);
                            $p4 = array ('Y' => $secondaryValue, 'X' => $previousValue);
                        } else {
                            $p1 = array ('X' => $secondaryValue, 'Y' => $value);
                            $p2 = array ('X' => $previousSecondaryValue, 'Y' => $value);
                            $p3 = array ('X' => $previousSecondaryValue, 'Y' => $previousValue);
                            $p4 = array ('X' => $secondaryValue, 'Y' => $previousValue);
                        }
    
                        $polygon[] = $this->_pointX($p1);
                        $polygon[] = $this->_pointY($p1);
                        $polygon[] = $this->_pointX($p2);
                        $polygon[] = $this->_pointY($p2);
                        $polygon[] = $this->_pointX($p3);
                        $polygon[] = $this->_pointY($p3);
                        $polygon[] = $this->_pointX($p4);
                        $polygon[] = $this->_pointY($p4);
    
                        $previousSecondaryValue = $secondaryValue;
    
                        ImageFilledPolygon($this->_canvas(), $polygon, 4, $this->_getFillStyle());
                        unset ($polygon);
                    }
                } else {
                    list($id, $p1) = each($secondaryPoints);
                    list($id, $p2) = each($secondaryPoints);

                    if ($this->_primaryAxis->_type == IMAGE_GRAPH_AXIS_X) {
                        $p1 = array ('Y' => $p1, 'X' => $value);
                        $p2 = array ('Y' => $p2, 'X' => $value);
                        $p3 = array ('Y' => $p2, 'X' => $previousValue);
                        $p4 = array ('Y' => $p1, 'X' => $previousValue);
                        $polygon[] = $this->_pointX($p1);
                        $polygon[] = $this->_top;
                        $polygon[] = $this->_pointX($p2);
                        $polygon[] = $this->_bottom;
                        $polygon[] = $this->_pointX($p3);
                        $polygon[] = $this->_bottom;
                        $polygon[] = $this->_pointX($p4);
                        $polygon[] = $this->_top;
                    } else {
                        $p1 = array ('X' => $p1, 'Y' => $value);
                        $p2 = array ('X' => $p2, 'Y' => $value);
                        $p3 = array ('X' => $p2, 'Y' => $previousValue);
                        $p4 = array ('X' => $p1, 'Y' => $previousValue);
                        $polygon[] = $this->_left;
                        $polygon[] = $this->_pointY($p1);
                        $polygon[] = $this->_right;
                        $polygon[] = $this->_pointY($p2);
                        $polygon[] = $this->_right;
                        $polygon[] = $this->_pointY($p3);
                        $polygon[] = $this->_left;
                        $polygon[] = $this->_pointY($p4);
                    }
    
                    ImageFilledPolygon($this->_canvas(), $polygon, 4, $this->_getFillStyle());
                    unset ($polygon);
                }                    
            }
            $i = 1 - $i;
            $previousValue = $value;
            $value = $this->_primaryAxis->_getNextLabel($value);
        }
    }

}

?>