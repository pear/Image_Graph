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
    $graph->setBackgroundColor(array(0x59, 0x59, 0x59));
    $graph->setSpaceFromBorder(5); // 5 pixels from each border
    $graph->setDefaultFontOptions(array("fontPath" => "", "fontFile" => "arial", "antiAliasing" => true, "fontSize" => 10, "color" => array(0,0,0) ));
    $graph->setDiagramTitleFontOptions(array("color" => array(0xBF,0xBF,0xBF), "spacerBottom" => 10));
    $graph->setAxesXTitleFontOptions(array("spacerTop" => 10, "spacerBottom" => 0));

    $graph->setDiagramTitleText("Downloads June 2003");
    $graph->setAxesXTitle("Days");
    $graph->setAxesYNumberformat  ("%.1f MB", 0);
    $graph->setAxesYNumberformat  ("%.0f files", 1);

// TO DO: add Y-axes titles
// $graph->setAxesYTitle("Traffic", 0);

    $graph->setAxesYFontOptions(array("color" => array(0xCC,0x29,0x29), "fontSize" => 8), 0);
    $graph->setAxesYFontOptions(array("color" => array(0x29,0xCC,0x29), "fontSize" => 8), 1);
    $graph->setAxesColor(array(0x40, 0x40, 0xFF));

    $data    = array( "Mo.\n8.9." => 15.8,
                      "Di.\n9.9." => 37.2,
                      "Mi.\n10.9." => 50.0
                    );

    $graph->setDataDefaultColor(array(0xCC,0x29,0x29));
    $graph->addData($data, "line", array("axeId" => 0));
    $data["Di.\n9.9."] = 20;
    $graph->addData($data, "bar",  array("axeId" => 1, "color" => array(0xBF,0xBF,0x30)));
    $graph->addData($data, "line", array("axeId" => 1, "color" => array(0x29,0xCC,0x29)));
    $graph->addData($data, "triangle",  array("axeId" => 1, "color" => array(0xBF,0xBF,0xBF)));

    $graph->setAxesYMin(10,0);
    $graph->setAxesYMax(50,0);
    $graph->setAxesYMin(5,1);
    $graph->setAxesYMax(100,1);
    $graph->setAxesYTicksMajor(array(0, 10, 20, 30, 40, 50), 0);
    $graph->setAxesYTicksMinor(array(5, 15, 25, 35, 45), 0);
    $graph->setAxesYTicksMajor(array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100), 1);
    $graph->setAxesYTicksMinor(array(5, 15, 25, 35, 45, 55, 65, 75, 85, 95), 1);

    $graph->setAxesYTickStyle(IMAGE_GRAPH_TICKS_BOTH, 1);
    $graph->setAxesYTickSize(5, 1);

    $image = $graph->getGDImage();

    Header ("Content-Type: image/png");
    imagepng($image);
?>