<?
// $Id$
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
    function Image_Graph_DataMarker_Common($attributes)
    {
        $this->_attributes = $attributes;
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
?>
