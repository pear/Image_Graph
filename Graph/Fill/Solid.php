<?
// $Id$
/**
* Solid fill-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Image/Graph/Fill/Common.php");

class Image_Graph_Fill_Solid extends Image_Graph_Fill_Common
{

    /**
    * Constructor for the class
    *
    * @param  array   attributes like color (to be extended to also include shading etc.)
    * @access public
    */
    function Image_Graph_Fill_Solid($attributes)
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
        $drawColor = imagecolorallocate($img, $this->_attributes["color"][0], $this->_attributes["color"][1], $this->_attributes["color"][2]);

        imagefilledrectangle ($img, $pos[0][0], $pos[0][1], $pos[1][0], $pos[1][1], $drawColor);
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
        // TO DO: check if there is a number of maximum points imagefilledpolygon supports
        
        $drawColor = imagecolorallocate($img, $this->_attributes["color"][0], $this->_attributes["color"][1], $this->_attributes["color"][2]);

        $points=array();
        foreach($pos as $currPos) {
            $points[] = $currPos[0];
            $points[] = $currPos[1];
        }
        imagefilledpolygon ($img, $points, count($pos), $drawColor);
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
