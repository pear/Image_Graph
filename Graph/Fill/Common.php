<?
// $Id$
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
    * Attributes for drawing the data element (color, shading, ...)
    *
    * @var array  initially contains only a color-definition of black
    * @access private
    */
    var $_attributes = array("color" => array(0, 0, 0));

    /**
    * Constructor for the class
    *
    * @param  array   attributes like color (to be extended to also include shading etc.)
    * @access public
    */
    function Image_Graph_Fill_Common($attributes)
    {
        $this->_attributes = $attributes;
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

    /**
    * Draws fill element, shape: columns of pixels (
    *
    * @param  gd-resource              image-resource to draw to
    * @param  int                      left y-coord of pixelcolumn to fill
    * @param  array of array of int    top and bottom x-coords for each column to fill
    * @access private
    */
    function drawGDPixelcolumns(&$img, $yLeft, $xTopBottom)
    {
        // implementation of this function in the derived fill-element-classes
    }
}
?>
