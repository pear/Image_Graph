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
 * Include file Image/Graph/Line/Solid.php
 */
require_once 'Image/Graph/Line/Solid.php';

/**
 * Formatted user defined line style.
 * 
 * Use this to create a user defined line style. Specify an array of colors that are to
 * be used for displaying the line. 
 *             
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Line   
 */
class Image_Graph_Line_Formatted extends Image_Graph_Line_Solid 
{

    /**
     * The style of the line
     *
     * @var array
     * @access private
     */
    var $_style;

    /**
     * Image_Graph_FormattedLine [Constructor]
     *
     * @param array $style The style of the line 
     */
    function &Image_Graph_Line_Formatted($style)
    {
        parent::Image_Graph_Line_Solid(reset($style));
        $this->_style = $style;
    }

    /**
     * Gets the line style of the element	 
     *
     * @return int A GD linestyle representing the line style 
     * @see Image_Graph_Line
     * @access private
     */
    function _getLineStyle()
    {
        return array(
            'color' => $this->_style, 
            'thickness' => $this->_thickness
        );
    }

}

?>