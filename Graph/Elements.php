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
* Contains various classes for elements of a graph
*
* This file includes several classes used in Image_Graph. We decided to have just one file with
* various classes in it for performance reasons (including and opening only one file is much faster).
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

/**
* Don't show any grid
*
* For usage with class Image_Graph_Grid
*
* @see Image_Graph_Grid
*/
define('IMAGE_GRAPH_GRID_NONE' ,'none');
/**
* Only show grid at major ticks of axis
*
* For usage with class Image_Graph_Grid
*
* @see Image_Graph_Grid
*/
define('IMAGE_GRAPH_GRID_MAJOR','major');
/**
* Show grid at minor (and major) ticks of axis
*
* For usage with class Image_Graph_Grid
*
* @see Image_Graph_Grid
*/
define('IMAGE_GRAPH_GRID_MINOR','minor');

/**
* Class for color handling (extended version of package: PEAR::Image_Color)
*/
require_once("Image/Graph/Color.php");

/**
* Base class for data-storage in common objects
*
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Base
{
    /**
    * Color for element
    *
    * @var    array           (4 ints for R,G,B,A); initially null
    * @access private
    */
    var $_color = null;

    /**
    * Options for fonts
    *
    * @var    array           stores information like fontPath, fontFile, fontSize, antiAliasing etc.; initially empty
    * @access private
    */
    var $_fontOptions = array();

    /**
    * Text
    *
    * @var    string          text to be displayed
    * @access private
    */
    var $_text = "";

    /**
    * Set color
    *
    * If the color-value is "null" instead of an array default values will be taken
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
    * Set options for fonts
    *
    * All information not given in this array (e.g. fontPath) will be taken from the default font-options set in the Image_Graph-object
    *
    * @param  array           stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @access public
    */
    function setFontOptions($options = array())
    {
        $this->_fontOptions = $options;
    }

    /**
    * Set text
    *
    * @param  array/string    lines of title; lines can also be separated by "\n" and will automatically be converted into an array
    * @access public
    */
    function setText($text)
    {
        if (is_string($text)) {
            $text = explode("\n", $text);
        }
        $this->_text = $text;
    }
}

