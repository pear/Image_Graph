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
    function Image_Graph_Data_Common(&$parent, $data, $attributes)
    {
        $this->_parent      = &$parent;
        $this->_data        = $data;
        $this->_attributes  = $attributes;
        
        $parent->_axes['y'][ $attributes['axeId'] ]['containsData'] = true;
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
        $graph = &$this->_parent;
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