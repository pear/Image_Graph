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

    $data    = array( 20,
                      42,
                      58,
                      65,
                      70
                    );

    $graph->setDataDefaultColor(array(0xCC,0x29,0x29));
    $graph->addData($data, "bar",  array("axeId" => 1, "color" => array(0xBF,0xBF,0x30)));

    $data    = array( 15.8,
                      37.2,
                      3,
                      8,
                      50.0
                    );

    $dataObj = &$graph->addData($data, "bar",  array("axeId" => 1, "color" => array(0x30,0xBF,0xBF)));
    $dataObj->setFill("gradient", array("color" => array(array(0xF4, 0x80, 0x80), array(0xCC, 0x29, 0x29))));
    
    $dataObj = &$graph->addData($data, "line", array("axeId" => 1, "color" => array(0x29,0xCC,0x29)));
    $dataObj->setFill("solid", array("color" => array(0,0,0xBF)));
    $marker =& $dataObj->setDataMarker("diamond", array("color" => array(0xBF,0xBF,0xBF)));
    $marker->setSize(7);

    $data    = array( 10,
                      12,
                      5,
                      7,
                      17
                    );

    $dataObj = &$graph->addData($data, "line",  array("axeId" => 1, "color" => array(0x30,0xBF,0xBF)));
    $dataObj->setFill("gradient", array("color" => array(array(0xF4, 0x80, 0x80), array(0xCC, 0x29, 0x29))));
    
    $graph->axeX->setLabels(array("Month 1", "Month 2", "Month 3", "Month 4", "Month 5"));

    $graph->axeY1->setBounds(0,100);
    $graph->axeY1->setTicksAutoSteps(10);

    $graph->axeY1->setTickStyle(IMAGE_GRAPH_TICKS_BOTH);
    $graph->axeY1->setTickSize (5);

    $graph->axeX->setTickStyle(IMAGE_GRAPH_TICKS_BOTH);
    $graph->axeX->setTickSize (5);
    
    $graph->stackData();

    $image = $graph->getGDImage();

    Header ("Content-Type: image/png");
    imagepng($image);
?>
