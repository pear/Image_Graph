<?
/**
* Bar data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Graph/common.php");

class Image_Graph_Data_Bar extends Image_Graph_Data_Common
{
    /**
    * Type of data element
    *
    * @var string
    * @access private
    */
    var $_type = "bar";

    /**
    * Constructor for the class
    *
    * @param  object  parent object (of type Image_Graph_Diagram)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Bar(&$parent, $data, $attributes)
    {
        if (!isset($attributes['width'])) {
            $attributes['width'] = 0.5;
        }
        parent::Image_Graph_Data_Common(&$parent, $data, $attributes);
        $parent->_addExtraSpace = 1;
    }

    /**
    * Draws diagram element 
    *
    * @param gd-resource image-resource to draw to
    * @access private
    */

    function drawGD(&$img)
    {
        $yAxe  = &$this->_parent->_axes['y'][ $this->_attributes['axeId'] ];
        $graph = &$this->_parent;
        $drawColor = imagecolorallocate($img, $this->_attributes["color"][0], $this->_attributes["color"][1], $this->_attributes["color"][2]);
        $numDatapoints = count($this->_datapoints);

        if ($numDatapoints < 2) {        
          $halfWidthPixel = floor($graph->_drawingareaSize[1] / 2);
        } else {
          $halfWidthPixel = floor(($this->_datapoints[1][0]-$this->_datapoints[0][0]) / 2 * $this->_attributes['width']);
        }

        for ($counter=0; $counter<$numDatapoints; $counter++) {
            if (!is_null($this->_datapoints[$counter])) { // otherwise do not draw this point
                imagefilledrectangle ($img, $this->_datapoints[$counter][0]-$halfWidthPixel, $this->_datapoints[$counter][1],
                                            $this->_datapoints[$counter][0]+$halfWidthPixel, $graph->_drawingareaPos[1]+$graph->_drawingareaSize[1]-2,
                 $drawColor);
            }
        }
    }
}
?>