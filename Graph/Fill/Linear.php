<?
// $Id$
/**
* Linear gradient fill-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Image/Graph/Fill/Common.php");
require_once("Image/Color.php"); // package: PEAR::Image_Color

class Image_Graph_Fill_Linear extends Image_Graph_Fill_Common
{
    /**
    * Constructor for the class
    *
    * @param  array   attributes like color (to be extended to also include shading etc.)
    * @access public
    */
    function Image_Graph_Fill_Linear($attributes)
    {
        parent::Image_Graph_Fill_Common($attributes);
    }
    
    /**
    * Draws fill element, shape: box 
    *
    * @param  gd-resource              image-resource to draw to
    * @param  array of array of int    absolute position for upper left and lower right edge
    * @access private
    */
    function drawGDBox(&$img, $pos)
    {
        // only horizontal gradient implemented yet
        // gradient runs from top to bottom
        
        $numSteps = $pos[1][1]-$pos[0][1];
        $colorObj = &new Image_Color();
        $tempCol1 = Image_Color::rgb2hex($this->_attributes["color"][0]);
        $tempCol2 = Image_Color::rgb2hex($this->_attributes["color"][1]);
        $colorObj->setColors($tempCol1, $tempCol2);
        $colors   = $colorObj->getRange($numSteps);
        foreach($colors as $key => $value) {
            $colors[$key] = Image_Color::hex2rgb($colors[$key]);
        }
        unset($colorObj); // save memory
        for ($step=0;$step<$numSteps;$step++)
        {
          $drawColor = imagecolorallocate($img, $colors[$step][0], $colors[$step][1], $colors[$step][2]);
          imageline ($img, $pos[0][0], $pos[0][1]+$step, $pos[1][0], $pos[0][1]+$step, $drawColor);
        }
    }
    
    /**
    * Draws fill element, shape: polygon
    *
    * @param  gd-resource              image-resource to draw to
    * @param  array of array of int    absolute positions of polygon-coordinates
    * @access private
    */
    function drawGDPolygon(&$img, $pos)
    {
        // only horizontal gradient implemented yet
        // gradient runs from top to bottom
        
        // we need at least 3 points to fill a polygon
        if (count($pos)<3) {
            return false;
        }
        
        // our special type of polygons always has an equal number of points
        if ((count($pos)%2) != 0) {
            return false;
        }
        
        // determine minY/maxY
        $minY = $pos[0][1];
        $maxY = $pos[0][1];
        foreach ($pos as $currPos) {
            $minY = min($minY, $currPos[1]);
            $maxY = max($maxY, $currPos[1]);
        }
        $numSteps = $maxY-$minY;
        $colorObj = &new Image_Color();
        $tempCol1 = Image_Color::rgb2hex($this->_attributes["color"][0]);
        $tempCol2 = Image_Color::rgb2hex($this->_attributes["color"][1]);
        $colorObj->setColors($tempCol1, $tempCol2);
        $colors   = $colorObj->getRange($numSteps);
        $colorsAllocated = array();
        foreach($colors as $currColor) {
            $tempColor = Image_Color::hex2rgb($currColor);
            $colorsAllocated[] = imagecolorallocate($img, $tempColor[0], $tempColor[1], $tempColor[2]);
        }
        unset($colorObj); // save memory
        unset($colors); // save memory

        // use an algo that's optimized to the way our polygons are constructed
        $numPoints = count($pos);
        for ($counter=0;$counter<($numPoints/2);$counter++)
        {
            $upperRight = $pos[$numPoints-$counter-1];
            $upperLeft  = $pos[$numPoints-$counter-2];
            $lowerRight = $pos[$counter];
            $lowerLeft  = $pos[$counter+1];

            $tempMaxY   = max($lowerRight[1], $lowerLeft[1]);
            $tempMinY   = min($upperRight[1], $upperLeft[1]);

            if (($upperLeft[1]-$upperRight[1]) == 0) {
                $upperSlope=0;
            } else {
                $upperSlope = ($upperRight[0]-$upperLeft[0] ) / ($upperLeft[1]-$upperRight[1]);
            }
            if (($lowerLeft[1]-$lowerRight[1]) == 0) {
                $lowerSlope=0;
            } else {
                $lowerSlope = ($lowerRight[0]-$lowerLeft[0]) / ($lowerLeft[1]-$lowerRight[1]);
            }
            for ($upDownCounter=$tempMinY; $upDownCounter<$tempMaxY; $upDownCounter++)
            {
                $tempLeft = $upperLeft[0];
                $tempRight = $upperRight[0];

                $boundLeft = $upperLeft[0] +($upperSlope*($upperLeft[1] -$upDownCounter));
                if ($upperSlope>0) {
                    $tempLeft  = max($tempLeft, $boundLeft);
                } elseif ($upperSlope<0) {
                    $tempRight = min($boundLeft, $tempRight);
                }
                $boundRight=$lowerRight[0]+($lowerSlope*($lowerRight[1]-$upDownCounter));
                if ($lowerSlope>0) {
                    $tempRight = min($boundRight, $tempRight);
                } elseif ($lowerSlope<0) {
                    $tempLeft = max($tempLeft, $boundRight);
                }
                imageline ($img, round($tempLeft), $upDownCounter, round($tempRight), $upDownCounter, $colorsAllocated[$upDownCounter-$minY]);
            }
        }
    }
    
    /**
    * Draws fill element, shape: columns of pixels (
    *
    * @param  gd-resource              image-resource to draw to
    * @param  int                      left y-coord of pixelcolumn to fill
    * @param  array of array of int    top and bottom x-coords for each column to fill
    * @access private
    */
    function drawGDPixelcolumns(&$img, $yLeft, $xTopBottom)
    {
        // not yet implemented
    }
}
?>
