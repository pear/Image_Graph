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
 * @subpackage Text     
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
 * A font.
 *  
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Text
 */ 
class Image_Graph_Font extends Image_Graph_Common 
{

    /**
     * The color of the font
     * @var Color
     * @access private
     */
    var $_color = 'black';

    /**
     * Image_Graph_Font [Constructor]
     */
    function &Image_Graph_Font()
    {
        parent::Image_Graph_Common();
    }

    /**
     * Set the color of the font
     * @param mixed $color The color object of the Font 
     */
    function setColor($color)
    {
        $this->_color = $color;
    }
    
    /**
     * Get the font 'array'
     * @return array The font 'summary' to pass to the driver 
     * @access private 
     */
    function _getFont($options = false)
    {    
        if ($options === false) {
            $options = array();
        }    
        $options['font'] = 1;
        if (!isset($options['color'])) {        
            $options['color'] = $this->_color;
        }        
        return $options;
    }    

}

?>