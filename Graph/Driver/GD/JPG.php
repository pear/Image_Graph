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
 * Class for handling output in JPEG format.
 *
 * @package Image_Graph
 * @subpackage Driver
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 * @since 0.3.0dev2
 */

/**
 * Include file Image/Graph/Driver/GD.php
 */
require_once 'Image/Graph/Driver/GD.php';

/**
 * GD Driver class.
 *
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @package Image_Graph
 * @subpackage Driver
 * @since 0.3.0dev2
 * @abstract
 */
class Image_Graph_Driver_GD_JPG extends Image_Graph_Driver_GD
{
    /**
     * Create the JPEG driver
     *
     * @param array $param Parameter array
     */
    function &Image_Graph_Driver_GD_JPG($param)
    {
        parent::Image_Graph_Driver_GD($param);

        $this->rectangle(
            $this->_left,
            $this->_top,
            $this->_left + $this->_width - 1,
            $this->_top + $this->_height - 1,
            'white',
            'transparent'
        );
    }

    /**
     * Output the result of the driver
     *
     * @param array $param Parameter array
     * @abstract
     */
    function done($param = false)
    {
        parent::done($param);
        if (($param === false) || (!isset($param['filename']))) {
            header('Content-type: image/jpg');
            header('Content-Disposition: inline; filename = \"'. basename($_SERVER['PHP_SELF'], '.php') . '.jpg\"');
            ImageJPEG($this->_canvas);
        } elseif (isset($param['filename'])) {
            ImageJPEG($this->_canvas, $param['filename']);
        }
    }

}

?>