<?php
/*
Plugin Name: Update Page Cache
Description: Update Page Cache flushes and generates again the page cache. Compatible with W3 Total Cache, WP Fastest Cache, WP Super Cache, WP Optimize.
Text Domain: up-cache
Author: Jose Mortellaro
Author URI: https://josemortellaro.com/
Domain Path: /languages/
Version: 0.0.5
*/
/*  This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'UPC_VERSION','0.0.5' );
define( 'EOS_UPC_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'EOS_UPC_PLUGIN_URL',untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'EOS_UPC_PLUGIN_BASE_NAME', untrailingslashit( plugin_basename( __FILE__ ) ) );
if( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && false !== strpos( $_REQUEST['action'],'eos_upc' ) ){
	//Load file for plugin ajax activities
	require EOS_UPC_PLUGIN_DIR.'/admin/upc-ajax.php';
}
if( is_admin() ){
	require EOS_UPC_PLUGIN_DIR.'/admin/upc-admin.php';
	if( isset( $_GET['post'] ) && absint( $_GET['post'] ) === intval( $_GET['post'] ) ){
		require EOS_UPC_PLUGIN_DIR.'/admin/upc-admin-single.php';
	}
}

//Clear post cache in case of supported caching plugin.
function eos_up_cache_flush_cache_by_id( $post_id ){
	$return = false;
	if( function_exists( 'w3tc_pgcache_flush_post' ) ){
		w3tc_pgcache_flush_post( $post_id );
		$return = true;
	}
	if( function_exists( 'wp_cache_post_change' ) ){
		wp_cache_post_change( $post_id );
		$return = true;
	}
	if( function_exists( 'wpfc_clear_post_cache_by_id' ) ){
		wpfc_clear_post_cache_by_id( $post_id );
		$return = true;
	}
	if( class_exists( 'WPO_Page_Cache' ) ){
		WPO_Page_Cache::delete_single_post_cache( $post_id );
		$return = true;
	}
	if( function_exists( 'breeze_get_cache_base_path' ) ){
		$url = get_the_permalink( $post_id );
		foreach( array( 'guest_breeze_cache_desktop','guest_breeze_cache_tablet','guest_breeze_cache_mobile' ) as $arg ){
			$file_name = add_query_arg( $arg,'',$url );
			$file_name1 = md5( $file_name.'/index.gzip.html' ).'.php';
			$file_name2 = md5( $file_name.'/index.html' ).'.php';
			$blog_id = is_multisite() ? get_current_blog_id() : 0;
			$path1 = breeze_get_cache_base_path( false,$blog_id ).md5( $url ).'/'.$file_name1;
			$path2 = breeze_get_cache_base_path( false,$blog_id ).md5( $url ).'/'.$file_name2;
			if( file_exists( $path1 ) ){
				@unlink( $path1 );
			}
			if( file_exists( $path2 ) ){
				@unlink( $path2 );
			}
		}
		$return = true;
	}
	return $return;
}

//Check if a supported caching plugin is active
function eos_up_cache_check_supported_plugin(){
	return function_exists( 'w3tc_pgcache_flush_post' )
		|| function_exists( 'wp_cache_post_change' )
		|| function_exists( 'wpfc_clear_post_cache_by_id' )
		|| function_exists( 'wpfc_clear_post_cache_by_id' )
		|| class_exists( 'WPO_Page_Cache' )
		|| function_exists( 'breeze_get_cache_base_path' );
}

//Return the plugin options array
function eos_up_get_main_options_array(){
	if( !is_multisite() ){
		return get_option( 'eos_up_cache_main' );
	}
	else{
		return get_blog_option( get_current_blog_id(),'eos_up_cache_main' );
	}
}

//Update options in case of single or multisite installation.
function eos_up_update_option( $option,$newvalue,$autoload = false ){
	if( !is_multisite() ){
		return update_option( $option,$newvalue,$autoload );
	}
	else{
		return update_blog_option( get_current_blog_id(),$option,$newvalue );
	}
}