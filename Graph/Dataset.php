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
 * @subpackage Dataset     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Data set used to represent a data collection to plot in a chart
 * @abstract
 */
class Image_Graph_Dataset 
{

    /**
     * The pointer of the data set
     * @var int
     * @access private
     */
    var $_posX = 0;

    /**
     * The minimum X value of the dataset
     * @var int
     * @access private
     */
    var $_minimumX = 0;

    /**
     * The maximum X value of the dataset
     * @var int
     * @access private
     */
    var $_maximumX = 0;

    /**
     * The minimum Y value of the dataset
     * @var int
     * @access private
     */
    var $_minimumY = 0;

    /**
     * The maximum Y value of the dataset
     * @var int
     * @access private
     */
    var $_maximumY = 0;

    /**
     * The number of points in the dataset
     * @var int
     * @access private
     */
    var $_count = 0;

    /**
     * The name of the dataset, used for legending
     * @var string
     * @access private
     */
    var $_name = '';

    /**
     * Image_Graph_Dataset [Constructor]
	 */
    function &Image_Graph_Dataset()
    {
    }

    /**
     * Sets the name of the data set, used for legending
     * @param string $name The name of the dataset
     */
    function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Add a point to the dataset
     * @param int $x The X value to add
     * @param int $y The Y value to add, can be omited
     * @param var $ID The ID of the point
	 */
    function addPoint($x, $y = false, $ID = false)
    {
        if ($this->_count) {
            $this->_minimumX = min($x, $this->_minimumX);
            $this->_maximumX = max($x, $this->_maximumX);
            $this->_minimumY = min($y, $this->_minimumY);
            $this->_maximumY = max($y, $this->_maximumY);
        } else {
            $this->_minimumX = $x;
            $this->_maximumX = $x;
            $this->_minimumY = $y;
            $this->_maximumY = $y;
        }

        $this->_count++;
    }

    /**
     * The number of values in the dataset
     * @return int The number of values in the dataset     
	 */
    function count()
    {
        return $this->_count;
    }

    /**
     * Gets a X point from the dataset
     * @param var $x The variable to return an X value from, fx in a vector function data set
     * @return var The X value of the variable
     * @access private
	 */
    function _getPointX($x)
    {
        return $x;
    }

    /**
     * Gets a Y point from the dataset
     * @param var $x The variable to return an Y value from, fx in a vector function data set
     * @return var The Y value of the variable
     * @access private
	 */
    function _getPointY($x)
    {
        return $y;
    }

    /**
     * Gets a ID from the dataset
     * @param var $x The variable to return an Y value from, fx in a vector function data set
     * @return var The ID value of the variable
     * @access private
	 */
    function _getPointID($x)
    {
        return false;
    }

    /**
     * The minimum X value
     * @return var The minimum X value
	 */
    function minimumX()
    {
        return $this->_minimumX;
    }

    /**
     * The maximum X value
     * @return var The maximum X value
	 */
    function maximumX()
    {
        return $this->_maximumX;
    }

    /**
     * The minimum Y value
     * @return var The minimum Y value
	 */
    function minimumY()
    {
        return $this->_minimumY;
    }

    /**
     * The maximum Y value
     * @return var The maximum Y value
	 */
    function maximumY()
    {
        return $this->_maximumY;
    }

    /**
     * The minimum X value
     * @param var $value The minimum X value
     * @access private
	 */
    function _setMinimumX($value)
    {
        $this->_minimumX = $value;
    }

    /**
     * The maximum X value
     * @param var $value The maximum X value
     * @access private
	 */
    function _setMaximumX($value)
    {
        $this->_maximumX = $value;
    }

    /**
     * The minimum Y value
     * @param var $value The minimum X value
     * @access private
	 */
    function _setMinimumY($value)
    {
        $this->_minimumY = $value;
    }

    /**
     * The maximum Y value
     * @param var $value The maximum X value
     * @access private
	 */
    function _setMaximumY($value)
    {
        $this->_maximumY = $value;
    }

    /**
     * The interval between 2 adjacent X values
     * @return var The interval
     * @access private
	 */
    function _stepX()
    {
        return 1;
    }

    /**
     * The interval between 2 adjacent Y values
     * @return var The interval
     * @access private
	 */
    function _stepY()
    {
        return 1;
    }

    /**
     * Reset the intertal dataset pointer
     * @return var The first X value
     * @access private
	 */
    function _reset()
    {
        $this->_posX = $this->_minimumX;
        return $this->_posX;
    }

    /**
     * Get a point close to the internal pointer
     * @param int Step Number of points next to the internal pointer, negative Step is towards lower X values, positive towards higher X values
     * @return array The point
     * @access private
	 */
    function _nearby($step = 0)
    {
        $x = $this->_getPointX($this->_posX + $this->_stepX() * $step);
        $y = $this->_getPointY($this->_posX + $this->_stepX() * $step);
        $ID = $this->_getPointID($this->_posX + $this->_stepX() * $step);
        if (($x === false) or ($y === false)) {
            return false;
        } else {
            return array ('X' => $x, 'Y' => $y, 'ID' => $ID);
        }
    }

    /**
     * Get the next point the internal pointer refers to and advance the pointer
     * @return array The next point
     * @access private
	 */
    function _next()
    {
        if ($this->_posX > $this->_maximumX) {
            return false;
        }

        $x = $this->_getPointX($this->_posX);
        $y = $this->_getPointY($this->_posX);
        $ID = $this->_getPointID($this->_posX);
        $this->_posX += $this->_stepX();

        return array ('X' => $x, 'Y' => $y, 'ID' => $ID);
    }

    /**
     * Get the average of the dataset's Y points
     * @return var The Y-average across the dataset
     * @access private
     */
    function _averageY()
    {
        $posX = $this->_minimumX;
        $count = 0;
        $total = 0;
        while ($posX < $this->_maximumX) {
            $count ++;
            $total += $this->_getPointY($posX);
            $posX += $this->_stepX();
        }

        if ($count != 0) {
            return $total / $count;
        } else {
            return false;
        }
    }        

}
?>