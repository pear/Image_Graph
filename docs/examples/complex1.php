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
    $graph->axeY1->title->setText ("Number of downloadsxxxxxxx");

    $graph->axeY0->setNumbercolor (array(0xCC,0x29,0x29));
    $graph->axeY1->setNumbercolor (array(0x29,0xCC,0x29));

    $graph->axeY0->title->setColor (array(0xCC,0x29,0x29));
    $graph->axeY1->title->setColor (array(0x29,0xCC,0x29));

    $graph->axeY0->title->setFontOptions (array("fontSize" => 8));
    $graph->axeY1->title->setFontOptions (array("fontSize" => 8));

// TO DO: add Y-axes titles

    $graph->setAxesColor(array(0x40, 0x40, 0xFF));

    $data    = array( "Mo.\n8.9." => 15.8,
                      "Di.\n9.9." => 37.2,
                      "Mi.\n10.9." => 50.0
                    );
    $graph->setDataDefaultColor(array(0xCC,0x29,0x29));
    $graph->addData($data, "line", array("axeId" => 0));
    $data["Di.\n9.9."] = 20;
    $graph->addData($data, "bar",  array("axeId" => 1, "color" => array(0xBF,0xBF,0x30)));
    $dataObj = &$graph->addData($data, "line", array("axeId" => 1, "color" => array(0x29,0xCC,0x29)));
    $marker =& $dataObj->setDataMarker("diamond", array("color" => array(0xBF,0xBF,0xBF)));
    $marker->setSize(7);

    $graph->axeY0->setBounds (10,  50);
    $graph->axeY1->setBounds ( 5, 100);
    $graph->axeY0->setTicksMajor(array(0, 10, 20, 30, 40, 50));
    $graph->axeY0->setTicksMinor(array(5, 15, 25, 35, 45));
    $graph->axeY1->setTicksMajor(array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100));
    $graph->axeY0->setTicksMinor(array(5, 15, 25, 35, 45, 55, 65, 75, 85, 95));

    $graph->axeY1->setTickStyle(IMAGE_GRAPH_TICKS_BOTH);
    $graph->axeY1->setTickSize (5);

    $image = $graph->getGDImage();

    Header ("Content-Type: image/png");
    imagepng($image);
?>
