<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action( 'admin_notices','eos_up_cache_admin_notices' );
//Notification about the cache clearing attempt
function eos_up_cache_admin_notices(){
	if( !eos_up_cache_check_supported_plugin() ){
		?>
		<div style="padding:20px" class="notice notice-warning is-dismissible"><?php esc_html__( 'You have no caching plugin that is supported by Update Page Cache.','eos-up-cache' ); ?></div>
		<?php			
	}
}

add_action( 'admin_bar_menu','eos_up_cache_top_bar_button',40 );
// Add button to admin top bar
function eos_up_cache_top_bar_button( $wp_admin_bar ){
	if( eos_up_cache_check_supported_plugin() && isset( $_GET['post'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] && current_user_can( 'edit_others_posts' ) ){
		$wp_admin_bar->add_menu( array(
			'id'    => 'eos-up-cache',
			'title' => '<span style="cursor:pointer" title="'.esc_attr__( 'Clear and regenerate the cache for this page','eos-up-cache' ).'"><span class="dashicons dashicons-update" style="font-family:dashicons"></span> '.esc_html__( 'Update page cache','eos-up-cache' ).'</span>'
		));
	}
}