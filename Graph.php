<?php
// +--------------------------------------------------------------------------+
// | Image_Graph                                                              |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2003, 2004 Jesper Veggerby                                 |
// | Email         pear.nosey@veggerby.dk                                     |
// | Web           http://pear.veggerby.dk                                    |
// | PEAR          http://pear.php.net/package/Image_Graph                    |
// +--------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU Lesser General Public               |
// | License as published by the Free Software Foundation; either             |
// | version 2.1 of the License, or (at your option) any later version.       |
// |                                                                          |
// | This library is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU        |
// | Lesser General Public License for more details.                          |
// |                                                                          |
// | You should have received a copy of the GNU Lesser General Public         |
// | License along with this library; if not, write to the Free Software      |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA |
// +--------------------------------------------------------------------------+

/**
 * Image_Graph - Main class for the graph creation.
 *
 * This class is the main class for graph creation
 * @package Image_Graph
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */

/**
 * Include file PEAR/ErrorStack.php - for error handling
 */
require_once 'PEAR/ErrorStack.php';

/**
 * Include file Image/Graph/Config.php
 */
require_once 'Image/Graph/Config.php';

/**
 * Include file Image/Graph/Element.php
 */
require_once 'Image/Graph/Element.php';

/**
 * Include file Image/Graph/Constants.php
 */
require_once 'Image/Graph/Constants.php';

/**
 * Include file Image/Graph/Color.php
 */
require_once 'Image/Graph/Color.php';

/**
 * Main class for the graph creation.
 *
 * This is the main class, it manages the driver and performs the final output
 * by sequentialy making the elements output their results. The final output is
 * handled using the {@link Image_Graph_Driver} classes which makes it possible
 * to use different engines (fx GD, PDFlib, libswf, etc) for output to several
 * formats with a non-intersecting API.
 *
 * This class also handles coordinates and the correct managment of setting the
 * correct coordinates on child elements.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 */
class Image_Graph extends Image_Graph_Element
{

    /**
     * Show generation time on graph
     * @var bool
     * @access private
     */
    var $_showTime = false;

    /**
     * Display errors on the canvas
     * @var boolean
     * @access private
     */
    var $_displayErrors = false;

    /**
     * Image_Graph [Constructor].
     *
     * If passing the 3 parameters they are defined as follows:'
     * 
     * Fx.:
     * 
     * $Graph =& new Image_Graph(400, 300);
     * 
     * or using the factory method:
     * 
     * $Graph =& Image_Graph::factory('graph', array(400, 300));
     * 
     * This causes a 'png' driver to be created by default. 
     * 
     * Otherwise use a single parameter either as an associated array or passing
     * the driver along to the constructor:
     *
     * 1) Create a new driver with the following parameters:
     *
     * 'driver' - The driver type, can be any of 'gd', 'jpg', 'png' or 'svg'
     * (more to come) - if omitted the default is 'gd'
     *
     * 'width' - The width of the graph
     *
     * 'height' - The height of the graph
     * 
     * An example of this usage:
     * 
     * $Graph =& Image_Graph::factory('graph', arrray(array('width' => 400,
     * 'height' => 300, 'driver' => 'jpg')));
     * 
     * NB! In thïs case remember the "double" array (see {@link Image_Graph::
     * factory()})
     * 
     * 2) Use the driver specified, pass a valid Image_Graph_Driver as
     * parameter. Remember to pass by reference, i. e. &amp;$driver, fx.:
     *
     * $Graph =& new Image_Graph(&$Driver);
     *
     * or using the factory method:
     *
     * $Graph =& Image_Graph::factory('graph', &$Driver));
     *
     * @param mixed $params The width of the graph, an indexed array
     * describing a new driver or a valid {@link Image_Graph_Driver} object
     * @param int $height The height of the graph in pixels
     * @param bool $createTransparent Specifies whether the graph should be
     *   created with a transparent background (fx for PNG's - note: transparent
     *   PNG's is not supported by Internet Explorer!)
     */
    function &Image_Graph($params, $height = false, $createTransparent = false)
    {
        parent::Image_Graph_Element();

        $this->setFont(Image_Graph::factory('Image_Graph_Font'));

        if (defined('IMAGE_GRAPH_DEFAULT_DRIVER_TYPE')) {
            $driverType = IMAGE_GRAPH_DEFAULT_DRIVER_TYPE;
        } else {
            $driverType = 'png'; // use GD as default, if nothing else is specified
        }

        if (is_array($params)) {
            if (isset($params['driver'])) {
                $driverType = $params['driver'];
            }

            $width = 0;
            $height = 0;

            if (isset($params['width'])) {
                $width = $params['width'];
            }

            if (isset($params['height'])) {
                $height = $params['height'];
            }
        } elseif (is_a($params, 'Image_Graph_Driver')) {
            $this->_driver =& $params;
            $width = $this->_driver->getWidth();
            $height = $this->_driver->getHeight();
        }

        if (is_int($params)) {
            $width = $params;
        }

        if ($this->_driver == null) {
            include_once 'Image/Graph/Driver.php';
            $this->_driver =&
                Image_Graph_Driver::factory(
                    $driverType,
                    array('width' => $width, 'height' => $height)
                );
        }

        $this->_setCoords(0, 0, $width - 1, $height - 1);
    }

