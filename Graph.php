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
* Package for drawing graphs (bars, lines, ...)
*
* This package offers you many ways to generate a graphical view from
* numerical data.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

/**
* Show ticks on inside of the axis
*
* For usage with class Image_Graph_Axis_X and Image_Graph_Axis_Y
*
* @see Image_Graph_Axis_X
* @see Image_Graph_Axis_Y
*/
define('IMAGE_GRAPH_TICKS_INSIDE',  1);
/**
* Show ticks on outside of the axis
*
* For usage with class Image_Graph_Axis_X and Image_Graph_Axis_Y
*
* @see Image_Graph_Axis_X
* @see Image_Graph_Axis_Y
*/
define('IMAGE_GRAPH_TICKS_OUTSIDE', 2);
/**
* Show ticks on inside and outside of the axis
*
* For usage with class Image_Graph_Axis_X and Image_Graph_Axis_Y
*
* @see Image_Graph_Axis_X
* @see Image_Graph_Axis_Y
*/
define('IMAGE_GRAPH_TICKS_BOTH',    3);

/**
* Axe is textual
*
* Only supported on axisX.
*
* @see Image_Graph_Axis_X
*/
define('IMAGE_GRAPH_AXISTYPE_TEXT',   'text');
/**
* Axe is linear
*
* Only supported on axisY0/axisY1.
*
* @see Image_Graph_Axis_Y
*/
define('IMAGE_GRAPH_AXISTYPE_LINEAR', 'linear');

