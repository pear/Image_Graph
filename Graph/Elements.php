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
* Contains various classes for elements of a graph
*
* This file includes several classes used in Image_Graph. We decided to
* have just one file with various classes in it for performance reasons
* (including and opening only one file is much faster).
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @category images
* @license  The PHP License, version 2.02
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
* Some attributes and methods to manipulate them are used in almost any
* object for the graph. This class provides the base-functionality for the
* classes derived from it.
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
    * These options are directly given to the class Image_Text which
    * handles text-output. For a full list of necessary/available
    * font-options please refer to the Image_Text-docs and/or have a
    * look at the examples shipped with the Image_Graph-package.
    * !! Note that the option "font_type" defaults to "ttf" (TrueType-fonts)
    * at the moment. As soon as PHP-internal (bitmap) fonts are also
    * supported by Image_Text this will default to those internal fonts
    * instead. !!
    *
    * @var    array           stores information like font_type, font_path, font_file, font_size, antialiasing etc.; initially empty
    * @access private
    */
    var $_fontOptions = array();

    /**
    * Spacer
    *
    * @var    array           (array of 4 ints) array with keys "top, bottom, left, right"
    * @access private
    */
    var $_spacer = array("top" => 0, "bottom" => 0, "left" => 0, "right" => 0);

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
    * For some objects if the color-value is "null" instead of an array
    * default values will be taken.
    * Note that the feature of using "null" might possibly not be available
    * for some objects.
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
    * All information not given in this array (e.g. font_path) will be
    * taken from the default font-options set in the Image_Graph-object.
    * These options are directly given to the class Image_Text which
    * handles text-output. For a full list of necessary/available
    * font-options please refer to the Image_Text-docs and/or have a
    * look at the examples shipped with the Image_Graph-package.
    * !! Note that the option "font_type" defaults to "ttf" (TrueType-fonts)
    * at the moment. As soon as PHP-internal (bitmap) fonts are also
    * supported by Image_Text this will default to those internal fonts
    * instead. !!
    *
    * @var    array           stores information like font_type, font_path, font_file, font_size, antialiasing etc.
    * @access public
    */
    function setFontOptions($options = array())
    {
        $this->_fontOptions = $options;
    }

    /**
    * Set spacer
    *
    * The spacer-array stores values how many pixels of space should
    * be added between above, below and besides an object.
    * Note that possibly not all values are used for all objects. This
    * depends upon the type of object and their position inside the
    * graph.
    * If some elements of the array are missing the current values are
    * left in place. This way you can e.g. just modify the top-spacer
    * for an object or set all bounds in one go.
    *
    * @param  array           (array of 4 ints) array with keys "top, bottom, left, right"
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

    /**
    * Set text
    *
    * Multiple lines are possible by using "\n" as separator.
    *
    * @param  string
    * @access public
    */
    function setText($text)
    {
        // @todo: make this a string, instead of an array
        if (is_string($text)) {
            $text = explode("\n", $text);
        }
        $this->_text = $text;
    }
}

/**
* Class for storing a title
*
* Titles are needed in various places: diagram title, axis titles, ...
* This function provides a common API for all titles
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
    * Note that not all values will have effect on all titles.
    * For the diagram title and the x-axis title currently only the
    * top/bottom-values are used/needed.
    * For an y-axis-title the left/right-values are used/needed.
    *
    * @var    array           (array of 4 ints) array with keys "top, bottom, left, right"
    * @access private
    */
    var $_spacer = array("top" => 2, "bottom" => 2, "left" => 2, "right" => 2);

    /**
    * Constructor
    *
    * The default color for a title is set to "black" here.
    *
    * @access public
    */
    function Image_Graph_Title()
    {
        $this->setColor("black");
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
        $this->_fontOptions['color'] = Image_Graph_Color::color2RGB($color);
    }
}

