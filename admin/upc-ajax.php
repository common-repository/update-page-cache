<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action( 'wp_ajax_eos_upc_update_cache','eos_upc_update_cache' );
//Update page cache
function eos_upc_update_cache(){
	if( !current_user_can( 'edit_others_posts' ) || !isset( $_POST['data'] ) ) return;
	$data = json_decode( sanitize_text_field( stripslashes( $_POST['data'] ) ) );
	if( isset( $data->nonce ) ){
		if( !wp_verify_nonce( esc_attr( $data->nonce ),'_up_cache_nonce' ) ){
			die();
			exit;
		}
	}
	$flushed = eos_up_cache_flush_cache_by_id( intval( $data->post_id ) );
	$response = wp_remote_get( get_permalink( intval( $data->post_id ) ) );
	if( !is_wp_error( $response ) && $flushed ){
		echo 1;
		die();
		exit;
	}
	echo 0;
	die();
	exit;
}

add_action( 'wp_ajax_eos_upc_save_settings','eos_upc_save_settings' );
//Save options
function eos_upc_save_settings(){
	if( !current_user_can( 'edit_others_posts' ) || !isset( $_POST['data'] ) ) return;
	$data = json_decode( sanitize_text_field( stripslashes( $_POST['data'] ) ),true );
	if( isset( $data['nonce'] ) ){
		if( !isset( $data['autos'] ) || !wp_verify_nonce( esc_attr( $data['nonce'] ),'_up_cache_setts_nonce' ) ){
			echo 0;
			die();
			exit;
		}
	}
	$opts = eos_up_get_main_options_array();
	$autos = $data['autos'];
	if( isset( $data['pages'] ) && is_array( $data['pages'] ) ){
		$opts['pages'] = eos_upc_reorder_array( $data['pages'] );
	}
	$opts['autos'] = sanitize_text_field( json_encode( $autos ) );
	$update = eos_up_update_option( 'eos_up_cache_main',$opts );
	if( $update ){
		echo 1;
		die();
		exit;
	}
	echo 0;
	die();
	exit;
}