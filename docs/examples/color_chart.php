<?php
/**
 * Not a real usage example for Image_Graph.
 * 
 * Main purpose: 
 * Color chart of named colors
 * 
 * Other: 
 * Using drivers "outside" Image_Graph
 * 
 * $Id$
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
$file = file('./data/colors.txt');

require 'Image/Graph/Driver.php';
require 'Image/Graph/Color.php';
require 'Image/Graph/Constants.php';

$Driver =& Image_Graph_Driver::factory('gd', array('width' => 600, 'height' => 1200));

$i = 0;
$cols = 10;
$Width = ($Driver->getWidth() / $cols);
$rows = count($file) / $cols;
$rows = floor($rows) + ($rows > floor($rows) ? 1 : 0);
$Height = ($Driver->getHeight() / $rows);
while (list($id, $color) = each($file)) {
    $color = trim($color);
    $x = ($i % $cols) * $Width + $Width / 2;
    $y = floor($i / $cols) * $Height;
    $Driver->setLineColor('black');
    $Driver->setFillColor($color);        
    $Driver->rectangle($x - $Width / 4, $y, $x + $Width / 4, $y + $Height / 3);
    $Driver->write($x, $y + $Height / 3 + 3, $color, IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_TOP);
    
    $rgbColor = Image_Graph_Color::color2RGB($color);
    $rgbs = 'RGB: ';
    unset($rgbColor[3]); 
    while (list($id, $rgb) = each($rgbColor)) {
        $rgbs .= ($rgb < 0x10 ? '0' : '') . dechex($rgb);
    }       
    $Driver->write($x, $y + $Height / 3 + 13, $rgbs, IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_TOP);
    $i++;
}

$Driver->done();      
?>