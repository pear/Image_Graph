<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Stefan Neufeind <pear.neufeind@speedpartner.de>              |
// +----------------------------------------------------------------------+
//
// $Id$

/**
* Class-file for a basic datamarker-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @category images
* @license  The PHP License, version 2.02
*/

/**
* Class for color handling (extended version of package: PEAR::Image_Color)
*/
require_once("Image/Graph/Color.php");

/**
* Base class for a datamarker-element (e.g. "diamond", "square" or "triangle")
*
* This class provides a basic implementation that is used and
* extended in all derived datamarker-elements.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_DataMarker_Common
{
    /**
    * Color for element
    *
    * @var array              (4 ints for R,G,B,A); initially black
    * @access private
    */
    var $_color = array(0, 0, 0, 255);

    /**
    * size
    *
    * @var int
    * @see setSize()
    * @access private
    */
    var $_size = 10;

    /**
    * Attributes for drawing the data element
    *
    * @var array
    * @access private
    */
    var $_attributes = array();

    /**
    * Constructor
    *
    * @param  array           attributes like color (to be extended to also include shading etc.)
    * @access public
    */
    function Image_Graph_DataMarker_Common($attributes)
    {
        if (isset($attributes['color'])) {
            $this->setColor($attributes['color']);
            unset($attributes['color']);
        }
        $this->_attributes = $attributes;
    }

    /**
    * Set color
    *
    * @param  mixed           any color representation supported by Image_Graph_Color::color2RGB()
    * @see    Image_Graph_Color::color2RGB()
    * @access public
    */
    function setColor($color)
    {
        $this->_color = Image_Graph_Color::color2RGB($color);
    }

    /**
    * Set size
    *
    * @param  int     size
    * @access public
    */
    function setSize($size)
    {
        $this->_size = max(1, $size);
    }

    /**
    * Draws data marker
    *
    * @param resource         GD-resource to draw to
    * @param array            (array of int) absolute position (x/y), where to draw the marker
    * @access private
    */
    function drawGD(&$img, $pos)
    {
        // implementation of this function in the derived datamarker-element-classes
    }
}
?>