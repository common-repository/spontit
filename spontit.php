<?php
/**
 * @package Spontit
 * @version 1.0
 */
/*
Plugin Name: Spontit
Plugin URI: https://wordpress.org/plugins/spontit
Description: This plugin creates a pop-up window that asks if the website visitor wants to follow a Spontit channel. The channel link can be added in settings, and the pop-up will only display if it's a valid Spontit link.
Author: He Jiang (Spontit)
Version: 1.0
Author URI: http://mjiang.dev
License:     GNUGPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Spontit - a wordpress plugin that adds a pop-up window that invites website visitors to follow on Spontit.
Copyright (C) 2020  He Jiang (email : info@spontit.com)

Spontit is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Spontit is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Spontit.  If not, see <https://www.gnu.org/licenses/gpl-3.0.html>.
*/
define( 'SPONTIT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once( SPONTIT__PLUGIN_DIR . 'class.spontit.php' );

add_action( 'init', array( 'Spontit', 'init' ) );