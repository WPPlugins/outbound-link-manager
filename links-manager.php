<?php
/*
Plugin Name: Outbound Link Manager
Plugin URI: http://www.searchmatters.com.au/wp-plugins/outbound-link-manager/
Description: The Outbound Link Manager monitors outbound links in your posts, easily allowing you to add or remove a nofollow tag, update anchor texts, or remove links altogether. <a href="admin.php?page=links-manager/links-manager-manage.php">Manage links now.</a>
Version: 1.0
Author: Morris Bryant, Ruben Sargsyan
Author URI: http://www.searchmatters.com.au
*/

/*  Copyright 2010 Morris Bryant (email: business@searchmatters.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

$links_manager_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
$links_manager_plugin_title = "Outbound Link Manager";
$links_manager_plugin_prefix = "outbound_link_manager_";
$links_manager_table_name = $wpdb->prefix."links_manager";

function links_manager_menu(){
    if(function_exists('add_menu_page')){
		add_menu_page(__('Manage', 'links-manager'), __('Links Manager', 'links-manager'), 'manage_options', 'outbound-link-manager/links-manager-manage.php');
	}
}

add_action('admin_menu', 'links_manager_menu');
?>