/**
* Class for storing axes settings
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Axis extends Image_Graph_Base
{
    /**
    * Title for axis
    *
    * @var    object Image_Graph_Title
    * @access public
    */
    var $title = null;

    /**
    * Bounds for axis (min/max value)
    *
    * @var    array (2 ints/floats/nulls)   null results in automatic detection of bounds
    * @access private
    * @see    $_boundsEffective
    */
    var $_bounds = array('min' => null, 'max' => null);
    
    /**
    * effective bounds for axis (min/max value)
    *
    * in contrast to $_bounds these values are not to be influenced by the user but used internally
    * for storing values that will be used for drawing; where $_bounds may contain null-values,
    * this array will store the automatically detected min/max-values that will be used for drawing
    *
    * @var    array (2 ints/floats)         contains min and max value for the bounds
    * @access private
    * @see    $_bounds
    */
    var $_boundsEffective = array('min' => null, 'max' => null);

    /**
    * Style for ticks on the axis
    *
    * @var int     tick-style
    * @access private
    */
    var $_tickStyle = IMAGE_GRAPH_TICKS_OUTSIDE;

    /**
    * Size for ticks on the axis
    *
    * @var    int             tick-size
    * @access private
    */
    var $_tickSize = 10;

    /**
    * Major ticks on axis
    *
    * @var    array (ints/floats)     null results in automatic detection of ticks (!to be implemented!)
    * @access private
    * @see    $_ticksMajorEffective
    */
    var $_ticksMajor = null;

    /**
    * effective major ticks on axis
    *
    * in contrast to $_ticksMajor these values are not to be influenced by the user but used internally
    * for storing values that will be used for drawing; where $_ticksMajor may contain null-values,
    * this array will store the automatically determined ticks that will be used for drawing
    *
    * @var    array (ints/floats)     contains the ticks that will be drawn
    * @access private
    * @see    $_ticksMajor
    */
    var $_ticksMajorEffective = array();

    /**
    * Minor ticks on axis
    *
    * @var    array (ints/floats)     null results in automatic detection of ticks (!to be implemented!)
    * @access private
    * @see    $_ticksMinorEffective
    */
    var $_ticksMinor = null;

    /**
    * effective minor ticks on axis
    *
    * in contrast to $_ticksMinor these values are not to be influenced by the user but used internally
    * for storing values that will be used for drawing; where $_ticksMinor may contain null-values,
    * this array will store the automatically determined ticks that will be used for drawing
    *
    * @var    array (ints/floats)     contains the ticks that will be drawn
    * @access private
    * @see    $_ticksMinor
    */
    var $_ticksMinorEffective = array();

    /**
    * maximum steps for automatic creation of ticksMajor / ticksMinor
    *
    * @var    array           (array of 2 ints) contains "major" and "minor" value (max steps) for the ticks
    * @access private
    * @see    $_ticksMajor
    * @see    $_ticksMinor
    * @see    setTicksAutoSteps()
    */
    var $_ticksAutoSteps = array("major" => 5, "minor" => 25);

    /**
    * Numberformat
    *
    * @var    string          format-string in printf-syntax
    * @access private
    */
    var $_numberformat = "%.02f";

    /**
    * Color for numbers
    *
    * @var    array           (4 ints for R,G,B,A); initially null
    * @access private
    */
    var $_numbercolor = null;

    /**
    * Type of the axis
    *
    * The variable is defined/used here as a "virtual" variable.
    * It will receive it's initial values in the derived classes for x-/y-axes
    *
    * @var    const           use constants IMAGE_GRAPH_AXISTYPE_* to evaluate/set this variable
    * @access private
    */
    var $_axistype = IMAGE_GRAPH_AXISTYPE_LINEAR;

    /**
    * Space for storing internal temporary values
    *
    * Stores information/calculations between different function calls; introduced for performance
    * directly set internally by functions of package Image_Graph
    * For sure this is somehow a "dirty trick" but it saves us from calculating a few things
    * several times.
    *
    * @var    boolean
    * @access private
    */
    var $_internalTempValues = array();

    /**
    * graph object
    *
    * @var    object Image_Graph
    * @access private
    */
    var $_graph = null;

    /**
    * Constructor
    *
    * @access public
    */
    function &Image_Graph_Axis()
    {
        $this->title  = new Image_Graph_Title();
    }

    /**
    * Set bounds
    *
    * Minimum and maximum value on the axis. Autodetection possible and enabled by default.
    *
    * @param  float           min-value; null results in automatic detection
    * @param  float           max-value; null results in automatic detection
    * @access public
    */
    function setBounds($min, $max)
    {
        $this->_bounds = array('min' => $min, 'max' => $max);
    }
    
    /**
    * Set maximum steps for automatic creation of ticksMinor/ticksMajor
    *
    * @param  int             value for steps of ticksMajor (null results in fallback to default of 5)
    * @param  int             value for steps of ticksMinor (null results in fallback to default of 25, at least the minor)
    * @access public
    * @see    $_ticksAutoSteps
    */
    function setTicksAutoSteps($major = null, $minor = null)
    {
        if ($major == null) {
            $major = 5;
        }
        if ($minor == null) {
            $minor = 25;
        }
        if ($minor < $major) {
            $minor = $major;
        }
        $this->_ticksAutoSteps = array("major" => $major, "minor" => $minor);
    }

    /**
    * Set tick-style for ticks on a axis
    *
    * @param  const           use constants IMAGE_GRAPH_TICKS_INSIDE, IMAGE_GRAPH_TICKS_OUTSIDE or IMAGE_GRAPH_TICKS_BOTH
    * @access public
    */
    function setTickStyle($style)
    {
        $this->_tickStyle = $style;
    }

    /**
    * Set size for (major) ticks on axis
    *
    * size is for the "major ticks"; all minor ticks will be half that size
    *
    * @param  int             size in pixels
    * @access public
    */
    function setTickSize($size)
    {
        $this->_tickSize = max(0, $size);
    }

    /**
    * Set major ticks on axis
    *
    * @param  array           list of values on the axis which should be "major ticks"; null results in automatic detection of bounds (!to be implemented!)
    * @access public
    */
    function setTicksMajor($ticks)
    {
        $this->_ticksMajor = $ticks;
    }

    /**
    * Set minor ticks on axis
    *
    * @param  array           list of values on the axis which should be "minor ticks"; null results in automatic detection of bounds (!to be implemented!)
    * @access public
    */
    function setTicksMinor($ticks)
    {
        $this->_ticksMinor = $ticks;
    }

    /**
    * Set numberformat for axis
    *
    * @param  string          format in which numbers on the axis (major ticks) will be shown; sprintf-format
    * @access public
    */
    function setNumberformat($format)
    {
        $this->_numberformat = $format;
    }


    /**
    * Set numbercolor for axis
    *
    * If the color-value is "null" instead of an array default values will be taken
    *
    * @param  mixed           any color representation supported by Image_Graph_Color::color2RGB()
    * @see    Image_Graph_Color::color2RGB()
    * @access public
    */
    function setNumbercolor($color)
    {
        $this->_numbercolor = Image_Graph_Color::color2RGB($color);
    }
}

