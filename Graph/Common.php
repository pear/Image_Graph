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
 * Image_Graph - PEAR PHP OO Graph Rendering Utility.
 * @package Image_Graph
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */

require_once 'Image/Color.php';

if (!function_exists('is_a')) {

    /**
     * Check if an object is of a given class, this function is available as of PHP 4.2.0, so if it exist it will not be declared
     * @link http://www.php.net/manual/en/function.is-a.php PHP.net Online Manual for function is_a()
     * @param object $object The object to check class for
     * @param string $class_name The name of the class to check the object for
     * @return bool Returns TRUE if the object is of this class or has this class as one of its parents
     */
    function is_a($object, $class_name)
    {
        if (empty ($object)) {
            return false;
        }
        $object = is_object($object) ? get_class($object) : $object;
        if (strtolower($object) == strtolower($class_name)) {
            return true;
        }
        return is_a(get_parent_class($object), $class_name);
    }
}

/**
 * Check which version of GD is installed
 * @return int 0 if GD isn't installed, 1 if GD 1.x is installed and 2 if GD 2.x is installed
 */
function Image_Graph_gd_version()
{
    if (function_exists('gd_info')) {
        $info = gd_info();
        $version = $info['GD Version'];
    } else {
        ob_start();
        phpinfo(8);
        $php_info = ob_get_contents();
        ob_end_clean();

        if (ereg("<td[^>]*>GD Version *<\/td><td[^>]*>([^<]*)<\/td>", $php_info, $result)) {
            $version = $result[1];
        }
    }

    if ($version) {
        define('GD_VERSION', $version);
    }

    if (ereg('1\.[0-9]{1,2}', $version)) {
        return 1;
    }
    elseif (ereg('2\.[0-9]{1,2}', $version)) {
        return 2;
    } else {
        return 0;
    }
}

/**
 * The ultimate ancestor of all Image_Graph classes.
 * This class contains common functionality needed by all Image_Graph classes.
 * @abstract
 */
class Image_Graph_Common
{

    /** The parent container of the current Image_Graph object
     * @var Image_Graph_Common
     * @access private
     */
    var $_parent = null;

    /** The sub-elements of the current Image_Graph container object
     * @var array
     * @access private
     */
    var $_elements;
    
    /**
     * Constructor
     */
    function &Image_Graph_Common()
    {
    }    

    /**
     * Sets the parent. The parent chain should ultimately be a GraPHP object
     * @see Image_Graph_Common
     * @param Image_Graph_Common $parent The parent
     * @access private
     */
    function _setParent(& $parent)
    {
        $this->_parent = & $parent;
    }

    /**
     * Adds an element to the objects element list, the new Image_Graph_elements parent is automatically set
     * @param Image_Graph_Common $element The new Image_Graph_element
     * @return Image_Graph_Common The new Image_Graph_element
     */
    function &add(& $element)
    {
        if (!is_a($element, 'Image_Graph_Font')) {
            $this->_elements[] = &$element;
        }
        $element->_setParent($this);
        return $element;
    }

    /**
     * Creates an object from the class specified and adds it to the objects element list.
     * If only one parameter is required for the constructor of the class simply pass this
     * parameter as the $params parameter, unless the parameter is an array or a reference
     * to a value, in that case you must 'enclose' the parameter in an array. Similar if
     * the constructor takes more than one parameter specify the parameters in an array.
     * See {@see Image_Graph::factory()}
     * @param string $class The class for the object
     * @param mixed $params The paramaters to pass to the constructor
     * @return Image_Graph_Common The new Image_Graph_element
     */    
    function &addNew($class, $params = null, $additional = false) 
    {
        require_once 'Image/Graph.php';
        $element =& Image_Graph::factory($class, $params);
        if ($additional === false) {
            return $this->add($element);
        } else {
            return $this->add($element, $additional);
        }            
    }
    
    /**
     * Get the error handling stack
     * @return PEAR_ErrorStack The package specific error handling stack
     * @access private
     */
    function &_getErrorStack() {
        $stack =& PEAR_ErrorStack::singleton('Image_Graph');
        return $stack;
    }        

    /**
     * Shows an error message box on the canvas
     * @param string $text The error text
     * @param array $params An array containing error specific details
     * @param int $error_code Error code   
     * @access private
     */
    function _error($text, $params = false, $error_code = IMAGE_GRAPH_ERROR_GENERIC)
    {
        $stack =& $this->_getErrorStack();
        $canvas =& $this->_canvas();
        if (!is_array($params)) {
            $params = array('image' => &$canvas, 'object' => &$this);
        } else {
            $params['canvas'] =& $canvas;
            $params['object'] =& $this;
        }
        $stack->push($error_code, 'error', $params, $text);        
    }

    /**
     * Returns the graph's canvas. Penultimately it should call Canvas() from the GraPHP object
     * @see Image_Graph
     * @return resource A GD image representing the graph's canvas
     * @access private
     */
    function &_canvas()
    {
        if ($this->_parent) {
            $canvas =& $this->_parent->_canvas();
            if (is_resource($canvas)) {
                return $canvas;
            } else {
                return false;                
            }
        } else {
            return false;
        }
    }

    /**
     * Returns the total width of the graph's canvas
     * @see Image_Graph
     * @return int The width of the canvas
     * @access private
     */
    function _graphWidth()
    {
        if ($this->_parent) {
            return $this->_parent->_graphWidth();
        } else {
            return 0;
        }
    }

    /**
     * Returns the total height of the graph's canvas
     * @see Image_Graph
     * @return int The height of the canvas
     * @access private
     */
    function _graphHeight()
    {
        if ($this->_parent) {
            return $this->_parent->_graphHeight();
        } else {
            return 0;
        }
    }

    /**
     * Get the color index for the RGB color
     * @param int $color The color
     * @return int The GD image index of the color
     * @access private
     */
    function _color($color = false)
    {
        if ($color === false) {
            return ImageColorTransparent($this->_canvas());
        } else {
            $canvas = $this->_canvas();
            return Image_Graph_Color::allocateColor($canvas, $color);
        }
    }
    /**
     * Causes the object to update all sub elements coordinates (Image_Graph_Common, does not itself have coordinates, this is basically an abstract method)
     * @access private
     */
    function _updateCoords()
    {
        if (is_array($this->_elements)) {
            reset($this->_elements);

            $keys = array_keys($this->_elements);
            while (list ($ID, $key) = each($keys)) {
                $this->_elements[$key]->_updateCoords();
            }
        }
    }

    /**
     * The last method to call. Calling Done causes output to the canvas. All sub elements done() method
     * will be invoked
     * @access private
     */
    function _done()
    {
        if (is_array($this->_elements)) {
            reset($this->_elements);

            $keys = array_keys($this->_elements);
            while (list ($ID, $key) = each($keys)) {
                $this->_elements[$key]->_done();
            }
        }
    }

}

?>