<?php
// $Id$

require_once("Image/Graph/DataMarker/Common.php");

/**
* Diamond datamarker-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/
class Image_Graph_DataMarker_Diamond extends Image_Graph_DataMarker_Common
{
    /**
    * size of the diamond (left to right)
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
    function Image_Graph_DataMarker_Diamond($attributes)
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
        $drawColor = Image_Graph_Color::allocateColor($img, $this->_color);
        
        // compute side-length using Pythagoras so that square and diamond look equal-size
        $sideLength = sqrt(2*$this->_size*$this->_size);
        $halfSizePixel = floor(($sideLength-1) / 2);

        $points = array($pos[0]               , $pos[1]-$halfSizePixel,
                        $pos[0]+$halfSizePixel, $pos[1],
                        $pos[0]               , $pos[1]+$halfSizePixel,
                        $pos[0]-$halfSizePixel, $pos[1]);
        imagefilledpolygon ($img, $points, 4, $drawColor);
    }
}
php?>