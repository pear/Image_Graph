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
 * This is a visual test case, testing driver fundamental driver functionality.
 * 
 * This is not a test in itself, since it depends on another source part to
 * create the driver. It is merely a common driver test include, to avoid
 * redundant code in every driver test.
 *
 * @package Image_Graph
 * @subpackage Tests
 * @category images
 * @copyright Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 * @version $Id$
 */

$driver->write(5, 5, 'Line', IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP);

$driver->setLineColor('black');
$driver->line(100, 5, 195, 5);
$driver->setLineColor('red');
$driver->line(200, 5, 295, 5);
$driver->setLineColor('green');
$driver->line(300, 5, 395, 5);
$driver->setLineColor('blue');
$driver->line(400, 5, 495, 5);

$driver->setLineColor(array('blue', 'red'));
$driver->line(100, 10, 195, 10);

$driver->setLineColor(array('blue', 'blue', 'transparent'));
$driver->line(200, 10, 295, 10);

$driver->setLineColor('yellow');
$driver->setLineThickness(2);
$driver->line(300, 10, 395, 10);

$driver->setLineColor('red');
$driver->setLineThickness(4);
$driver->line(400, 10, 495, 10);

$driver->setLineColor('black@0.4');
$driver->setLineThickness(4);
$driver->line(100, 15, 220, 15);
$driver->setLineColor('red@0.4');
$driver->setLineThickness(4);
$driver->line(200, 15, 320, 15);
$driver->setLineColor('green@0.4');
$driver->setLineThickness(4);
$driver->line(300, 15, 420, 15);
$driver->setLineColor('blue@0.4');
$driver->setLineThickness(4);
$driver->line(400, 15, 495, 15);

$driver->write(5, 30, 'Rectangle', IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP);

$driver->setLineColor('black');
$driver->rectangle(100, 30, 150, 80);
$driver->setLineColor('red');
$driver->rectangle(155, 30, 205, 80);
$driver->setLineColor('green');
$driver->rectangle(210, 30, 260, 80);
$driver->setLineColor('blue');
$driver->rectangle(265, 30, 315, 80);

$driver->setFillColor('black');
$driver->rectangle(100, 85, 150, 135);
$driver->setLineColor('black');
$driver->setFillColor('red');
$driver->rectangle(155, 85, 205, 135);
$driver->setLineColor('black');
$driver->setFillColor('green');
$driver->rectangle(210, 85, 260, 135);
$driver->setLineColor('black');
$driver->setFillColor('blue');
$driver->rectangle(265, 85, 315, 135);

$driver->setLineColor('red');
$driver->setFillColor('red@0.3');
$driver->rectangle(340, 30, 400, 90);
$driver->setLineColor('green');
$driver->setFillColor('green@0.3');
$driver->rectangle(380, 50, 440, 110);
$driver->setLineColor('blue');
$driver->setFillColor('blue@0.3');
$driver->rectangle(360, 70, 420, 130);

$driver->write(5, 140, 'Circle / Ellipse', IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP);

$driver->setLineColor('black');
$driver->ellipse(130, 170, 30, 30);
$driver->setLineColor('red');
$driver->ellipse(195, 170, 30, 20);
$driver->setLineColor('blue');
$driver->ellipse(250, 170, 20, 30);
$driver->setLineColor('green');
$driver->ellipse(305, 170, 30, 30);

$driver->setFillColor('black');
$driver->ellipse(130, 235, 30, 30);
$driver->setLineColor('black');
$driver->setFillColor('red');
$driver->ellipse(195, 235, 30, 20);
$driver->setLineColor('black');
$driver->setFillColor('blue');
$driver->ellipse(250, 235, 20, 30);
$driver->setLineColor('black');
$driver->setFillColor('green');
$driver->ellipse(305, 235, 30, 30);

$driver->setLineColor('brown');
$driver->setFillColor('brown@0.3');
$driver->ellipse(400, 200, 40, 40);
$driver->setLineColor('orange');
$driver->setFillColor('orange@0.3');
$driver->ellipse(430, 220, 30, 40);
$driver->setLineColor('purple');
$driver->setFillColor('purple@0.3');
$driver->ellipse(390, 230, 40, 20);

$driver->write(5, 270, 'Pie slices', IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP);

$c = 0;
for ($i = 360; $i >= 45; $i -= 45) {
    $driver->setLineColor('black');
    $driver->setFillColor('blue@' . sprintf('%0.1f', ((360 - $i) / 360)));
    $driver->pieSlice(130 + $c * 55, 295, 25, 25, 0, $i);      
    $c++;
}

$driver->write(5, 325, 'Polygon', IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP);

$driver->setLineColor('green');
for ($i = 0; $i < 8; $i++) {
    $driver->polygonAdd(115 + $i * 50, 330);
    $driver->polygonAdd(100 + $i * 50, 325);
    $driver->polygonAdd(125 + $i * 50, 350);
}
$driver->polygonEnd(false);