require_once 'Image/Graph/Elements.php';
require_once 'Image/Graph/Data/Common.php';  // include to have IMAGE_GRAPH_DRAW_*-constants
require_once("Image/Graph/Color.php");       // extended version of package: PEAR::Image_Color

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
    * X-axis of diagram
    *
    * @var object Image_Graph_Axis_X
    * @access public
    */
    var $axisX = null;

    /**
    * first Y-axis of diagram
    *
    * @var object Image_Graph_Axis_Y
    * @access public
    */
    var $axisY0 = null;

    /**
    * second Y-axis of diagram
    *
    * @var object Image_Graph_Axis_Y
    * @access public
    */
    var $axisY1 = null;

    /**
    * Title of diagram
    *
    * @var object Image_Graph_Title
    * @access public
    */
    var $diagramTitle = null;

    /**
    * Width and height of graph canvas
    *
    * @var array        width/height; initially 0, 0
    * @access private
    */
    var $_size = array(0, 0);

    /**
    * X/Y-position of diagram (from upper left corner)
    *
    * @var array        x/y-position; initially 0, 0
    * @access private
    */
    var $_pos = array(0, 0);

    /**
    * Width and height of drawingarea of diagram
    *
    * @var array        width/height; initially 0, 0
    * @access private
    */
    var $_drawingareaSize = array(0, 0);

    /**
    * X/Y-position of drawingarea of diagram on graph canvas
    *
    * @var array        x/y-position; initially 0, 0
    * @access private
    */
    var $_drawingareaPos = array(0, 0);

    /**
    * Extra borderspace that should be added
    *
    * @var int          space in pixels; initially 0
    * @access private
    */
    var $_borderspace = 0;

    /**
    * Background color
    *
    * @var array              (4 ints for R,G,B,A); initially white, full intensity
    * @access private
    */
    var $_bgcolor = array(255, 255, 255, 255);

    /**
    * Shows if additional space should be added on the left/right side of the graph
    *
    * Normally 0; if set to 1 the data-elements don't start on the y-axis but a bit further right
    * needed for bar-graphs
    *
    * @var int
    * @see addData()
    * @access private
    */
    var $_addExtraSpace = 0;

    /**
    * Default color for new data; used by addData
    *
    * @var array              (4 ints for R,G,B,A); initially black, full intensity
    * @see addData()
    * @access private
    */
    var $_dataDefaultColor = array(0, 0, 0, 255);

    /**
    * Data elements of the diagram (e.g. a "line")
    *
    * @var array              contains references to data-element-objects (Image_Graph_Data_Common and derived)
    * @see addData()
    * @see $_dataElementsEffective
    * @access private
    */
    var $_dataElements = array();

    /**
    * Data elements of the diagram (e.g. a "line")
    *
    * To be filled by function _prepareDataElements() based on data found in _dataElements
    * Will contain objects (like $_dataElements) or, if data is to be stacked, arrays of objects of same type
    *
    * @var array              contains references to objects or arrays containing references to objects
    * @see $_dataElements
    * @access private
    */
    var $_dataElementsEffective = array();

    /**
    * Default options for fonts
    *
    * @var array              stores information like fontPath, fontFile, fontSize, antiAliasing etc.
    * @access private
    */
    var $_defaultFontOptions = array();

    /**
    * Data types to be stacked
    *
    * @var string/array       string "all" allowed for "every data-type"; otherwise an array of strings representing type
    * @access private
    */
    var $_stackData = array();

    /**
    * Constructor for the class
    *
    * @param  int             width of graph-image
    * @param  int             height of graph-image
    * @param  int             x-position of graph-image
    * @param  int             y-position of graph-image
    * @access public
    */
    function &Image_Graph($width, $height, $pos_x=0, $pox_y=0)
    {
        $this->axisX  =& new Image_Graph_Axis_X();
        $this->axisY0 =& new Image_Graph_Axis_Y();
        $this->axisY1 =& new Image_Graph_Axis_Y();
        $this->axisY0->title->setSpacer(array("right" => 5));
        $this->axisY1->title->setSpacer(array("left"  => 5));
        
        $this->gridX  =& new Image_Graph_Grid($this->axisX);
        $this->gridY0 =& new Image_Graph_Grid($this->axisY0);
        $this->gridY1 =& new Image_Graph_Grid($this->axisY1);
        $this->diagramTitle = new Image_Graph_Title();

        $this->_size = array($width, $height);
        $this->_pos  = array($pos_x, $pox_y);
        $this->setSpaceFromBorder(10);
        $this->setAxesColor("black"); // set default color to black, all axes

        $this->_drawingareaSize = $this->_size;
        $this->_drawingareaPos  = $this->_pos;
    }

    /**
    * Sets background color of canvas
    *
    * @param  mixed           any color representation supported by Image_Graph_Color::color2RGB()
    * @see    Image_Graph_Color::color2RGB()
    * @access public
    */
    function setBackgroundColor($color)
    {
        $this->_bgcolor = Image_Graph_Color::color2RGB($color);
    }

    /**
    * Sets space of graph from each border (of the whole image)
    *
    * @param  int             space (in pixel) from each border
    * @access public
    */
    function setSpaceFromBorder($space)
    {
        $this->_borderspace = max(0, $space); // don't allow values < 0
    }

    /**
    * Set color of axes
    *
    * This function allows you to set the color of all axis (axisX, axisY0 and axisY1) all
    * to the same value. It does the same as setting the color for each of those axes
    * manually. It was added solely for convenience since often you will need to give all
    * axes the same new color.
    *
    * @param  mixed           any color representation supported by Image_Graph_Color::color2RGB()
    * @see    Image_Graph_Color::color2RGB()
    * @access public
    */
    function setAxesColor($axesColor)
    {
        $this->axisX->setColor  ($axesColor);
        $this->axisY0->setColor ($axesColor);
        $this->axisY1->setColor ($axesColor);
    }

    /**
    * Set default options for fonts
    *
    * @param  array           stores information like fontType, fontPath, fontFile, fontSize, antiAliasing etc.
    * @access public
    */
    function setDefaultFontOptions($options = array())
    {
        // !! PLEASE NOTE !! As soon as PHP-internal
        // (bitmap) fonts are also supported by Image_Text
        // this will default to those internal fonts
        // instead of TTF
        if (!isset($options['fontType'])) {
            $options['fontType'] = 'TTF';
        }
        
        if (!isset($options['fontPath'])) {
            $options['fontPath'] = './';
        }
        if (!isset($options['fontSize'])) {
            $options['fontSize'] = 10;
        }
        if (!isset($options['color'])) {
            $options['color'] = array(0, 0, 0, 255); // black
        }
        if (!isset($options['antiAliasing'])) {
            $options['antiAliasing'] = false;
        }
        $this->_defaultFontOptions = $options;
    }

    /**
    * Set default color for new data; used by addData()
    *
    * You can set a new dataDefaultColor using this function. It will be
    * used for all *following* calls to addData if you don't explicitly
    * supply a color.
    *
    * @param  mixed           any color representation supported by Image_Graph_Color::color2RGB()
    * @see    Image_Graph_Color::color2RGB()
    * @see    addData()
    * @access public
    */
    function setDataDefaultColor($dataDefaultColor)
    {
        $this->_dataDefaultColor = Image_Graph_Color::color2RGB($dataDefaultColor);
    }

    /**
    * Add new data to the graph
    *
    * This function generate an instance of the given data representation-type (e.g.
    * it will generate an instance of Image_Graph_Data_Line if you supply "line" for
    * $representation). The new object will automatically be added to an internal list
    * of data-objects.
    * A reference to the object which you just added to the graph will also be returned
    * by this function. Using that reference you can e.g. call functions like setColor(),
    * or whatever the specific data-element might support, to customize the data-element.
    * You can use the array $attributes to supply attribute-data to the data-element upon
    * creation. This might be quite handy if you add several data and don't want to call
    * e.g. setColor() on each one separately. But this is up to you ...
    * If no "color" is specified in the attributes-array, the "dataDefaultColor" will be
    * used, which can be set using setDataDefaultColor().
    *
    * @param  array           data to draw
    * @param  string          data representation (e.g. "line")
    * @param  array           attributes like color
    * @return object Image_Graph_Data_Common
    * @see    setDataDefaultColor()
    * @access public
    */
    function &addData($data, $representation = "line", $attributes = array())
    {
        $representation = strtolower($representation);
        $dataElementFile  = "Image/Graph/Data/".ucfirst($representation).".php";
        $dataElementClass = "Image_Graph_Data_".ucfirst($representation);

        if (!isset($attributes['color'])) {
            $attributes['color'] = $this->_dataDefaultColor;
        }
        if (!isset($attributes['axisId'])) {
            $attributes["axisId"] = 0;
        }

        if (!class_exists($dataElementClass)) {
            require_once($dataElementFile);
        }
        $myNew = &new $dataElementClass($this, $data, $attributes);
        $this->_dataElements[count($this->_dataElements)] =& $myNew;
        
        // the following is only true if axisX is of axistype IMAGE_GRAPH_AXISTYPE_TEXT
        $this->axisX->_bounds['max'] = max($this->axisX->_bounds['max'], count($data));

        $this->{"axisY".$attributes['axisId']}->_containsData = true;

        return $myNew;
    }

    /**
    * Set option that data should be stacked
    *
    * Use this function to turn on data-stacking. If $stackWhat is supplied only specific data-types
    * will be stacked.
    * Please note that possibly not all data-types might support stacking, in which case they will
    * simply be left unstacked. But the base-types "bar" and "line" do support it!
    * If you use both Y-axes then only data on the same axis can be stacked (sure!). So if you have
    * e.g. line-data on both axes they will be stacked independently.
    *
    * @param  string/array    name or array of names of data-types you want to be stacked
    * @access public
    */
    function stackData($stackWhat = "all")
    {
        if ($stackWhat == "all") {
            $this->_stackData = "all";
        } elseif (is_array($stackWhat)) { // array of strings, representing data-types like "bar" or "line"
            $this->_stackData = $stackWhat;
        } else {
            $this->_stackData = array($stackWhat); // assume it's just one string (e.g. "line"); transform to array
        }
    }

    /**
    * adjust the min/max-values and the ticks of the Y-axis; calculate if "null"-values (auto-detect)
    *
    * @access private
    */
    function _calculateAxesYMinMaxTicks()
    {
        for ($axisCount=0; $axisCount<=1; $axisCount++) {
            $currAxis = "axisY".$axisCount;
            if (isset($this->{$currAxis}->_bounds['min'])) {
                $this->{$currAxis}->_boundsEffective['min'] = $this->{$currAxis}->_bounds['min'];
            } else {
                foreach ($this->_dataElementsEffective as $currDataElement) {
                    if ($currDataElement->_attributes['axisId'] == $axisCount) {
                        // workaround - maybe solve this more elegant
                        if (!is_array($currDataElement)) {
                            $currDataElementTemp = array(& $currDataElement);
                        } else {
                            $currDataElementTemp = & $currDataElement;
                        }

                        if (!isset($this->{$currAxis}->_boundsEffective['min'])) {
                            $this->{$currAxis}->_boundsEffective['min'] = $currDataElementTemp[0]->_data[0];
                        }
    
                        foreach ($currDataElementTemp as $currDataElementEffective) {
                            if (!is_null($currDataElementEffective->_stackingData)) {
                                $dataTemp = & $currDataElementEffective->_stackingData[0];
                            } else {
                                $dataTemp = & $currDataElementEffective->_data;
                            }
                            foreach ($dataTemp as $currData) {
                                if ($this->{$currAxis}->_boundsEffective['min'] > $currData) {
                                    $this->{$currAxis}->_boundsEffective['min'] = $currData;
                                }
                            }
                        }
                    }
                }
            }

            if (isset($this->{$currAxis}->_bounds['max'])) {
                $this->{$currAxis}->_boundsEffective['max'] = $this->{$currAxis}->_bounds['max'];
            } else {
                foreach ($this->_dataElementsEffective as $currDataElement) {
                    if ($currDataElement->_attributes['axisId'] == $axisCount) {
                        // workaround - maybe solve this more elegant
                        if (!is_array($currDataElement)) {
                            $currDataElementTemp = array(& $currDataElement);
                        } else {
                            $currDataElementTemp = & $currDataElement;
                        }

                        if (!isset($this->{$currAxis}->_boundsEffective['max'])) {
                            $this->{$currAxis}->_boundsEffective['max'] = $currDataElementTemp[0]->_data[0];
                        }
    
                        foreach ($currDataElementTemp as $currDataElementEffective) {
                            if (!is_null($currDataElementEffective->_stackingData)) {
                                $dataTemp = & $currDataElementEffective->_stackingData[1];
                            } else {
                                $dataTemp = & $currDataElementEffective->_data;
                            }
                            foreach ($dataTemp as $currData) {
                                if ($this->{$currAxis}->_boundsEffective['max'] < $currData) {
                                    $this->{$currAxis}->_boundsEffective['max'] = $currData;
                                }
                            }
                        }
                    }
                }
            }

            // correction if only one y-value is present in the diagram
            if ($this->{$currAxis}->_boundsEffective['min'] == $this->{$currAxis}->_boundsEffective['max']) {
                if (($this->{$currAxis}->_boundsEffective['min']-1) >= 0) {
                    $this->{$currAxis}->_boundsEffective['min']--;
                }
                $this->{$currAxis}->_boundsEffective['max']++;
            }

            $this->{$currAxis}->_autoadjustBoundsAndTicks();

            // remove ticks outside the axis-ranges
            foreach ($this->{$currAxis}->_ticksMajorEffective as $key => $value) {
                if (($value < $this->{$currAxis}->_boundsEffective['min']) ||
                    ($value > $this->{$currAxis}->_boundsEffective['max'])) {
                    unset($this->{$currAxis}->_ticksMajorEffective[$key]);
                }
            }
            foreach ($this->{$currAxis}->_ticksMinorEffective as $key => $value) {
                if (($value < $this->{$currAxis}->_boundsEffective['min']) ||
                    ($value > $this->{$currAxis}->_boundsEffective['max'])) {
                    unset($this->{$currAxis}->_ticksMinorEffective[$key]);
                }
            }
        } // for ($axisCount=0; $axisCount<=1; $axisCount++)
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
    * Prepare objects in $_dataElements and fill $_dataElementsEffective
    *
    * If data is to be stacked this function will summarize all objects that belong together in one array.
    + All other references to data objects will be copied "as is" from $_dataElements to $_dataElementsEffective.
    *
    * @access private
    */
    function _prepareDataElements()
    {
        $tempDataElements = array();
        
        // if string, assume it's "all"
        // everything else would have been converted into an array by stackData()
        if (is_string($this->_stackData)) {
            foreach ($this->_dataElements as $key => $value) {
                $element = &$this->_dataElements[$key];
                $class = get_class($element);
                if (is_callable(array($class, "stackingPrepare"))) {
                    // if data-element supports stacking

                    $internalName = $class."-".$element->_attributes['axisId'];
                    if (!isset($tempDataElements[$internalName])) {
                        $tempDataElements[$internalName] = array();
                    }
                    $tempDataElements[$internalName][] = & $element;
                } else {
                    // if data-element does not support stacking
                    
                    $tempDataElements[] = & $element;
                }
            }
        } else {
            foreach ($this->_dataElements as $key => $value) {
                $element = &$this->_dataElements[$key];
                $class = get_class($element);
                // use strtolower so that it will "hopefully" make this class a bit more PHP5-compatible :-)
                $datatype = str_replace("image_graph_data_", "", strtolower($class));
                if (in_array($datatype, $this->_stackData) && is_callable(array($class, "stackingPrepare"))) {
                    $internalName = $class."-".$element->_attributes['axisId'];
                    if (!isset($tempDataElements[$internalName])) {
                        $tempDataElements[$internalName] = array();
                    }
                    $tempDataElements[$internalName][] = & $element;
                } else {
                    $tempDataElements[] = & $element;
                }
            }
        }

        // strip of all keys temporarily used inside this array to make it "clean"
        // not really needed, but it's cleaner this way
        // and while we're processing the elements also do some stacking-preparations
        $this->_dataElementsEffective = array();
        foreach ($tempDataElements as $key => $value) {
            $element = & $tempDataElements[$key]; // workaround for PHP4
            // if element is a "stack-group" call static method of the class to prepare stacking
            if (is_array($element)) {
                $class = get_class($element[0]);
                call_user_func(array($class, "stackingPrepare"), $element);
            } else {
                // reset any possible previously set stackingOptions
                // not really needed, but it's cleaner this way
                $element->_stackingData = null;
            }
            $this->_dataElementsEffective[] = & $element;
        }
    }

    /**
    * Prepare some internal variables
    *
    * This function is needed for some limit-checking, setting internal variables to appropriate values etc.
    * It's necessary to call this function shortly before actually drawing since it does all necessary preparations.
    *
    * @access private
    */
    function _prepareInternalVariables()
    {
        // do initialisation of axes here
        // can't be done in the constructor of Image_Graph because of problems with references in PHP4
        
        $this->axisX->_graph  = &$this;
        $this->axisY0->_graph = &$this;
        $this->axisY1->_graph = &$this;

        $this->_prepareDataElements();
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

            if (!empty($this->axisX->title->_text)) {
                $this->axisX->title->_fontOptions = $this->_mergeFontOptions($this->axisX->title->_fontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text($this->axisX->title->_text, $this->axisX->title->_fontOptions);
                $textSize = $tempText->getSize();
                $borderspaceSum["bottom"] += $textSize['height'];
                $borderspaceSum['bottom'] += $this->axisX->title->_spacer['top'];
                $borderspaceSum['bottom'] += $this->axisX->title->_spacer['bottom'];
            }

            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = "axisY".$axisCount;

                $this->{$currAxis}->_internalTempValues['totalTitleWidth'] = 0;
                if (!empty($this->{$currAxis}->title->_text)) {
                    $this->{$currAxis}->title->_fontOptions = $this->_mergeFontOptions($this->{$currAxis}->title->_fontOptions);

                    require_once 'Image/Text.php';
                    $tempText = new Image_Text($this->{$currAxis}->title->_text, $this->{$currAxis}->title->_fontOptions);
                    $tempText->rotate(90);
                    $textSize = $tempText->getSize();

                    $totalTitleWidth = $textSize['height'] +
                                       $this->{$currAxis}->title->_spacer['left'] +
                                       $this->{$currAxis}->title->_spacer['right'];
                    $this->{$currAxis}->_internalTempValues['totalTitleWidth'] = $totalTitleWidth;

                    if ($axisCount == 0)
                    {
                        $borderspaceSum['left'] += $totalTitleWidth;
                    } else { // else axisCount == 1
                        $borderspaceSum['right'] += $totalTitleWidth;
                    }
                }
            }

            // prepare drawing of labels for the X-axes
            if (!empty($this->axisX->_labels)) {
                $this->axisX->_fontOptions = $this->_mergeFontOptions($this->axisX->_fontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text("", $this->axisX->_fontOptions);
                $maxHeight = 0;

                for ($labelCount=0; $labelCount<$this->axisX->_bounds['max']; $labelCount++) {
                    if (isset($this->axisX->_labels[$labelCount])) {
                        $currLabel = $this->axisX->_labels[$labelCount];
                    } else {
                        $currLabel = null;
                    }
                    if (!empty($currLabel)) {
                        // @todo: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                        if (is_string($currLabel)) {
                            $tempText->lines = array(new Image_Text_Line($currLabel, $tempText->options));
                        } else {
                            $tempText->lines = array(new Image_Text_Line(sprintf($this->axisX->_numberformat, $currLabel), $tempText->options));
                        }

                        $textSize = $tempText->getSize();
                        $maxHeight = max ($maxHeight, $textSize['height']);

                        $this->axisX->_internalTempValues['maxLabelHeight'] = $maxHeight;
                    }
                }
                $borderspaceSum["bottom"] += $maxHeight;
            }

            if (($this->axisX->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
                ($this->axisX->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
               ) {
                $borderspaceSum["bottom"]  += $this->axisX->_tickSize;
            }

            // prepare drawing of numbers for the Y-axes
            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = "axisY".$axisCount;
                if ($this->{$currAxis}->_containsData) {
                    $this->{$currAxis}->_fontOptions = $this->_mergeFontOptions($this->{$currAxis}->_fontOptions);

                    require_once 'Image/Text.php';
                    $tempText = new Image_Text("", $this->{$currAxis}->_fontOptions);

                    $maxWidth = 0;
                    foreach ($this->{$currAxis}->_ticksMajorEffective as $currTick) {
                        // @todo: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                        $tempText->lines = array(new Image_Text_Line(sprintf($this->{$currAxis}->_numberformat, $currTick), $tempText->options));
                        $textSize = $tempText->getSize();
                        $maxWidth = max ($maxWidth, $textSize['width']);
                    }
                    $this->{$currAxis}->_internalTempValues['maxNumWidth'] = $maxWidth;

                    if ($maxWidth > 0) {
                        $maxWidth += 2; // add a few pixels between text and axis-major-ticks
                        if ($axisCount == 0) { // axis 0 (left axis)
                            $borderspaceSum["left"]  += $maxWidth;
                        } else { // axis 1 (right axis)
                            $borderspaceSum["right"] += $maxWidth;
                        }
                    }
                }
            }
        } // if (!empty($this->_defaultFontOptions))

        if ( ($this->axisY0->_containsData) &&
            (($this->axisY0->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
             ($this->axisY0->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
            )
           ) {
            $borderspaceSum["left"]  += $this->axisY0->_tickSize;
        }

        if ( ($this->axisY1->_containsData) &&
            (($this->axisY1->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
             ($this->axisY1->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
            )
           ) {
            $borderspaceSum["right"] += $this->axisY1->_tickSize;
        }

        $this->_drawingareaSize = array ($this->_size[0]-$borderspaceSum["left"]-$borderspaceSum["right"],
                                         $this->_size[1]-$borderspaceSum["top"] -$borderspaceSum["bottom"]);
        $this->_drawingareaPos  = array ($this->_pos[0] +$borderspaceSum["left"],
                                         $this->_pos[1] +$borderspaceSum["top"]);
    }

    /**
    * Draw titles for diagram
    *
    * @param  resource        GD-resource; image to draw to
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

            // draw x-axis text
            if (!empty($this->axisX->title->_text)) {
                require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once

                $tempText = new Image_Text($this->axisX->title->_text, $this->axisX->title->_fontOptions);

                $tempText->align(IMAGE_TEXT_ALIGN_CENTER);
                $textSize = $tempText->getSize();
                $textX = $this->_pos[0] + ($this->_size[0] / 2);
                $textY = $this->_pos[1] + $this->_size[1] - $textSize['height'] - $this->_borderspace;

                $tempText->colorize(array ("r" => $this->axisX->title->_color[0],
                                           "g" => $this->axisX->title->_color[1],
                                           "b" => $this->axisX->title->_color[2]));
                $tempText->renderImage($textX, $textY, $img);
            }

            // draw y-axis texts
            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = "axisY".$axisCount;
                if (!empty($this->{$currAxis}->title->_text)) {
                    require_once 'Image/Text.php';
                    $tempText = new Image_Text($this->{$currAxis}->title->_text, $this->{$currAxis}->title->_fontOptions);
                    $tempText->rotate(90);
                    $textSize = $tempText->getSize();
                    if ($axisCount == 0) {
                        $textX = $this->_pos[0] + $this->_borderspace + $this->{$currAxis}->title->_spacer['left'] + $textSize['height'];
                    } else {
                        $textX = $this->_pos[0] + $this->_size[0] - $this->_borderspace - $this->{$currAxis}->title->_spacer['right'];
                    }
                    $textY = $this->_drawingareaPos[1] + ($this->_drawingareaSize[1]/2) + ($textSize['width']/2);

                    // BEGIN: workaround for current Image_Text v0.2
                    $textY -= ($textSize['height'] + ($this->{$currAxis}->title->_fontOptions['fontSize'] / 4));
                    // END: workaround for current Image_Text v0.2

                    if (is_null($this->{$currAxis}->title->_color)) {
                        $this->{$currAxis}->title->_color = $this->{$currAxis}->_color;
                    }
                    $tempText->colorize(array ("r" => $this->{$currAxis}->title->_color[0],
                                               "g" => $this->{$currAxis}->title->_color[1],
                                               "b" => $this->{$currAxis}->title->_color[2]));
                    $tempText->renderImage($textX, $textY, $img);
                }
            }
        } // if (!empty($this->_defaultFontOptions))
    }

    /**
    * Draw axes for diagram
    *
    * @param  resource        GD-resource; image to draw to
    * @access private
    */
    function _drawGDAxes(&$img)
    {
        $drawColor = Image_Graph_Color::allocateColor($img, $this->axisX->_color);
        // draw X-axis
        imageline    ($img, $this->_drawingareaPos[0],                              $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                            $this->_drawingareaPos[0]+$this->_drawingareaSize[0]-1, $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1, $drawColor);

        for ($labelCount=0; $labelCount<$this->axisX->_bounds['max']; $labelCount++) {
            $currPos = $this->axisX->valueToPixelAbsolute($labelCount);
            $tickSize = $this->axisX->_tickSize;
            switch ($this->axisX->_tickStyle) {
                case IMAGE_GRAPH_TICKS_INSIDE:
                      imageline ($img, $currPos, $this->_drawingareaPos[1]+($this->_drawingareaSize[1]-1)-$tickSize,
                                       $currPos, $this->_drawingareaPos[1]+($this->_drawingareaSize[1]-1),
                                 $drawColor);
                      break;
                case IMAGE_GRAPH_TICKS_OUTSIDE:
                      imageline ($img, $currPos, $this->_drawingareaPos[1]+($this->_drawingareaSize[1]-1),
                                       $currPos, $this->_drawingareaPos[1]+($this->_drawingareaSize[1]-1)+$tickSize,
                                 $drawColor);
                      break;
                case IMAGE_GRAPH_TICKS_BOTH:
                      imageline ($img, $currPos, $this->_drawingareaPos[1]+($this->_drawingareaSize[1]-1)-$tickSize,
                                       $currPos, $this->_drawingareaPos[1]+($this->_drawingareaSize[1]-1)+$tickSize,
                                 $drawColor);
                      break;
            }
        }
                
        // drawing of numbers for the X-axis
        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
            $textoptions = $this->axisX->_fontOptions;
            $tempText = new Image_Text("", $textoptions);

            $textY = $this->_drawingareaPos[1] + $this->_drawingareaSize[1];
            if (($this->axisX->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
                ($this->axisX->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
               ) {
                $textY += $this->axisX->_tickSize;
            }


            for ($labelCount=0; $labelCount<$this->axisX->_bounds['max']; $labelCount++) {
                if (isset($this->axisX->_labels[$labelCount])) {
                    $currLabel = $this->axisX->_labels[$labelCount];
                } else {
                    $currLabel = null;
                }
                if (!empty($currLabel)) {
                    // @todo: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                    if (is_string($currLabel)) {
                        $tempText->lines = array(new Image_Text_Line($currLabel, $tempText->options));
                    } else {
                        $tempText->lines = array(new Image_Text_Line(sprintf($this->axisX->_numberformat, $currLabel), $tempText->options));
                    }

                    $tempText->align(IMAGE_TEXT_ALIGN_CENTER);
                    $textSize = $tempText->getSize();
                    $textX = $this->axisX->valueToPixelAbsolute($labelCount);

                    if (is_null($this->axisX->_numbercolor)) {
                        $this->axisX->_numbercolor = $this->axisX->_color;
                    }

                    $tempText->colorize(array ("r" => $this->axisX->_numbercolor[0],
                                               "g" => $this->axisX->_numbercolor[1],
                                               "b" => $this->axisX->_numbercolor[2]));
                    $tempText->renderImage($textX, $textY, $img);
                }
            }
        } // if (!empty($this->_defaultFontOptions))

        // draw Y-axes
        $axesXpositions = array ($this->_drawingareaPos[0],
                                 $this->_drawingareaPos[0]+$this->_drawingareaSize[0]-1);

        $axesXfactors   = array (1, -1);

        for ($axisCount=0; $axisCount<=1; $axisCount++) {
            $currAxis = "axisY".$axisCount;
            if ($this->{$currAxis}->_containsData) {
                imageline    ($img, $axesXpositions[$axisCount], $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                                    $axesXpositions[$axisCount], $this->_drawingareaPos[1], $drawColor);

                foreach ($this->{$currAxis}->_ticksMajorEffective as $currTick) {
                    $relativeYPosition = $this->{$currAxis}->valueToPixelRelative($currTick);

                    $tickSize = $this->{$currAxis}->_tickSize * $axesXfactors[$axisCount];
                    switch ($this->{$currAxis}->_tickStyle) {
                        case IMAGE_GRAPH_TICKS_INSIDE:
                              imageline ($img, $axesXpositions[$axisCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                               $axesXpositions[$axisCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                         $drawColor);
                              break;
                        case IMAGE_GRAPH_TICKS_OUTSIDE:
                              imageline ($img, $axesXpositions[$axisCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                               $axesXpositions[$axisCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                         $drawColor);
                              break;
                        case IMAGE_GRAPH_TICKS_BOTH:
                              imageline ($img, $axesXpositions[$axisCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                               $axesXpositions[$axisCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                         $drawColor);
                              break;
                    }
                }

                foreach ($this->{$currAxis}->_ticksMinorEffective as $currTick) {
                    $relativeYPosition = $this->{$currAxis}->valueToPixelRelative($currTick);
                    $tickSize = ceil($this->{$currAxis}->_tickSize/2) * $axesXfactors[$axisCount];
                    switch ($this->{$currAxis}->_tickStyle) {
                        case IMAGE_GRAPH_TICKS_INSIDE:
                              imageline ($img, $axesXpositions[$axisCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                               $axesXpositions[$axisCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                         $drawColor);
                              break;
                        case IMAGE_GRAPH_TICKS_OUTSIDE:
                              imageline ($img, $axesXpositions[$axisCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                               $axesXpositions[$axisCount]          , $this->_drawingareaPos[1]+$relativeYPosition,
                                         $drawColor);
                              break;
                        case IMAGE_GRAPH_TICKS_BOTH:
                              imageline ($img, $axesXpositions[$axisCount]-$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                               $axesXpositions[$axisCount]+$tickSize, $this->_drawingareaPos[1]+$relativeYPosition,
                                         $drawColor);
                              break;
                    }
                }
            }
        }

        // drawing of numbers for the Y-axes
        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = "axisY".$axisCount;
                if ($this->{$currAxis}->_containsData) {
                    require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                    $textoptions = $this->{$currAxis}->_fontOptions;
                    $textoptions['width'] = $this->{$currAxis}->_internalTempValues['maxNumWidth'];
                    $tempText = new Image_Text("", $textoptions);

                    if ($axisCount == 0) { // axis 0 (left axis)
                        $textX = $this->_pos[0] + $this->_borderspace + $this->{$currAxis}->_internalTempValues['totalTitleWidth'];
                    } else { // axis 1 (right axis)
                        $textX = $this->_pos[0] + $this->_size[0] - $this->_borderspace - $this->{$currAxis}->_internalTempValues['maxNumWidth'] - $this->{$currAxis}->_internalTempValues['totalTitleWidth'];
                    }

                    foreach ($this->{$currAxis}->_ticksMajorEffective as $currTick) {
                        // @todo: remove this dirty little hack :-) we shouldn't access the lines directly, should we?
                        $tempText->lines = array(new Image_Text_Line(sprintf($this->{$currAxis}->_numberformat, $currTick), $tempText->options));

                        $tempText->align(IMAGE_TEXT_ALIGN_RIGHT);
                        $textSize = $tempText->getSize();
                        $relativeYPosition = $this->{$currAxis}->valueToPixelRelative($currTick);
                        $textY = $this->_drawingareaPos[1]+$relativeYPosition - ($textSize['height']/2);
                        // BEGIN: workaround for current Image_Text v0.2
                        $textY -= ($this->{$currAxis}->_fontOptions['fontSize'] / 4);
                        // END: workaround for current Image_Text v0.2

                        if (is_null($this->{$currAxis}->_numbercolor)) {
                            $this->{$currAxis}->_numbercolor = $this->{$currAxis}->_color;
                        }

                        $tempText->colorize(array ("r" => $this->{$currAxis}->_numbercolor[0],
                                                   "g" => $this->{$currAxis}->_numbercolor[1],
                                                   "b" => $this->{$currAxis}->_numbercolor[2]));
                        $tempText->renderImage($textX, $textY, $img);
                    }
                }
            }
        } // if (!empty($this->_defaultFontOptions))
    }

    /**
    * Create a GD-image-resource for the graph
    *
    * This function will return a truecolor GD-resource. The GD-resource will have the
    * size you supplied when creating the Image_Graph-instance.
    * If you need a palette GD-resource (e.g. for writing of GIF-images) please be sure
    * to downsample the truecolor GD-resource returned by this function yourself.
    * A truecolor-resource is needed for all alpha-channel-features etc. to receive
    * best possible image-quality.
    * It's possible to optionally give this function an already existing gd-resource. It
    * will then be used for drawing. Please note that (at the moment) no solid standard
    * background-fill will be done.
    *
    * @param  resource        if supplied an existing gd-resource will be used for drawing
    * @return resource        truecolor gd-resource containing image of graph
    * @access public
    */
    function getGDImage($gdResource = null)
    {
        $this->_prepareInternalVariables();

        // GD-specific part
        if (!is_resource($gdResource)) {
            $img = imagecreatetruecolor($this->_size[0], $this->_size[1]);
            $bgcolor = Image_Graph_Color::allocateColor($img, $this->_bgcolor);
            imagefill($img, 0, 0, $bgcolor);
        } else {
            $img = &$gdResource;
        }

        // elements to draw before the data
        $this->_drawGDtitles($img);

        // draw grids
        $this->gridX->drawGD($img);
        $this->gridY0->drawGD($img);
        $this->gridY1->drawGD($img);

        // loop through all data-objects and display them
        foreach ($this->_dataElementsEffective as $currDataElement) {
            if (is_array($currDataElement)) {
                $class = get_class($currDataElement[0]);
                call_user_func(array($class, "stackingDrawGD"), $currDataElement, $img);
            } else {
                $currDataElement->drawGD($img);
            }
        }

        // elements to draw after the data
        $this->_drawGDAxes($img);

        return $img;
    }
}
?>