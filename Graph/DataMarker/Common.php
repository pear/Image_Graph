<?php
// $Id$

require_once("Image/Graph/Color.php"); // extended version of package: PEAR::Image_Color

/**
* Class template for a Image_Graph diagram data marker (e.g. a "square" or a "rhomb")
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
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
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setColor($color)
    {
        $this->_color = Image_Graph_Color::color2RGB($color);
    }

    /**
    * Draws data marker
    *
    * @param gd-resource    image-resource to draw to
    * @param array of int   absolute position, where to draw the marker
    * @access private
    */
    function drawGD(&$img, $pos)
    {
        // implementation of this function in the derived datamarker-element-classes
    }
}
php?>