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
* Line data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

/**
* The parent class
*/
require_once("Image/Graph/Data/Common.php");

/**
* Line data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Data_Line extends Image_Graph_Data_Common
{
    /**
    * Constructor
    *
    * @param  object Image_Graph    parent object
    * @param  array                 numerical data to be drawn
    * @param  array                 attributes like color
    * @access public
    */
    function Image_Graph_Data_Line(&$parent, $data, $attributes=array())
    {
        parent::Image_Graph_Data_Common($parent, $data, $attributes);
    }

    /**
    * Prepare given dataElements of this type for stacking
    *
    * This function is called from inside the Image_Graph-baseinstance to stack
    * elements of this data-type. This is done for every data-type in it's
    * respective classes separately because stacking might depend on the type
    * of data-representation.
    * This method is called statically and receives a list of references to
    * objects of this data-type. Using the references it can directly access
    * all methods and attributes of each object.
    *
    * @param  array           references to dataElements (objects of this type)
    * @access public
    * @static
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
    * Draw all diagram elements in this stacking-group
    *
    * The specific data-type-class knows best how to draw the data-elements
    * of this type. If stacking is used this function will be called instead
    * of drawGD().
    * The best results for drawing lines are achieved by first drawing the
    * fill of each element and afterwards drawing the lines. This is the best
    * solution for the resulting image.
    *
    * @param  array           references to dataElements (objects of this type)
    * @access public
    * @static
    */
    function stackingDrawGD(&$dataElements, &$img)
    {
        foreach($dataElements as $element) {
            $element->drawGD($img, IMAGE_GRAPH_DRAW_JUSTFILL);
        }
        foreach($dataElements as $element) {
            $element->drawGD($img, IMAGE_GRAPH_DRAW_JUSTBORDER);
        }
    }

    /**
    * Draws diagram element
    *
    * @param resource     GD-resource to draw to
    * @param int          choose what to draw; use constants IMAGE_GRAPH_DRAW_FILLANDBORDER, IMAGE_GRAPH_DRAW_JUSTFILL or IMAGE_GRAPH_DRAW_JUSTBORDER
    * @access public
    */
    function drawGD(&$img, $drawWhat=IMAGE_GRAPH_DRAW_FILLANDBORDER)
    {
        // TO DO: implement handling for $drawWhat

        $graph = &$this->_graph;
        $axisX = &$graph->axisX;
        $axisY = &$graph->{"axisY".$this->_attributes['axisId']};
        $drawColor = Image_Graph_Color::allocateColor($img, $this->_color);
        $numData = count($this->_data);

        if ((($drawWhat == IMAGE_GRAPH_DRAW_FILLANDBORDER) ||
             ($drawWhat == IMAGE_GRAPH_DRAW_JUSTFILL)) &&
            (isset($this->_fill))) {
            $polygon=array();
            if (!is_array($this->_stackingData)) {
                // if we don't use data-stacking
                for ($counter=0; $counter<$numData; $counter++) {
                    $datapoint = &$this->_data[$counter];
                    if (is_null($datapoint)) {
                        if (!empty($polygon)) {
                            // fill polygon
                            $this->_fill->drawGDPolygon($img, $polygon);
                            // empty point-array so we can start with the next polygon
                            $polygon=array();
                        }
                    } else {
                        $xValue = $axisX->valueToPixelAbsolute($counter);
                        // prepend lower point to array
                        $temppoint = array($xValue, $axisY->valueToPixelAbsolute(0));
                        array_unshift($polygon, $temppoint);
                        // append higher point to array
                        $temppoint = array($xValue, $axisY->valueToPixelAbsolute($datapoint));
                        $polygon[] = $temppoint;
                    }
                }
            } else {
                // if we do use data-stacking
                for ($counter=0; $counter<$numData; $counter++) {
                    if (is_null($this->_stackingData[$counter][0]) || is_null($this->_stackingData[$counter][1])) {
                        if (!empty($polygon)) {
                            // fill polygon
                            $this->_fill->drawGDPolygon($img, $polygon);
                            // empty point-array so we can start with the next polygon
                            $polygon=array();
                        }
                    } else {
                        // prepend lower point to array
                        $temppoint = array($axisX->valueToPixelAbsolute($counter), $axisY->valueToPixelAbsolute($this->_stackingData[$counter][0]));
                        array_unshift($polygon, $temppoint);
                        // append higher point to array
                        $temppoint = array($axisX->valueToPixelAbsolute($counter), $axisY->valueToPixelAbsolute($this->_stackingData[$counter][1]));
                        $polygon[] = $temppoint;
                    }
                }
            }
            if (!empty($polygon)) {
                $this->_fill->drawGDPolygon($img, $polygon);
            }
        }

        if (($drawWhat == IMAGE_GRAPH_DRAW_FILLANDBORDER) ||
            ($drawWhat == IMAGE_GRAPH_DRAW_JUSTBORDER)) {
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
                        if (($axisY->_boundsEffective['min'] <= $currData[1]) && ($currData[1] <= $axisY->_boundsEffective['max'])) {
                            imagesetpixel ($img, $axisX->valueToPixelAbsolute($counter), $axisY->valueToPixelAbsolute($currData[1]), $drawColor);
                        } // otherwise do not draw that point since it's out of the drawingarea
                    } else {
                        $newCoords = $this->_calculateClippedLineCoords(array($axisX->valueToPixelAbsolute($counter-1), $axisY->valueToPixelAbsolute($beforeData[1])),
                                                                        array($axisX->valueToPixelAbsolute($counter)  , $axisY->valueToPixelAbsolute($currData[1]  ))
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
}
?>