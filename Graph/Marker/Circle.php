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
 * @subpackage Marker     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Marker.php
 */
require_once 'Image/Graph/Marker.php';

/**
 * Data marker as circle (require GD2)
 */
class Image_Graph_Marker_Circle extends Image_Graph_Marker 
{

    /**
     * The 'size' of the marker, the meaning depends on the specific Marker implementation
     * @var int
     * @access private
     */
    var $_size = 10;

    /**
     * Draw the marker on the canvas
     * @param int $x The X (horizontal) position (in pixels) of the marker on the canvas 
     * @param int $y The Y (vertical) position (in pixels) of the marker on the canvas 
     * @param array $values The values representing the data the marker 'points' to 
     * @access private
     */
    function _drawMarker($x, $y, $values = false)
    {
        $dA = 2*pi()/($this->_size*2);
        $angle = 0;
        while ($angle < 2*pi()) {
            $polygon[] = $x + $this->_size*cos($angle);        
            $polygon[] = $y - $this->_size*sin($angle);
            $angle += $dA;
        }

        $polygon[] = $x + $this->_size*cos(0);        
        $polygon[] = $y - $this->_size*sin(0);

        ImageFilledPolygon($this->_canvas(), $polygon, count($polygon)/2, $this->_getFillStyle());
        ImagePolygon($this->_canvas(), $polygon, count($polygon)/2, $this->_getLineStyle());

        parent::_drawMarker($x, $y, $values);
    }

}

?>