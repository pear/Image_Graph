<?
// $Id$
/**
* Example for using the Image_Graph-class
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

    require_once("Image/Graph.php");

    $graph = new Image_Graph (400, 200);
    $graph->setBackgroundColor (array(0x59, 0x59, 0x59));
    $graph->setSpaceFromBorder (5); // 5 pixels from each border
    $graph->setDefaultFontOptions (array("fontPath" => "", "fontFile" => "arial", "antiAliasing" => true, "fontSize" => 10, "color" => array(0,0,0) ));
    $graph->diagramTitle->setColor (array(0xBF,0xBF,0xBF));
    $graph->diagramTitle->setSpacer (array("bottom" => 10));
    $graph->axeX->title->setSpacer (array("top" => 10, "bottom" => 0));

    $graph->diagramTitle->setText ("Downloads June 2003");
    $graph->axeX->title->setText ("Days");
    $graph->axeY0->setNumberformat ("%.1f MB");
    $graph->axeY1->setNumberformat ("%.0f files");

    $graph->axeY0->title->setText ("Traffic");
    $graph->axeY1->title->setText ("Number of downloads");

    $graph->axeY0->setNumbercolor (array(0xCC,0x29,0x29));
    $graph->axeY1->setNumbercolor (array(0x29,0xCC,0x29));

    $graph->axeY0->title->setColor (array(0xCC,0x29,0x29));
    $graph->axeY1->title->setColor (array(0x29,0xCC,0x29));

    $graph->axeY0->title->setFontOptions (array("fontSize" => 8));
    $graph->axeY1->title->setFontOptions (array("fontSize" => 8));

    $graph->setAxesColor(array(0x40, 0x40, 0xFF));

    $data    = array( 15.8,
                      37.2,
                      50.0
                    );
    $graph->setDataDefaultColor(array(0xCC,0x29,0x29));
    $graph->addData($data, "line", array("axeId" => 0));
    $data[2] = 97;
    $graph->addData($data, "bar",  array("axeId" => 1, "color" => array(0xBF,0xBF,0x30)));
    $dataObj = &$graph->addData($data, "line", array("axeId" => 1, "color" => array(0x29,0xCC,0x29)));
    $marker =& $dataObj->setDataMarker("diamond", array("color" => array(0xBF,0xBF,0xBF)));
    $marker->setSize(7);
    
    $graph->axeX->setLabels(array("Month 1", "Month 2", "Month 3"));

    $graph->axeY1->setTicksAutoSteps(10);

/* these function-calls might be useful to try out */
/* but we don't need them in our example since we use the auto-values */
//    $graph->axeY0->setBounds (10,  50);
//    $graph->axeY1->setBounds ( 5, 100);
//    $graph->axeY0->setTicksMajor(array(0, 10, 20, 30, 40, 50));
//    $graph->axeY0->setTicksMinor(array(5, 15, 25, 35, 45));
//    $graph->axeY1->setTicksMajor(array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100));
//    $graph->axeY0->setTicksMinor(array(5, 15, 25, 35, 45, 55, 65, 75, 85, 95));

    $graph->axeY1->setTickStyle(IMAGE_GRAPH_TICKS_BOTH);
    $graph->axeY1->setTickSize (5);

    $graph->axeX->setTickStyle(IMAGE_GRAPH_TICKS_BOTH);
    $graph->axeX->setTickSize (5);

    $image = $graph->getGDImage();

    Header ("Content-Type: image/png");
    imagepng($image);
?>
