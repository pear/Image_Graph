<?php
	include('Image/Graph.php');
	
	// create the graph
	$Graph =& Image_Graph::factory('graph', array(400, 300));

    // add a TrueType font
    $Arial =& $Graph->addNew('ttf_font', 'arial.ttf');
    // set the font size to 11 pixels
    $Arial->setSize(11);
    // add a title using the created font    
		
	// create the plotarea
	$Graph->add(
        Image_Graph::vertical(
            Image_Graph::factory('title', array('Meat Export', &$Arial)),
            $Plotarea = Image_Graph::factory('plotarea'),
            5            
        )
    );
			
	// create the 1st dataset
	$Dataset1 =& Image_Graph::factory('dataset');
	$Dataset1->addPoint('Beef', rand(1, 10));
	$Dataset1->addPoint('Pork', rand(1, 10));
	$Dataset1->addPoint('Poultry', rand(1, 10));
	$Dataset1->addPoint('Camels', rand(1, 10));
	$Dataset1->addPoint('Other', rand(1, 10));
	// create the 1st plot as smoothed area chart using the 1st dataset
	$Plot1 =& $Plotarea->addNew('Image_Graph_Plot_Pie', &$Dataset1);
	$Plotarea->hideAxis();
	
	// create a Y data value marker
	$Marker =& $Plot1->addNew('Image_Graph_Marker_Value', VALUE_X);
	// create a pin-point marker type
	$PointingMarker =& $Plot1->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
	// and use the marker on the 1st plot
	$Plot1->setMarker($PointingMarker);	
	// format value marker labels as percentage values
	
	$Plot1->Radius = 2;
	
	$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
	$Plot1->setFillStyle($FillArray);
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'green', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'blue', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'yellow', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'red', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'orange', 'white'));
		
	// output the Graph
	$Graph->done();
?>
