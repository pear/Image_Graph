<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Stefan Neufeind <pear.neufeind@speedpartner.de>              |
// +----------------------------------------------------------------------+
//
// $Id$

/**
* Diamond datamarker-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/

/**
* The parent class
*/
require_once("Image/Graph/DataMarker/Common.php");

/**
* Diamond datamarker-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_DataMarker_Diamond extends Image_Graph_DataMarker_Common
{
    /**
    * size (left to right)
    *
    * @var int          size
    * @see setSize()
    * @access private
    */
    var $_size = 10;

    /**
    * Constructor for the class
    *
    * @param  array   attributes like color
    * @access public
    */
    function Image_Graph_DataMarker_Diamond($attributes=array())
    {
        parent::Image_Graph_DataMarker_Common($attributes);
    }

    /**
    * Set size
    *
    * @param  int     size
    * @access public
    */
    function setSize($size)
    {
        if ($size >= 1) {
            $this->_size = $size;
        }
    }
    
    /**
    * Draws diagram element 
    *
    * @param resource         GD-resource to draw to
    * @param array            (array of int) absolute position (x/y), where to draw the marker
    * @access public
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
?>