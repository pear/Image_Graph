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
 * @subpackage Layout     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/Layout.php
 */
require_once 'Image/Graph/Layout.php';

/**
 * A default/standard plotarea layout.
 * Image_Graph_PlotareaLayout creates a Image_Graph_Plotarea with assoiated layout's for a title, an X-axis 
 * title and a Y-axis title. 
 */
class Image_Graph_Layout_Plotarea extends Image_Graph_Layout 
{
    
    /**
     * The plotarea
     * @var Image_Graph_Plotarea
     * @access private
     */
    var $_plotarea;

    /**
     * The title
     * @var Image_Graph_Title
     * @access private
     */
    var $_title;

    /**
     * The x-axis title
     * @var Image_Graph_Title
     * @access private
     */
    var $_titleAxisX;

    /**
     * The y-axis title
     * @var Image_Graph_Title
     * @access private
     */
    var $_titleAxisY;

    /**
     * PlotareaLayout [Constructor]
     * @param string $title The plotarea title
     * @param string $axisXTitle The title displayed on the X-axis (i.e. at the bottom)
     * @param string $axisYTitle The title displayed on the Y-axis (i.e. on the left - vertically)
     */
    function &Image_Graph_Layout_Plotarea($title, $axisXTitle, $axisYTitle)
    {
        parent::Image_Graph_Layout();

        include_once 'Image/Graph/Layout/Horizontal.php';
        include_once 'Image/Graph/Layout/Vertical.php';
        include_once 'Image/Graph/Plotarea.php';
        include_once 'Image/Graph/Title.php';
        
        $this->_plotarea = & Image_Graph::factory('plotarea');
        $this->_title =& Image_Graph::factory('title', array($title, &$GLOBALS['_Image_Graph_font']));
        $this->_titleAxisX =& Image_Graph::factory('title', array($axisXTitle, &$GLOBALS['_Image_Graph_font']));
        $this->_titleAxisY =& Image_Graph::factory('title', array($axisYTitle, &$GLOBALS['_Image_Graph_verticalFont']));

        $this->add(
            Image_Graph::vertical(
                $this->_title,        
            	Image_Graph::horizontal(
                    $this->_titleAxisY,
                    Image_Graph::vertical(
                        $this->_plotarea,
                        $this->_titleAxisX,
                        95
                    ),
                    5
                ),
                10
            )
        );
    }
    
    /**
     * Get the plotarea
     * @return Image_Graph_Plotarea The plotarea 
     */
    function &getPlotarea() {
        return $this->_plotarea;     
    }

    /**
     * Get the title
     * @return Image_Graph_Title The title
     */
    function &getTitle() {
        return $this->_title;     
    }

    /**
     * Get the X-axis title
     * @return Image_Graph_Title The x-axis title
     */
    function &getTitleAxisX() {
        return $this->_titleAxisX;     
    }

    /**
     * Get the X-axis title
     * @return Image_Graph_Title The x-axis title
     */
    function &getTitleAxisY() {
        return $this->_titleAxisY;     
    }

}

?>