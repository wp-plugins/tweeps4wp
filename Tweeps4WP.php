<?php
/*
Plugin Name: Tweeps4WP
Plugin URI: http://wp.anoop.net/tweeps4wp
Description: Display your friends or followers in a widget in WP
Version: 0.0.5
Author: Anoop Bhat
Author URI: http://wp.anoop.net

  Copyright 2009  ANOOP BHAT  (email : anoop.bhat@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// include the location for our class
include_once('class.Tweeps4WP.php');

// instantiate our new clas
$tweeps4wp = new Tweeps4WP_widget();

// Hoook into the 'wp_dashboard_setup' action to register our other functions
add_action('widgets_init', array($tweeps4wp, 'tweeps4wp_addwidget') );

?>
