<?php

    include 'Image/Graph/Driver.php';
    
    $driver =& Image_Graph_Driver::factory(
        (isset($_GET['driver']) ? $_GET['driver'] : 'png'), 
        array('width' => 605, 'height' => 350)
    );
    
    $gradient = array(
        'type' => 'gradient', 
        'start' => 'yellow', 
        'end' => 'maroon' 
    );

    $space = 10;
    $size = 75;
    
    $driver->setLineColor('black');
    $driver->rectangle(0, 0, $driver->getWidth() - 1, $driver->getHeight() - 1);
    
    for ($i = 0; $i < 7; $i ++) {// there are 7 different gradient types
        $gradient['direction'] = $i + 1;

        $x = $space + ($i * ($size + $space));
        
        $y = $space;
        $driver->setGradientFill($gradient);    
        $driver->rectangle($x, $y, $x + $size, $y + $size);

        $y += $size + $space;
        $driver->setGradientFill($gradient);    
        $driver->ellipse($x + $size / 2, $y + $size / 2, $size / 2, $size / 2);      

        $y += $size + $space;
        $driver->setGradientFill($gradient);    
        $driver->pieSlice($x + $size / 2, $y + $size / 2, $size / 2, $size / 2, 45, 270);
       
        $y += $size + $space;
        $points = array();
        $points[] = array('x' => $x + $size / 3, 'y' => $y);
        $points[] = array('x' => $x + $size, 'y' => $y + $size / 2);
        $points[] = array('x' => $x + $size / 3, 'y' => $y + 3 * $size / 4);
        $points[] = array('x' => $x + $size / 5, 'y' => $y + $size);
        $points[] = array('x' => $x, 'y' => $y + $size / 3);
        $y += $size + $space;
        $driver->setGradientFill($gradient);
        foreach ($points as $point) {
            $driver->polygonAdd($point['x'], $point['y']);
        }
        $driver->polygonEnd(true);    

    }
            
    $driver->done();

?>