/**
* Class for storing x-axis settings
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Axis_X extends Image_Graph_Axis
{
    /**
    * Type of the axis
    *
    * @var    const           use constants IMAGE_GRAPH_AXISTYPE_*; currently only IMAGE_GRAPH_AXISTYPE_TEXT allowed
    * @access private
    */
    var $_axistype = IMAGE_GRAPH_AXISTYPE_TEXT;

    /**
    * Labels for data on this axis
    *
    * Will be used (shown) when axis has $_axistype of IMAGE_GRAPH_AXISTYPE_TEXT
    *
    * @var    array           one string per data-column
    * @access private
    */
    var $_labels = array();

    /**
    * Constructor
    *
    * @access public
    */
    function Image_Graph_Axis_X()
    {
        parent::Image_Graph_Axis();
    }

    /**
    * Set type of axis
    *
    * @param  const           only IMAGE_GRAPH_AXISTYPE_TEXT allowed
    * @access public
    * @see    $_axistype
    */
    function setAxistype($type)
    {
        if ($type == IMAGE_GRAPH_AXISTYPE_TEXT) {
            $this->_axistype = IMAGE_GRAPH_AXISTYPE_TEXT;
        }
        
        /*
         elseif ($type == IMAGE_GRAPH_AXISTYPE_LINEAR) {
            if ($this->_axistype == IMAGE_GRAPH_AXISTYPE_TEXT) {
                //@TO DO: add conversion-functions here to transform axis from text to linear
                //        if this is not possible, throw an error
            }
            $this->_axistype = IMAGE_GRAPH_AXISTYPE_LINEAR;
        }
        */
        //@TO DO: add support for a logarithmic scale here someday :-)
    }

    /**
    * Set labels
    *
    * @param  mixed
    * @access public
    */
    function setLabels($labels)
    {
        if (is_array($labels)) {
            //@TO DO: do additional checks here - only array of strings/ints/floats allowed;
            //        numbers will later be translated when drawing using the current set
            //        numberformat
            
            $arrayEmpty = true;
            foreach ($labels as $currLabel) {
                if (!empty($currLabel)) {
                    $arrayEmpty = false;
                }
            }
            
            // if array is empty (or all array-entries, e.g. strings, are empty) only set an empty array
            if ($arrayEmpty) {
                $this->_labels = array();
            } else {
                $this->_labels = $labels;
            }
        }
    }

    /**
    * Calculate relative position (in pixel-coordinates) for a certain value on the axis
    *
    * @param  float           data value
    * @access public
    */
    function valueToPixelRelative($value)
    {
        // currently only implemented for text scale
        $pixelPerColumn = (float) ($this->_graph->_drawingareaSize[0]-1) / ($this->_bounds['max']-1 + ($this->_graph->_addExtraSpace));
        return (round(($value + ($this->_graph->_addExtraSpace*0.5)) * $pixelPerColumn));
    }

    /**
    * Calculate absolute position (in pixel-coordinates) for a certain value on the axis
    *
    * @param  float           data value
    * @access public
    */
    function valueToPixelAbsolute($value)
    {
        return $this->_graph->_drawingareaPos[0] + $this->valueToPixelRelative($value);
    }
}

