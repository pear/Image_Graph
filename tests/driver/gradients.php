<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing driver support for gradient fillings.
 * 
 * Passing a GET parameter with the driver type switches drivers so that other
 * driver types can be tested, i.e.:
 * 
 * http://.../gradients.php?driver=(png|jpg|gif|wbmp|svg|pdflib)
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Tests
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Image_Graph
 */

include 'Image/Graph/Driver.php';

$driver =& Image_Graph_Driver::factory(
    (isset($_GET['driver']) ? $_GET['driver'] : 'png'), 
    array('width' => 605, 'height' => 350)
);

$gradient = array(
    'type' => 'gradient', 
    'start' => 'yellow', 
    'end' => 'maroon' 
);

$space = 10;
$size = 75;

$driver->setLineColor('black');
$driver->rectangle(0, 0, $driver->getWidth() - 1, $driver->getHeight() - 1);

for ($i = 0; $i < 7; $i ++) {// there are 7 different gradient types
    $gradient['direction'] = $i + 1;

    $x = $space + ($i * ($size + $space));
    
    $y = $space;
    $driver->setGradientFill($gradient);    
    $driver->rectangle($x, $y, $x + $size, $y + $size);

    $y += $size + $space;
    $driver->setGradientFill($gradient);    
    $driver->ellipse($x + $size / 2, $y + $size / 2, $size / 2, $size / 2);      

    $y += $size + $space;
    $driver->setGradientFill($gradient);    
    $driver->pieSlice($x + $size / 2, $y + $size / 2, $size / 2, $size / 2, 45, 270);
   
    $y += $size + $space;
    $points = array();
    $points[] = array('x' => $x + $size / 3, 'y' => $y);
    $points[] = array('x' => $x + $size, 'y' => $y + $size / 2);
    $points[] = array('x' => $x + $size / 3, 'y' => $y + 3 * $size / 4);
    $points[] = array('x' => $x + $size / 5, 'y' => $y + $size);
    $points[] = array('x' => $x, 'y' => $y + $size / 3);
    $y += $size + $space;
    $driver->setGradientFill($gradient);
    foreach ($points as $point) {
        $driver->polygonAdd($point['x'], $point['y']);
    }
    $driver->polygonEnd(true);    

}
        
$driver->done();

?>