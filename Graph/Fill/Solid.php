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
* Solid fill-element for a Image_Graph diagram
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
* @category images
* @license  The PHP License, version 2.02
*/

/**
* The parent class
*/
require_once("Image/Graph/Fill/Common.php");

/**
* Solid fill-element
*
* This class is used throughout Image_Graph to perform solid fills of a box
* (rectangle) or a polygon. It is mainly used for diagram-data-elements (like
* a bar or a line) and the diagram-grid.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/
class Image_Graph_Fill_Solid extends Image_Graph_Fill_Common
{
    /**
    * Constructor
    *
    * @param  array           attributes like color
    * @access public
    */
    function Image_Graph_Fill_Solid($attributes)
    {
        parent::Image_Graph_Fill_Common($attributes);
    }

    /**
    * Draws fill element, shape: box
    *
    * @param  resource                 GD-resource to draw to
    * @param  array of array of int    absolute position for upper left and lower right edge
    * @access public
    */
    function drawGDBox(&$img, $pos)
    {
        $drawColor = Image_Graph_Color::allocateColor($img, $this->_color);

        imagefilledrectangle ($img, $pos[0][0], $pos[0][1], $pos[1][0], $pos[1][1], $drawColor);
    }

    /**
    * Draws fill element, shape: polygon
    *
    * @param  resource                 GD-resource to draw to
    * @param  array of array of int    absolute positions of polygon-coordinates
    * @access private
    */
    function drawGDPolygon(&$img, $pos)
    {
        // @todo: check if there is a number of maximum points imagefilledpolygon supports

        $drawColor = Image_Graph_Color::allocateColor($img, $this->_color);

        $points=array();
        foreach($pos as $currPos) {
            $points[] = $currPos[0];
            $points[] = $currPos[1];
        }
        imagefilledpolygon ($img, $points, count($pos), $drawColor);
    }
}
?>