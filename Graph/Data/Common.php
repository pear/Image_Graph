<?
// $Id$
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
    var $_attributes = array("color" => array(0, 0, 0), "axeId" => 0);

    /**
    * graph object (of type Image_Graph_Diagram)
    *
    * @var object
    * @access private
    */
    var $_graph = null;

    /**
    * data marker object (of type Image_Graph_DataMarker_...)
    *
    * @var object
    * @access private
    */
    var $_datamarker = null;

    /**
    * Constructor for the class
    *
    * @param  object  graph object (of type Image_Graph_Diagram)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Common(&$graph, $data, $attributes)
    {
        $this->_graph       =& $graph;
        $this->_data        = $data;
        $this->_attributes  = $attributes;
    }
    
    /**
    * Set a data marker to be used
    *
    * @param  string  data representation (e.g. "triangle")
    * @param  array   attributes like color (to be extended to also include shading etc.)
    * @return object  data-marker-object
    * @access public
    */
    function &setDataMarker($representation = "line", $attributes = array())
    {
        $representation = strtolower($representation);
        $dataElementFile  = "Image/Graph/DataMarker/".strtolower($representation).".php";
        $dataElementClass = "Image_Graph_DataMarker_".ucfirst($representation);

        if (!isset($attributes["color"])) {
            $attributes["color"] = $this->_dataDefaultColor;
        }

        if (!class_exists($dataElementClass)) {
            require_once($dataElementFile);
        }
        $newMarker =& new $dataElementClass($attributes);
        $this->_datamarker =& $newMarker;
        return $newMarker;
    }

    /**
    * Draws the data marker (if set)
    *
    * @param gd-resource image-resource to draw to
    * @access private
    */

    function _drawDataMarkerGD(&$img)
    {
        if (is_object($this->_datamarker))
        {
            $graph = &$this->_graph;
            $yAxe  = &$graph->{"axeY".$this->_attributes['axeId']};
            $dataKeys  = array_keys($this->_data);
            $numDatapoints = count($this->_datapoints);
    
            for ($counter=0; $counter<$numDatapoints; $counter++) {
                if (!is_null($this->_datapoints[$counter]) &&
                    ($yAxe->_bounds['min'] <= $this->_data[ $dataKeys[$counter] ]) && ($this->_data[ $dataKeys[$counter] ] <= $yAxe->_bounds['max'])
                   ) { // otherwise do not draw
                    $this->_datamarker->drawGD($img, $this->_datapoints[$counter]);
                }
            }
        }
    }

    /**
    * Calculates coordinates for a line in the drawing-area
    *
    * If one point is outside the drawingarea it recalculated to get a "clipped" line.
    * Pleas note: Only line that exceed the Y-axe-limits are clipped; no direct clipping for the X-axe
    *
    * @param gd-resource image-resource to draw to
    * @access private
    */
    function _calculateClippedLineCoords($from, $to)
    {
        $graph = &$this->_graph;
        $upperLimit = $graph->_drawingareaPos[1];
        $lowerLimit = $graph->_drawingareaPos[1]+$graph->_drawingareaSize[1]-1;
        
        // handle trivial cases first
        if (($from[1] < $upperLimit) &&
            ($to[1]   < $upperLimit)
           ) {
            // both points above the max-limit
            return (array());
        }

        if (($from[1] > $lowerLimit) &&
            ($to[1]   > $lowerLimit)
           ) {
            // both points below the min-limit
            return (array());
        }
        
        $newFrom = $from;
        $newTo   = $to;
        
        if ($from[1] < $upperLimit) {
            // from above the max-limit
            $factor = ($to[0]-$from[0]) / ($to[1]-$from[1]);
            $newFrom = array( $to[0]- $factor*($to[1]-$upperLimit),
                              $upperLimit
                            );
        } elseif ($from[1] > $lowerLimit) {
            // from below the min-limit
            $factor = ($to[0]-$from[0]) / ($to[1]-$from[1]);
            $newFrom = array( $to[0]- $factor*($to[1]-$lowerLimit),
                              $lowerLimit
                            );
        }

        if ($to[1] < $upperLimit) {
            // to above the max-limit
            $factor = ($to[0]-$from[0]) / ($to[1]-$from[1]);
            $newTo = array( $from[0]- $factor*($from[1]-$upperLimit),
                            $upperLimit
                          );
        } elseif ($to[1] > $lowerLimit) {
            // to below the min-limit
            $factor = ($from[0]-$to[0]) / ($from[1]-$to[1]);
            $newTo = array( $from[0]- $factor*($from[1]-$lowerLimit),
                            $lowerLimit
                          );
        }

        return (array($newFrom, $newTo));
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
