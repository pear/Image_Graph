<?php
    // include the libraries 
    include 'Image/Graph.php';
    include 'Image/Graph/Driver.php';

    // create a PNG driver (this is normally default)
    $Driver =& Image_Graph_Driver::factory('png',
        array(
            'width' => 400,
            'height' => 200
        )
    );            
        

    // create the graph
    $Graph =& Image_Graph::factory('graph', &$Driver);

    // add a new font
    $Font =& $Graph->addNew('ttf_font', 'Gothic');
    $Font->setSize(8);

    // use this by default in the graph
    $Graph->setFont($Font);

    // create the layout
    $Graph->add(
        Image_Graph::vertical(
            Image_Graph::factory('title', array('Gradient Filled Step Chart', 11)),
            Image_Graph::horizontal(
                $Plotarea = Image_Graph::factory('plotarea'),
                Image_Graph::factory('title', array('Anybody recognize?', array('size' => 7, 'color' => 'gray@0.6', 'angle' => 270))),
                98
            ),
        5)
    );
    
    // add a grid
    $Grid =& $Plotarea->addNew('line_grid', array(), IMAGE_GRAPH_AXIS_Y);
    $Grid->setLineColor('white@0.4');           
    
    // create dataset
    $Dataset =& Image_Graph::factory('dataset');
    $Dataset->addPoint(1, 20);
    $Dataset->addPoint(2, 10);
    $Dataset->addPoint(3, 35);
    $Dataset->addPoint(4, 5);
    $Dataset->addPoint(5, 18);
    $Dataset->addPoint(6, 33);
    
    // add a step chart
    $Plot =& $Plotarea->addNew('step', &$Dataset);
      
    // fill the plot with a vertical gradient fill
    $Fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'darkgreen', 'white'));
    $Plot->setFillStyle($Fill);

    // fill the plotarea with a vertical gradient fill
    $Fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'yellow', 'darkred'));
    $Plotarea->setFillStyle($Fill);

    // configure the y-axis options    
    $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
    $AxisY->forceMaximum(40);
    $AxisY->setLabelInterval(10);
    
    // set some graph options
    $Graph->setBackgroundColor('green@0.2');
    $Graph->setBorderColor('black');
    $Graph->setPadding(10);    
    
    // make the plot have a black border
    $Plot->setBorderColor('black');      
                    
    // output the graph
    $Graph->done();
?>