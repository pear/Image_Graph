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
 * @subpackage Line     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Common.php
 */
require_once 'Image/Graph/Common.php';

/**
 * Simple colored line style.
 * 
 * Use a color for line style.
 *             
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Line 
 */
class Image_Graph_Line_Solid extends Image_Graph_Common 
{

    /**
     * The thickness of the line (requires GD 2)
     * @var int
     * @access private
     */
    var $_thickness = 1;

    /**
     * The color of the line
     * @var mixed
     * @access private
     */
    var $_color;

    /**
     * Image_Graph_SolidLine [Constructor]
     * @param mixed $color The color of the line 
     */
    function &Image_Graph_Line_Solid($color)
    {
        parent::Image_Graph_Common();
        $this->_color = $color;
    }

    /**
     * Set the thickness of the linestyle
     * @param int $thickness The line width in pixels 
     */
    function setThickness($thickness)
    {
        $this->_thickness = $thickness;
    }

    /**
     * Gets the line style of the element	 
     * @return int A GD linestyle representing the line style 
     * @see Image_Graph_Line
     * @access private
     */
    function _getLineStyle()
    {
        return array(
            'color' => $this->_color, 
            'thickness' => $this->_thickness
        );
    }

}

?>