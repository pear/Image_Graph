<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Negative values
 * (more a test really)
 * 
 * Other: 
 * None specific
 * 
 * $Id$
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */


include 'Image/Graph.php';
require 'Image/Graph/Driver.php';

$Driver =& Image_Graph_Driver::factory('png', array('width' => 400, 'height' => 300, 'antialias' => true));      

// create the graph
$Graph =& Image_Graph::factory('graph', &$Driver);
 // add a TrueType font
$Font =& $Graph->addNew('ttf_font', 'Gothic');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        $Plotarea = Image_Graph::factory('plotarea'),
        $Legend = Image_Graph::factory('legend'),
        90
    )
);
$Legend->setPlotarea($Plotarea);

$Dataset =& Image_Graph::factory('dataset');
$Dataset->addPoint('Jan', 1);
$Dataset->addPoint('Feb', 2);
$Dataset->addPoint('Mar', -2);
$Dataset->addPoint('Apr', 4);
$Dataset->addPoint('May', 3);
$Dataset->addPoint('Jun', 6);
$Dataset->addPoint('Jul', -1);
$Dataset->addPoint('Aug', -3);
$Dataset->addPoint('Sep', 2);
$Dataset->addPoint('Oct', 3);
$Dataset->addPoint('Nov', 1);
$Dataset->addPoint('Dec', 4);    

$Dataset2 =& Image_Graph::factory('dataset');
$Dataset2->addPoint('Jan', 3);
$Dataset2->addPoint('Feb', 4);
$Dataset2->addPoint('Mar', 1);
$Dataset2->addPoint('Apr', -2);
$Dataset2->addPoint('May', 3);
$Dataset2->addPoint('Jul', 1);

$Plot =& $Plotarea->addNew('area', &$Dataset);
$Plot->setFillColor('red@0.2');

$Plot2 =& $Plotarea->addNew('bar', &$Dataset2);
$Plot2->setFillColor('blue@0.2');

// output the Graph
$Graph->done();
?>