/**
* Class for storing y-axes settings
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Axis_Y extends Image_Graph_Axis
{
    /**
    * Type of the axis
    *
    * always use the constants IMAGE_GRAPH_AXISTYPE_* to evaluate / set this variable
    *
    * @var    mixed           only IMAGE_GRAPH_AXISTYPE_LINEAR allowed
    * @access private
    */
    var $_axistype = IMAGE_GRAPH_AXISTYPE_LINEAR;

    /**
    * Indicator if axis currently contains data
    *
    * this variable is only used on the Y-axis; directly set internally by functions of package Image_Graph
    *
    * @var    boolean
    * @access private
    */
    var $_containsData = false;
    
    /**
    * Constructor for the class
    *
    * @access public
    */
    function &Image_Graph_Axis_Y()
    {
        parent::Image_Graph_Axis();
    }

    /**
    * Set type of axis
    *
    * @param  mixed           only IMAGE_GRAPH_AXISTYPE_LINEAR allowed
    * @access public
    * @see    $_axistype
    */
    function setAxistype($type)
    {
        if ($type == IMAGE_GRAPH_AXISTYPE_LINEAR) {
            $this->_axistype = IMAGE_GRAPH_AXISTYPE_LINEAR;
        }
        //@TO DO: add support for a logarithmic scale here someday :-)
    }

    /**
    * Calculate relative position (in pixel-coordinates) for a certain value on the axis
    *
    * @param  float           data value
    * @access public
    */
    function valueToPixelRelative($value)
    {
        // currently only implemented for linear scale

        // restrict values to the display range
        $value = min($this->_boundsEffective['max'], $value);
        $value = max($this->_boundsEffective['min'], $value);

        // calculate pixel value
        return ($this->_graph->_drawingareaSize[1] - 1 -
                floor(
                      (float) ($this->_graph->_drawingareaSize[1]-1) /
                      ($this->_boundsEffective['max'] - $this->_boundsEffective['min']) *
                      ($value - $this->_boundsEffective['min'])
                     )
               );
    }

    /**
    * Calculate absolute position (in pixel-coordinates) for a certain value on the axis
    *
    * @param  int/float       data value
    * @access public
    */
    function valueToPixelAbsolute($value)
    {
        return $this->_graph->_drawingareaPos[1] + $this->valueToPixelRelative($value);
    }
    
    /**
    * automatically adjust all fields (bounds + ticks) where auto-detection of values is enabled
    *
    * @access private
    */
    function _autoadjustBoundsAndTicks()
    {
        // calling this without any data doesn't make too much sense
        if (!$this->_containsData) {
            return;
        }
        $step = 1;
        $faktor = 1;

        do
        {
            $stepfaktor = $step*$faktor;
            if (isset($this->_bounds['min'])) {
                $newMin = $this->_bounds['min'];
            } else {
                $newMin = floor($this->_boundsEffective['min'] / $stepfaktor) * $stepfaktor;
            }
            if (isset($this->_bounds['max'])) {
                $newMax = $this->_bounds['max'];
            } else {
                $newMax = ceil($this->_boundsEffective['max'] / $stepfaktor) * $stepfaktor;
            }
            $currSteps = (($newMax-$newMin) / $stepfaktor) + 1;
            if ($currSteps < $this->_ticksAutoSteps['major'])
            {
                $faktor = $faktor * 0.1;
            }
        } while ($currSteps < $this->_ticksAutoSteps['major']);

        while ($currSteps > $this->_ticksAutoSteps['major'])
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
            if (isset($this->_bounds['min'])) {
                $newMin = $this->_bounds['min'];
            } else {
                $newMin = floor($this->_boundsEffective['min'] / $stepfaktor) * $stepfaktor;
            }
            if (isset($this->_bounds['max'])) {
                $newMax = $this->_bounds['max'];
            } else {
                $newMax = ceil($this->_boundsEffective['max'] / $stepfaktor) * $stepfaktor;
            }
            $currSteps = (($newMax-$newMin) / $stepfaktor) + 1;
        }

        if (isset($this->_ticksMajor)) {
            $stepsMajor = $this->_ticksMajor;
        } else {
            $stepsMajor = array();
            $stepfaktor = $step*$faktor;
            for ($count = $newMin; $count<=$newMax; $count+=$stepfaktor)
            {
                $stepsMajor[] = $count;
            }
        }

        if (isset($this->_ticksMinor)) {
            $stepsMinor = $this->_ticksMinor;
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
            } while ($currSteps <= $this->_ticksAutoSteps['minor']);

            $stepsMinor = array();
            $stepfaktor = $stepMinor*$faktorMinor;
            for ($count = $newMin; $count<=$newMax; $count+=$stepfaktor)
            {
                if (!in_array($count, $stepsMajor)) {
                    $stepsMinor[] = $count;
                }
            }
        }

        $this->_boundsEffective['min'] = $newMin;
        $this->_boundsEffective['max'] = $newMax;
        $this->_ticksMajorEffective = $stepsMajor;
        $this->_ticksMinorEffective = $stepsMinor;
    }
}

