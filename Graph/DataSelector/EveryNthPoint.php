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
 * @subpackage DataSelector     
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */ 

/**
 * Include file Image/Graph/DataSelector.php
 */
require_once 'Image/Graph/DataSelector.php';

/**
 * Filter out all points except every Nth point.
 * Use this dataselector if you have a large number of datapoints, but only want to
 * show markers for a small number of them, say every 10th.
 */
class Image_Graph_DataSelector_EveryNthPoint extends Image_Graph_DataSelector 
{

    /**
     * The number of points checked
     * @var int
     * @access private
     */
    var $_pointNum = 0;

    /**
     * The number of points between every 'show', default: 10	 
     * @var int
     * @access private
     */
    var $_pointInterval = 10;

    /**
     * EvertNthPoint [Constructor]
     * @param int $pointInterval The number of points between every 'show', default: 10
	 */
    function &Image_Graph_DataSelector_EveryNthpoint($pointInterval = 10)
    {
        parent::Image_Graph_DataSelector();
        $this->_pointInterval = $pointInterval;
    }

    /**
     * Check if a specified value should be 'selected', ie shown as a marker
     * @param array $values The values to check
     * @return bool True if the Values should cause a marker to be shown, false if not
     * @access private     
	 */
    function _select($values)
    {
        $oldPointNum = $this->_pointNum;
        $this->_pointNum++;
        return (($oldPointNum % $this->_pointInterval) == 0);
    }

}

?>