/**
* Class for storing axis settings
*
* All axes used in the Graph (1 x-axis and 2 y-axes) have common
* functionality. This class is used for storage of various settings
* like the axis-type ("linear" or "text"), the axis-title, "ticks"
* (minor and major lines) and their layout on the axis, the
* axis-bounds (min/max-values), a representation for numerical
* values (string in sprintf-format) and colors.
* The functionality for actually drawing an axis is (currently)
* implemented in the main class Image_Graph.
* All specialized features like mapping of values to
* relative/absolute pixel-values are done in the derived classes
* for Axis_X and Axis_Y.
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
    * Values on the axis
    *
    * @var    object Image_Graph_Axis_Values
    * @access public
    */
    var $values = null;

    /**
    * Bounds for axis (min/max value)
    *
    * @var    array           (2 ints/floats/nulls) null results in automatic detection of bounds
    * @access private
    * @see    $_boundsEffective
    */
    var $_bounds = array('min' => null, 'max' => null);

    /**
    * effective bounds for axis (min/max value)
    *
    * in contrast to $_bounds these values are not to be influenced by the
    * user but used internally for storing values that will be used for
    * drawing; where $_bounds may contain null-values, this array will store
    * the automatically detected min/max-values that will be used for drawing
    *
    * @var    array           (2 ints/floats) contains min and max value for the bounds
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
    * @var    array           (ints/floats) null results in automatic detection of ticks (!to be implemented!)
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
    * @var    array           (ints/floats) contains the ticks that will be drawn
    * @access private
    * @see    $_ticksMajor
    */
    var $_ticksMajorEffective = array();

    /**
    * Minor ticks on axis
    *
    * @var    array           (ints/floats) null results in automatic detection of ticks (!to be implemented!)
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
    * @var    array           (ints/floats) contains the ticks that will be drawn
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
    * Type of the axis
    *
    * The variable is defined/used here as a "virtual" variable.
    * It will receive it's initial values in the derived classes for x-/y-axes
    *
    * @var    mixed           use constants IMAGE_GRAPH_AXISTYPE_* to evaluate/set this variable
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
    * Reference (!) to the graph object
    *
    * @var    object Image_Graph
    * @access private
    */
    var $_graph = null;

    /**
    * Constructor
    *
    * The colors for title and values on the axis are not set by default.
    * If you don't explicitly set them, the color of the axis will be inherited.
    *
    * @access public
    */
    function &Image_Graph_Axis()
    {
        $this->title  = new Image_Graph_Title();
        unset($this->title->_fontOptions['color']);
        $this->values = new Image_Graph_Axis_Values();
    }

    /**
    * Set bounds
    *
    * You can set the minimum and maximum value on the axis. If you
    * don't want/can't give a min/max value, set it to null for
    * automatic detection.
    * Autodetection of bounds values is enabled by default.
    *
    * @param  mixed           (int/float/null) min-value
    * @param  mixed           (int/float/null) max-value
    * @access public
    */
    function setBounds($min, $max)
    {
        $this->_bounds = array('min' => $min, 'max' => $max);
    }

    /**
    * Set maximum steps for automatic creation of ticksMajor/ticksMinor
    *
    * Use this function to set the maximum number of steps for the
    * major/minor ticks that are used if automatic generation of
    * ticks is turned on. If you set your own values for the ticks on
    * the axis this settings will not have an effect.
    * Note that by default the ticks are generated automatically.
    *
    * @param  int             value for steps of ticksMajor (null => default of 5 used)
    * @param  int             value for steps of ticksMinor (null => default of 25 used; at least the minor value)
    * @access public
    * @see    $_ticksAutoSteps
    * @see    setTicksMajor()
    * @see    setTicksMinor()
    */
    function setTicksAutoSteps($major = null, $minor = null)
    {
        if ($major == null) {
            $major = 5;
        }
        if ($minor == null) {
            $minor = 25;
        }
        $major = max(0, $major); // let $major be >= 0
        $minor = max(0, $minor); // let $major be >= 0
        if ($minor < $major) {
            $minor = $major;
        }
        $this->_ticksAutoSteps = array("major" => $major, "minor" => $minor);
    }

    /**
    * Set tick-style for ticks on a axis
    *
    * You can have ticks (lines) that are located on the inside of the
    * graph, on the outside or on both sides.
    * For the x-axis "inside" means above the axis.
    * For the y0-axis "inside" means on the right side of the axis,
    * since in most cases it's located on the left side of the graph.
    * For the y1-axis "inside" means on the left side of the axis,
    * since in most cases it's located on the right side of the graph.
    *
    * @param  mixed           use constants IMAGE_GRAPH_TICKS_INSIDE, IMAGE_GRAPH_TICKS_OUTSIDE or IMAGE_GRAPH_TICKS_BOTH
    * @access public
    */
    function setTickStyle($style)
    {
        $this->_tickStyle = $style;
    }

    /**
    * Set size for (major) ticks on axis
    *
    * The size you set is for the "major ticks". All minor ticks will
    * be half that size.
    *
    * @param  int             size in pixels
    * @access public
    */
    function setTickSize($size)
    {
        $this->_tickSize = max(0, $size); // let size be >= 0
    }

    /**
    * Set major ticks on axis
    *
    * The list of values on the axis indicates where you would like
    * "major ticks" to be drawn. Null results in automatic detection
    * of ticks based upon the auto-steps-setting.
    * Note that drawing of numbers on the axis will be done at all
    * major ticks. If you don't want numbers to be drawn, refer to
    * the minor ticks. Another alternative is to set the numberformat
    * to an empty string.
    *
    * @param  array           list of values (int/float) on the axis or null
    * @access public
    * @see    setTicksMinor()
    * @see    setTicksAutoSteps()
    * @see    setNumberformat()
    */
    function setTicksMajor($ticks)
    {
        $this->_ticksMajor = $ticks;
    }

    /**
    * Set minor ticks on axis
    *
    * The list of values on the axis indicates where you would like
    * "major ticks" to be drawn. Null results in automatic detection
    * of ticks based upon the auto-steps-setting.
    * Note that drawing of numbers on the axis will be done only at
    * at the major ticks.
    *
    * @param  array           list of values (int/float) on the axis or null
    * @access public
    * @see    setTicksMajor()
    * @see    setTicksAutoSteps()
    */
    function setTicksMinor($ticks)
    {
        $this->_ticksMinor = $ticks;
    }

    /**
    * Set numberformat for axis
    *
    * !! This function is deprecated. Use values->setNumberformat() !!
    *
    * @param  string          sprintf-formatstring
    * @access public
    * @see    Image_Graph_Axis_Values::setNumberformat()
    * @see    $values
    * @deprecated
    */
    function setNumberformat($format)
    {
        $this->values->setNumberformat($format);
    }

    /**
    * Set color for numbers on the axis
    *
    * !! This function is deprecated. Use values->setColor() !!
    *
    * @param  mixed           any color representation supported by Image_Graph_Color::color2RGB()
    * @see    Image_Graph_Color::color2RGB()
    * @access public
    * @see    Image_Graph_Axis_Values::setColor()
    * @see    $values
    * @deprecated
    */
    function setNumbercolor($color)
    {
        $this->values->setColor($color);
    }
}

