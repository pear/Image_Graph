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
 * Display a line grid on the plotarea.
 *
 * {@link Image_Graph_Grid}
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Grid
 */
class Image_Graph_Grid_Lines extends Image_Graph_Grid
{

    /**
     * GridLines [Constructor]
     */
    function &Image_Graph_Grid_Lines()
    {
        parent::Image_Graph_Grid();
        $this->_lineStyle = 'lightgrey';
    }

    /**
     * Output the grid
     *
     * @access private
     */
    function _done()
    {
        if (parent::_done() === false) {
            return false;
        }

        if (!$this->_primaryAxis) {
            return false;
        }

        $value = $this->_primaryAxis->_getNextLabel();

        $secondaryPoints = $this->_getSecondaryAxisPoints();

        while (($value <= $this->_primaryAxis->_getMaximum()) && ($value !== false)) {
            if ($value > $this->_primaryAxis->_getMinimum()) {
                reset($secondaryPoints);
                list ($id, $previousSecondaryValue) = each($secondaryPoints);
                while (list ($id, $secondaryValue) = each($secondaryPoints)) {
                    if ($this->_primaryAxis->_type == IMAGE_GRAPH_AXIS_Y) {
                        $p1 = array ('X' => $secondaryValue, 'Y' => $value);
                        $p2 = array ('X' => $previousSecondaryValue, 'Y' => $value);
                    } else {
                        $p1 = array ('X' => $value, 'Y' => $secondaryValue);
                        $p2 = array ('X' => $value, 'Y' => $previousSecondaryValue);
                    }

                    $x1 = $this->_pointX($p1);
                    $y1 = $this->_pointY($p1);
                    $x2 = $this->_pointX($p2);
                    $y2 = $this->_pointY($p2);

                    $previousSecondaryValue = $secondaryValue;

                    $this->_getLineStyle();
                    $this->_driver->line($x1, $y1, $x2, $y2);
                }
            }
            $value = $this->_primaryAxis->_getNextLabel($value);
        }
    }

}
?>