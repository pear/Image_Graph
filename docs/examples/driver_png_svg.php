<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate switching between drivers
 * 
 * Other: 
 * PNG and SVG driver usage
 * 
 * $Id$
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
include 'Image/Graph.php';
include 'Image/Graph/Driver.php';

// create a new GD driver
$Driver =& Image_Graph_Driver::factory('gd',
    array(
        'filename' => './images/modify.jpg',
        'left' => 400,
        'top' => 100,
        'width' => 500,
        'height' => 500,
        'transparent' => true            
        )
    ); 
 
    // create the graph using the GD driver
$Graph =& Image_Graph::factory('graph', &$Driver);

// create a simple graph
$Graph->add(
    Image_Graph::vertical(
        $Plotarea = Image_Graph::factory('plotarea'),
        $Legend = Image_Graph::factory('legend'),
        90
    )
);    
$Legend->setPlotarea($Plotarea);        
$Dataset =& Image_Graph::factory('random', array(10, 2, 15, true));       
$Plot =& $Plotarea->addNew('area', &$Dataset);
$Plot->setLineColor('gray');
$Plot->setFillColor('blue@0.2');

// add a TrueType font
$Font =& $Graph->addNew('ttf_font', 'Gothic');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);
$Graph->addNew('title', array('Simple Area Chart Sample', 12));
    
// output the graph using the GD driver
$Graph->done(array('filename' => './driversample.png'));

// create a new SVG driver
$Driver =& Image_Graph_Driver::factory('svg',
    array(
        'width' => 600,
        'height' => 400
    )
); 
// make the graph use this now instead
$Graph->setDriver($Driver);

// 're'-output the graph, but not using the SVG driver
$Graph->done(array('filename' => './driversample.svg'));
?>
