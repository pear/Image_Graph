<?
// $Id$

require_once("Image/Graph/Color.php"); // extended version of package: PEAR::Image_Color

define('IMAGE_GRAPH_DRAW_FILLANDBORDER',  1);
define('IMAGE_GRAPH_DRAW_JUSTFILL',       2);
define('IMAGE_GRAPH_DRAW_JUSTBORDER',     3);

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
    * Data to be drawn (array of numerical values)
    *
    * @var array  data to be drawn
    * @access private
    */
    var $_data = array();

    /**
    * Color for element
    *
    * @var array (3 ints for R,G,B); initially null
    * @access private
    */
    var $_color = array(0, 0, 0);

    /**
    * Attributes for drawing the data element
    *
    * @var array
    * @access private
    */
    var $_attributes = array("axisId" => 0);

    /**
    * graph object (of type Image_Graph)
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
    * fill object (of type Image_Graph_Fill_...)
    *
    * @var object
    * @access private
    */
    var $_fill = null;

    /**
    * Constructor for the class
    *
    * @param  object  graph object (of type Image_Graph)
    * @param  array   numerical data to be drawn
    * @access public
    */
    function Image_Graph_Data_Common(&$graph, $data, $attributes)
    {
        $this->_graph       =& $graph;
        $this->_data        = $data;
        if (isset($attributes['color'])) {
            $this->setColor($attributes['color']);
            unset($attributes['color']);
        }
        $this->_attributes  = $attributes;
    }

    /**
    * Set color
    *
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setColor($color)
    {
        $this->_color = Image_Graph_Color::color2RGB($color);
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
        if (is_null($representation)) {
            unset($this->_datamarker);
            return null;
        }

        $representation = strtolower($representation);
        $dataMarkerFile  = "Image/Graph/DataMarker/".ucfirst($representation).".php";
        $dataMarkerClass = "Image_Graph_DataMarker_".ucfirst($representation);

        if (!isset($attributes["color"])) {
            $attributes["color"] = $this->_dataDefaultColor;
        }

        if (!class_exists($dataMarkerClass)) {
            require_once($dataMarkerFile);
        }
        $newMarker =& new $dataMarkerClass($attributes);
        $this->_datamarker =& $newMarker;
        return $newMarker;
    }

    /**
    * Set a fill element to be used
    *
    * @param  string  type of fill (e.g. "solid")
    * @param  array   attributes like color
    * @return object  fill-object
    * @access public
    */
    function &setFill($type = "solid", $attributes = array())
    {
        if (is_null($type)) {
            unset($this->_fill);
            return null;
        }

        $type = strtolower($type);
        $fillFile  = "Image/Graph/Fill/".ucfirst($type).".php";
        $fillClass = "Image_Graph_Fill_".ucfirst($type);

        if (!isset($attributes["color"])) {
            $attributes["color"] = $this->_dataDefaultColor;
        }

        if (!class_exists($fillClass)) {
            require_once($fillFile);
        }
        $newFill =& new $fillClass($attributes);
        $this->_fill =& $newFill;
        return $newFill;
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
            $axisX  = &$graph->axisX;
            $axisY  = &$graph->{"axisY".$this->_attributes['axisId']};
            $numData = count($this->_data);

            for ($counter=0; $counter<$numData; $counter++) {
                if (!is_null($this->_data[$counter]) &&
                    ($axisY->_boundsEffective['min'] <= $this->_data[$counter]) && ($this->_data[$counter] <= $axisY->_boundsEffective['max'])
                   ) { // otherwise do not draw
                    $this->_datamarker->drawGD($img, array($axisX->valueToPixelAbsolute($counter),
                                                           $axisY->valueToPixelAbsolute($this->_data[$counter])
                                                          )
                                               );
                }
            }
        }
    }

    /**
    * Calculates coordinates for a line in the drawing-area
    *
    * If one point is outside the drawingarea it recalculated to get a "clipped" line.
    * Pleas note: Only line that exceed the Y-axis-limits are clipped; no direct clipping for the X-axis
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
    * Draw all diagram elements in this stacking-group
    *
    * @param array    references to dataElements (objects of this type)
    * @access private
    * @static
    */
    function stackingDrawGD(&$dataElements, &$img)
    {
        foreach($dataElements as $element) {
            $element->drawGD($img);
        }
    }

    /**
    * Draws diagram element
    *
    * @param gd-resource  image-resource to draw to
    * @param int          choose what to draw; use constants IMAGE_GRAPH_DRAW_FILLANDBORDER, IMAGE_GRAPH_DRAW_JUSTFILL or IMAGE_GRAPH_DRAW_JUSTBORDER
    * @access private
    */
    function drawGD(&$img, $drawWhat=IMAGE_GRAPH_DRAW_FILLANDBORDER)
    {
        // implementation of this function in the derived diagram-element-classes
    }
}
?>
