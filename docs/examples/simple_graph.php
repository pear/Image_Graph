<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate how to use simple graph
 * 
 * Other: 
 * None specific
 * 
 * $Id$
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

include('Image/Graph/Simple.php');    

$Data = array(
    'Dogs' => 3,
    'Cats' => 1,
    'Parrots' => 4,
    'Mice' => 5
);

// create the graph
$Graph =& Image_Graph_Simple::factory( 
    400, 
    300,
    'Image_Graph_Plot_Smoothed_Area',
    $Data,
    'Simple Graph Example',
    'gray',
    'blue@0.2',
    'Gothic'
);
			 
// output the Graph
$Graph->done();
?>
