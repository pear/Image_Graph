<?php
    include 'Image/Graph.php';
    
    function XtoYear($Value)
    {
        return floor($Value+1998);
    }
    
    function salaries($Value)
    {
        // I wish!
        return exp($Value)+1000;
    }
    
    // create the graph as a 500 x 300 image
    $Graph =& Image_Graph::factory('graph', array(600, 300));      
    
    // create a random dataset to use for demonstrational purposes
    $DataSet =& Image_Graph::factory('Image_Graph_Dataset_Function', array(1, 9, 'salaries', 9));
    
    $DataSet2 =& Image_Graph::factory('dataset');
    $DataSet2->addPoint('CEO', 10);
    $DataSet2->addPoint('TAP', 32); 
    $DataSet2->addPoint('TBF', 13); 
    $DataSet2->addPoint('ABC', 19); 
    $DataSet2->addPoint('QED', 26); 
    
    // create the title font
    $Font =& $Graph->addNew('ttf_font', 'arial.ttf');
    $Font->setSize(11);       
    
    // create the title font
    $VerticalFont =& $Graph->addNew('Image_Graph_Font_Vertical');
    $Font->setSize(11);
    
    // add a plot area in a vertical layout to display a title on top 
    $Graph->add(
        Image_Graph::vertical(
            Image_Graph::factory('title', array('Annual income', &$Font)),
            Image_Graph::horizontal(          
                $Plotarea = Image_Graph::factory('plotarea'),
                $Plotarea2 = Image_Graph::factory('plotarea')
            ),                
            5
        ),
        5
    );
    
    //
    $Grid =& $Plotarea->addNew('bar_grid', null, IMAGE_GRAPH_AXIS_Y);
    $Grid->setFillStyle(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'lightgrey')));       
    
    // add the line plot to the plotarea
    $Plot =& $Plotarea->addNew('line', &$DataSet);
    
    // add coins-icon as marker
    $Plot->setMarker(Image_Graph::factory('Image_Graph_Marker_Icon', './images/coins.png'));
    
    $AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
    $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
    // make x-axis start at 0
    $AxisX->forceMinimum(0);
    
    // make x-axis end at 11
    $AxisX->forceMaximum(9);
    
    // show axis arrows
    $AxisX->showArrow();  
    $AxisY->showArrow();
    
    // create a datapreprocessor to map X-values to years
    $AxisX->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Function', 'XtoYear'));
    $AxisY->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Currency', "US$"));    
    
    $Plot2 =& $Plotarea2->addNew('Image_Graph_Plot_Pie', &$DataSet2);
    $Plotarea2->hideAxis();
    $Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
    $Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'red', 'white'));
    $Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'blue', 'white'));
    $Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'yellow', 'white'));
    $Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'green', 'white'));
    $Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'orange', 'white'));
    $Plot2->setFillStyle($Fill);
    
    $Marker2 =& $Graph->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);
    $Plot2->setMarker($Marker2);
    $Marker2->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.0f%%'));    
    
    // output the graph
    $Graph->done();
?>