<?
/**
* Class template for a Image_Graph diagram data element (e.g. a "line")
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/
class Image_Graph_Data_Common
{
    /**
    * Type of data element
    *
    * @var string
    * @access private
    */
    var $_type = "common";

    /**
    * Data to be drawn (array of numerical values)
    *
    * @var array  data to be drawn
    * @access private
    */
    var $_data = array();

    /**
    * Datapoints to be drawn (array of x/y-pixel values); filled by Image_Graph_Diagram::_calculateDatapoints()
    *
    * @var array  datapoints to be drawn
    * @see Image_Graph_Diagram::_calculateDatapoints()
    * @access private
    */
    var $_datapoints = array();

    /**
    * Attributes for drawing the data element (color, shading, ...)
    *
    * @var array  initially contains only a color-definition of black
    * @access private
    */
    var $_attributes = array("color" => array(0, 0, 0));

    /**
    * parent object (of type Image_Graph_Diagram)
    *
    * @var object
    * @access private
    */
    var $_parent = null;

    /**
    * Constructor for the class
    *
    * @param  object  parent object (of type Image_Graph_Diagram)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Common(&$parent, $data)
    {
        $this->_parent = $parent;
        $this->_data   = $data;
    }

    /**
    * Draws diagram element 
    *
    * @param gd-resource image-resource to draw to
    * @access private
    */

    function drawGD(&$img)
    {
        // implementation of this function in the derived diagram-element-classes
    }
}
?>