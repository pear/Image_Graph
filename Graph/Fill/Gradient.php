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
* Gradient fill-element
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
* Gradient fill-element
*
* This class is used throughout Image_Graph to perform gradient fills of a box
* (rectangle) or a polygon. It is mainly used for diagram-data-elements (like
* a bar or a line) and the diagram-grid.
* Please note that also more than 2 colors can be provided which will result in
* a gradient-fill from one color to the next [...] to the last one (multiple
* gradients).
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Graph
*/
class Image_Graph_Fill_Gradient extends Image_Graph_Fill_Common
{
    /**
    * Constructor for the class
    *
    * @param  array           attributes, e.g. "color"
    * @access public
    */
    function Image_Graph_Fill_Gradient($attributes)
    {
        if (!isset($attributes['type']) ||
            ($attributes['type'] != IMAGE_GRAPH_FILL_LINEAR)) {
            $attributes['type'] = IMAGE_GRAPH_FILL_LINEAR;
        }
        parent::Image_Graph_Fill_Common($attributes);
    }

    /**
    * Set color
    *
    * @param  array           array of colors
    * @see    Image_Graph_Color::color2RGB()
    * @access public
    */
    function setColor($color)
    {
        $tempColors = array();
        foreach ($color as $currColor) {
            $tempColors[] = Image_Graph_Color::color2RGB($currColor);
        }
        $this->_color = $tempColors;
    }

    /**
    * Draws fill element, shape: box
    *
    * @param  resource        GD-resource to draw to
    * @param  array           array of array of int; absolute position for upper left and lower right edge
    * @access public
    */
    function drawGDBox(&$img, $pos)
    {
        // only horizontal linear gradient implemented yet
        // gradient runs from top to bottom

        if ($this->_attributes['type'] != IMAGE_GRAPH_FILL_LINEAR) {
            return false;
        }

        $numSteps = $pos[1][1]-$pos[0][1];
        $colorObj = &new Image_Graph_Color();
        $colorObj->setColors($this->_color[0], $this->_color[1]);
        $colors   = $colorObj->getRange($numSteps);
        unset($colorObj); // save memory
        for ($step=0;$step<$numSteps;$step++)
        {
          $drawColor = Image_Graph_Color::allocateColor($img, $colors[$step]);
          imageline ($img, $pos[0][0], $pos[0][1]+$step, $pos[1][0], $pos[0][1]+$step, $drawColor);
        }
    }

    /**
    * Draws fill element, shape: polygon
    *
    * @param  resource                 GD-resource to draw to
    * @param  array of array of int    absolute positions of polygon-coordinates
    * @access public
    */
    function drawGDPolygon(&$img, $pos)
    {
        // only horizontal linear gradient implemented yet
        // gradient runs from top to bottom

        if ($this->_attributes['type'] != IMAGE_GRAPH_FILL_LINEAR) {
            return false;
        }

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
        $numColors = count($this->_color);
        $colorObj = &new Image_Graph_Color();
        $colors = array();
        for ($counter=0;$counter<($numColors-1);$counter++) {
            $colorObj->setColors($this->_color[$counter], $this->_color[$counter+1]);
            $colors = array_merge($colors, $colorObj->getRange($numSteps/($numColors-1)));
        }
        $colorsAllocated = array();
        foreach($colors as $currColor) {
            $colorsAllocated[] = Image_Graph_Color::allocateColor($img, $currColor);
        }
        unset($colorObj); // save memory
        unset($colors); // save memory

        // use an algo that's optimized to the way our polygons are constructed
        $numPoints = count($pos);
        for ($counter=0;$counter<(($numPoints/2)-1);$counter++)
        {
            $upperRight = $pos[$numPoints-$counter-1];
            $upperLeft  = $pos[$numPoints-$counter-2];
            $lowerRight = $pos[$counter];
            $lowerLeft  = $pos[$counter+1];

            if ($counter>0)
            {
              $upperRight[0]--;
              $lowerRight[0]--;
            }

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
            for ($lineCounter=$tempMinY; $lineCounter<$tempMaxY; $lineCounter++)
            {
                $tempLeft = $upperLeft[0];
                $tempRight = $upperRight[0];

                $boundLeft = $upperLeft[0] +($upperSlope*($upperLeft[1] -$lineCounter));
                if ($upperSlope>0) {
                    $tempLeft  = max($tempLeft, $boundLeft);
                } elseif ($upperSlope<0) {
                    $tempRight = min($boundLeft, $tempRight);
                }
                $boundRight=$lowerRight[0]+($lowerSlope*($lowerRight[1]-$lineCounter));
                if ($lowerSlope>0) {
                    $tempRight = min($boundRight, $tempRight);
                } elseif ($lowerSlope<0) {
                    $tempLeft = max($tempLeft, $boundRight);
                }
                imageline ($img, round($tempLeft),  $lineCounter,
                                 round($tempRight), $lineCounter,
                                 $colorsAllocated[$lineCounter-$minY]);
            }
        }
    }
}
?>