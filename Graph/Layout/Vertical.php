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
 * @subpackage Layout
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */

/**
 * Include file Image/Graph/Layout/Horizontal.php
 */
require_once 'Image/Graph/Layout/Horizontal.php';

/**
 * Layout for displaying two elements on top of each other.
 *
 * This splits the area contained by this element in two on top of each other
 * by a specified percentage (relative to the top). A layout can be nested.
 * Fx. a {@link Image_Graph_Layout_Horizontal} can layout two VerticalLayout's to
 * make a 2 by 2 matrix of 'element-areas'.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Layout
 */
class Image_Graph_Layout_Vertical extends Image_Graph_Layout_Horizontal
{

    /**
     * (Add description here)
     *
     * @since 0.3.0dev2
     *
     * @access private
     */
    function _getAbsolute(&$part)
    {
        $part1Size = $this->_part1->_getAutoSize();
        $part2Size = $this->_part2->_getAutoSize();
        $this->_percentage = false;
        if (($part1Size !== false) and ($part2Size !== false)) {
            $height = $this->_fillHeight() * $part1Size / ($part1Size + $part2Size);
        } elseif ($part1Size !== false) {
            $height = $part1Size;
        } elseif ($part2Size !== false) {
            $height = -$part2Size;
        } else {
            $height = $this->_fillHeight() / 2;
        }

        if ($part == 'auto_part2') {
//            $height = $this->_fillHeight() - $height;
        }

        return $height;
    }

    /**
     * Splits the layout between the parts, by the specified percentage
     *
     * @access private
     */
    function _split()
    {
        if (($this->_part1) && ($this->_part2)) {
            if ($this->_percentage !== false) {
                $split1 = 100 - $this->_percentage;
                $split2 = $this->_percentage;
                $this->_part1->_push('bottom', "$split1%");
                $this->_part2->_push('top', "$split2%");
            } else {
                $this->_part1->_push('bottom', 'auto_part1');
                $this->_part2->_push('top', 'auto_part2');
            }
        }
    }

}

?>