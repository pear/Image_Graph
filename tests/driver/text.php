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
 * This is a visual test case, testing driver support for text output.
 * 
 * Passing a GET parameter with the driver type switches drivers so that other
 * driver types can be tested, i.e.:
 * 
 * http://.../gradients.php?driver=(png|jpg|gif|wbmp|svg|pdflib)
 *
 * @package Image_Graph
 * @subpackage Tests
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */
 
include 'Image/Graph/Driver.php';

$driver =& Image_Graph_Driver::factory(
    (isset($_GET['driver']) ? $_GET['driver'] : 'png'), 
    array('width' => 300, 'height' => 200)
);
   
$driver->setLineColor('black');
$driver->rectangle(0, 0, $driver->getWidth() - 1, $driver->getHeight() - 1);

$driver->setLineColor('lightgrey@0.3');
$driver->rectangle(10, 10, 290, 190);
$driver->setLineColor('lightgrey@0.3');
$driver->line(10, 100, 290, 100);
$driver->setLineColor('lightgrey@0.3');
$driver->rectangle(150, 10, 150, 190);

$font = array('ttf' => 'Sans Serif', 'size' => 12, 'angle' => 0, 'vertical' => false);

$align = array(
    array(
        IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP,
        IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_TOP,
        IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_TOP
    ),
    array(
        IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_CENTER_Y,
        IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_CENTER_Y,
        IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_CENTER_Y
    ),
    array(
        IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_BOTTOM,
        IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_BOTTOM,
        IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_BOTTOM
    )
);

for ($row = 0; $row < 3; $row++) {
    for ($col = 0; $col < 3; $col++) {
        $x = 10 + $col * 140;
        $y = 10 + $row * 90;
        
        switch ($row) {
            case 0: 
                $text = 'Top'; 
                break;
            case 1: 
                $text = 'Center'; 
                break;
            case 2: 
                $text = 'Bottom'; 
                break;
        }            
        switch ($col) {
            case 0: 
                $text .= 'Left'; 
                break;
            case 1:
                if ($row !== 1) { 
                    $text .= 'Center';
                } 
                break;
            case 2: 
                $text .= 'Right'; 
                break;
        }
        
        $driver->setLineColor('red');
        $driver->line($x - 5, $y, $x + 5, $y); 
        $driver->setLineColor('red');
        $driver->line($x, $y - 5, $x, $y + 5);

        $driver->setFont($font);
        $driver->write($x, $y, $text, $align[$row][$col]);
    }
} 
        
$driver->done();

?>