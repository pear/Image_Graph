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
 * - automated creation of "ticks" (major and minor)
 * - add a grid (horizontal / vertical)
 * - add images (background, ...)
 * - add alpha-channel functionality (for half-transparent bars etc.)
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
    * Default options for fonts
    *
    * @var array      stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @access private
    */
    var $_defaultFontOptions = array();

    /**
    * Font options for title
    *
    * if any information is not explicitly set (e.g. fontPath) the defaults from $_defaultFontOptions are taken
    *
    * @var array      stores information like fontPath, fontFile, antiAliasing etc.
    * @see $_defaultFontOptions
    * @access private
    */
    var $_diagramTitleFontOptions = array();

    /**
    * Title for diagram
    *
    * Each line is stored separately in the array
    *
    * @var array      contains strings; lines of title
    * @access private
    */
    var $_diagramTitleText = array();

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
        $this->_pos  = array($pos_x, $pox_y);
        $this->setSpaceFromBorder(10);
        $this->setAxesColor(array(0, 0, 0)); // set default color to black, all axes
        $this->setAxesYTickStyle     (IMAGE_GRAPH_TICKS_OUTSIDE, 0);
        $this->setAxesYTickStyle     (IMAGE_GRAPH_TICKS_OUTSIDE, 1);
        $this->setAxesYTickSize      (10, 0);
        $this->setAxesYTickSize      (10, 1);
        $this->setAxesYTicksMajor    (array(), 0);
        $this->setAxesYTicksMinor    (array(), 0);
        $this->setAxesYTicksMajor    (array(), 1);
        $this->setAxesYTicksMinor    (array(), 1);
        $this->setAxesYNumberformat  ("%.02f", 0);
        $this->setAxesYNumberformat  ("%.02f", 1);

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
    * Set numberformat for a Y-axe
    *
    * @param  string  format in which numbers on the axes (major ticks) will be shown; sprintf-format
    * @param  int     optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYNumberformat($format, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['numberformat'] = $format;
    }

    /**
    * Set default options for fonts
    *
    * @param  array   stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @access public
    */
    function setDefaultFontOptions($options = array())
    {
        if (!isset($options['fontPath'])) {
            $options['fontPath'] = './';
        }
        if (!isset($options['fontSize'])) {
            $options['fontSize'] = 10;
        }
        if (!isset($options['color'])) {
            $options['color'] = array(0, 0, 0); // black
        }
        if (!isset($options['antiAliasing'])) {
            $options['antiAliasing'] = false;
        }
        $this->_defaultFontOptions = $options;
    }

    /**
    * Set font options for diagram title
    *
    * @param  array   stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @access public
    */
    function setDiagramTitleFontOptions($options)
    {
        $this->_diagramTitleFontOptions = $options;
    }

    /**
    * Set font options for X-axe-title
    *
    * @param  array   stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @access public
    */
    function setAxesXTitleFontOptions($options)
    {
        $this->_axes['x']['titleFontOptions'] = $options;
    }

    /**
    * Set font options for a Y-axe
    *
    * @param  array   stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @param  int     optional; id of axe (0 = left axe, 1 = right axe)
    * @access public
    */
    function setAxesYFontOptions($options, $axeId = 0)
    {
        $this->_axes['y'][$axeId]['fontOptions'] = $options;
    }

    /**
    * Set text for title
    *
    * @param  array/string   lines of title; lines can also be separated by "\n" and will automatically be converted into an array
    * @access public
    */
    function setDiagramTitleText($text)
    {
        if (is_string($text)) {
            $text = explode("\n", $text);
        }
        $this->_diagramTitleText = $text;
    }

    /**
    * Set text for X-axe
    *
    * @param  array/string   lines of title; lines can also be separated by "\n" and will automatically be converted into an array
    * @access public
    */
    function setAxesXTitle($text)
    {
        if (is_string($text)) {
            $text = explode("\n", $text);
        }
        $this->_axes['x']['titleText'] = $text;
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
    * @return object  data-object
    * @access public
    */
    function &addData($data, $representation = "line", $attributes = array())
    {
        $representation = strtolower($representation);
        $dataElementFile  = "Image/Graph/Data/".strtolower($representation).".php";
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
        $myNew = &new $dataElementClass($this, $data, $attributes);
        $this->_dataElements[count($this->_dataElements)] =& $myNew;
        return $myNew;
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
    * Get font options, combined with the _defaultFontOptions
    *
    * If certain values are not in the array given to the function, they are taken from the _defaultFontOptions
    *
    * @var    array     stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @return array     combined font-options
    * @access private
    */
    function _getFontOptions($options)
    {
        foreach ($this->_defaultFontOptions as $key => $value) {
            if (!isset($options[$key])) {
                $options[$key] = $value;
            }
        }
        return $options;
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
        $this->_calculateAxesYMinMax();

        // remove ticks outside the axe-ranges
        for($axeCount=0; $axeCount<=1; $axeCount++) {
            foreach ($this->_axes['y'][$axeCount]['ticksMajor'] as $key => $value) {
                if (($value < $this->_axes['y'][$axeCount]['min']) ||
                    ($value > $this->_axes['y'][$axeCount]['max'])) {
                    unset($this->_axes['y'][$axeCount]['ticksMajor'][$key]);
                }
            }
            foreach ($this->_axes['y'][$axeCount]['ticksMinor'] as $key => $value) {
                if (($value < $this->_axes['y'][$axeCount]['min']) ||
                    ($value > $this->_axes['y'][$axeCount]['max'])) {
                    unset($this->_axes['y'][$axeCount]['ticksMinor'][$key]);
                }
            }
        }

        $borderspaceSum=array("top"    => $this->_borderspace,
                              "bottom" => $this->_borderspace,
                              "left"   => $this->_borderspace,
                              "right"  => $this->_borderspace);

        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            if (!empty($this->_diagramTitleText)) {
                $this->_diagramTitleFontOptions = $this->_getFontOptions($this->_diagramTitleFontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text($this->_diagramTitleText, $this->_diagramTitleFontOptions);
                $textSize = $tempText->getSize();
                $borderspaceSum['top'] += $textSize['height'];
                if (isset($this->_diagramTitleFontOptions['spacerTop'])) {
                    $borderspaceSum['top'] += $this->_diagramTitleFontOptions['spacerTop'];
                }
                if (isset($this->_diagramTitleFontOptions['spacerBottom'])) {
                    $borderspaceSum['top'] += $this->_diagramTitleFontOptions['spacerBottom'];
                }
            }

            if (!empty($this->_axes['x']['titleText'])) {
                $this->_axes['x']['titleFontOptions'] = $this->_getFontOptions($this->_axes['x']['titleFontOptions']);

                require_once 'Image/Text.php';
                $tempText = new Image_Text($this->_axes['x']['titleText'], $this->_axes['x']['titleFontOptions']);
                $textSize = $tempText->getSize();
                $borderspaceSum["bottom"] += $textSize["height"];
                if (isset($this->_axes['x']['titleFontOptions']['spacerTop'])) {
                    $borderspaceSum['bottom'] += $this->_axes['x']['titleFontOptions']['spacerTop'];
                }
                if (isset($this->_axes['x']['titleFontOptions']['spacerBottom'])) {
                    $borderspaceSum['bottom'] += $this->_axes['x']['titleFontOptions']['spacerBottom'];
                }
            }

            // prepare drawing of numbers for the Y-axes
            for ($axeCount=0; $axeCount<=1; $axeCount++) {
                if (isset($this->_axes['y'][$axeCount]['containsData'])) {
                    $this->_axes['y'][$axeCount]['fontOptions'] = $this->_getFontOptions($this->_axes['y'][$axeCount]['fontOptions']);

                    require_once 'Image/Text.php';
                    $tempText = new Image_Text("", $this->_axes['y'][$axeCount]['fontOptions']);

                    $maxWidth = 0;
                    foreach ($this->_axes['y'][$axeCount]['ticksMajor'] as $currTick) {
                        // TO DO: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                        $tempText->lines = array(new Image_Text_Line(sprintf($this->_axes['y'][$axeCount]['numberformat'], $currTick), $tempText->options));
                        $textSize = $tempText->getSize();
                        $maxWidth = max ($maxWidth, $textSize['width']);
                    }
                    $this->_axes['y'][$axeCount]['_maxNumWidth'] = $maxWidth;

                    if ($maxWidth > 0) {
                        $maxWidth += 2; // add a few pixels between text and axe-major-ticks
                        if ($axeCount == 0) { // axe 0 (left axe)
                            $borderspaceSum["left"]  += $maxWidth;
                        } else { // axe 1 (right axe)
                            $borderspaceSum["right"] += $maxWidth;
                        }
                    }
                }
            }
        } // if (!empty($this->_defaultFontOptions))

        if (( isset($this->_axes['y'][0]['containsData'])) &&
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
    * Draw titles for diagram
    *
    * @param  GD-resource image to draw to
    * @access private
    */
    function _drawGDtitles(&$img)
    {
        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            // draw title text
            if (!empty($this->_diagramTitleText)) {
                require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                $tempText = new Image_Text($this->_diagramTitleText, $this->_diagramTitleFontOptions);

                $tempText->align(IMAGE_TEXT_ALIGN_CENTER);
                $textX = $this->_pos[0] + ($this->_size[0] / 2);
                $textY = $this->_pos[1] + $this->_borderspace;

                $tempText->colorize(array ("r" => $this->_diagramTitleFontOptions['color'][0],
                                           "g" => $this->_diagramTitleFontOptions['color'][1],
                                           "b" => $this->_diagramTitleFontOptions['color'][2]));
                $tempText->renderImage($textX, $textY, $img);
            }

            // draw x-axe text
            if (!empty($this->_axes['x']['titleText'])) {
                require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                $tempText = new Image_Text($this->_axes['x']['titleText'], $this->_axes['x']['titleFontOptions']);

                $tempText->align(IMAGE_TEXT_ALIGN_CENTER);
                $textSize = $tempText->getSize();
                $textX = $this->_pos[0] + ($this->_size[0] / 2);
                $textY = $this->_pos[1] + $this->_size[1] - $textSize['height'] - $this->_borderspace;

                $tempText->colorize(array ("r" => $this->_axes['x']['titleFontOptions']['color'][0],
                                           "g" => $this->_axes['x']['titleFontOptions']['color'][1],
                                           "b" => $this->_axes['x']['titleFontOptions']['color'][2]));
                $tempText->renderImage($textX, $textY, $img);
            }
        } // if (!empty($this->_defaultFontOptions))
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
            if (isset($this->_axes['y'][$axeCount]['containsData'])) {
                imageline    ($img, $axesXpositions[$axeCount], $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                                    $axesXpositions[$axeCount], $this->_drawingareaPos[1], $drawColor);

                if (is_array($this->_axes['y'][$axeCount]['ticksMajor'])) {
                    foreach ($this->_axes['y'][$axeCount]['ticksMajor'] as $currTick) {
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
                if (is_array($this->_axes['y'][$axeCount]['ticksMinor'])) {
                    foreach ($this->_axes['y'][$axeCount]['ticksMinor'] as $currTick) {
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

        // drawing of numbers for the Y-axes
        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            for ($axeCount=0; $axeCount<=1; $axeCount++) {
                if (isset($this->_axes['y'][$axeCount]['containsData'])) {
                    require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                    $textoptions = $this->_axes['y'][$axeCount]['fontOptions'];
                    $textoptions['width'] = $this->_axes['y'][$axeCount]['_maxNumWidth'];
                    $tempText = new Image_Text("", $textoptions);
                    if ($axeCount == 0) { // axe 0 (left axe)
                        $textX = $this->_pos[0] + $this->_borderspace;
                    } else { // axe 1 (right axe)
                        $textX = $this->_pos[0] + $this->_size[0] - $this->_borderspace - $this->_axes['y'][$axeCount]['_maxNumWidth'];
                    }

                    foreach ($this->_axes['y'][$axeCount]['ticksMajor'] as $currTick) {
                        // TO DO: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                        $tempText->lines = array(new Image_Text_Line(sprintf($this->_axes['y'][$axeCount]['numberformat'], $currTick), $tempText->options));


                        $tempText->align(IMAGE_TEXT_ALIGN_RIGHT);
                        $textSize = $tempText->getSize();
                        $relativeYPosition = $this->_calculateValueToPixelLinear($currTick, $axeCount);
                        $textY = $this->_drawingareaPos[1]+$relativeYPosition - ($textSize['height']/2);
                        $tempText->colorize(array ("r" => $this->_axes['y'][$axeCount]['fontOptions']['color'][0],
                                                   "g" => $this->_axes['y'][$axeCount]['fontOptions']['color'][1],
                                                   "b" => $this->_axes['y'][$axeCount]['fontOptions']['color'][2]));
                        $tempText->renderImage($textX, $textY, $img);
                    }
                }
            }
        } // if (!empty($this->_defaultFontOptions))

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

        $this->_drawGDtitles($img);
        $this->_drawGDAxes($img);
        foreach ($this->_dataElements as $currDataElement) {
            $currDataElement->drawGD($img, $this);
        }

        return $img;
    }
}
