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
* Basic fill-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

/**
* Class for color handling (extended version of package: PEAR::Image_Color)
*/
require_once("Image/Graph/Color.php");

/*
* Linear fill-mode
* @see Imaga_Graph_Fill_Gradient
*/
define('IMAGE_GRAPH_FILL_LINEAR',  1);
/*
* Radial fill-mode
* @see Imaga_Graph_Fill_Gradient
*/
define('IMAGE_GRAPH_FILL_RADIAL',  2); // not yet implemented

/**
* Base class for a fill-element (e.g. a "solid" fill or a "gradient" fill)
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/
class Image_Graph_Fill_Common
{
    /**
    * Color for element
    *
    * @var array              (4 ints for R,G,B,A); initially black
    * @access private
    */
    var $_color = array(0, 0, 0, 255);

    /**
    * Attributes for drawing the data element (shading, ...)
    *
    * @var array
    * @access private
    */
    var $_attributes = array("color" => array(0, 0, 0));

    /**
    * Constructor
    *
    * @param  array           attributes like color
    * @access public
    */
    function Image_Graph_Fill_Common($attributes=array())
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
    * Draws fill element, shape: box
    *
    * @param  resource                 image-resource to draw to
    * @param  array of array of int    absolute position for upper left and lower right edge
    * @access public
    */
    function drawGDBox(&$img, $pos)
    {
        // implementation of this function in the derived fill-element-classes
    }

    /**
    * Draws fill element, shape: polygon
    *
    * @param  resource                 image-resource to draw to
    * @param  array of array of int    absolute positions of polygon-coordinates
    * @access public
    */
    function drawGDPolygon(&$img, $pos)
    {
        // implementation of this function in the derived fill-element-classes
    }
}
php?>