<?
// $Id$
/**
* Rhomb data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Image/Graph/common.php");

class Image_Graph_Data_Rhomb extends Image_Graph_Data_Common
{
    /**
    * Type of data element
    *
    * @var string
    * @access private
    */
    var $_type = "rhomb";

    /**
    * Constructor for the class
    *
    * @param  object  parent object (of type Image_Graph_Diagram)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Rhomb(&$parent, $data, $attributes)
    {
        if (!isset($attributes['size'])) {
            $attributes['size'] = 10;
        }
        parent::Image_Graph_Data_Common($parent, $data, $attributes);
    }

    /**
    * Draws diagram element 
    *
    * @param gd-resource image-resource to draw to
    * @access private
    */

    function drawGD(&$img)
    {
        $yAxe = &$this->_parent->_axes['y'][ $this->_attributes['axeId'] ];
        $drawColor = imagecolorallocate($img, $this->_attributes["color"][0], $this->_attributes["color"][1], $this->_attributes["color"][2]);
        $dataKeys  = array_keys($this->_data);
        $numDatapoints = count($this->_datapoints);

        // compute side-length using Pythagoras so that square and rhomb look equal-size
        $sideLength = sqrt(2*$this->_attributes['size']*$this->_attributes['size']);
        $halfSizePixel = floor(($sideLength-1) / 2);

        for ($counter=0; $counter<$numDatapoints; $counter++) {
            if (!is_null($this->_datapoints[$counter]) &&
                ($yAxe['min'] <= $this->_data[ $dataKeys[$counter] ]) && ($this->_data[ $dataKeys[$counter] ] <= $yAxe['max'])
               ) { // otherwise do not draw
                $points = array($this->_datapoints[$counter][0]               , $this->_datapoints[$counter][1]-$halfSizePixel,
                                $this->_datapoints[$counter][0]+$halfSizePixel, $this->_datapoints[$counter][1],
                                $this->_datapoints[$counter][0]               , $this->_datapoints[$counter][1]+$halfSizePixel,
                                $this->_datapoints[$counter][0]-$halfSizePixel, $this->_datapoints[$counter][1]);
                imagefilledpolygon ($img, $points, 4, $drawColor);
            }
        }
    }
}
?>
