<?php
    include 'Image/Graph.php';
    
    // create the graph
    $Graph =& Image_Graph::factory('graph', array(400, 300));
    
    // add a TrueType font
    $Arial =& $Graph->addNew('ttf_font', 'arial.ttf');
    // set the font size to 15 pixels
    $Arial->setSize(11);
    
    $Arial1 =& $Graph->addNew('ttf_font', 'arial.ttf');
    // set the font size to 15 pixels
    $Arial1->setSize(9);
    $Arial1->setAngle(90);
    
    // add a TrueType font
    $Arial2 =& $Graph->addNew('ttf_font', 'arial.ttf');
    // set the font size to 15 pixels
    $Arial2->setSize(9);
    $Arial2->setAngle(270);
    
    // create the plotarea
    $Graph->add(
        Image_Graph::vertical(
            Image_Graph::factory('title', array('German Car Popularity', &$Arial)),
            Image_Graph::horizontal(
                Image_Graph::factory('title', array('Popularity', &$Arial1)),
                Image_Graph::horizontal(
                    Image_Graph::vertical(
                        $Plotarea = Image_Graph::factory('plotarea'),
                        $Legend = Image_Graph::factory('legend'),
                        95
                    ),
                    Image_Graph::factory('title', array('Defects / 1000 units', &$Arial2)),
                    95
                ),
                7
            ),
            5
        )
    );
    
    $Legend->setPlotarea($Plotarea);
    
    // create the dataset
    $Dataset =& Image_Graph::factory('dataset');
    $Dataset->addPoint('Audi', 100);
    $Dataset->addPoint('Mercedes', 41);
    $Dataset->addPoint('Porsche', 78);
    $Dataset->addPoint('BMW', 12);
    //$Dataset =& Image_Graph::factory('random', array(10, 2, 15, true));
    
    $Dataset2 =& Image_Graph::factory('dataset');
    $Dataset2->addPoint('Audi', 10);
    $Dataset2->addPoint('Mercedes', 17);
    $Dataset2->addPoint('Porsche', 12);
    $Dataset2->addPoint('BMW', 21);
    
    $GridY =& $Plotarea->addNew('bar_grid', null, IMAGE_GRAPH_AXIS_Y);
    $GridY->setFillStyle(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'lightgrey')));
    
    // create the plot as bar chart using the dataset
    $Plot =& $Plotarea->addNew('bar', array(&$Dataset, 'normal', 'Popularity'));
    $FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
    $Plot->setFillStyle($FillArray);
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'green', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'blue', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'yellow', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'red', 'white'));
    $FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'orange', 'white'));
    
    $Marker =& $Graph->addNew('Image_Graph_Marker_Array');
    $Marker->addNew('Image_Graph_Marker_Icon', './images/audi.png');
    $Marker->addNew('Image_Graph_Marker_Icon', './images/mercedes.png');
    $Marker->addNew('Image_Graph_Marker_Icon', './images/porsche.png');
    $Marker->addNew('Image_Graph_Marker_Icon', './images/bmw.png');
    
    $Plot->setMarker($Marker);
    
    $Plot2 =& $Plotarea->addNew('line', array(&$Dataset2, 'normal', 'Defects'), IMAGE_GRAPH_AXIS_Y_SECONDARY);
    $Plot2->setLineColor('blue@0.4');
    
    $Marker =& $Graph->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);
    $Plot2->setMarker($Marker);
    
    $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
    $AxisY->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.0f%%'));
    $AxisY->forceMaximum(105);
    
    // output the Graph
    $Graph->done();
?>
