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

/**
* Basic data-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

/**
* Class for color handling (extended version of package: PEAR::Image_Color)
*/
require_once("Image/Graph/Color.php");

/**
* Draw the fill as well as the rest (border, ...) of the data-element
* @access private
*/
define('IMAGE_GRAPH_DRAW_FILLANDBORDER',  1);
/**
* Draw just the fill of the data-element
* @access private
*/
define('IMAGE_GRAPH_DRAW_JUSTFILL',       2);
/**
* Draw the rest (all except the fill) of the data-element
* @access private
*/
define('IMAGE_GRAPH_DRAW_JUSTBORDER',     3);

/**
* Class template for a data-element (e.g. "line")
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
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
    * @var array              (4 ints for R,G,B,A); initially black
    * @see setColor()
    * @access private
    */
    var $_color = array(0, 0, 0, 255);

    /**
    * Various attributes for the data-element
    *
    * axisID => axis to which this data-element belongs (0 or 1; for axisY0 or axisY1)
    *
    * @var array
    * @access private
    */
    var $_attributes = array("axisId" => 0);

    /**
    * graph object
    *
    * @var object Image_Graph
    * @access private
    */
    var $_graph = null;

    /**
    * data marker object
    *
    * @var object Image_Graph_DataMarker_Common
    * @access private
    */
    var $_datamarker = null;

    /**
    * fill object
    *
    * @var object Image_Graph_Fill_Common
    * @access private
    */
    var $_fill = null;

    /**
    * Constructor
    *
    * @param  object Image_Graph    parent object
    * @param  array                 numerical data to be drawn
    * @param  array                 attributes like color
    * @access public
    */
    function Image_Graph_Data_Common(&$graph, $data, $attributes=array())
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
    * @param  mixed           any color representation supported by Image_Graph_Color::color2RGB()
    * @see    Image_Graph_Color::color2RGB()
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
    * @param  array   attributes like color
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
    * Pleas note: Only line that exceed the Y-axis-limits are clipped; no clipping for the X-axis (yet)
    *
    * @param array            (array of int) starting point of line
    * @param array            (array of int) destination point of line
    * @return array           (array of array of int) corrected from/to-points
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
    * @access public
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
    * @access public
    */
    function drawGD(&$img, $drawWhat=IMAGE_GRAPH_DRAW_FILLANDBORDER)
    {
        // implementation of this function in the derived diagram-element-classes
    }
}
php?>