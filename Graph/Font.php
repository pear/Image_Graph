<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Image_Graph - PEAR PHP OO Graph Rendering Utility.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Text
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * Include file Image/Graph/Common.php
 */
require_once 'Image/Graph/Common.php';

/**
 * A font.
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Text
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_Graph
 * @abstract
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
     *
     * @param mixed $color The color object of the Font
     */
    function setColor($color)
    {
        $this->_color = $color;
    }

    /**
     * Get the font 'array'
     *
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