<?php
    include 'Image/Graph/Driver.php';
    
    $driver =& Image_Graph_Driver::factory(
        (isset($_GET['driver']) ? $_GET['driver'] : 'png'), 
        array('width' => 300, 'height' => 200)
    );
       
    $driver->setLineColor('black');
    $driver->rectangle(0, 0, $driver->getWidth() - 1, $driver->getHeight() - 1);

    $driver->setLineColor('lightgrey@0.3');
    $driver->rectangle(10, 10, 290, 190);
    $driver->setLineColor('lightgrey@0.3');
    $driver->line(10, 100, 290, 100);
    $driver->setLineColor('lightgrey@0.3');
    $driver->rectangle(150, 10, 150, 190);
    
    $font = array('ttf' => 'Sans Serif', 'size' => 12, 'angle' => 0, 'vertical' => false);
    
    $align = array(
        array(
            IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_TOP,
            IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_TOP,
            IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_TOP
        ),
        array(
            IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_CENTER_Y,
            IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_CENTER_Y,
            IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_CENTER_Y
        ),
        array(
            IMAGE_GRAPH_ALIGN_LEFT + IMAGE_GRAPH_ALIGN_BOTTOM,
            IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_BOTTOM,
            IMAGE_GRAPH_ALIGN_RIGHT + IMAGE_GRAPH_ALIGN_BOTTOM
        )
    );
    
    for ($row = 0; $row < 3; $row++) {
        for ($col = 0; $col < 3; $col++) {
            $x = 10 + $col * 140;
            $y = 10 + $row * 90;
            
            switch ($row) {
                case 0: 
                    $text = 'Top'; 
                    break;
                case 1: 
                    $text = 'Center'; 
                    break;
                case 2: 
                    $text = 'Bottom'; 
                    break;
            }            
            switch ($col) {
                case 0: 
                    $text .= 'Left'; 
                    break;
                case 1:
                    if ($row !== 1) { 
                        $text .= 'Center';
                    } 
                    break;
                case 2: 
                    $text .= 'Right'; 
                    break;
            }
            
            $driver->setLineColor('red');
            $driver->line($x - 5, $y, $x + 5, $y); 
            $driver->setLineColor('red');
            $driver->line($x, $y - 5, $x, $y + 5);

            $driver->setFont($font);
            $driver->write($x, $y, $text, $align[$row][$col]);
        }
    } 
            
    $driver->done();

?>