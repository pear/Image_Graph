<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
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
* Basic datamarker-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

/**
* Class for color handling (extended version of package: PEAR::Image_Color)
*/
require_once("Image/Graph/Color.php");

/**
* Class template for a datamarker-element (e.g. "diamond", "square" or "triangle")
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
    * @var array (4 ints for R,G,B,A); initially black
    * @access private
    */
    var $_color = array(0, 0, 0, 255);

    /**
    * Attributes for drawing the data element (shading, ...)
    *
    * @var array
    * @access private
    */
    var $_attributes = array();

    /**
    * Constructor for the class
    *
    * @param  array   attributes like color (to be extended to also include shading etc.)
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
    * Draws data marker
    *
    * @param gd-resource      GD-resource to draw to
    * @param array            (array of int) absolute position (x/y), where to draw the marker
    * @access private
    */
    function drawGD(&$img, $pos)
    {
        // implementation of this function in the derived datamarker-element-classes
    }
}
php?>