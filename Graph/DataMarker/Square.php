<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
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
* Class-file for the square datamarker-element
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @category images
* @license  The PHP License, version 2.02
*/

/**
* The parent class
*/
require_once("Image/Graph/DataMarker/Common.php");

/**
* Square datamarker-element
*
* The shape of this datamarker is a square.
* The size set by setSize() is the width/height.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @access   public
*/
class Image_Graph_DataMarker_Square extends Image_Graph_DataMarker_Common
{
    /**
    * Constructor
    *
    * @param  array   attributes like color
    * @access public
    */
    function Image_Graph_DataMarker_Square($attributes=array())
    {
        if (!isset($attributes['size'])) {
            $attributes['size'] = 10;
        }
        parent::Image_Graph_DataMarker_Common($attributes);
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

        $halfSizePixel = floor(($this->_size-1) / 2);

        imagefilledrectangle ($img, $pos[0]-$halfSizePixel, $pos[1]-$halfSizePixel,
                                    $pos[0]+$halfSizePixel, $pos[1]+$halfSizePixel,
                              $drawColor);
    }
}
?>