/**
* Class for storing x-axis settings
*
* Based upon the the common attributes/methods provided by the
* Image_Graph_Axis-class this one addresses all features which
* are needed for the x-axis.
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
    * always use the constants IMAGE_GRAPH_AXISTYPE_* to evaluate/set this variable
    *
    * @var    mixed           only IMAGE_GRAPH_AXISTYPE_TEXT supported
    * @access private
    */
    var $_axistype = IMAGE_GRAPH_AXISTYPE_TEXT;

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
    * This function will allow you to change the type of axis/scale used
    * on this axis. The default is IMAGE_GRAPH_AXISTYPE_TEXT.
    * Note that currently only a textual x-axis is supported, so this
    * function is mainly provided for API-completeness and will become
    * useful as soon as other types are implemented.
    *
    * @param  mixed           use constants IMAGE_GRAPH_AXISTYPE_*; currently only IMAGE_GRAPH_AXISTYPE_TEXT supported
    * @access public
    * @see    $_axistype
    */
    function setAxistype($type)
    {
        if ($type == IMAGE_GRAPH_AXISTYPE_TEXT) {
            $this->_axistype = IMAGE_GRAPH_AXISTYPE_TEXT;
        }
        // @todo: add support for a linear and logarithmic scale
    }

    /**
    * Set labels for data on this axis
    *
    * !! This function is deprecated. Use values->setText() !!
    *
    * @param  array           (array of string) Text-labels on the axis
    * @access public
    * @see    setAxistype()
    * @see    Image_Graph_Axis_Values::setText()
    * @see    $values
    */
    function setLabels($labels)
    {
        $this->values->setText($labels);
    }

    /**
    * Calculate relative position for a value on the axis
    *
    * The value returned by this function is in pixel-coordinates and
    * relative to the upper-left corner of the drawing-area of the graph.
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
    * Calculate absolute position for a value on the axis
    *
    * The value returned by this function is in pixel-coordinates and
    * absolute to the upper-left corner of the image canvas.
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
* Class for storing x-axis settings
*
* Based upon the the common attributes/methods provided by the
* Image_Graph_Axis-class this one addresses all features which
* are needed for the y-axis.
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
    * always use the constants IMAGE_GRAPH_AXISTYPE_* to evaluate/set this variable
    *
    * @var    mixed           only IMAGE_GRAPH_AXISTYPE_LINEAR supported
    * @access private
    */
    var $_axistype = IMAGE_GRAPH_AXISTYPE_LINEAR;

    /**
    * Indicator if axis currently contains data
    *
    * This variable is used internally for performace-reasons to be able
    * to quickly check if there are any data-elements currently assigned
    * to this axis. It's set to true directly by Image_Graph::addData()
    *
    * @var    boolean
    * @access private
    * @see    Image_Graph::addData()
    */
    var $_containsData = false;

    /**
    * Constructor
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
    * This function will allow you to change the type of axis/scale used
    * on this axis.
    * Note that currently only a linear y-axis is supported, so this
    * function is mainly provided for API-completeness and will become
    * useful as soon as other types are implemented.
    *
    * @param  mixed           use constants IMAGE_GRAPH_AXISTYPE_*; currently only IMAGE_GRAPH_AXISTYPE_LINEAR supported
    * @access public
    * @see    $_axistype
    */
    function setAxistype($type)
    {
        if ($type == IMAGE_GRAPH_AXISTYPE_LINEAR) {
            $this->_axistype = IMAGE_GRAPH_AXISTYPE_LINEAR;
        }
        //@todo: add support for a logarithmic scale
    }

    /**
    * Calculate relative position for a value on the axis
    *
    * The value returned by this function is in pixel-coordinates and
    * relative to the upper-left corner of the drawing-area of the graph.
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
    * Calculate absolute position for a value on the axis
    *
    * The value returned by this function is in pixel-coordinates and
    * absolute to the upper-left corner of the image canvas.
    *
    * @param  int/float       data value
    * @access public
    */
    function valueToPixelAbsolute($value)
    {
        return $this->_graph->_drawingareaPos[1] + $this->valueToPixelRelative($value);
    }

    /**
    * Automatically adjust all fields for bounds + ticks
    *
    * Adjusts the values for fields where auto-detection of values is
    * enabled (means: the values are currently null).
    * The attributes _bounds and _ticksMajor/_ticksMajor are evaluated but
    * this function does not change them. Instead it sets _boundsEffective,
    * _ticksMajorEffective and _ticksMinorEffective. This allows seeing
    * which attributes are were set to autodetection originally and to
    * easily recalculate the effective values using this function
    * whenever they might possibly have changed.
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

        do {
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
            if ($currSteps < $this->_ticksAutoSteps['major']) {
                $faktor = $faktor * 0.1;
            }
        } while ($currSteps < $this->_ticksAutoSteps['major']);

        while ($currSteps > $this->_ticksAutoSteps['major'])
        {
            if ($step == 1) {
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
            for ($count = $newMin; $count<=$newMax; $count+=$stepfaktor) {
                $stepsMajor[] = $count;
            }
        }

        if (isset($this->_ticksMinor)) {
            $stepsMinor = $this->_ticksMinor;
        } else {
            do {
                $stepMinor = $step;
                $faktorMinor = $faktor;

                if ($step == 5) {
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
            for ($count = $newMin; $count<=$newMax; $count+=$stepfaktor) {
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

class Image_Graph_Axis_Values extends Image_Graph_Title
{
    /**
    * Numberformat
    *
    * @var    string          format-string in printf-syntax
    * @access private
    */
    var $_numberformat = "%.02f";

    /**
    * Constructor
    *
    * @access public
    */
    function Image_Graph_Axis_Values()
    {
        // standard is no color, will later be inherited from axis if
        //   not explicitly set for these values
        unset($this->_fontOptions['color']);
    }

    /**
    * Set numberformat for numerical values on the axis
    *
    * Use this to set the format in which numbers on the axis will be drawn.
    * E.g. you can use it to set a certain number of decimal values or
    * prepend/append text to the numbers.
    * Numbers will be drawn at all "major ticks" on the axis.
    * Note that the "numberformat" is only used if the type of the axis is
    * numerical (linear or at some later step even logarithmic), not if
    * it's a textual axis (x-axis).
    *
    * @param  string          sprintf-formatstring
    * @access public
    */
    function setNumberformat($format)
    {
        $this->_numberformat = $format;
    }

    /**
    * Set text-labels on axis
    *
    * These text.labels will be used (shown) when the axis-type is
    * IMAGE_GRAPH_AXISTYPE_TEXT.
    * Multiple lines are possible by using "\n" as separator.
    *
    * @param  array           (array of string) Text-labels
    * @access public
    */
    function setText($texts)
    {
        if (is_array($texts)) {
            // @todo: add additional checks here; only array of strings/ints/floats allowed

            $arrayEmpty = true;
            foreach ($texts as $currText) {
                if (!empty($currText)) {
                    $arrayEmpty = false;
                }
            }

            // if array is empty (or all array-entries, e.g. strings, are empty) only set an empty array
            if ($arrayEmpty) {
                $this->_text = array();
            } else {
                $this->_text = $texts;
            }
        }
    }
}

