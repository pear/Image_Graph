<?
  require_once("Image/Graph.php");

$graph = new Image_Graph (400, 200);
$graph->setBackgroundColor(array(200, 200, 200));
$graph->setSpaceFromBorder(10); // 10 pixels from each border
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
$graph->setAxesColor(array(255, 0, 0));

$data    = array( "Mo.\n8.9." => 15.8,
                  "Di.\n9.9." => 37.2,
                  "Mi.\n10.9." => 50.0
                );
//$shading = array("type" => "linear", "degrees" => 0, "start" => $rgb_red, "stop" => $rgb_blue);

$graph->setDataDefaultColor(array(0, 0, 255));
//$graph->addData("line"  , $data, array("shading" => $shading, "color" => $rgb_green) );
$graph->addData($data, "line", array("axeId" => 0));

$data["Di.\n9.9."] = 20;
$graph->addData($data, "bar",  array("axeId" => 1, "color" => array(100,0,200)));
$graph->addData($data, "line", array("axeId" => 1, "color" => array(255,255,0)));
//$graph->addData("spline", $data);
//$graph->addData("points", $data, array("size" => 3, "shape" => "square") );

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

Header ("Content-Type: image/png");
imagepng($image);
//print_r($image);
?>