    /**
     * Gets the driver for this graph.
     *
     * The driver is set by either passing it to the constructor {@link
     * Image_Graph::ImageGraph()} or using the {@link Image_Graph::setDriver()}
     * method.
     *
     * @return Image_Graph_Driver The driver used by this graph
     * @access private
     * @since 0.3.0dev2
     */
    function &_getDriver()
    {
        return $this->_driver;
    }

    /**
     * Sets the driver for this graph.
     *
     * Calling this method makes this graph use the newly specified driver for
     * handling output. This method should be called whenever multiple
     * 'outputs' are required. Invoke this method after calls to {@link
     * Image_Graph:: done()} has been performed, to switch drivers.
     *
     * @param Image_Graph_Driver $driver The new driver
     * @return Image_Graph_Driver The new driver
     * @since 0.3.0dev2
     */
    function &setDriver(&$driver)
    {
        $this->_driver =& $driver;
        $this->_setCoords(
            0,
            0,
            $this->_driver->getWidth() - 1,
            $this->_driver->getHeight() - 1
        );
        return $this->_driver;
    }

    /**
     * Gets a very precise timestamp
     *
     * @return The number of seconds to a lot of decimals
     * @access private
     */
    function _getMicroTime()
    {
        list($usec, $sec) = explode(' ', microtime()); 
        return ((float)$usec + (float)$sec); 
    }

    /**
     * Gets the width of this graph.
     *
     * The width is returned as 'defined' by the driver.
     *
     * @return int the width of this graph
     */
    function width()
    {
        return $this->_driver->getWidth();
    }

    /**
     * Gets the height of this graph.
     *
     * The height is returned as 'defined' by the driver.
     *
     * @return int the height of this graph
     */
    function height()
    {
        return $this->_driver->getHeight();
    }

    /**
     * Enables displaying of errors on the output.
     *
     * Use this method to enforce errors to be displayed on the output. Calling
     * this method makes PHP uses this graphs error handler as default {@link
     * Image_Graph::_default_error_handler()}.
     */
    function displayErrors()
    {
        $this->_displayErrors = true;
        set_error_handler(array(&$this, '_default_error_handler'));
    }

    /**
     * Sets the log method for this graph.
     *
     * Use this method to enable logging. This causes any errors caught
     * by either the error handler {@see Image_Graph::displayErrors()}
     * or explicitly by calling {@link Image_Graph_Common::_error()} be
     * logged using the specified logging method.
     *
     * If a filename is specified as log method, a Log object is created (using
     * the 'file' handler), with a handle of 'Image_Graph Error Log'.
     *
     * Logging requires {@link Log}.
     *
     * @param mixed $log The log method, either a Log object or filename to log
     * to
	 * @since 0.3.0dev2
     */
    function setLog($log)
    {
        $stack =& $this->_getErrorStack();
        if (is_string($log)) {
            include_once 'Log.php';
            $log =& Log::factory('file', $log, 'Image_Graph Error Log');
        }
        $stack->setLogger($log);
    }

