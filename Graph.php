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
* Package for drawing graphs (bars, lines, ...)
*
* This package offers you many ways to generate a graphical view from
* numerical data.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @category images
* @license  The PHP License, version 2.02
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
* Main class for Graph generation
*
* This class is the starting-point if you want to generate graphs. It
* handles the various objects involved (e.g. axes, title, list of
* data-elements). It also provides the basic functions to add
* data-elements (using addData()), for automatic determination of
* min/max values for the axes, for data-handling and for the final
* generation of the graph.
* An instance of Image_Graph is the root for a tree of
* class-instances that will store data and settings, will provide
* methods for customizing the appearance of your graph and will finally
* create the output (graph on a canvas).
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
    * @access private
    * @see    Image_Text
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

        $this->gridX  =& new Image_Graph_Grid($this->axisX);
        $this->gridY0 =& new Image_Graph_Grid($this->axisY0);
        $this->gridY1 =& new Image_Graph_Grid($this->axisY1);
        $this->diagramTitle = new Image_Graph_Title();

        $this->_size = array($width, $height);
        $this->_pos  = array($pos_x, $pox_y);
        $this->setSpaceFromBorder(2);
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
    * These options are directly given to the class Image_Text which
    * handles text-output. For a full list of necessary/available
    * font-options please refer to the Image_Text-docs and/or have a
    * look at the examples shipped with the Image_Graph-package.
    * !! Note that the option "font_type" defaults to "ttf" (TrueType-fonts)
    * at the moment. As soon as PHP-internal (bitmap) fonts are also
    * supported by Image_Text this will default to those internal fonts
    * instead. !!
    *
    * @param  array           stores information like font_type, font_path, font_file, font_size, antialiasing etc.
    * @access public
    */
    function setDefaultFontOptions($options = array())
    {
        if (!isset($options['font_type'])) {
            $options['font_type'] = 'ttf';
        }

        if (!isset($options['font_path'])) {
            $options['font_path'] = './';
        }
        if (!isset($options['font_size'])) {
            $options['font_size'] = 10;
        }
        if (!isset($options['color'])) {
            $options['color'] = array(0, 0, 0, 255); // black
        }
        if (!isset($options['anti_aliasing'])) {
            $options['anti_aliasing'] = false;
        }
        $options['max_lines'] = 100;
        $options['width'] = $this->_size[0];
        $options['height'] = $this->_size[1];
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
    * A reference to the object which was added to the graph will also be returned by
    * this function. Using that reference you can e.g. call functions like setColor(),
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
            $currAxis = & $this->{"axisY".$axisCount};

            $bounds_min_auto = !isset($currAxis->_bounds['min']);
            $bounds_max_auto = !isset($currAxis->_bounds['max']);

            if (!$bounds_min_auto) {
                $currAxis->_boundsEffective['min'] = $currAxis->_bounds['min'];
            }
            if (!$bounds_max_auto) {
                $currAxis->_boundsEffective['max'] = $currAxis->_bounds['max'];
            }
            if (($bounds_min_auto) || ($bounds_max_auto)) {
                foreach ($this->_dataElementsEffective as $currDataElement) {
                    if (!is_array($currDataElement)) {
                        $currDataElementTemp = array($currDataElement);
                    } else {
                        $currDataElementTemp = $currDataElement;
                    }
                    if ($currDataElementTemp[0]->_attributes['axisId'] == $axisCount) {
                        if ($bounds_min_auto && !isset($currAxis->_boundsEffective['min'])) {
                            $currAxis->_boundsEffective['min'] = $currDataElementTemp[0]->_data[0];
                        }
                        if ($bounds_max_auto && !isset($currAxis->_boundsEffective['max'])) {
                            $currAxis->_boundsEffective['max'] = $currDataElementTemp[0]->_data[0];
                        }

                        foreach ($currDataElementTemp as $currDataElementEffective) {
                            if (!is_null($currDataElementEffective->_stackingData)) {
                                $dataTemp = & $currDataElementEffective->_stackingData[0];
                            } else {
                                $dataTemp = & $currDataElementEffective->_data;
                            }
                            foreach ($dataTemp as $currData) {
                                if ($bounds_min_auto && ($currAxis->_boundsEffective['min'] > $currData)) {
                                    $currAxis->_boundsEffective['min'] = $currData;
                                }
                                if ($bounds_max_auto && ($currAxis->_boundsEffective['max'] < $currData)) {
                                    $currAxis->_boundsEffective['max'] = $currData;
                                }
                            }
                        }
                    }
                }
            }

            // correction if only one y-value is present in the diagram
            if ($currAxis->_boundsEffective['min'] == $currAxis->_boundsEffective['max']) {
                if (($currAxis->_boundsEffective['min']-1) >= 0) {
                    $currAxis->_boundsEffective['min']--;
                }
                $currAxis->_boundsEffective['max']++;
            }

            $currAxis->_autoadjustBoundsAndTicks();

            // remove ticks outside the axis-ranges
            foreach ($currAxis->_ticksMajorEffective as $key => $value) {
                if (($value < $currAxis->_boundsEffective['min']) ||
                    ($value > $currAxis->_boundsEffective['max'])) {
                    unset($currAxis->_ticksMajorEffective[$key]);
                }
            }
            foreach ($currAxis->_ticksMinorEffective as $key => $value) {
                if (($value < $currAxis->_boundsEffective['min']) ||
                    ($value > $currAxis->_boundsEffective['max'])) {
                    unset($currAxis->_ticksMinorEffective[$key]);
                }
            }
        } // for ($axisCount=0; $axisCount<=1; $axisCount++)
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
            // diagram title
            if (!empty($this->diagramTitle->_text)) {
                $this->diagramTitle->_fontOptions = array_merge($this->_defaultFontOptions, $this->diagramTitle->_fontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text($this->diagramTitle->_text, $this->diagramTitle->_fontOptions);
                $tempText->init();
                $tempText->measurize();
                $textSize = $tempText->_realTextSize;
                $borderspaceSum['top'] += $textSize['height'];
                $borderspaceSum['top'] += $this->diagramTitle->_spacer['top'];
                $borderspaceSum['top'] += $this->diagramTitle->_spacer['bottom'];
            }

            // title for x-axis
            $currAxis = & $this->axisX;
            if (!empty($currAxis->title->_text)) {
                if (!isset($currAxis->title->_fontOptions['color'])) {
                    $currAxis->title->_fontOptions['color'] = $currAxis->_color;
                }
                $currAxis->title->_fontOptions = array_merge($this->_defaultFontOptions, $currAxis->title->_fontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text($currAxis->title->_text, $currAxis->title->_fontOptions);
                $tempText->init();
                $tempText->measurize();
                $textSize = $tempText->_realTextSize;
                $borderspaceSum["bottom"] += $textSize['height'];
                $borderspaceSum['bottom'] += $currAxis->title->_spacer['top'];
                $borderspaceSum['bottom'] += $currAxis->title->_spacer['bottom'];

                $totalTitleWidth = $textSize['width'] +
                                       $currAxis->title->_spacer['left'] +
                                       $currAxis->title->_spacer['right'];
                $currAxis->_internalTempValues['totalTitleWidth'] = $totalTitleWidth;

                $totalTitleHeight = $textSize['height'] +
                                       $currAxis->title->_spacer['top'] +
                                       $currAxis->title->_spacer['bottom'];
                $currAxis->_internalTempValues['totalTitleHeight'] = $totalTitleHeight;
                $currAxis->_internalTempValues['titleHeight'] = $textSize['height'];
            }

            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = & $this->{"axisY".$axisCount};

                $currAxis->_internalTempValues['totalTitleWidth'] = 0;
                if (!empty($currAxis->title->_text)) {
                    if (!isset($currAxis->title->_fontOptions['color'])) {
                        $currAxis->title->_fontOptions['color'] = $currAxis->_color;
                    }
                    $currAxis->title->_fontOptions = array_merge($this->_defaultFontOptions, $currAxis->title->_fontOptions);

                    require_once 'Image/Text.php';
                    $tempText = new Image_Text($currAxis->title->_text, $currAxis->title->_fontOptions);
                    $tempText->set('angle', 90);
                    $tempText->init();
                    $tempText->measurize();
                    $textSize = $tempText->_realTextSize;

                    $totalTitleWidth = $textSize['height'] +
                                       $currAxis->title->_spacer['left'] +
                                       $currAxis->title->_spacer['right'];
                    $currAxis->_internalTempValues['totalTitleWidth'] = $totalTitleWidth;

                    $totalTitleHeight = $textSize['width'] +
                                       $currAxis->title->_spacer['top'] +
                                       $currAxis->title->_spacer['bottom'];
                    $currAxis->_internalTempValues['totalTitleHeight'] = $totalTitleHeight;

                    if ($axisCount == 0)
                    {
                        $borderspaceSum['left'] += $totalTitleWidth;
                        $borderspaceSum['left'] += $currAxis->title->_spacer['left'];
                        $borderspaceSum['left'] += $currAxis->title->_spacer['right'];
                    } else { // else axisCount == 1
                        $borderspaceSum['right'] += $totalTitleWidth;
                        $borderspaceSum['right'] += $currAxis->title->_spacer['left'];
                        $borderspaceSum['right'] += $currAxis->title->_spacer['right'];
                    }
                }
            }

            // prepare drawing of labels for the X-axes
            $currAxis = & $this->axisX;

            if (!empty($currAxis->values->_text)) {
                if (!isset($currAxis->values->_fontOptions['color'])) {
                    $currAxis->values->_fontOptions['color'] = $currAxis->_color;
                }
                $currAxis->values->_fontOptions = array_merge($this->_defaultFontOptions, $currAxis->values->_fontOptions);

                require_once 'Image/Text.php';
                $tempText = new Image_Text("", $currAxis->values->_fontOptions);
                $maxHeight = 0;

                $currAxis->_internalTempValues['maxLabelHeight'] = 0;
                for ($labelCount=0; $labelCount < $currAxis->_bounds['max']; $labelCount++) {
                    $currLabel = $currAxis->values->_text[$labelCount];
                    if (isset($currLabel) && !empty($currLabel)) {
                        if (is_string($currLabel)) {
                            $tempText->set('text', $currLabel);
                        } else {
                            $tempText->set('text', sprintf($currAxis->values->_numberformat, $currLabel));
                        }

                        $tempText->init();
                        $tempText->measurize();
                        $textSize = $tempText->_realTextSize;
                        $maxHeight = max ($maxHeight, $textSize['height']);

                        $currAxis->_internalTempValues['maxLabelHeight'] = $maxHeight;
                    }
                }
                $borderspaceSum["bottom"] += $maxHeight;
                $borderspaceSum["bottom"] += $currAxis->values->_spacer['top'];
                $borderspaceSum["bottom"] += $currAxis->values->_spacer['bottom'];
            }

            if (($currAxis->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
                ($currAxis->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
               ) {
                $borderspaceSum["bottom"]  += $currAxis->_tickSize;
            }

            // prepare drawing of numbers for the Y-axes
            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = & $this->{"axisY".$axisCount};

                if ($currAxis->_containsData) {
                    if (!isset($currAxis->values->_fontOptions['color'])) {
                        $currAxis->values->_fontOptions['color'] = $currAxis->_color;
                    }
                    $currAxis->values->_fontOptions = array_merge($this->_defaultFontOptions, $currAxis->values->_fontOptions);

                    require_once 'Image/Text.php';
                    $maxWidth = 0;
                    foreach ($currAxis->_ticksMajorEffective as $currTick) {
                        $textStr  = sprintf($currAxis->values->_numberformat, $currTick);
                        $tempText = new Image_Text($textStr, $currAxis->values->_fontOptions);
                        $tempText->init();
                        $tempText->measurize();
                        $textSize = $tempText->_realTextSize;
                        $maxWidth = max ($maxWidth, $textSize['width']);
                    }
                    $currAxis->_internalTempValues['maxNumWidth'] = $maxWidth;

                    if ($maxWidth > 0) {
                        if ($axisCount == 0) { // axis 0 (left axis)
                             $borderspaceSum["left"] += $maxWidth;
                             $borderspaceSum["left"] += $currAxis->values->_spacer['left'];
                             $borderspaceSum["left"] += $currAxis->values->_spacer['right'];
                        } else { // axis 1 (right axis)
                             $borderspaceSum["right"] += $maxWidth;
                             $borderspaceSum["right"] += $currAxis->values->_spacer['left'];
                             $borderspaceSum["right"] += $currAxis->values->_spacer['right'];
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
            // draw diagram title
            if (!empty($this->diagramTitle->_text)) {
                require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                $textY = $this->_pos[1] + $this->_borderspace + $this->diagramTitle->_spacer['top'];
                $textX = 0;
                $options = array_merge($this->diagramTitle->_fontOptions, array('x' => $textX, 'y' => $textY));

                $tempText = new Image_Text($this->diagramTitle->_text, $options);
                $tempText->set('halign', IMAGE_TEXT_ALIGN_CENTER);
                $tempText->set('canvas', $img);
                $tempText->init();
                $tempText->measurize();
                $tempText->render();
            }

            // draw x-axis title
            $currAxis = & $this->axisX;
            if (!empty($currAxis->title->_text)) {
                require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                $textY = $this->_pos[1] + $this->_size[1] - $currAxis->_internalTempValues['titleHeight'] - $this->_borderspace - $currAxis->title->_spacer['bottom'];
                $height = $currAxis->_internalTempValues['titleHeight'];

                $width = $this->_drawingareaSize[0];
                $textX = $this->_drawingareaPos[0];

                $options = array_merge($currAxis->title->_fontOptions, array('x' => $textX, 'y' => $textY, 'width' => $width, 'height' => $height));
                $tempText = new Image_Text($currAxis->title->_text, $options);
                $tempText->set('halign', IMAGE_TEXT_ALIGN_CENTER);
                $tempText->set('canvas', $img);
                $tempText->init();
                $tempText->measurize();
                $tempText->render();
            }

            // draw y-axis titles
            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = & $this->{"axisY".$axisCount};

                if (!empty($currAxis->title->_text)) {
                    require_once 'Image/Text.php';
                    $options = array('angle' => 90, 'canvas' => $img);
                    $options = array_merge($options, $currAxis->title->_fontOptions);

                    $tempText = new Image_Text($currAxis->title->_text, $options);
                    $tempText->set('canvas', $img);
                    $tempText->init();
                    $tempText->measurize();
                    $textSize = $tempText->_realTextSize;
                    if ($axisCount == 0) {
                        $textX = $this->_pos[0] + $this->_borderspace + $currAxis->title->_spacer['left'];
                    } else {
                        $textX = $this->_pos[0] + $this->_size[0] - 1 - $this->_borderspace - $currAxis->title->_spacer['right'] - $textSize['height'];
                    }
                    $textY = $this->_drawingareaPos[1] + ($this->_drawingareaSize[1]/2) + ($textSize['width']/2);

                    // @todo: !!! adapt this to the new API !!!
                    $tempText->set(array('x' => $textX, 'y' => $textY));
                    $tempText->render();
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

        // drawing of labels/numbers on the X-axis
        if (!empty($this->_defaultFontOptions) &&   // otherwise we don't have correct settings for font-filename etc.
            !empty($this->axisX->values->_text)
           ) {
            require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
            $options = $this->axisX->values->_fontOptions;
            $tempText = new Image_Text("", $options);
            $tempText->set('canvas', $img);

            $textY = $this->_drawingareaPos[1] + $this->_drawingareaSize[1];
            if (($this->axisX->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
                ($this->axisX->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)
               ) {
                $textY += $this->axisX->_tickSize;
            }
            $textY += round($this->axisX->_internalTempValues['maxLabelHeight'] / 2);
            $textY += $this->axisX->values->_spacer['top'];
            $tempText->set('height', $this->axisX->_internalTempValues['maxLabelHeight']);


            for ($labelCount=0; $labelCount<$this->axisX->_bounds['max']; $labelCount++) {
                unset($currLabel);
                if (isset($this->axisX->values->_text[$labelCount])) {
                    $currLabel = $this->axisX->values->_text[$labelCount];
                }
                if (isset($currLabel) && !empty($currLabel)) {
                    if (is_string($currLabel)) {
                        $tempText->set('text', $currLabel);
                    } else {
                        $tempText->set('text', sprintf($this->axisX->values->_numberformat, $currLabel));
                    }

                    $tempText->set('halign', IMAGE_TEXT_ALIGN_CENTER);
                    $tempText->init();
                    $tempText->measurize();
                    $textSize = $tempText->_realTextSize;
                    $textX = $this->axisX->valueToPixelAbsolute($labelCount);

                    // @todo: !!! adapt this to the new API !!!
                    $options = array('cx' => $textX, 'cy' => $textY, 'canvas' => $img);
                    $tempText->set($options);
                    $tempText->init();
                    $tempText->measurize();
                    $tempText->render();
                }
            }
        } // if (!empty($this->_defaultFontOptions))

        // draw Y-axes
        $axesXpositions = array ($this->_drawingareaPos[0],
                                 $this->_drawingareaPos[0]+$this->_drawingareaSize[0]-1);

        for ($axisCount=0; $axisCount<=1; $axisCount++) {
            $currAxis = & $this->{"axisY".$axisCount};

            if ($currAxis->_containsData) {
                imageline    ($img, $axesXpositions[$axisCount], $this->_drawingareaPos[1]+$this->_drawingareaSize[1]-1,
                                    $axesXpositions[$axisCount], $this->_drawingareaPos[1], $drawColor);

                $tickSize = $currAxis->_tickSize;
                $currAxis->_internalTempValues['tickSize'] = $tickSize;

                foreach ($currAxis->_ticksMajorEffective as $currTick) {
                    $relativeYPosition = $currAxis->valueToPixelRelative($currTick);

                    switch ($currAxis->_tickStyle) {
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

                $tickSize = ceil($currAxis->_tickSize/2);
                foreach ($currAxis->_ticksMinorEffective as $currTick) {
                    $relativeYPosition = $currAxis->valueToPixelRelative($currTick);

                    switch ($currAxis->_tickStyle) {
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

        // drawing of labels/numbers on the Y-axes
        if (!empty($this->_defaultFontOptions)) { // otherwise we don't have correct settings for font-filename etc.
            for ($axisCount=0; $axisCount<=1; $axisCount++) {
                $currAxis = & $this->{"axisY".$axisCount};

                if ($currAxis->_containsData) {
                    require_once 'Image/Text.php'; // already done in _prepareInternalVariables() - but remember it's an require_once
                    $options = $currAxis->values->_fontOptions;
                    $options['width'] = $currAxis->_internalTempValues['maxNumWidth'];
                    $tempText = new Image_Text("", $options);
                    $tempText->set('canvas', $img);

                    if ($axisCount == 0) { // axis 0 (left axis)
                        $textX = $this->_drawingareaPos[0]-1
                                 - $currAxis->_internalTempValues['maxNumWidth'];
                        if (($currAxis->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
                            ($currAxis->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)) {
                            $textX -= $currAxis->_internalTempValues['tickSize'];
                        }
                        $textX -= $currAxis->values->_spacer['right'];
                    } else { // axis 1 (right axis)
                        $textX = $this->_drawingareaPos[0] + $this->_drawingareaSize[0]-1;
                        if (($currAxis->_tickStyle == IMAGE_GRAPH_TICKS_OUTSIDE) ||
                            ($currAxis->_tickStyle == IMAGE_GRAPH_TICKS_BOTH)) {
                            $textX += $currAxis->_internalTempValues['tickSize'];
                        }
                        $textX += $currAxis->values->_spacer['left'];
                    }

                    foreach ($currAxis->_ticksMajorEffective as $currTick) {
                        $tempText->set('text', sprintf($currAxis->values->_numberformat, $currTick));
                        $tempText->set('halign', IMAGE_TEXT_ALIGN_RIGHT);
                        $tempText->set('width', $currAxis->_internalTempValues['maxNumWidth']);
                        $tempText->set('canvas', $img);
                        $tempText->init();
                        $tempText->measurize();
                        $textSize = $tempText->_realTextSize;
                        $relativeYPosition = $currAxis->valueToPixelRelative($currTick);

                        $textY = (int) round($this->_drawingareaPos[1]+$relativeYPosition - ($textSize['height']/2));

                        // @todo: !!! adapt this to the new API !!!
/*
                        if (is_null($currAxis->_numbercolor)) {
                            $currAxis->_numbercolor = $currAxis->_color;
                        }

                        $tempText->setColor(array ("r" => $currAxis->_numbercolor[0],
                                                   "g" => $currAxis->_numbercolor[1],
                                                   "b" => $currAxis->_numbercolor[2]), 0);
*/
                        $options = array('x' => (int)$textX, 'y' => (int)$textY, 'canvas' => $img);
                        $tempText->set($options);
                        $tempText->init();
                        $tempText->measurize();
                        $tempText->render();
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