<?
  require_once("Image/Graph.php");

$graph = new Image_Graph (400, 200);
$graph->setBackgroundColor(array(200, 200, 200));
$graph->setSpaceFromBorder(20); // 20 pixels from each border
//$graph->setDefaultFont("Times New Roman"); // will be used for all diagrams if they are created via "addDiagram" with size-values
//$graph->setDefaultFontsize(10);

//$graph->setDefaultFont("Times New Roman"); // will be used for titles / axes if not otherwise specified
//$graph->setDefaultFontsize(10);

//$graph->setTitleText("Traffic usage\nJune 2003"[, "top"]); // second parameter can be top, bottom, left, right
//$graph->setTitleColor($rgb_black);
//$graph->setTitleFont("Arial");
//$graph->setTitleFontsize(12);
//$graph->setAxesXTitle("Days");
//$graph->setAxesYTitle("Traffic");
//$graph->setAxesYPrescale(1024*1024*1024); // data-Werte erst multiplizieren
//$graph->setAxesYDivisor(1024*1024*1024);
$graph->setAxesYMin(10);
$graph->setAxesYMax(50);
$graph->setAxesColor(array(255, 0, 0));

$data    = array( "Mo.\n8.9." => 15.8,
                  "Di.\n9.9." => 37.2,
                  "Mi.\n10.9." => 50.0
                );
//$shading = array("type" => "linear", "degrees" => 0, "start" => $rgb_red, "stop" => $rgb_blue);

$graph->setDataDefaultColor(array(0, 0, 255));
//$graph->addData("line"  , $data, array("shading" => $shading, "color" => $rgb_green) );
$graph->addData($data, "line");
//$graph->addData("spline", $data);
//$graph->addData("points", $data, array("size" => 3, "shape" => "square") );


/*
or:
$graph2->setAxesYUnit(IMAGE_GRAPH_UNIT_BYTE);
$graph2->setAxesYMin(10);
$graph2->setAxesYMax(50);
$graph2->setAxesYNumberformat("%.02f"); // number-format for use with sprintf
$graph2->setAxesColor($rgb_yellow);

$graph2->addData("line"  , $data);
$graph2->setSize(100, 100); // modify size of diagram
*/

$image = $graph->getGDImage();

Header ("Content-Type: image/jpeg");
imagejpeg($image);
//print_r($image);
?>