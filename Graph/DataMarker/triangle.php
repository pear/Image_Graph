<?
// $Id$
/**
* Triangle data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Image/Graph/DataMarker/common.php");

class Image_Graph_DataMarker_Triangle extends Image_Graph_DataMarker_Common
{
    /**
    * size of the triangle (left to right)
    *
    * @var int   size
    * @see setSize()
    * @access private
    */
    var $_size = 10;

    /**
    * Constructor for the class
    *
    * @param  array   attributes like color (to be extended to also include shading etc.)
    * @access public
    */
    
    function Image_Graph_DataMarker_Triangle($attributes)
    {
        parent::Image_Graph_DataMarker_Common($attributes);
    }

    /**
    * Set size of triangle
    *
    * @param  int     size
    * @access public
    */
    
    function setSize($size)
    {
        $this->_size = $size;
    }

    /**
    * Draws diagram element 
    *
    * @param gd-resource image-resource to draw to
    * @param array of int absolute position, where to draw the marker
    * @access private
    */

    function drawGD(&$img, $pos)
    {
        $drawColor = imagecolorallocate($img, $this->_attributes["color"][0], $this->_attributes["color"][1], $this->_attributes["color"][2]);

        // compute side-length using Pythagoras so that square and rhomb look equal-size
        $sideLength = sqrt(2*$this->_size*$this->_size);
        $halfSizePixelUp = floor(($sideLength-1) / 2);
        $halfSizePixelSide = floor(($this->_size-1) / 2);

        $points = array($pos[0]                   , $pos[1]-$halfSizePixelSide,
                        $pos[0]+$halfSizePixelSide, $pos[1]+$halfSizePixelSide,
                        $pos[0]-$halfSizePixelSide, $pos[1]+$halfSizePixelSide);
        imagefilledpolygon ($img, $points, 3, $drawColor);
    }
}
?>