    /**
     * Factory method to create Image_Graph objects.
     *
     * Used for 'lazy including', i.e. loading only what is necessary, when it
     * is necessary. If only one parameter is required for the constructor of
     * the class simply pass this parameter as the $params parameter, unless the
     * parameter is an array or a reference to a value, in that case you must
     * 'enclose' the parameter in an array. Similar if the constructor takes
     * more than one parameter specify the parameters in an array, i.e
     *
     * Image_Graph::factory('MyClass', array($param1, $param2, &$param3));
     *
     * Variables that need to be passed by reference *must* have the &amp;
     * before the variable, i.e:
     *
     * Image_Graph::factory('line', &$Dataset);
     *
     * or
     *
     * Image_graph::factory('bar', array(array(&$Dataset1, &$Dataset2),
     * 'stacked'));
     *
     * Class name can be either of the following:
     *
     * 1 The 'real' Image_Graph class name, i.e. Image_Graph_Plotarea or
     * Image_Graph_Plot_Line
     *
     * 2 Short class name (leave out Image_Graph) and retain case, i.e.
     * Plotarea, Plot_Line *not* plot_line
     *
     * 3 Class name 'alias', the following are supported:
     *
     * 'graph' = Image_Graph
     *
     * 'plotarea' = Image_Graph_Plotarea
     *
     * 'line' = Image_Graph_Plot_Line
     *
     * 'area' = Image_Graph_Plot_Area
     *
     * 'bar' = Image_Graph_Plot_Bar
     *
     * 'pie' = Image_Graph_Plot_Pie
     *
     * 'radar' = Image_Graph_Plot_Radar
     *
     * 'step' = Image_Graph_Plot_Step
     *
     * 'impulse' = Image_Graph_Plot_Impulse
     *
     * 'dot' or 'scatter' = Image_Graph_Plot_Dot
     *
     * 'smooth_line' = Image_Graph_Plot_Smoothed_Line
     *
     * 'smooth_area' = Image_Graph_Plot_Smoothed_Area

     * 'dataset' = Image_Graph_Dataset_Trivial
     *
     * 'random' = Image_Graph_Dataset_Random
     *
     * 'function' = Image_Graph_Dataset_Function
     *
     * 'vector' = Image_Graph_Dataset_VectorFunction
     *
     * 'axis' = Image_Graph_Axis
     *
     * 'axis_log' = Image_Graph_Axis_Logarithmic
     *
     * 'title' = Image_Graph_Title
     *
     * 'line_grid' = Image_Graph_Grid_Lines
     *
     * 'bar_grid' = Image_Graph_Grid_Bars
     *
     * 'polar_grid' = Image_Graph_Grid_Polar
     *
     * 'legend' = Image_Graph_Legend
     *
     * 'ttf_font' = Image_Graph_Font_TTF
     *
     * 'gradient' = Image_Graph_Fill_Gradient
     *
     * @param string $class The class for the new object
     * @param mixed $params The paramaters to pass to the constructor
     * @return object A new object for the class
     * @static
     */
    function &factory($class, $params = null)
    {
        if (substr($class, 0, 11) != 'Image_Graph') {
            switch ($class) {
            case 'graph':
                $class = 'Image_Graph';
                break;

            case 'plotarea':
                $class = 'Image_Graph_Plotarea';
                break;

            case 'line':
                $class = 'Image_Graph_Plot_Line';
                break;

            case 'area':
                $class = 'Image_Graph_Plot_Area';
                break;

            case 'bar':
                $class = 'Image_Graph_Plot_Bar';
                break;

            case 'smooth_line':
                $class = 'Image_Graph_Plot_Smoothed_Line';
                break;

            case 'smooth_area':
                $class = 'Image_Graph_Plot_Smoothed_Area';
                break;

            case 'pie':
                $class = 'Image_Graph_Plot_Pie';
                break;

            case 'radar':
                $class = 'Image_Graph_Plot_Radar';
                break;

            case 'step':
                $class = 'Image_Graph_Plot_Step';
                break;

            case 'impulse':
                $class = 'Image_Graph_Plot_Impulse';
                break;

            case 'dot':
            case 'scatter':
                $class = 'Image_Graph_Plot_Dot';
                break;

            case 'dataset':
                $class = 'Image_Graph_Dataset_Trivial';
                break;

            case 'random':
                $class = 'Image_Graph_Dataset_Random';
                break;

            case 'function':
                $class = 'Image_Graph_Dataset_Function';
                break;

            case 'vector':
                $class = 'Image_Graph_Dataset_VectorFunction';
                break;

            case 'axis':
                $class = 'Image_Graph_Axis';
                break;

            case 'axis_log':
                $class = 'Image_Graph_Axis_Logarithmic';
                break;

            case 'title':
                $class = 'Image_Graph_Title';
                break;

            case 'line_grid':
                $class = 'Image_Graph_Grid_Lines';
                break;

            case 'bar_grid':
                $class = 'Image_Graph_Grid_Bars';
                break;

            case 'polar_grid':
                $class = 'Image_Graph_Grid_Polar';
                break;

            case 'legend':
                $class = 'Image_Graph_Legend';
                break;

            case 'ttf_font':
                $class = 'Image_Graph_Font_TTF';
                break;

            case 'gradient':
                $class = 'Image_Graph_Fill_Gradient';
                break;

            default:
                $class = 'Image_Graph_' . $class;
                break;

            }
        }

        /* A small check whether the class/file should be included or not, since
         * it appears as if include_once is pretty 'time' consuming
         */         
        if (!class_exists($class)) {
    	   include_once str_replace('_', '/', $class) . '.php';
        }

        if (is_array($params)) {
            switch (count($params)) {
            case 1:
                return new $class(
                    $params[0]
                );
                break;

            case 2:
                return new $class(
                    $params[0],
                    $params[1]
                );
                break;

            case 3:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2]
                );
                break;

            case 4:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3]
                );
                break;

            case 5:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4]
                );
                break;

            case 6:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5]
                );
                break;

            case 7:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6]
                );
                break;

            case 8:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7]
                );
                break;

            case 9:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8]
                );
                break;

            case 10:
                return new $class(
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8],
                    $params[9]
                );
                break;

            default:
                return new $class();
                break;

            }
        } else {
            if ($params == null) {
                return new $class();
            } else {
                return new $class($params);
            }
    	}
    }

    /**
     * Factory method to create layouts.
     *
     * This method is used for easy creation, since using {@link Image_Graph::
     * factory()} does not work with passing newly created objects from
     * Image_Graph::factory() as reference, this is something that is
     * fortunately fixed in PHP5. Also used for 'lazy including', i.e. loading
     * only what is necessary, when it is necessary.
     *
     * Use {@link Image_Graph::horizontal()} or {@link Image_Graph::vertical()}
     * instead for easier access.
     *
     * @param mixed $layout The type of layout, can be either 'Vertical'
     *   or 'Horizontal' (case sensitive)
     * @param Image_Graph_Element $part1 The 1st part of the layout
     * @param Image_Graph_Element $part2 The 2nd part of the layout
     * @param int $percentage The percentage of the layout to split at
     * @return Image_Graph_Layout The newly created layout object
     * @static
     */
    function &layoutFactory($layout, &$part1, &$part2, $percentage = 50)
    {
        include_once "Image/Graph/Layout/$layout.php";
        $class = "Image_Graph_Layout_$layout";
        return new $class($part1, $part2, $percentage);
    }

    /**
     * Factory method to create horizontal layout.
     *
     * See {@link Image_Graph::layoutFactory()}
     *
     * @param Image_Graph_Element $part1 The 1st (left) part of the layout
     * @param Image_Graph_Element $part2 The 2nd (right) part of the layout
     * @param int $percentage The percentage of the layout to split at
     *   (percentage of total height from the left side)
     * @return Image_Graph_Layout The newly created layout object
     * @static
     */
    function &horizontal(&$part1, &$part2, $percentage = 50)
    {
        return Image_Graph::layoutFactory('Horizontal', $part1, $part2, $percentage);
    }

    /**
     * Factory method to create vertical layout.
     *
     * See {@link Image_Graph::layoutFactory()}
     *
     * @param Image_Graph_Element $part1 The 1st (top) part of the layout
     * @param Image_Graph_Element $part2 The 2nd (bottom) part of the layout
     * @param int $percentage The percentage of the layout to split at
     *   (percentage of total width from the top edge)
     * @return Image_Graph_Layout The newly created layout object
     * @static
     */
    function &vertical(&$part1, &$part2, $percentage = 50)
    {
        return Image_Graph::layoutFactory('Vertical', $part1, $part2, $percentage);
    }

    /**
     * The error handling routine set by set_error_handler().
     *
     * This method is used internaly by Image_Graph and PHP as a proxy for {@link
     * Image_Graph::_error()}. 
     *
     * @param string $error_type The type of error being handled.
     * @param string $error_msg The error message being handled.
     * @param string $error_file The file in which the error occurred.
     * @param integer $error_line The line in which the error occurred.
     * @param string $error_context The context in which the error occurred.
     * @access private
     */
    function _default_error_handler($error_type, $error_msg, $error_file, $error_line, $error_context)
    {
        switch( $error_type ) {
        case E_ERROR:
            $level = 'error';
            break;

        case E_USER_ERROR:
            $level = 'user error';
            break;

        case E_WARNING:
            $level = 'warning';
            break;

        case E_USER_WARNING:
            $level = 'user warning';
            break;

        case E_NOTICE:
            $level = 'notice';
            break;

        case E_USER_NOTICE:
            $level = 'user notice';
            break;

        default:
            $level = '(unknown)';
            break;

        }

        $this->_error("PHP $level: $error_msg",
            array(
                'type' => $error_type,
                'file' => $error_file,
                'line' => $error_line,
                'context' => $error_context
            )
        );
    }

    /**
     * Displays the errors on the error stack.
     *
     * Invoking this method cause all errors on the error stack to be displayed
     * on the graph-output, by calling the {@link Image_Graph::_displayError()}
     * method.
     *
     * @access private
     */
    function _displayErrors()
    {
        $stack =& $this->_getErrorStack();
        if ($stack->hasErrors()) {
            $errors = $stack->getErrors();
            if (is_array($errors)) {
                $y = 0;
                foreach ($errors as $error) {
                    $this->_displayError(0, $y, $error);
                    $y += 20;
                }
            }
        }
    }

    /**
     * Display an error from the error stack.
     *
     * This method writes error messages caught from the {@link Image_Graph::
     * _default_error_handler()} if {@Image_Graph::displayErrors()} was invoked,
     * and the error explicitly set by the system using {@link
     * Image_Graph_Common::_error()}.
     *
     * @param int $x The horizontal position of the error message
     * @param int $y The vertical position of the error message
     * @param array $error The error context
     *
     * @access private
     */
    function _displayError($x, $y, $error)
    {
        $driver =& $error['params']['driver'];
        if (is_a($driver, 'Image_Graph_Driver')) {
            $driver->setFont(array('font' => 1, 'color' => 'black'));
            $driver->write($x, $y, $error['message']);
        }
    }

    /**
     * Enable caching of the output.
     *
     * Any change at all to any part of the graph makes it output again. Do
     * *NOT* use caching with plots using {@link Image_Graph_Dataset_Random},
     * since the graph will (always) change. The specified cache directory must
     * exist prior to caching.
     *
     * Requires {@link Cache}.
     *
     * @param string $cacheDir The directory where cached files are put. If
     *   false caching is disabled.
     */
    function cache($cacheDir = 'cache/')
    {
        $this->_cache = $cacheDir;
    }

    /**
     * Outputs this graph using the driver.
     *
     * This causes the graph to make all elements perform their output. Their
     * result is 'written' to the output using the driver, which also performs
     * the actual output, fx. it being to a file or directly to the browser
     * (in the latter case, the driver will also make sure the correct HTTP
     * headers are sent, making the browser handle the output correctly, if
     * supported by it).
     *
     * @param mixed $param The output parameters to pass to the driver
     * @return bool Was the output 'good' (true) or 'bad' (false).
     */
    function done($param = false)
    {
        $this->_reset();
        return $this->_done($param);
    }

    /**
     * Outputs this graph using the driver.
     *
     * This causes the graph to make all elements perform their output. Their
     * result is 'written' to the output using the driver, which also performs
     * the actual output, fx. it being to a file or directly to the browser
     * (in the latter case, the driver will also make sure the correct HTTP
     * headers are sent, making the browser handle the output correctly, if
     * supported by it).
     *
     * @param mixed $param The output parameters to pass to the driver
     * @return bool Was the output 'good' (true) or 'bad' (false).
     * @access private
     */
    function _done($param = false)
    {
        $useCached = false;
        $timeStart = $this->_getMicroTime();

        if ($useCached === false) {
            if ($this->_shadow) {
                $this->setPadding(20);
                $this->_setCoords(
                    $this->_left,
                    $this->_top,
                    $this->_right - 10,
                    $this->_bottom - 10);
            }

            $this->_updateCoords();


            if ($this->_getBackground()) {
                $this->_driver->rectangle(
                    $this->_left,
                    $this->_top,
                    $this->_right,
                    $this->_bottom
                );
            }

            $result = parent::_done();

            if ($this->_displayErrors) {
                $this->_displayErrors();
            }

            $timeEnd = $this->_getMicroTime();

            if (($this->_showTime) || 
                ((isset($param['showtime'])) && ($param['showtime'] === true))
            ) {
                $text = 'Generated in ' .
                    sprintf('%0.3f', $timeEnd - $timeStart) . ' sec';
                $this->write(
                    $this->_right,
                    $this->_bottom,
                    $text,
                    IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_BOTTOM,
                    array('color' => 'red')
                );
            }

        }

        return $this->_driver->done($param);
    }
}

// General to-do's
// TODO Implement a way to display graphs when there are *no* data
// TODO Create bar-chart-type where bar have individual widths (i.e. in the dataset fx).
// TODO Update private tags to protected wherever necessary
// TODO Check performance with recursive/polymorphic methods
?>