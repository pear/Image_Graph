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
 * Linechart.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Plot
 */
class Image_Graph_Plot_Line extends Image_Graph_Plot
{

    /**
     * Gets the fill style of the element
     *
     * @return int A GD filestyle representing the fill style
     * @see Image_Graph_Fill
     * @access private
     */
    function _getFillStyle($ID = false)
    {
        return IMG_COLOR_TRANSPARENT;
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
        // TODO Consider new legend icon
        $y = ($y0 + $y1) / 2;
        $dx = abs($x1 - $x0) / 3;
        $dy = abs($y1 - $y0) / 5;
        $this->_driver->polygonAdd($x0, $y);
        $this->_driver->polygonAdd($x0 + $dx, $y - $dy * 2);
        $this->_driver->polygonAdd($x1 - $dx, $y + $dy);
        $this->_driver->polygonAdd($x1, $y - $dy);
        $this->_driver->polygonEnd(false);
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

        reset($this->_dataset);

        if ($this->_multiType == 'stacked100pct') {
            $total = $this->_getTotals();
        }

        $p1 = false;

        $keys = array_keys($this->_dataset);
        foreach ($keys as $key) {
            $dataset = & $this->_dataset[$key];
            $dataset->_reset();
            while ($point = $dataset->_next()) {
                if (($this->_multiType == 'stacked') ||
                    ($this->_multiType == 'stacked100pct'))
                {
                    $x = $point['X'];
                    if (!isset($current[$x])) {
                        $current[$x] = 0;
                    }
                    if ($this->_multiType == 'stacked') {
                        $py = $current[$x] + $point['Y'];
                    } else {
                        $py = 100 * ($current[$x] + $point['Y']) / $total['TOTAL_Y'][$x];
                    }
                    $current[$x] += $point['Y'];
                    $point['Y'] = $py;
                }

                $p2['X'] = $this->_pointX($point);
                $p2['Y'] = $this->_pointY($point);

                $this->_driver->polygonAdd($p2['X'], $p2['Y']);
            }
            $this->_getLineStyle($key);
            $this->_driver->polygonEnd(false);
        }
        unset($keys);
        $this->_drawMarker();
    }

}

?>