/**
* Class for storing all titles (diagram title, axis-titles, ...)
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Title extends Image_Graph_Base
{
    /**
    * Spacer
    *
    * @var    array           (array of 4 ints) array with keys "top, bottom, left, right"; describes extra-space to add beside this title
    * @access private
    */
    var $_spacer = array("top" => 0, "bottom" => 0, "left" => 0, "right" => 0);

    /**
    * Constructor for the class
    *
    * @access public
    */
    function Image_Graph_Title()
    {
        $this->setColor("black"); // black
    }

    /**
    * Set spacer
    *
    * extra-space to add beside this title
    *
    * @param  array           (array of 4 ints) array with keys "top, bottom, left, right"; if some elements are omitted current values are left in place
    * @access public
    */
    function setSpacer($spacer)
    {
        foreach ($spacer as $currKey => $currValue) {
            if (isset($this->_spacer[$currKey])) {
                $this->_spacer[$currKey] = max(0, $currValue);
            }
        }
    }
}

/**
* Class for storing grid-element settings
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Grid extends Image_Graph_Base
{
    /**
    * Reference (!) to parent axis
    *
    * @var    object Image_Graph_Axis_Common
    * @access private
    */
    var $_axis = null;

    /**
    * Line color
    *
    * @var    array           (array of 4 ints for R,G,B,A); initialised by constructor
    * @access private
    */
    var $_lineColor = null;

    /**
    * Fill color
    *
    * @var    array           (array of array of 4 ints for R,G,B,A); initialised by constructor
    * @access private
    */
    var $_fillColor = null;

    /**
    * Type of grid-lines
    *
    * @var    const           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
    * @access private
    */
    var $_lineType = IMAGE_GRAPH_GRID_NONE;

    /**
    * Type of grid-fill
    *
    * @var    const           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
    * @access private
    */
    var $_fillType = IMAGE_GRAPH_GRID_NONE;

    /**
    * array of fill objects
    *
    * @var    array           array of type Image_Graph_Fill_...
    * @access private
    */
    var $_fill = array();

    /**
    * Constructor for the class
    *
    * @access public
    */
    function &Image_Graph_Grid(&$axis)
    {
        // store a reference to the parent axis
        $this->_axis = &$axis;
        
        // set line color
        $this->setColor('darkgray');
        // set fill colors
        $this->setFill(array("solid", array("color" => "lightgrey@0.8")),
                       array("solid", array("color" => "lightblue@0.4"))
                      );
        // but force grid to be turned off initially
        $this->_fillType = IMAGE_GRAPH_GRID_NONE;
    }

    /**
    * Set type of grid-lines
    *
    * @param  const           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
    * @access public
    */
    function setLineType($type)
    {
        $this->_lineType = $type;
    }
    
    /**
    * Set type of grid-fill
    *
    * @param  const           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
    * @access public
    */
    function setFillType($type)
    {
        $this->_fillType = $type;
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
    * Set fill elements to be used
    *
    * @param  array   $fillelements,...       each element consists of a fill element type (e.g. "solid") and an option array
    * @return array   array of fill-objects
    * @access public
    */
    function &setFill($fillelements)
    {
        // get the variable parameter-list supplied to this function
        $elements = func_get_args();
        
        $this->_fill = array();
        
        if (is_null($elements) || empty($elements)) {
            $this->_fillType = IMAGE_GRAPH_GRID_NONE;
            return $this->_fill; // no elements to customize; immediately return
        }

        // if you set a fill, automatically also set fillType
        // (since setting a fill without wanting to fill anything would not be wise :-))
        if ($this->_fillType == IMAGE_GRAPH_GRID_NONE) {
            $this->_fillType = IMAGE_GRAPH_GRID_MAJOR;
        }

        foreach($elements as $currElement) {
            $type       = $currElement[0];
            $attributes = $currElement[1];

            $type = strtolower($type);
            $fillFile  = "Image/Graph/Fill/".ucfirst($type).".php";
            $fillClass = "Image_Graph_Fill_".ucfirst($type);

            if (!class_exists($fillClass)) {
                require_once($fillFile);
            }
            $newFill =& new $fillClass($attributes);
            $this->_fill[] =& $newFill;
        }
        return $newFill;
    }

    /**
    * Draws diagram element
    *
    * @param  gd-resource     image-resource to draw to
    * @param  const           choose what to draw; use constants IMAGE_GRAPH_DRAW_FILLANDBORDER, IMAGE_GRAPH_DRAW_JUSTFILL or IMAGE_GRAPH_DRAW_JUSTBORDER; BORDER means "grid-line" in this context
    * @access private
    */
    function drawGD(&$img, $drawWhat=IMAGE_GRAPH_DRAW_FILLANDBORDER)
    {
        if ($this->_fillType == IMAGE_GRAPH_GRID_NONE) {
            // do not fill if filling is turned off
            return;
        }

        $axe_type = str_replace("image_graph_axis_", "", strtolower(get_class($this->_axis))); // returns "x" or "y"
        $graph    = &$this->_axis->_graph;
        if ($axe_type == "x") {
            // for the moment x-axis can only have "major" ticks
            // TO DO: make this a bit more flexible :-))
            
            $fillCounter=0;
            
            // this is a quick-hack :-)
            // if bars are used we need to subtract the "extraSpace" added to the x-axis
            $extraSpace = $this->_axis->valueToPixelRelative(0);
            for ($counter=0; $counter<($this->_axis->_bounds['max']); $counter++) {
                $shortenBy = ( $counter<($this->_axis->_bounds['max']) ) ? 1 : 0;
                $points = array(array($this->_axis->valueToPixelAbsolute($counter)-$extraSpace,
                                      $graph->_drawingareaPos[1]),
                                array($this->_axis->valueToPixelAbsolute($counter+1)-$extraSpace-$shortenBy,
                                      $graph->_drawingareaPos[1]+$graph->_drawingareaSize[1]-1)
                               );
                if (isset($this->_fill[$fillCounter])) {
                        $this->_fill[$fillCounter]->drawGDBox($img, $points);
                }
                $fillCounter++;
                if ($fillCounter>=count($this->_fill)) {
                    $fillCounter=0;
                }
            }
        } else {
            if ($this->_fillType == IMAGE_GRAPH_GRID_MAJOR) {
                $ticks = $this->_axis->_ticksMajorEffective;
            } else { // else IMAGE_GRAPH_GRID_MINOR
                $ticks = array_merge($this->_axis->_ticksMinorEffective, $this->_axis->_ticksMajorEffective);
                sort($ticks);
                $ticks = array_unique($ticks);
            }

            $fillCounter=0;
            for ($counter=0; $counter<(count($ticks)-1); $counter++) {
                $shortenBy = ( $counter<(count($ticks)-1) ) ? 1 : 0;
                $points = array(array($graph->_drawingareaPos[0],
                                      $this->_axis->valueToPixelAbsolute($ticks[$counter+1])+$shortenBy),
                                array($graph->_drawingareaPos[0]+$graph->_drawingareaSize[0]-1,
                                      $this->_axis->valueToPixelAbsolute($ticks[$counter]))
                               );

                if (isset($this->_fill[$fillCounter])) {
                        $this->_fill[$fillCounter]->drawGDBox($img, $points);
                }
                $fillCounter++;
                if ($fillCounter>=count($this->_fill)) {
                    $fillCounter=0;
                }
            }
        }
    }
}
php?>