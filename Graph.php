<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Stefan Neufeind <pear.neufeind@speedpartner.de>              |
// +----------------------------------------------------------------------+
//
// $Id$

/**************************TODO*******************************************/
/*
 * - ...
 */

define('IMAGE_GRAPH_TICKS_INSIDE',  1);
define('IMAGE_GRAPH_TICKS_OUTSIDE', 2);
define('IMAGE_GRAPH_TICKS_BOTH',    3);

/**
* Class for drawing graphs out of numerical data
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph
{
    /**
    * Width and height of graph canvas
    *
    * @var array; initially 0, 0
    * @access private
    */
    var $_size = array(0, 0);

    /**
    * X/Y-position of diagram (from upper left corner)
    *
    * @var array; initially 0, 0
    * @access private
    */
    var $_pos = array(0, 0);

    /**
    * Width and height of drawingarea of diagram
    *
    * @var array; initially 0, 0
    * @access private
    */
    var $_drawingareaSize = array(0, 0);

    /**
    * X/Y-position of drawingarea of diagram on graph canvas
    *
    * @var array; initially 0, 0
    * @access private
    */
    var $_drawingareaPos = array(0, 0);

    /**
    * Extra borderspace that should be added
    *
    * @var int  space in pixels; initially all 0
    * @access private
    */
    var $_borderspace = 0;

    /**
    * Background color
    *
    * @var array (3 ints for R,G,B); initially white
    * @access private
    */
    var $_bgcolor = array(255, 255, 255);

    /**
    * Store properties and values for the axes
    *
    * @var array complex structure - to be further detailed in the docs at a later stage :-((
    * @access private
    */
    var $_axes = array("x" => array(),
                       "y" => array(
                                    array(), array() // 2 y-axes
                                   )
                      );

    /**
    * normally 0; if set to 1 the data-elements don't start on the y-axe but a bit further right - needed for bar-graphs
    *
    * @var array
    * @see addData()
    * @access private
    */
    var $_addExtraSpace = 0;

    /**
    * Default color for new data; used by addData
    *
    * @var array (3 ints for R,G,B); initially black
    * @see addData()
    * @access private
    */
    var $_dataDefaultColor = array(0, 0, 0);

    /**
    * Data elements of the diagram (e.g. a "line")
    *
    * @var array
    * @see addData()
    * @access private
    */
    var $_dataElements = array();

    /**
    * Constructor for the class
    *
    * @param  int     width of graph-image
    * @param  int     height of graph-image
    * @param  int     x-position of graph-image
    * @param  int     y-position of graph-image
    * @access public
    */
    function Image_Graph($width, $height, $pos_x=0, $pox_y=0)
    {
        $this->_size = array($width, $height);
        $this->_pos  = array($pox_x, $pox_y);
        $this->setAxesColor(array(0, 0, 0)); // set default color to black, all axes
        $this->setAxesYTickStyle (IMAGE_GRAPH_TICKS_OUTSIDE, 0);
        $this->setAxesYTickStyle (IMAGE_GRAPH_TICKS_OUTSIDE, 1);
        $this->setAxesYTickSize  (10, 0);
        $this->setAxesYTickSize  (10, 1);

        $this->_drawingareaSize = $this->_size;
        $this->_drawingareaPos  = $this->_pos;
    }

    /**
    * Sets background color of canvas
    *
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setBackgroundColor($color)
    {
        $this->_bgcolor = $color;
    }

    /**
    * Sets space of graph from each border
    *
    * @param  int     space (in pixel) from each border
    * @access public
    */
    function setSpaceFromBorder($space)
    {
        $this->_borderspace = max(0, $space); // don't allow values < 0
    }

    /**
    * Set minimum value for Y-axe
    *
    * @param  int/float  min value, or null for automatic mode
    * @param  int        optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYMin($axesYMin, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['min'] = $axesYMin;
    }

    /**
    * Set maximum value for Y-axe
    *
    * @param  int/float  min value, or null for automatic mode
    * @param  int        optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYMax($axesYMax, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['max'] = $axesYMax;
    }

    /**
    * Set tick-style for ticks on a Y-axe
    *
    * use constants IMAGE_GRAPH_TICKS_INSIDE, IMAGE_GRAPH_TICKS_OUTSIDE or IMAGE_GRAPH_TICKS_BOTH
    *
    * @param  int     tick-style
    * @param  int     optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYTickStyle($style, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['tickStyle'] = $style;
    }

    /**
    * Set size for (major) ticks on a Y-axe
    *
    * size is for the "major ticks"; all minor ticks will be half that size
    *
    * @param  int     size in pixels
    * @param  int     optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYTickSize($size, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['tickSize'] = max(0, $size);
    }
    
    /**
    * Set major ticks on a Y-axe
    *
    * @param  array   list of values on the Y-axe which should be "major ticks"
    * @param  int     optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYTicksMajor($ticks, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['ticksMajor'] = $ticks;
    }
    
    /**
    * Set minor ticks on a Y-axe
    *
    * @param  array   list of values on the Y-axe which should be "minor ticks"
    * @param  int     optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYTicksMinor($ticks, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['ticksMinor'] = $ticks;
    }
    
    /**
    * Set color of axes
    *
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setAxesColor($axesColor)
    {
        $this->_axes["x"]["color"]    = $axesColor;
        $this->_axes["y"][0]["color"] = $axesColor; // color for left  axe
        $this->_axes["y"][1]["color"] = $axesColor; // color for right axe
    }

    /**
    * Set default color for new data; used by addData
    *
    * @param  array (3 ints for R,G,B)
    * @see    addData()
    * @access public
    */
    function setDataDefaultColor($dataDefaultColor)
    {
        $this->_dataDefaultColor = $dataDefaultColor;
    }
    
    /**
    * Add new data to the graph
    *
    * @param  array   data to draw
    * @param  string  data representation (e.g. "line")
    * @param  array   attributes like color (to be extended to also include shading etc.)
    * @access public
    */
    function addData($data, $representation = "line", $attributes = array())
    {
        $representation = strtolower($representation);
        $dataElementFile  = "Image/Graph/".strtolower($representation).".php";
        $dataElementClass = "Image_Graph_Data_".ucfirst($representation);

        if (!isset($attributes["color"])) {
            $attributes["color"] = $this->_dataDefaultColor;
        }
        if (!isset($attributes["axeId"])) {
            $attributes["axeId"] = 0;
        }

        if (!class_exists($dataElementClass)) {
            require_once($dataElementFile);
        }
        $myNew = new $dataElementClass(&$this, $data, $attributes);
        $this->_dataElements[] = $myNew;
    }

    /**
    * Calculate the min/max-values of the Y-axe if they are "null" (auto-detect of values)
    *
    * @access private
    */
    function _calculateAxesYMinMax()
    {
        for($axeCount=0; $axeCount<=1; $axeCount++) {
            if (!isset($this->_axes['y'][$axeCount]['min'])) {
                foreach ($this->_dataElements as $currDataElement) {
                    if (!isset($this->_axes['y'][$axeCount]['min'])) {
                        $this->_axes['y'][$axeCount]['min'] = $currDataElement->_data[0];
                    }
    
                    foreach ($currDataElement->_data as $currData) {
                        if ($this->_axes['y'][$axeCount]['min'] > $currData) {
                            $this->_axes['y'][$axeCount]['min'] = $currData;
                        }
                    }
                }
            }
    
            if (!isset($this->_axes['y'][$axeCount]['max'])) {
                foreach ($this->_dataElements as $currDataElement) {
                    if (!isset($this->_axes['y'][$axeCount]['max'])) {
                        $this->_axes['y'][$axeCount]['max'] = $currDataElement->_data[0];
                    }
    
                    foreach ($currDataElement->_data as $currData) {
                        if ($this->_axes['y'][$axeCount]['max'] < $currData) {
                            $this->_axes['y'][$axeCount]['max'] = $currData;
                        }
                    }
                }
            }
    
            // correction if only one y-value is present in the diagram
            if ($this->_axes['y'][$axeCount]['min'] == $this->_axes['y'][$axeCount]['max']) {
                if (($this->_axes['y'][$axeCount]['min']-1) >= 0) {
                    $this->_axes['y'][$axeCount]['min']--;
                }
                $this->_axes['y'][$axeCount]['max']++;
            }
        }
    }

    /**
    * Prepare some internal variables
    *
    * This function is needed to do some limit-checking, set internal variables to appropriate values etc.
    * It's necessary to call this function shortly before actually drawing since it does all necessary preparations.
    *
    * @access private
    */
    function _prepareInternalVariables()
    {
        $borderspaceSum=array("top"    => $this->_borderspace,
                              "bottom" => $this->_borderspace,
                              "left"   => $this->_borderspace,
                              "right"  => $this->_borderspace);
        
        if (( $this->_axes['y'][0]['containsData']) &&
            (($this->_axes['y'][0]['tickStyle'] == IMAGE_GRAPH_TICKS_OUTSIDE) ||
             ($this->_axes['y'][0]['tickStyle'] == IMAGE_GRAPH_TICKS_BOTH)
            )
           ) {
            $borderspaceSum["left"]  += $this->_axes['y'][0]['tickSize'];
        }

        if (( $this->_axes['y'][1]['containsData']) &&
            (($this->_axes['y'][1]['tickStyle'] == IMAGE_GRAPH_TICKS_OUTSIDE) ||
             ($this->_axes['y'][1]['tickStyle'] == IMAGE_GRAPH_TICKS_BOTH)
            )
           ) {
            $borderspaceSum["right"] += $this->_axes['y'][1]['tickSize'];
        }

              
        $this->_drawingareaSize = array ($this->_size[0]-$borderspaceSum["left"]-$borderspaceSum["right"],
                                         $this->_size[1]-$borderspaceSum["top"] -$borderspaceSum["bottom"]);
        $this->_drawingareaPos  = array ($this->_pos[0] +$borderspaceSum["left"],
                                         $this->_pos[1] +$borderspaceSum["top"]);
    }
    
    /**
    * Calculate relative position (in pixel-coordinates) for a certain pixel-value
    *
    * @param  float   data value
    * @param  int     optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function _calculateValueToPixelLinear($currData, $axeId = 0)
    {
        $relativeYPosition = $this->_drawingareaSize[1] - 1 - floor(
                             (float) ($this->_drawingareaSize[1]-1) /
                             ($this->_axes['y'][$axeId]['max'] - $this->_axes['y'][$axeId]['min']) *
                             ($currData-$this->_axes['y'][$axeId]['min'])
                             );
        return ($relativeYPosition);
    }
  
    /**
    * Calculate the datapoints (x/y-positions of data) in the diagram for each diagram element
    *
    * @access private
    */
    function _calculateDatapoints()
    {
        $this->_calculateAxesYMinMax();
        $maxElements = 0;

        // calculate max. number of elements
        foreach ($this->_dataElements as $currDataElement) {
            $numElement = count($currDataElement->_data);
            if ($maxElements < $numElement) {
                $maxElements = $numElement;
            }
        }

        $relativeXPositions = array();
        $pixelPerColumn = (float) ($this->_drawingareaSize[0]-1) / ($maxElements-1 + ($this->_addExtraSpace));
        for ($counter=0; $counter<$maxElements; $counter++) {
            $relativeXPositions[] = round(($counter + ($this->_addExtraSpace*0.5)) * $pixelPerColumn);
        }

        // calculate linear position of datapoints
        foreach ($this->_dataElements as $currDataElementKey => $currDataElementValue) {
            $currDataElement = &$this->_dataElements[$currDataElementKey];
            $currDataElement->_datapoints = array();
            if (isset($currDataElement->_attributes["axeId"])) {
                $axeId = $currDataElement->_attributes["axeId"];
            } else {
                $axeId = 0;
            }
            reset($relativeXPositions);

            foreach ($currDataElement->_data as $currDataKey => $currDataValue) {
                $currData = &$currDataElement->_data[$currDataKey];
                $relativeYPosition = $this->_calculateValueToPixelLinear($currData, $axeId);
                $currDataElement->_datapoints[] = array($this->_drawingareaPos[0] + current($relativeXPositions),
                                                        $this->_drawingareaPos[1] + $relativeYPosition);
                next($relativeXPositions);
            }
        }
    }

    /**
    * Draw axes for diagram
    *
    * @param  GD-resource image to draw to
    * @access private
    */
    function _drawGDAxes(&$img)
    {
        $drawColor = imagecolorallocate($img, $this->_axes['x']['color'][0], $this->_axes['x']['color'][1], $this->_axes['x']['color'][2]);
        // draw X-axe
        imageline    ($img, $this->_drawingareaPos[0],                              $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                            $this->_drawingareaPos[0]+$this->_drawingareaSize[0]-1, $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1, $drawColor);
        // draw Y-axes
        $axesXpositions = array ($this->_drawingareaPos[0],
                                 $this->_drawingareaPos[0]+$this->_drawingareaSize[0]-1);

        $axesXfactors   = array (1, -1);
        
        for($axeCount=0; $axeCount<=1; $axeCount++) {
            if ($this->_axes['y'][$axeCount]['containsData']) {
                $drawColor = imagecolorallocate($img, $this->_axes['y'][$axeCount]['color'][0], $this->_axes['x'][$axeCount]['color'][1], $this->_axes['x'][$axeCount]['color'][2]);
                imageline    ($img, $axesXpositions[$axeCount], $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                                    $axesXpositions[$axeCount], $this->_drawingareaPos[1], $drawColor);
    
                if (is_array($this->_axes['y'][$axeCount]['ticksMajor'])) {
                    foreach ($this->_axes['y'][$axeCount]['ticksMajor'] as $currTick) {
                        if (($currTick >= $this->_axes['y'][$axeCount]['min']) &&
                            ($currTick <= $this->_axes['y'][$axeCount]['max'])) {
                            $relativeYPosition = $this->_calculateValueToPixelLinear($currTick, $axeCount);
                            $tickSize = $this->_axes['y'][$axeCount]['tickSize'] * $axesXfactors[$axeCount];
                            switch ($this->_axes['y'][$axeCount]['tickStyle']) {
                                case IMAGE_GRAPH_TICKS_INSIDE: 
                                      imageline ($img, $axesXpositions[$axeCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                                       $axesXpositions[$axeCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                 $drawColor);
                                      break;
                                case IMAGE_GRAPH_TICKS_OUTSIDE: 
                                      imageline ($img, $axesXpositions[$axeCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                       $axesXpositions[$axeCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                                 $drawColor);
                                      break;
                                case IMAGE_GRAPH_TICKS_BOTH: 
                                      imageline ($img, $axesXpositions[$axeCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                       $axesXpositions[$axeCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                 $drawColor);
                                      break;
                            }
                        }
                    }
                }
                if (is_array($this->_axes['y'][$axeCount]['ticksMinor'])) {
                    foreach ($this->_axes['y'][$axeCount]['ticksMinor'] as $currTick) {
                        if (($currTick >= $this->_axes['y'][$axeCount]['min']) &&
                            ($currTick <= $this->_axes['y'][$axeCount]['max'])) {
                            $relativeYPosition = $this->_calculateValueToPixelLinear($currTick, $axeCount);
                            $tickSize = ceil($this->_axes['y'][$axeCount]['tickSize']/2) * $axesXfactors[$axeCount];
                            switch ($this->_axes['y'][$axeCount]['tickStyle']) {
                                case IMAGE_GRAPH_TICKS_INSIDE: 
                                      imageline ($img, $axesXpositions[$axeCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                                       $axesXpositions[$axeCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                 $drawColor);
                                      break;
                                case IMAGE_GRAPH_TICKS_OUTSIDE: 
                                      imageline ($img, $axesXpositions[$axeCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                       $axesXpositions[$axeCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                                 $drawColor);
                                      break;
                                case IMAGE_GRAPH_TICKS_BOTH: 
                                      imageline ($img, $axesXpositions[$axeCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                       $axesXpositions[$axeCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                                 $drawColor);
                                      break;
                            }
                        }
                    }
                }
            }
        }

        // TO DO: add separators / ticks for x-axe
        // TO DO: add possibility to turn on a "grid"
    }

    /**
    * Create a GD-image-resource for the graph
    *
    * @return gd-resource true color GD-resource containing image of graph
    * @access public
    */
    function getGDImage()
    {
        $this->_prepareInternalVariables();
        $this->_calculateDatapoints();

        // GD-specific part
        $img = imagecreatetruecolor($this->_size[0], $this->_size[1]);
        $bgcolor = imagecolorallocate($img, $this->_bgcolor[0], $this->_bgcolor[1], $this->_bgcolor[2]);
        imagefill($img, 0, 0, $bgcolor);

        $this->_drawGDAxes($img);
        foreach ($this->_dataElements as $currDataElement) {
            $currDataElement->drawGD($img, $this);
        }

        return $img;
    }
}
