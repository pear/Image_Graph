<?
// $Id$
/**
* Square data-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   private
*/

require_once("Image/Graph/DataMarker/common.php");

class Image_Graph_DataMarker_Square extends Image_Graph_DataMarker_Common
{
    /**
    * size of the rhomb (left to right)
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

    function Image_Graph_DataMarker_Square($attributes)
    {
        if (!isset($attributes['size'])) {
            $attributes['size'] = 10;
        }
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

        $halfSizePixel = floor(($this->_size-1) / 2);

        imagefilledrectangle ($img, $pos[0]-$halfSizePixel, $pos[1]-$halfSizePixel,
                                    $pos[0]+$halfSizePixel, $pos[1]+$halfSizePixel,
                              $drawColor);
    }
}
?>
