<?
// $Id$
/**
* Line data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Image/Graph/Data/Common.php");

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
    * @param  object  parent object (of type Image_Graph)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Line(&$parent, $data, $attributes)
    {
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
        $graph = &$this->_graph;
        $xAxe  = &$graph->axeX;
        $yAxe  = &$graph->{"axeY".$this->_attributes['axeId']};
        $drawColor = imagecolorallocate($img, $this->_attributes["color"][0], $this->_attributes["color"][1], $this->_attributes["color"][2]);
        $numData = count($this->_data);
        for ($counter=0; $counter<$numData; $counter++) {
            if (!is_null($this->_data[$counter])) { // otherwise do not draw this point
                if (($counter == 0) || (is_null($this->_data[$counter-1]))) {
                    if (($yAxe->_boundsEffective['min'] <= $this->_data[$counter]) && ($this->_data[$counter] <= $yAxe->_boundsEffective['max'])) {
                        imagesetpixel ($img, $xAxe->valueToPixelAbsolute($counter), $yAxe->valueToPixelAbsolute($this->_data[$counter]), $drawColor);
                    } // otherwise do not draw that point since it's out of the drawingarea
                } else {
                    $newCoords = $this->_calculateClippedLineCoords(array($xAxe->valueToPixelAbsolute($counter-1), $yAxe->valueToPixelAbsolute($this->_data[$counter-1])),
                                                                    array($xAxe->valueToPixelAbsolute($counter)  , $yAxe->valueToPixelAbsolute($this->_data[$counter]  ))
                                                                   );
                    if (!empty($newCoords)) {
                        imageline ($img, $newCoords[0][0], $newCoords[0][1], $newCoords[1][0], $newCoords[1][1], $drawColor);
                    }
                }
            }
        }
      $this->_drawDataMarkerGD($img);
    }
}
?>