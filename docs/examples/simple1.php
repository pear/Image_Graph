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
    $graph->setBackgroundColor(array(200, 200, 200));
    $graph->setDefaultFontOptions(array("fontPath" => "", "fontFile" => "arial", "antiAliasing" => true, "fontSize" => 10, "color" => array(0,0,0) ));

    $graph->setDiagramTitleText("Downloads June 2003");

    $graph->setAxesYFontOptions(array("fontSize" => 8), 0);
    $graph->setAxesColor(array(0x40, 0x40, 0xFF));

    $data    = array( "Mo.\n8.9." => 15.8,
                      "Di.\n9.9." => 37.2,
                      "Mi.\n10.9." => 50.0
                    );

    $graph->setDataDefaultColor(array(0xCC,0x29,0x29));
    $graph->addData($data, "line", array("axeId" => 0));

    $graph->setAxesYMin(10,0);
    $graph->setAxesYMax(50,0);
    $graph->setAxesYTicksMajor(array(0, 10, 20, 30, 40, 50), 0);
    $graph->setAxesYTicksMinor(array(5, 15, 25, 35, 45), 0);

    $image = $graph->getGDImage();

    Header ("Content-Type: image/png");
    imagepng($image);
?>