$driver->setLineColor('purple');
$driver->setFillColor('purple@0.3');
for ($i = 0; $i < 8; $i++) {
    $driver->polygonAdd(100 + $i * 50, 355);
    $driver->polygonAdd(125 + $i * 50, 380 + 2 * $i);
}
$driver->polygonAdd(550, 355);
for ($i = 4; $i >= 0; $i--) {
    $driver->polygonAdd(120 + $i * 100, 430 + $i * 5);
    $driver->polygonAdd(110 + $i * 100, 405 - $i * 5);
}
$driver->polygonEnd();

$driver->write(5, 455, 'Splines', IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP);

$points = array();
$points[] = array(
    'x' => 100, 'y' => 470,
    'p1x' => 120, 'p1y' => 455,
    'p2x' => 150, 'p2y' => 460
);

$points[] = array(
    'x' => 170, 'y' => 490,
    'p1x' => 190, 'p1y' => 500,
    'p2x' => 200, 'p2y' => 510
);

$points[] = array(
    'x' => 210, 'y' => 540,
    'p1x' => 200, 'p1y' => 550,
    'p2x' => 160, 'p2y' => 560
);

$points[] = array(
    'x' => 120, 'y' => 480
);

// draw control points! not directly a driver test!
foreach ($points as $point) {
    if (isset($last)) {
        $driver->setLineColor('gray@0.2');
        $driver->line($last['p2x'], $last['p2y'], $point['x'], $point['y']);
    }

    $driver->setLineColor('red');
    $driver->ellipse($point['x'], $point['y'], 3, 3);
    
    if (isset($point['p1x'])) {   
        $driver->setLineColor('green');
        $driver->ellipse($point['p1x'], $point['p1y'], 2, 2);
        $driver->setLineColor('green');
        $driver->ellipse($point['p2x'], $point['p2y'], 2, 2);
        
        $driver->setLineColor('gray@0.2');
        $driver->line($point['x'], $point['y'], $point['p1x'], $point['p1y']);
        $driver->setLineColor('gray@0.2');
        $driver->line($point['p1x'], $point['p1y'], $point['p2x'], $point['p2y']);
    
        $last  = $point;
    }
}

foreach ($points as $point) {
    if (isset($point['p1x'])) {
        $driver->splineAdd($point['x'], $point['y'], $point['p1x'], $point['p1y'], $point['p2x'], $point['p2y']);
    } else {
        $driver->polygonAdd($point['x'], $point['y']);
    }
}  

$driver->setLineColor('black');
$driver->splineEnd(false);

$points = array();
$points[] = array(
    'x' => 220, 'y' => 470,
    'p1x' => 240, 'p1y' => 455,
    'p2x' => 270, 'p2y' => 460
);

$points[] = array(
    'x' => 240, 'y' => 490,
    'p1x' => 310, 'p1y' => 460,
    'p2x' => 320, 'p2y' => 470
);

$points[] = array(
    'x' => 330, 'y' => 500,
    'p1x' => 320, 'p1y' => 550,
    'p2x' => 280, 'p2y' => 560
);

$points[] = array(
    'x' => 240, 'y' => 520,
    'p1x' => 230, 'p1y' => 490,
    'p2x' => 225, 'p2y' => 490
);

$points[] = array(
    'x' => 220, 'y' => 470
);

unset($last);
// draw control points! not directly a driver test!
foreach ($points as $point) {
    if (isset($last)) {
        $driver->setLineColor('gray@0.2');
        $driver->line($last['p2x'], $last['p2y'], $point['x'], $point['y']);
    }

    $driver->setLineColor('red');
    $driver->ellipse($point['x'], $point['y'], 3, 3);
    
    if (isset($point['p1x'])) {   
        $driver->setLineColor('green');
        $driver->ellipse($point['p1x'], $point['p1y'], 2, 2);
        $driver->setLineColor('green');
        $driver->ellipse($point['p2x'], $point['p2y'], 2, 2);
        
        $driver->setLineColor('gray@0.2');
        $driver->line($point['x'], $point['y'], $point['p1x'], $point['p1y']);
        $driver->setLineColor('gray@0.2');
        $driver->line($point['p1x'], $point['p1y'], $point['p2x'], $point['p2y']);
    
        $last  = $point;
    }
}

foreach ($points as $point) {
    if (isset($point['p1x'])) {
        $driver->splineAdd($point['x'], $point['y'], $point['p1x'], $point['p1y'], $point['p2x'], $point['p2y']);
    } else {
        $driver->polygonAdd($point['x'], $point['y']);
    }
}  

$driver->setLineColor('black');
$driver->setFillColor('red@0.2');
$driver->splineEnd(true);

$driver->write(375, 455, 'Image', IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP);

$driver->overlayImage(445, 455, './pear-icon.png');

$driver->overlayImage(445, 495, './pear-icon.png', 20, 20);

$driver->overlayImage(445, 523, './pear-icon.png', 40, 40);

$driver->done();

?>