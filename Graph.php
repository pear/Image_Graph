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
    * Background color
    *
    * @var array (3 ints for R,G,B); initially white
    * @access private
    */
    var $_bgcolor = array(255, 255, 255);

    /**
    * Minimum value in diagram
    *
    * @var float minimal value; initially null => determine value automatically from data
    * @access private
    */
    var $_axesYMin = null;

    /**
    * Maximum value in diagram
    *
    * @var float maximal value; initially null => determine value automatically from data
    * @access private
    */
    var $_axesYMax = null;

    /**
    * Color of axes
    *
    * @var array (3 ints for R,G,B); initially black
    * @access private
    */
    var $_axesColor = array(0, 0, 0);

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
        $this->_drawingareaSize = array ($this->_size[0]-(2*$space), $this->_size[1]-(2*$space));
        $this->_drawingareaPos  = array ($this->_pos[0]+$space, $this->_pos[1]+$space);
    }

    /**
    * Set minimum value for Y-axe
    *
    * @param  int  min value, or null for automatic mode
    * @access public
    */
    function setAxesYMin($axesYMin)
    {
        $this->_axesYMin = $axesYMin;
    }

    /**
    * Set maximum value for Y-axe
    *
    * @param  int  max value, or null for automatic mode
    * @access public
    */
    function setAxesYMax($axesYMax)
    {
        $this->_axesYMax = $axesYMax;
    }

    /**
    * Set color of axes
    *
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setAxesColor($axesColor)
    {
        $this->_axesColor = $axesColor;
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

        if (!class_exists($dataElementClass)) {
            require_once($dataElementFile);
        }
        $myNew = new $dataElementClass($this, $data);
        $this->_dataElements[] = $myNew;
    }

    /**
    * Calculate the min/max-values of the Y-axe if they are "null" (auto-detect of values)
    *
    * @access private
    */
    function _calculateAxesYMinMax()
    {
        if (is_null($this->_axesYMin)) {
            foreach ($this->_dataElements as $currDataElement) {
                if (is_null($this->_axesYMin)) {
                    $this->_axesYMin = $currDataElement->_data[0];
                }

                foreach ($currDataElement->_data as $currData) {
                    if ($this->_axesYMin > $currData) {
                        $this->_axesYMin = $currData;
                    }
                }
            }
        }

        if (is_null($this->_axesYMax)) {
            foreach ($this->_dataElements as $currDataElement) {
                if (is_null($this->_axesYMax)) {
                    $this->_axesYMax = $currDataElement->_data[0];
                }

                foreach ($currDataElement->_data as $currData) {
                    if ($this->_axesYMax < $currData) {
                        $this->_axesYMax = $currData;
                    }
                }
            }
        }

        // correction if only one y-value is present in the diagram
        if ($this->_axesYMin == $this->_axesYMax) {
            if (($this->_axesYMin-1) >= 0) {
                $this->_axesYMin--;
            }
            $this->_axesYMax++;
        }
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

        $x_positions = array();
        $pixelPerColumn = floor ((float) $this->_drawingareaSize[0] / ($maxElements-1 + ($this->_addExtraSpace*2)));
        for ($counter=0; $counter<$maxElements; $counter++) {
            $x_positions[] = ($counter + $this->_addExtraSpace) * $pixelPerColumn;
        }

        // calculate linear position of datapoints
        foreach ($this->_dataElements as $currDataElementKey => $currDataElementValue) {
            $currDataElement = &$this->_dataElements[$currDataElementKey];
            $currDataElement->_datapoints = array();
            reset($x_positions);
            foreach ($currDataElement->_data as $currDataKey => $currDataValue) {
                $currData = &$currDataElement->_data[$currDataKey];
                if (($currData < $this->_axesYMin) ||  ($currData > $this->_axesYMax)) {
                    $y_position = null;
                } else {
                    $y_position = $this->_drawingareaSize[1] - floor((float) $this->_drawingareaSize[1] / ($this->_axesYMax-$this->_axesYMin) * ($currData-$this->_axesYMin));
                }
                $currDataElement->_datapoints[] = array($this->_drawingareaPos[0]+current($x_positions),
                                                        $this->_drawingareaPos[1]+$y_position);
                next($x_positions);
            }
        }
    }

    /**
    * Draw axes for diagram
    *
    * @param  GD-resource image to draw to
    * @access private
    */
    function _drawAxes(&$img)
    {
        $drawColor = imagecolorallocate($img, $this->_axesColor[0], $this->_axesColor[1], $this->_axesColor[2]);
        // draw X-axe
        imageline    ($img, $this->_drawingareaPos[0],                            $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                            $this->_drawingareaPos[0]+$this->_drawingareaSize[0], $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1, $drawColor);
        // draw Y-axe
        imageline    ($img, $this->_drawingareaPos[0], $this->_drawingareaPos[1]+$this->_drawingareaSize[1],
                            $this->_drawingareaPos[0], $this->_drawingareaPos[1], $drawColor);

        // TO DO: add separators to the axes
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
        $img = imagecreatetruecolor($this->_size[0], $this->_size[1]);
        $bgcolor = imagecolorallocate($img, $this->_bgcolor[0], $this->_bgcolor[1], $this->_bgcolor[2]);
        imagefill($img, 0, 0, $bgcolor);

        $this->_calculateDatapoints();
        $this->_drawAxes($img);
        foreach ($this->_dataElements as $currDataElement) {
            $currDataElement->drawGD($img, $this);
        }

        return $img;
    }
}
