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
    * !static function! Prepare given dataElements of this type for stacking
    *
    * @param array    references to dataElements (objects of this type)
    * @access private
    */
    function stackingPrepare(&$dataElements)
    {
        $dataElements[0]->_stackingData = array();
        foreach($dataElements[0]->_data as $tempData) {
            $dataElements[0]->_stackingData[] = array(0, $tempData);
        }
        for($elementCount=1; $elementCount<count($dataElements); $elementCount++) {
            $dataElements[$elementCount]->_stackingData = array();
            for($dataCount=0; $dataCount<count($dataElements[$elementCount]->_data); $dataCount++) {
                $lastDataPoint = $dataElements[$elementCount-1]->_stackingData[$dataCount][1];
                $newDataPoint  = $lastDataPoint + $dataElements[$elementCount]->_data[$dataCount];
                $dataElements[$elementCount]->_stackingData[] = array($lastDataPoint, $newDataPoint);
            }
        }
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
            if (!is_array($this->_stackingData)) {
                $beforeData = array(0, $this->_data[$counter-1]);
                $currData   = array(0, $this->_data[$counter]);
            } else {
                $beforeData = $this->_stackingData[$counter-1];
                $currData   = $this->_stackingData[$counter];
            }
            if (!is_null($currData[0]) && !is_null($currData[1])) {
                // otherwise do not draw
                if (($counter == 0) || (is_null($beforeData))) {
                    if (($yAxe->_boundsEffective['min'] <= $currData[1]) && ($currData[1] <= $yAxe->_boundsEffective['max'])) {
                        imagesetpixel ($img, $xAxe->valueToPixelAbsolute($counter), $yAxe->valueToPixelAbsolute($currData[1]), $drawColor);
                    } // otherwise do not draw that point since it's out of the drawingarea
                } else {
                    $newCoords = $this->_calculateClippedLineCoords(array($xAxe->valueToPixelAbsolute($counter-1), $yAxe->valueToPixelAbsolute($beforeData[1])),
                                                                    array($xAxe->valueToPixelAbsolute($counter)  , $yAxe->valueToPixelAbsolute($currData[1]  ))
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