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
 * @subpackage Legend
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
 * Displays a legend for a plotarea.
 *
 * A legend can be displayed in two ways:
 *
 * 1 As an overlayed box within the plotarea
 *
 * 2 Layout'ed on the canvas smewhere next to the plotarea.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Legend
 */
class Image_Graph_Legend extends Image_Graph_Layout
{

    /**
     * Alignment of the text
     * @var int
     * @access private
     */
    var $_alignment = false;

    /**
     * The plotarea to show the legend for
     * @var Plotarea
     * @access private
     */
    var $_plotarea = false;

    /**
     * Should markers be shown or not on this legend
     * @var bool
     * @access private
     */
    var $_showMarker = false;

    /**
     * Image_Graph_Legend [Constructor]
     */
    function &Image_Graph_Legend()
    {
        parent::Image_Graph_Layout();
        $this->_padding = 5;
    }

    /**
     * The number of actual plots in the plot area
     *
     * @return int The number of plotes
     * @access private
     */
    function _plotCount()
    {
        if (is_a($this->_plotarea, 'Image_Graph_Plotarea')) {
            $elements = $this->_plotarea->_elements;

            $maxSize = 0;
            $count = 0;
            $keys = array_keys($elements);
            foreach ($keys as $key) {
                $element =& $elements[$key];
                if (is_a($element, 'Image_Graph_Plot')) {
                    $count ++;
                }
            }
            unset($keys);
            return $count;
        }
    }

    /**
     * The height of the element on the canvas
     *
     * @return int Number of pixels representing the height of the element
     * @access private
     */
    function _height()
    {
        $parent = (is_object($this->_parent) ? get_class($this->_parent) : $this->_parent);

        if (strtolower($parent) == 'Image_Graph_plotarea') {
            $count = $this->_plotCount();
            return $count * 10 + // The height of 'plot'-legends
             ($count -1) * $this->_padding + // The space between plots
            2 * $this->_padding; // Top and bottom padding
        } else {
            return parent::height();
        }
    }

    /**
     * The width of the element on the canvas
     *
     * @return int Number of pixels representing the width of the element
     * @access private
     */
    function _width()
    {
        $parent = (is_object($this->_parent) ? get_class($this->_parent) : $this->_parent);

        if (strtolower($parent) == 'Image_Graph_plotarea') {
            $elements = $this->_plotarea->_elements;

            $this->_driver->setFont($this->_getFont());

            $maxSize = 0;
            $keys = array_keys($elements);
            foreach ($keys as $key) {
                $element =& $elements[$key];
                if (is_a($element, 'Image_Graph_Plot')) {
                    $maxSize = max($maxSize, $this->_driver->textWidth($element->Title));
                }
            }
            unset($keys);
            return $maxSize + // The width of the text
            10 + // Spacing between legend and text
            10 + // The width of the legend
            2 * $this->_padding; // Left and right padding
        } else {
            return parent::width();
        }
    }

    /**
     * Set the alignment of the legend
     *
     * @param int $alignment The alignment
     */
    function setAlignment($alignment)
    {
        $this->_alignment = $alignment;
    }

    /**
     * Update coordinates
     *
     * @access private
     */
    function _updateCoords()
    {
        parent::_updateCoords();

        $parent = (is_object($this->_parent) ? get_class($this->_parent) : $this->_parent);

        if (strtolower($parent) == 'image_graph_plotarea') {
            if ($this->_alignment === false) {
                $this->_alignment = IMAGE_GRAPH_ALIGN_TOP + IMAGE_GRAPH_ALIGN_RIGHT;
            }

            if (($this->_alignment & IMAGE_GRAPH_ALIGN_BOTTOM) != 0) {
                $y = $this->_parent->_fillBottom() - $this->_height() - 5 - 10;
            } else {
                $y = $this->_parent->_fillTop();
            }

            if (($this->_alignment & IMAGE_GRAPH_ALIGN_LEFT) != 0) {
                $x = $this->_parent->_fillLeft() + $this->_width() + 10;
            } else {
                $x = $this->_parent->_fillRight() - 5;
            }

            $this->_setCoords($x, $y, $x + $this->_width(), $y + $this->_height());
        }
    }

