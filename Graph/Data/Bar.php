<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
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
* Class-file for the bar data-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @category images
* @license  The PHP License, version 2.02
*/

/**
* The parent class
*/
require_once("Image/Graph/Data/Common.php");

/**
* Bar data-element
*
* This class is used to draw a bar in the diagram.
* Support for stacking is implemented in this element.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Data_Bar extends Image_Graph_Data_Common
{
    /**
    * Constructor
    *
    * @param  object Image_Graph    parent object
    * @param  array                 numerical data to be drawn
    * @param  array                 attributes like color
    * @access public
    */
    function Image_Graph_Data_Bar(&$parent, $data, $attributes=array())
    {
        if (!isset($attributes['width'])) {
            $attributes['width'] = 0.5;
        }
        parent::Image_Graph_Data_Common($parent, $data, $attributes);

        // a bar is filled by default
        $this->setFill("solid", array("color" => $this->_color));
        $parent->_addExtraSpace = 1;
    }

    /**
    * Prepare given dataElements of this type for stacking
    *
    * @param array    references to dataElements (objects of this type)
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
    * Checks if two arrays are equal
    *
    * @param array
    * @param array
    * @return boolean   true if both are equal
    * @access private
    */
    function _arraysEqual($arr1, $arr2)
    {
        $equal = true;
        foreach ($arr1 as $key => $value) {
            if (!isset($arr2[$key]) || ($value != $arr2[$key])) {
                $equal = false;
            }
        }
        return $equal;
    }

    /**
    * Draws the data element, using GD-output
    *
    * The parameter $drawWhat is needed to be able to first draw the fill
    * of all bar-elements and later draw the border, in case that stacking
    * is used.
    *
    * @param resource     GD-resource to draw to
    * @param int          choose what to draw; use constants IMAGE_GRAPH_DRAW_FILLANDBORDER, IMAGE_GRAPH_DRAW_JUSTFILL or IMAGE_GRAPH_DRAW_JUSTBORDER
    * @access public
    */
    function drawGD(&$img, $drawWhat=IMAGE_GRAPH_DRAW_FILLANDBORDER)
    {
        $graph = &$this->_graph;
        $axisX  = &$graph->axisX;
        $axisY  = &$graph->{"axisY".$this->_attributes['axisId']};
        $drawColor = Image_Graph_Color::allocateColor($img, $this->_color);
        $numData = count($this->_data);

        if ($numData < 2) {
          $halfWidthPixel = floor($graph->_drawingareaSize[1] / 2);
        } else {
          $halfWidthPixel = floor(($axisX->valueToPixelRelative(1) - $axisX->valueToPixelRelative(0)) / 2 * $this->_attributes['width']);
        }

        for ($counter=0; $counter<$numData; $counter++) {
            if (!is_array($this->_stackingData)) {
                $currData = array(0, $this->_data[$counter]);
            } else {
                $currData = $this->_stackingData[$counter];
            }
            if (!is_null($currData[0]) && !is_null($currData[1])) {
                // otherwise do not draw
                $xPos = $axisX->valueToPixelAbsolute($counter);

                // clip if necessary
                if ($currData[0] < $axisY->_boundsEffective['min']) {
                    $currData[0] = $axisY->_boundsEffective['min'];
                }
                if ($currData[1] > $axisY->_boundsEffective['max']) {
                    $currData[1] = $axisY->_boundsEffective['max'];
                }

                $points = array(array($xPos-$halfWidthPixel, $axisY->valueToPixelAbsolute($currData[1])),
                                array($xPos+$halfWidthPixel, $axisY->valueToPixelAbsolute($currData[0])));

                if ((($drawWhat == IMAGE_GRAPH_DRAW_FILLANDBORDER) ||
                     ($drawWhat == IMAGE_GRAPH_DRAW_JUSTFILL)) &&
                    (isset($this->_fill))) {
                    $this->_fill->drawGDBox($img, $points);
                }

                if (($drawWhat == IMAGE_GRAPH_DRAW_FILLANDBORDER) ||
                    ($drawWhat == IMAGE_GRAPH_DRAW_JUSTBORDER)) {
                    if (!is_null($this->_fill) &&
                        (strtolower(get_class($this->_fill)) == "image_graph_fill_solid") &&
                        ($this->_arraysEqual($this->_color, @$this->_fill->_attributes["color"]))
                       ) {
                        // simply do nothing in this case since drawing a border for the bar in the same color will
                        // look the same if a solid fill is used as if we simply not draw the border at all :-))
                    } else {
                        imagerectangle ($img, $points[0][0], $points[0][1], $points[1][0], $points[1][1], $drawColor);
                    }
                }
            }
        }
    }
}
?>