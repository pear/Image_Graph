<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// $Id$

/**
* Base class for data-storage in common objects
*
* Please note that some functions may not be needed in all derived classes and will
* explicitly be marked "(optional)" in those places. This layout was chosen to
* prevent from having too much classes, which would be bad for PHP.
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
    * @var array (3 ints for R,G,B); initially null
    * @access private
    */
    var $_color = null;

    /**
    * Options for fonts
    *
    * @var array      stores information like fontPath, fontFile, fontSize, antiAliasing etc.; initially empty
    * @access private
    */
    var $_fontOptions = array();

    /**
    * Text
    *
    * @var string     text to be displayed
    * @access private
    */
    var $_text = "";

    /**
    * Set color
    *
    * If the color-value is "null" instead of an array default values will be taken
    *
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setColor($color)
    {
        $this->_color = $color;
    }

    /**
    * Set options for fonts
    *
    * All information not given in this array (e.g. fontPath) will be taken from the default font-options set in the Image_Graph-object
    *
    * @param  array   stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @access public
    */
    function setFontOptions($options = array())
    {
        $this->_fontOptions = $options;
    }

    /**
    * Set text
    *
    * @param  array/string   lines of title; lines can also be separated by "\n" and will automatically be converted into an array
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
* Class for storing specific settings for axes
*
* Please note that some functions may not be needed in all derived classes and will
* explicitly be marked "(optional)" in those places. This layout was chosen to
* prevent from having too much classes, which would be bad for PHP.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_Axe extends Image_Graph_Base
{
    /**
    * Title for axe
    *
    * @var object
    * @access public
    */
    var $title = null;

    /**
    * Bounds for axe (min/max value)
    *
    * @var array (2 ints/floats/nulls)     null results in automatic detection of bounds
    * @access private
    * @see $_boundsEffective
    */
    var $_bounds = array('min' => null, 'max' => null);

    /**
    * effective bounds for axe (min/max value)
    *
    * in contrast to $_bounds these values are not to be influenced by the user but used internally
    * for storing values that will be used for drawing; where $_bounds may contain null-values,
    * this array will store the automatically detected min/max-values that will be used for drawing
    *
    * @var array (2 ints/floats)           contains min and max value for the bounds
    * @access private
    * @see $_bounds
    */
    var $_boundsEffective = array('min' => null, 'max' => null);

    /**
    * Style for ticks on the axe
    *
    * @var int     tick-style
    * @access private
    */
    var $_tickStyle = IMAGE_GRAPH_TICKS_OUTSIDE;

    /**
    * Size for ticks on the axe
    *
    * @var int     tick-size
    * @access private
    */
    var $_tickSize = 10;

    /**
    * Major ticks on axe
    *
    * @var array (ints/floats)     null results in automatic detection of ticks (!to be implemented!)
    * @access private
    * @see $_ticksMajorEffective
    */
    var $_ticksMajor = null;

    /**
    * effective major ticks on axe
    *
    * in contrast to $_ticksMajor these values are not to be influenced by the user but used internally
    * for storing values that will be used for drawing; where $_ticksMajor may contain null-values,
    * this array will store the automatically determined ticks that will be used for drawing
    *
    * @var array (ints/floats)     contains the ticks that will be drawn
    * @access private
    * @see $_ticksMajor
    */
    var $_ticksMajorEffective = array();

    /**
    * Minor ticks on axe
    *
    * @var array (ints/floats)     null results in automatic detection of ticks (!to be implemented!)
    * @access private
    * @see $_ticksMinorEffective
    */
    var $_ticksMinor = null;

    /**
    * effective minor ticks on axe
    *
    * in contrast to $_ticksMinor these values are not to be influenced by the user but used internally
    * for storing values that will be used for drawing; where $_ticksMinor may contain null-values,
    * this array will store the automatically determined ticks that will be used for drawing
    *
    * @var array (ints/floats)     contains the ticks that will be drawn
    * @access private
    * @see $_ticksMinor
    */
    var $_ticksMinorEffective = array();

    /**
    * maximum steps for automatic creation of ticksMajor / ticksMinor
    *
    * @var array (2 ints)          contains "major" and "minor" value (max steps) for the ticks
    * @access private
    * @see $_ticksMajor
    * @see $_ticksMinor
    * @see setTicksAutoSteps
    */
    var $_ticksAutoSteps = array("major" => 5, "minor" => 25);

    /**
    * Numberformat
    *
    * @var string     format-string in printf-syntax
    * @access private
    */
    var $_numberformat = "%.02f";

    /**
    * Color for numbers
    *
    * @var array (3 ints for R,G,B); initially null
    * @access private
    */
    var $_numbercolor = null;

    /**
    * Indicator if axe currently contains data
    *
    * this variable is only used on the Y-axis; directly set internally by functions of package Image_Graph
    *
    * @var boolean
    * @access private
    */
    var $_containsData = false;

    /**
    * Space for storing internal temporary values
    *
    * stores information / calculations between different function calls; introduced for performance
    * directly set internally by functions of package Image_Graph
    *
    * @var boolean
    * @access private
    */
    var $_internalTempValues = array();

    /**
    * Constructor for the class
    *
    * @access public
    */
    function Image_Graph_Axe()
    {
      $this->title = new Image_Graph_Title();
    }

    /**
    * Set bounds
    *
    * @param  int/float     min-value; null results in automatic detection
    * @param  int/float     max-value; null results in automatic detection
    * @access public
    */
    function setBounds($min, $max)
    {
        $this->_bounds = array('min' => $min, 'max' => $max);
    }
    
    /**
    * Set maximum steps for automatic creation of ticksMinor / ticksMajor
    *
    * @param  int           value for steps of ticksMajor (null results in fallback to default of 5)
    * @param  int           value for steps of ticksMinor (null results in fallback to default of 25, at least the minor)
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
    * Set tick-style for ticks on a axe
    *
    * use constants IMAGE_GRAPH_TICKS_INSIDE, IMAGE_GRAPH_TICKS_OUTSIDE or IMAGE_GRAPH_TICKS_BOTH
    *
    * @param  int     tick-style
    * @access public
    */
    function setTickStyle($style)
    {
        $this->_tickStyle = $style;
    }

    /**
    * Set size for (major) ticks on axe
    *
    * size is for the "major ticks"; all minor ticks will be half that size
    *
    * @param  int     size in pixels
    * @access public
    */
    function setTickSize($size)
    {
        $this->_tickSize = max(0, $size);
    }

    /**
    * Set major ticks on axe
    *
    * @param  array   list of values on the axe which should be "major ticks"; null results in automatic detection of bounds (!to be implemented!)
    * @access public
    */
    function setTicksMajor($ticks)
    {
        $this->_ticksMajor = $ticks;
    }

    /**
    * Set minor ticks on axe
    *
    * @param  array   list of values on the axe which should be "minor ticks"; null results in automatic detection of bounds (!to be implemented!)
    * @access public
    */
    function setTicksMinor($ticks)
    {
        $this->_ticksMinor = $ticks;
    }

    /**
    * Set numberformat for axe
    *
    * @param  string  format in which numbers on the axe (major ticks) will be shown; sprintf-format
    * @access public
    */
    function setNumberformat($format)
    {
        $this->_numberformat = $format;
    }


    /**
    * Set numbercolor for axe
    *
    * If the color-value is "null" instead of an array default values will be taken
    *
    * @param  array (3 ints for R,G,B)
    * @access public
    */
    function setNumbercolor($color)
    {
        $this->_numbercolor = $color;
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
    * @var array (4 ints)     array with keys "top, bottom, left, right"; describes extra-space to add beside this title
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
        $this->setColor(array(0, 0, 0)); // black
    }

    /**
    * Set spacer
    *
    * extra-space to add beside this title
    *
    * @param  array (ints)    array with keys "top, bottom, left, right"; if some elements are omitted current values are left in place
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