    /**
     * Sets Plotarea
     *
     * @param Image_Graph_Plotarea $plotarea The plotarea
     */
    function setPlotarea(& $plotarea)
    {
        $this->_plotarea = & $plotarea;
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
        if ($this->_plotarea === false) {
            $this->_plotarea = & $parent;
        }
    }

    /**
     * Set if this legends should show markers
     *
     * @param bool $showMarker True if markers are to be shown, false is not
     */
    function setShowMarker($showMarker)
    {
        $this->_showMarker = $showMarker;
    }


    /**
     * Output the plot
     *
     * @access private
     */
    function _done()
    {

        $shadow = $this->_shadow;
        $this->_shadow = false;

        if (Image_Graph_Element::_done() === false) {
            return false;
        }

        $param['left'] = $this->_left + $this->_padding;
        $param['top'] = $this->_top + $this->_padding;
        $param['right'] = $this->_right - $this->_padding;
        $param['bottom'] = $this->_bottom - $this->_padding;
        $param['align'] = $this->_alignment;
        $param['x'] = $this->_left + $this->_padding;
        $param['y'] = $this->_top + $this->_padding;
        $param['width'] = 16;
        $param['height'] = 16;
        $param['show_marker'] = $this->_showMarker;

        $legend = 0;
        if (is_a($this->_plotarea, 'Image_Graph_Plotarea')) {
            $elements = $this->_plotarea->_elements;

            if (is_array($elements)) {
                $param['font'] = $this->_getFont();

                $parent = (is_object($this->_parent) ?
                    get_class($this->_parent) :
                    $this->_parent
                );

                if (strtolower($parent) == 'image_graph_plotarea') {
                    $this->_setCoords(
                        $this->_right - $this->_width(),
                        $this->_top,
                        $this->_right,
                        $this->_top + $this->_height()
                    );

                    $this->_getFillStyle();
                    $this->_getLineStyle();
                    $this->_driver->rectangle(
                        $this->_left,
                        $this->_top,
                        $this->_right,
                        $this->_bottom
                    );

                    $y = 0;
                    $keys = array_keys($elements);
                    foreach($keys as $key) {
                        $element =& $elements[$key];
                        if (is_a($element, 'Image_Graph_Plot')) {
                            $element->_legendSample($param);
                        }
                    }
                    unset($keys);
                } else {
                    $param0 = $param;
                    $param0['simulate'] = true;
                    $keys = array_keys($elements);
                    foreach($keys as $key) {
                        $element =& $elements[$key];
                        if (is_a($element, 'Image_Graph_Plot')) {
                            $element->_legendSample($param0);
                        }
                    }
                    unset($keys);
                    if (($this->_alignment & IMAGE_GRAPH_ALIGN_VERTICAL) != 0) {
                        if ($param0['x'] == $param['x']) {
                            $param['y'] = $param['y'] + ($this->_height() - ($param0['y'] - $param['y']))/2;
                        }
                    } else {
                        if ($param0['y'] == $param['y']) {
                            $param['x'] = $param['x'] + ($this->_width() - ($param0['x'] - $param['x']))/2;
                        }
                    }

                    if ($plotCount = $this->_plotCount()) {
                        $legendWidth = $this->_width() / $plotCount;
                    }
                    $keys = array_keys($elements);
                    foreach ($keys as $key) {
                        $element =& $elements[$key];
                        if (is_a($element, 'Image_Graph_Plot')) {
                            $element->_legendSample($param);
                        }
                    }
                    unset($keys);
                }
            }
        }
/*        if ($shadow) {
            $this->displayShadow();
        }*/
    }
}
?>