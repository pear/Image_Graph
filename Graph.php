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

require_once 'Image/Graph/Elements.php';

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
    * X-axe of diagram
    *
    * @var object
    * @access public
    */
    var $axeX = null;

    /**
    * first Y-axe of diagram
    *
    * @var object
    * @access public
    */
    var $axeY0 = null;

    /**
    * second Y-axe of diagram
    *
    * @var object
    * @access public
    */
    var $axeY1 = null;

    /**
    * Title of diagram
    *
    * @var object
    * @access public
    */
    var $diagramTitle = null;

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
        $this->axeX = new Image_Graph_Axe();
        $this->axeY0 = new Image_Graph_Axe();
        $this->axeY1 = new Image_Graph_Axe();
        $this->axeY0->title->setSpacer(array("right" => 5));
        $this->axeY1->title->setSpacer(array("left"  => 5));
        $this->diagramTitle = new Image_Graph_Title();

        $this->_size = array($width, $height);
        $this->_pos  = array($pos_x, $pox_y);
        $this->setSpaceFromBorder(10);
        $this->setAxesColor(array(0, 0, 0)); // set default color to black, all axes

/*
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
*/

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
    * Set color of axes
    *
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setAxesColor($axesColor)
    {
        $this->axeX->setColor ($axesColor);
        $this->axeY0->setColor ($axesColor);
        $this->axeY1->setColor ($axesColor);
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
        $dataElementFile  = "Image/Graph/Data/".ucfirst($representation).".php";
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

        $this->{"axeY".$attributes['axeId']}->_containsData = true;

        return $myNew;
    }

    /**
    * automatically adjust all fields (bounds + ticks) where auto-detection of values is enabled
    *
    * @access private
    */
    function _autoadjustBoundsAndTicks(&$axe)
    {
        $step = 1;
        $faktor = 1;
        
//        unset($axe->_bounds['min']);
//        unset($axe->_bounds['max']);

        do
        {
            $stepfaktor = $step*$faktor;
            if (isset($axe->_bounds['min'])) {
                $newMin = $axe->_bounds['min'];
            } else {
                $newMin = floor($axe->_boundsEffective['min'] / $stepfaktor) * $stepfaktor;
            }
            if (isset($axe->_bounds['max'])) {
                $newMax = $axe->_bounds['max'];
            } else {
                $newMax = ceil($axe->_boundsEffective['max'] / $stepfaktor) * $stepfaktor;
            }
            $currSteps = (($newMax-$newMin) / $stepfaktor) + 1;
            if ($currSteps<$maxSteps)
            {
                $faktor = $faktor * 0.1;
            }
        } while ($currSteps < $axe->_ticksAutoSteps['major']);

        while ($currSteps > $axe->_ticksAutoSteps['major'])
        {
            if ($step == 1)
            {
                $step = 2;
            } elseif ($step == 2) {
                $step = 5;
            } else { // $step == 5
                $step = 1;
                $faktor *= 10;
            }

            $stepfaktor = $step*$faktor;
            if (isset($axe->_bounds['min'])) {
                $newMin = $axe->_bounds['min'];
            } else {
                $newMin = floor($axe->_boundsEffective['min'] / $stepfaktor) * $stepfaktor;
            }
            if (isset($axe->_bounds['max'])) {
                $newMax = $axe->_bounds['max'];
            } else {
                $newMax = ceil($axe->_boundsEffective['max'] / $stepfaktor) * $stepfaktor;
            }
            $currSteps = (($newMax-$newMin) / $stepfaktor) + 1;
        }

        if (isset($axe->_ticksMajor)) {
            $stepsMajor = $axe->_ticksMajor;
        } else {
            $stepsMajor = array();
            $stepfaktor = $step*$faktor;
            for ($count = $newMin; $count<=$newMax; $count+=$stepfaktor)
            {
                $stepsMajor[] = $count;
            }
        }

        if (isset($axe->_ticksMinor)) {
            $stepsMinor = $axe->_ticksMinor;
        } else {
            do
            {
                $stepMinor = $step;
                $faktorMinor = $faktor;
                
                if ($step == 5)
                {
                    $step = 2;
                } elseif ($step == 2) {
                    $step = 1;
                } else { // $step == 5
                    $step = 5;
                    $faktor *= 0.1;
                }
    
                $stepfaktor = $step*$faktor;
                $currSteps = (($newMax-$newMin) / $stepfaktor) + 1;
            } while ($currSteps <= $axe->_ticksAutoSteps['minor']);
    
            $stepsMinor = array();
            $stepfaktor = $stepMinor*$faktorMinor;
            for ($count = $newMin; $count<=$newMax; $count+=$stepfaktor)
            {
                if (!in_array($count, $stepsMajor)) {
                    $stepsMinor[] = $count;
                }
            }
        }
        
        $axe->_boundsEffective['min'] = $newMin;
        $axe->_boundsEffective['max'] = $newMax;
        $axe->_ticksMajorEffective = $stepsMajor;
        $axe->_ticksMinorEffective = $stepsMinor;
    }

    /**
    * adjust the min/max-values and the ticks of the Y-axe;
    * calculate them if "null"-values (auto-detect of values) are given
    *
    * @access private
    */
    function _calculateAxesYMinMaxTicks()
    {
        for ($axeCount=0; $axeCount<=1; $axeCount++) {
            $currAxe = "axeY".$axeCount;
            if (isset($this->{$currAxe}->_bounds['min'])) {
                $this->{$currAxe}->_boundsEffective['min'] = $this->{$currAxe}->_bounds['min'];
            } else {
                foreach ($this->_dataElements as $currDataElement) {
                    if (!isset($this->{$currAxe}->_boundsEffective['min'])) {
                        $this->{$currAxe}->_boundsEffective['min'] = $currDataElement->_data[0];
                    }

                    foreach ($currDataElement->_data as $currData) {
                        if ($this->{$currAxe}->_boundsEffective['min'] > $currData) {
                            $this->{$currAxe}->_boundsEffective['min'] = $currData;
                        }
                    }
                }
            }

            if (isset($this->{$currAxe}->_bounds['max'])) {
                $this->{$currAxe}->_boundsEffective['max'] = $this->{$currAxe}->_bounds['max'];
            } else {
                foreach ($this->_dataElements as $currDataElement) {
                    if (!isset($this->{$currAxe}->_boundsEffective['max'])) {
                        $this->{$currAxe}->_boundsEffective['max'] = $currDataElement->_data[0];
                    }

                    foreach ($currDataElement->_data as $currData) {
                        if ($this->{$currAxe}->_boundsEffective['max'] < $currData) {
                            $this->{$currAxe}->_boundsEffective['max'] = $currData;
                        }
                    }
                }
            }

            // correction if only one y-value is present in the diagram
            if ($this->{$currAxe}->_boundsEffective['min'] == $this->{$currAxe}->_boundsEffective['max']) {
                if (($this->{$currAxe}->_boundsEffective['min']-1) >= 0) {
                    $this->{$currAxe}->_boundsEffective['min']--;
                }
                $this->{$currAxe}->_boundsEffective['max']++;
            }

            $this->_autoadjustBoundsAndTicks($this->{$currAxe});

            // remove ticks outside the axe-ranges
            foreach ($this->{$currAxe}->_ticksMajorEffective as $key => $value) {
                if (($value < $this->{$currAxe}->_boundsEffective['min']) ||
                    ($value > $this->{$currAxe}->_boundsEffective['max'])) {
                    unset($this->{$currAxe}->_ticksMajorEffective[$key]);
                }
            }
            foreach ($this->{$currAxe}->_ticksMinorEffective as $key => $value) {
                if (($value < $this->{$currAxe}->_boundsEffective['min']) ||
                    ($value > $this->{$currAxe}->_boundsEffective['max'])) {
                    unset($this->{$currAxe}->_ticksMinorEffective[$key]);
                }
            }

        } // for ($axeCount=0; $axeCount<=1; $axeCount++)
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
    function _mergeFontOptions($options)
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
        $this->_calculateAxesYMinMaxTicks();

        $borderspaceSum=array("top"    => $this->_borderspace,
                              "bottom" => $this->_borderspace,
                              "left"   => $this->_borderspace,
                              "right"  => $this->_borderspace);

        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            if (!empty($this->diagramTitle->_text)) {
                $this->diagramTitle->_fontOptions = $this->_mergeFontOptions($this->diagramTitle->_fontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text($this->diagramTitle->_text, $this->diagramTitle->_fontOptions);
                $textSize = $tempText->getSize();
                $borderspaceSum['top'] += $textSize['height'];
                $borderspaceSum['top'] += $this->diagramTitle->_spacer['top'];
                $borderspaceSum['top'] += $this->diagramTitle->_spacer['bottom'];
            }

            if (!empty($this->axeX->title->_text)) {
                $this->axeX->title->_fontOptions = $this->_mergeFontOptions($this->axeX->title->_fontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text($this->axeX->title->_text, $this->axeX->title->_fontOptions);
                $textSize = $tempText->getSize();
                $borderspaceSum["bottom"] += $textSize['height'];
                $borderspaceSum['bottom'] += $this->axeX->title->_spacer['top'];
                $borderspaceSum['bottom'] += $this->axeX->title->_spacer['bottom'];
            }

/* !!! Y-title not yet fully implemented - left out for the moment !!! */

            for ($axeCount=0; $axeCount<=1; $axeCount++) {
                $currAxe = "axeY".$axeCount;

                $this->{$currAxe}->_internalTempValues['totalTitleWidth'] = 0;
                if (!empty($this->{$currAxe}->title->_text)) {
                    $this->{$currAxe}->title->_fontOptions = $this->_mergeFontOptions($this->{$currAxe}->title->_fontOptions);

                    require_once 'Image/Text.php';
                    $tempText = new Image_Text($this->{$currAxe}->title->_text, $this->{$currAxe}->title->_fontOptions);
                    $tempText->rotate(90);
                    $textSize = $tempText->getSize();

                    $totalTitleWidth = $textSize['height'] +
                                       $this->{$currAxe}->title->_spacer['left'] +
                                       $this->{$currAxe}->title->_spacer['right'];
                    $this->{$currAxe}->_internalTempValues['totalTitleWidth'] = $totalTitleWidth;

                    if ($axeCount == 0)
                    {
                        $borderspaceSum['left'] += $totalTitleWidth;
                    } else { // else axeCount == 1
                        $borderspaceSum['right'] += $totalTitleWidth;
                    }
                }
            }

            // prepare drawing of numbers for the Y-axes
            for ($axeCount=0; $axeCount<=1; $axeCount++) {
                $currAxe = "axeY".$axeCount;
                if ($this->{$currAxe}->_containsData) {
                    $this->{$currAxe}->_fontOptions = $this->_mergeFontOptions($this->{$currAxe}->_fontOptions);

                    require_once 'Image/Text.php';
                    $tempText = new Image_Text("", $this->{$currAxe}->_fontOptions);

                    $maxWidth = 0;
                    foreach ($this->{$currAxe}->_ticksMajorEffective as $currTick) {
                        // TO DO: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                        $tempText->lines = array(new Image_Text_Line(sprintf($this->{$currAxe}->_numberformat, $currTick), $tempText->options));
                        $textSize = $tempText->getSize();
                        $maxWidth = max ($maxWidth, $textSize['width']);
                    }
                    $this->{$currAxe}->_internalTempValues['maxNumWidth'] = $maxWidth;

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

        if ( ($this->axeY0->_containsData) &&
            (($this->axeY0->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
             ($this->axeY0->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
            )
           ) {
            $borderspaceSum["left"]  += $this->axeY0->_tickSize;
        }

        if ( ($this->axeY1->_containsData) &&
            (($this->axeY1->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
             ($this->axeY1->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
            )
           ) {
            $borderspaceSum["right"] += $this->axeY1->_tickSize;
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
                                 ($this->{"axeY".$axeId}->_boundsEffective['max'] - $this->{"axeY".$axeId}->_boundsEffective['min']) *
                                 ($currData - $this->{"axeY".$axeId}->_boundsEffective['min'])
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
if ($axeId > 1)
{
  var_dump($currDataElement);
  exit();
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
            if (!empty($this->diagramTitle->_text)) {
                require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                $tempText = new Image_Text($this->diagramTitle->_text, $this->diagramTitle->_fontOptions);

                $tempText->align(IMAGE_TEXT_ALIGN_CENTER);
                $textX = $this->_pos[0] + ($this->_size[0] / 2);
                $textY = $this->_pos[1] + $this->_borderspace;

                $tempText->colorize(array ("r" => $this->diagramTitle->_color[0],
                                           "g" => $this->diagramTitle->_color[1],
                                           "b" => $this->diagramTitle->_color[2]));
                $tempText->renderImage($textX, $textY, $img);
            }

            // draw x-axe text
            if (!empty($this->axeX->title->_text)) {
                require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once

                $tempText = new Image_Text($this->axeX->title->_text, $this->axeX->title->_fontOptions);

                $tempText->align(IMAGE_TEXT_ALIGN_CENTER);
                $textSize = $tempText->getSize();
                $textX = $this->_pos[0] + ($this->_size[0] / 2);
                $textY = $this->_pos[1] + $this->_size[1] - $textSize['height'] - $this->_borderspace;

                $tempText->colorize(array ("r" => $this->axeX->title->_color[0],
                                           "g" => $this->axeX->title->_color[1],
                                           "b" => $this->axeX->title->_color[2]));
                $tempText->renderImage($textX, $textY, $img);
            }

            // draw y-axis texts
            for ($axeCount=0; $axeCount<=1; $axeCount++) {
                $currAxe = "axeY".$axeCount;
                if (!empty($this->{$currAxe}->title->_text)) {
                    require_once 'Image/Text.php';
                    $tempText = new Image_Text($this->{$currAxe}->title->_text, $this->{$currAxe}->title->_fontOptions);
                    $tempText->rotate(90);
                    $textSize = $tempText->getSize();
                    if ($axeCount == 0) {
                        $textX = $this->_pos[0] + $this->_borderspace + $this->{$currAxe}->title->_spacer['left'] + $textSize['height'];
                    } else {
                        $textX = $this->_pos[0] + $this->_size[0] - $this->_borderspace - $this->{$currAxe}->title->_spacer['right'];
                    }
                    $textY = $this->_drawingareaPos[1] + ($this->_drawingareaSize[1]/2) + ($textSize['width']/2);

                    // BEGIN: workaround for current Image_Text v0.2
                    $textY -= ($textSize['height'] + ($this->{$currAxe}->title->_fontOptions['fontSize'] / 4));
                    // END: workaround for current Image_Text v0.2

                    if (is_null($this->{$currAxe}->title->_color)) {
                        $this->{$currAxe}->title->_color = $this->{$currAxe}->_color;
                    }
                    $tempText->colorize(array ("r" => $this->{$currAxe}->title->_color[0],
                                               "g" => $this->{$currAxe}->title->_color[1],
                                               "b" => $this->{$currAxe}->title->_color[2]));
                    $tempText->renderImage($textX, $textY, $img);
                }
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
        $drawColor = imagecolorallocate($img, $this->axeX->_color[0], $this->axeX->_color[1], $this->axeX->_color[2]);
        // draw X-axe
        imageline    ($img, $this->_drawingareaPos[0],                              $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                            $this->_drawingareaPos[0]+$this->_drawingareaSize[0]-1, $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1, $drawColor);

        // draw Y-axes
        $axesXpositions = array ($this->_drawingareaPos[0],
                                 $this->_drawingareaPos[0]+$this->_drawingareaSize[0]-1);

        $axesXfactors   = array (1, -1);

        for ($axeCount=0; $axeCount<=1; $axeCount++) {
            $currAxe = "axeY".$axeCount;
            if ($this->{$currAxe}->_containsData) {
                imageline    ($img, $axesXpositions[$axeCount], $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                                    $axesXpositions[$axeCount], $this->_drawingareaPos[1], $drawColor);

                foreach ($this->{$currAxe}->_ticksMajorEffective as $currTick) {
                    $relativeYPosition = $this->_calculateValueToPixelLinear($currTick, $axeCount);
                    $tickSize = $this->{$currAxe}->_tickSize * $axesXfactors[$axeCount];
                    switch ($this->{$currAxe}->_tickStyle) {
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

                foreach ($this->{$currAxe}->_ticksMinorEffective as $currTick) {
                    $relativeYPosition = $this->_calculateValueToPixelLinear($currTick, $axeCount);
                    $tickSize = ceil($this->{$currAxe}->_tickSize/2) * $axesXfactors[$axeCount];
                    switch ($this->{$currAxe}->_tickStyle) {
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

        // drawing of numbers for the Y-axes
        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            for ($axeCount=0; $axeCount<=1; $axeCount++) {
                $currAxe = "axeY".$axeCount;
                if ($this->{$currAxe}->_containsData) {
                    require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                    $textoptions = $this->{$currAxe}->_fontOptions;
                    $textoptions['width'] = $this->{$currAxe}->_internalTempValues['maxNumWidth'];
                    $tempText = new Image_Text("", $textoptions);

                    if ($axeCount == 0) { // axe 0 (left axe)
                        $textX = $this->_pos[0] + $this->_borderspace + $this->{$currAxe}->_internalTempValues['totalTitleWidth'];
                    } else { // axe 1 (right axe)
                        $textX = $this->_pos[0] + $this->_size[0] - $this->_borderspace - $this->{$currAxe}->_internalTempValues['maxNumWidth'] - $this->{$currAxe}->_internalTempValues['totalTitleWidth'];
                    }

                    foreach ($this->{$currAxe}->_ticksMajorEffective as $currTick) {
                        // TO DO: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                        $tempText->lines = array(new Image_Text_Line(sprintf($this->{$currAxe}->_numberformat, $currTick), $tempText->options));

                        $tempText->align(IMAGE_TEXT_ALIGN_RIGHT);
                        $textSize = $tempText->getSize();
                        $relativeYPosition = $this->_calculateValueToPixelLinear($currTick, $axeCount);
                        $textY = $this->_drawingareaPos[1]+$relativeYPosition - ($textSize['height']/2);
                        // BEGIN: workaround for current Image_Text v0.2
                        $textY -= ($this->{$currAxe}->_fontOptions['fontSize'] / 4);
                        // END: workaround for current Image_Text v0.2

                        if (is_null($this->{$currAxe}->_numbercolor)) {
                            $this->{$currAxe}->_numbercolor = $this->{$currAxe}->_color;
                        }

                        $tempText->colorize(array ("r" => $this->{$currAxe}->_numbercolor[0],
                                                   "g" => $this->{$currAxe}->_numbercolor[1],
                                                   "b" => $this->{$currAxe}->_numbercolor[2]));
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
