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
 * @subpackage Logo
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */

/**
 * Include file Image/Graph/Element.php
 */
require_once 'Image/Graph/Element.php';

/**
 * Displays a logo on the canvas.
 *
 * By default the logo is displayed in the top-right corner of the canvas.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Logo
 */
class Image_Graph_Logo extends Image_Graph_Element
{

    /**
     * The file name
     * @var stirng
     * @access private
     */
    var $_filename;

    /**
     * The GD Image resource
     * @var resource
     * @access private
     */
    var $_image;

    /**
     * Alignment of the logo
     * @var int
     * @access private
     */
    var $_alignment;

    /**
     * Logo [Constructor]
     *
     * @param string $filename The filename and path of the image to use for logo
     */
    function &Image_Graph_Logo($filename, $alignment = IMAGE_GRAPH_ALIGN_TOP_RIGHT)
    {
        parent::Image_Graph_Element();
        $this->_filename = $filename;
        $this->_alignment = $alignment;
    }

    /**
     * Sets the parent. The parent chain should ultimately be a GraPHP object
     *
     * @see Image_Graph
     * @param Image_Graph_Common $parent The parent
     * @access private
     */
    function _setParent(& $parent)
    {
        parent::_setParent($parent);
        $this->_setCoords(
            $this->_parent->_left,
            $this->_parent->_top,
            $this->_parent->_right,
            $this->_parent->_bottom
        );
    }

    /**
     * Output the logo
     *
     * @return bool Was the output 'good' (true) or 'bad' (false).
     * @access private
     */
    function _done()
    {
        if (parent::_done() === false) {
            return false;
        }

        if ($this->_alignment & IMAGE_GRAPH_ALIGN_LEFT) {
            $x = $this->_parent->_left + 2;
        } elseif ($this->_alignment & IMAGE_GRAPH_ALIGN_RIGHT) {
            $x = $this->_parent->_right - 2;
        } else {
            $x = ($this->_parent->_left + $this->_parent->_right) / 2;
        }

        if ($this->_alignment & IMAGE_GRAPH_ALIGN_TOP) {
            $y = $this->_parent->_top + 2;
        } elseif ($this->_alignment & IMAGE_GRAPH_ALIGN_BOTTOM) {
            $y = $this->_parent->_bottom - 2;
        } else {
            $y = ($this->_parent->_top + $this->_parent->_bottom) / 2;
        }

        $this->_driver->overlayImage(
            $x,
            $y,
            $this->_filename,
            false,
            false,
            $this->_alignment
        );
        return true;
    }

}

?>