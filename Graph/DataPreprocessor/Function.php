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
 * @subpackage DataPreprocessor     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/DataPreprocessor.php
 */
require_once 'Image/Graph/DataPreprocessor.php';

/**
 * Formatting a value using a userdefined function.
 * Use this method to convert/format a value to a 'displayable' lable using a (perhaps)
 * more complex function. An example could be (not very applicable though) if one would
 * need for values to be displayed on the reverse order, i.e. 1234 would be displayed as 
 * 4321, then this method can solve this by creating the function that converts the value
 * and use the FunctionData datapreprocessor to make Image_Graph use this function. 
 */
class Image_Graph_DataPreprocessor_Function extends Image_Graph_DataPreprocessor 
{

    /**
     * The name of the PHP function
     * @var string
     * @access private
     */
    var $_dataFunction;

    /**
     * Create a FunctionData preprocessor
     * @param string $function The name of the PHP function to use as a preprocessor, this function must take a single parameter
     * and return a formatted version of this parameter 
     */
    function &Image_Graph_DataPreprocessor_Function($function)
    {
        parent::Image_Graph_DataPreprocessor();
        $this->_dataFunction = $function;
    }

    /**
     * Process the value
     * @param var $value The value to process/format
     * @return string The processed value
     * @access private
     */
    function _process($value)
    {
        $function = $this->_dataFunction;
        return $function ($value);
    }

}
?>