/**
* Class for storing grid-element settings
*
* The "grid" is used to make values (positions) inside the graph easier to
* read by using horizontal/vertical lines and/or filled areas as the
* graph background. It's possible to draw the grid at the major or minor
* ticks.
* Using two or more alternating colors/fills for the grid is an "impressive"
* and useful feature for the generated graphs.
* All fill-elements (derived from Image_Graph_Fill_Common) can be used.
* In most cases you will want to choose a solid fills with a certain color.
* However it might be useful to e.g. use some pattern fills (stripes, dots,
* ...) which will make reading possible even if you print it out in
* black/white.
* Since the normal fill-elements are used, you can also use alpha-values
* for the colors. This way it's still possible to see the graph background
* through the fill.
* Note that you shouldn't use too many different colors/fills since this
* can have a negative and disturbing effect on the readability of the graph.
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
    * Type of grid-lines
    *
    * @var    mixed           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
    * @access private
    */
    var $_lineType = IMAGE_GRAPH_GRID_NONE;

    /**
    * Type of grid-fill
    *
    * @var    mixed           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
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
    * Constructor
    *
    * The default line color is set to darkgray.
    * The default fills will be two alternating solid fills in lightgrey
    * with an alpha-value of 80% and lightblue with an alpha-value of 40%.
    * Note that these color-defaults might be subject to change in one
    * of the next versions!
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
    * Choose whether want to have lines drawn at the major ticks, minor
    * ticks or if you would like no lines at all.
    *
    * @param  mixed           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
    * @access public
    */
    function setLineType($type)
    {
        $this->_lineType = $type;
    }

    /**
    * Set type of grid-fill
    *
    * Choose whether want to have horizontal/vertical fills drawn at the
    * major ticks, minor ticks or if you would like no lines at all.
    *
    * @param  mixed           use constants IMAGE_GRAPH_GRID_NONE, IMAGE_GRAPH_GRID_MAJOR, IMAGE_GRAPH_GRID_MINOR
    * @access public
    */
    function setFillType($type)
    {
        $this->_fillType = $type;
    }

    /**
    * Set fill elements to be used
    *
    * This function will create instances of fill-objects with the options
    * you provide. You can set an arbitrary number of fillelements which will
    * be used in an alternating order.
    * For a list of possible options in the options array please refer to
    * the fill element class you wish to use (e.g. Image_Graph_Fill_Solid).
    * A reference to the fill objects will also be returned by this function.
    * Using that reference you can access methods provided by that fill
    * object, e.g. to customize it.
    *
    * @param  array   $fillelements,...       array with fill element type (e.g. "solid") and an option array
    * @return array                           array of fill-objects
    * @access public
    */
    function &setFill()
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
    * Draws the grid, using GD-output
    *
    * The parameter $drawWhat is needed to be able to first draw the fill
    * and later draw the lines.
    * In the context of this type of graphical element, a grid, the word
    * "border" in the fill-constants refers to the term "lines". This is
    * because the constants are mainly used for data-elements.
    *
    * @param  resource        GD-resource to draw to
    * @param  mixed           choose what to draw; use constants IMAGE_GRAPH_DRAW_FILLANDBORDER, IMAGE_GRAPH_DRAW_JUSTFILL or IMAGE_GRAPH_DRAW_JUSTBORDER; BORDER means "grid-line" in this context
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
            // @todo: make this a bit more flexible :-))

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
?>