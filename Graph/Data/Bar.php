<?
// $Id$
/**
* Bar data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Image/Graph/Data/Common.php");

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
    * @param  object  parent object (of type Image_Graph)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Bar(&$parent, $data, $attributes)
    {
        if (!isset($attributes['width'])) {
            $attributes['width'] = 0.5;
        }
        parent::Image_Graph_Data_Common($parent, $data, $attributes);
        $parent->_addExtraSpace = 1;
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

        if ($numData < 2) {        
          $halfWidthPixel = floor($graph->_drawingareaSize[1] / 2);
        } else {
          $halfWidthPixel = floor(($xAxe->valueToPixelRelative(1) - $xAxe->valueToPixelRelative(0)) / 2 * $this->_attributes['width']);
        }
        
        for ($counter=0; $counter<$numData; $counter++) {
            if (!is_array($this->_stackingData)) {
                $currData = array(0, $this->_data[$counter]);
            } else {
                $currData = $this->_stackingData[$counter];
            }
            if (!is_null($currData[0]) && !is_null($currData[1])) {
                // otherwise do not draw
                $xPos = $xAxe->valueToPixelAbsolute($counter);
                
                // clip if necessary
                if ($currData[0] < $yAxe->_boundsEffective['min']) {
                    $currData[0] = $yAxe->_boundsEffective['min'];
                }
                if ($currData[1] > $yAxe->_boundsEffective['max']) {
                    $currData[1] = $yAxe->_boundsEffective['max'];
                }
                imagefilledrectangle ($img, $xPos-$halfWidthPixel, $yAxe->valueToPixelAbsolute($currData[1]),
                                            $xPos+$halfWidthPixel, $yAxe->valueToPixelAbsolute($currData[0]),
                                      $drawColor);
            }
        }
    }
}
?>