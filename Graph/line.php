<?
/**
* Line data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Graph/common.php");

class Image_Graph_Data_Line extends Image_Graph_Data_Common
{
    /**
    * Type of data element
    *
    * @var string
    * @access private
    */
    var $_type = "line";

    /**
    * Constructor for the class
    *
    * @param  object  parent object (of type Image_Graph_Diagram)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Line(&$parent, $data)
    {
//        $this->_parent  = $parent;
        $this->_data    = $data;
    }

    /**
    * Draws diagram element 
    *
    * @param gd-resource image-resource to draw to
    * @access private
    */

    function drawGD(&$img)
    {
        $drawColor = imagecolorallocate($img, $attributes["color"][0], $attributes["color"][1], $attributes["color"][2]);
        $numDatapoints = count($this->_datapoints);
        for ($counter=0; $counter<$numDatapoints; $counter++) {
            if (!is_null($this->_datapoints[$counter])) { // otherwise do not draw this point
                if (($counter == 0) || (is_null($this->_datapoints[$counter-1]))) {
                    imagesetpixel($img, $this->_datapoints[$counter][0], $this->_datapoints[$counter][1], $drawColor);
                } else {
                    imageline    ($img, $this->_datapoints[$counter-1][0], $this->_datapoints[$counter-1][1],
                                        $this->_datapoints[$counter][0],   $this->_datapoints[$counter][1], $drawColor);
                }
            }
        }
    }
}
?>