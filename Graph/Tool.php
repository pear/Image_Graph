<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Image_Graph - Main class for the graph creation.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Graph
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * This class contains a set of tool-functions.
 * 
 * These functions are all to be called statically
 *
 * @category   Images
 * @package    Image_Graph
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_Graph
 */
class Image_Graph_Tool
{

    /**
     * Return the average of 2 points
     *
     * @param double P1 1st point
     * @param double P2 2nd point
     * @return double The average of P1 and P2
     * @static
     */
    function mid($p1, $p2)
    {
        return ($p1 + $p2) / 2;
    }

    /**
     * Mirrors P1 in P2 by a amount of Factor
     *
     * @param double $p1 1st point, point to mirror
     * @param double $o2 2nd point, mirror point
     * @param double $factor Mirror factor, 0 returns $p2, 1 returns a pure
     * mirror, ie $p1 on the exact other side of $p2
     * @return double $p1 mirrored in $p2 by Factor
     * @static
     */
    function mirror($p1, $p2, $factor = 1)
    {
        return $p2 + $factor * ($p2 - $p1);
    }

    /**
     * Calculates a Bezier control point, this function must be called for BOTH
     * X and Y coordinates (will it work for 3D coordinates!?)
     *
     * @param double $p1 1st point
     * @param double $p2 Point to
     * @param double $factor Mirror factor, 0 returns P2, 1 returns a pure
     *   mirror, i.e. P1 on the exact other side of P2
     * @return double P1 mirrored in P2 by Factor
     * @static
     */
    function controlPoint($p1, $p2, $factor, $smoothFactor = 0.75)
    {
        $sa = Image_Graph_Tool::mirror($p1, $p2, $smoothFactor);
        $sb = Image_Graph_Tool::mid($p2, $sa);

        $m = Image_Graph_Tool::mid($p2, $factor);

        $pC = Image_Graph_Tool::mid($sb, $m);

        return $pC;
    }

    /**
     * Calculates a Bezier point, this function must be called for BOTH X and Y
     * coordinates (will it work for 3D coordinates!?)
     *
     * @param double $t A position between $p2 and $p3, value between 0 and 1
     * @param double $p1 Point to use for calculating control points
     * @param double $p2 Point 1 to calculate bezier curve between
     * @param double $p3 Point 2 to calculate bezier curve between
     * @param double $p4 Point to use for calculating control points
     * @return double The bezier value of the point t between $p2 and $p3 using
     * $p1 and $p4 to calculate control points
     * @static
     */
    function bezier($t, $p1, $p2, $p3, $p4)
    {
        // (1-t)^3*p1 + 3*(1-t)^2*t*p2 + 3*(1-t)*t^2*p3 + t^3*p4
        return pow(1 - $t, 3) * $p1 +
            3 * pow(1 - $t, 2) * $t * $p2 +
            3 * (1 - $t) * pow($t, 2) * $p3 +
            pow($t, 3) * $p4;
    }

}

?>