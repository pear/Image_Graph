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

    $graph->diagramTitle->setText("Downloads June 2003");

    $graph->axeY0->setFontOptions(array("fontSize" => 8));
    $graph->axeY0->setColor(array(0x40, 0x40, 0xFF));

    $data    = array( 15.8,
                      37.2,
                      50.0
                    );

    $graph->setDataDefaultColor(array(0xCC,0x29,0x29));
    $dataObj = &$graph->addData($data, "line", array("axeId" => 0));
    $dataObj->setFill("gradient", array("color" => array(array(0xF4, 0x80, 0x80,150), array(0xCC, 0x29, 0x29,150),"#00FF00")));

    $graph->axeY0->setBounds(10,50);

    $img = imagecreatefrompng("images/simple2.png");
    $image = $graph->getGDImage($img);

    Header ("Content-Type: image/png");
    imagepng($image);
?>