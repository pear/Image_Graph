<?php
// $Id$

require_once("Image/Graph/Color.php"); // extended version of package: PEAR::Image_Color

define('IMAGE_GRAPH_FILL_LINEAR',  1);
define('IMAGE_GRAPH_FILL_RADIAL',  2); // not yet implemented

/**
* Class template for a Image_Graph diagram fill element (e.g. a "solid" fill or a "gradient" fill)
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/
class Image_Graph_Fill_Common
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
    var $_attributes = array("color" => array(0, 0, 0));

    /**
    * Constructor for the class
    *
    * @param  array   attributes like color
    * @access public
    */
    function Image_Graph_Fill_Common($attributes)
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
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setColor($color)
    {
        $this->_color = Image_Graph_Color::color2RGB($color);
    }

    /**
    * Draws fill element, shape: box
    *
    * @param  gd-resource              image-resource to draw to
    * @param  array of array of int    absolute position for upper left and lower right edge
    * @access private
    */
    function drawGDBox(&$img, $pos)
    {
        // implementation of this function in the derived fill-element-classes
    }

    /**
    * Draws fill element, shape: polygon
    *
    * @param  gd-resource              image-resource to draw to
    * @param  array of array of int    absolute positions of polygon-coordinates
    * @access private
    */
    function drawGDPolygon(&$img, $pos)
    {
        // implementation of this function in the derived fill-element-classes
    }
}
php?>