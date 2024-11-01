<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'admin_head','eos_up_cache_enqueue_script' );
//Add inine script
function eos_up_cache_enqueue_script(){
	if( isset( $_GET['action']  ) && 'edit' === $_GET['action'] ){
		?><style id="upc-css" type="text/css">#wp-admin-bar-eos-up-cache .dashicons:before{display:inline-block;margin-top:-11px;font-size:22px}#wp-admin-bar-eos-up-cache.upc-in-progress .dashicons:before{font-size:10px;padding:6px;background-size:22px 22px;background-position:center center;background-repeat:no-repeat;background-image:url(<?php echo EOS_UPC_PLUGIN_URL.'/assets/img/ajax-loader.gif'; ?>)}</style><?php
		wp_enqueue_script( 'upc-admin-single',EOS_UPC_PLUGIN_URL.'/assets/js/upc-admin-single.js',array(),UPC_VERSION,true );
		wp_localize_script( 'upc-admin-single','upc_js',array( 
			'nonce' => wp_create_nonce( '_up_cache_nonce','_up_cache_nonce' ),
			'post_id' => absint( $_GET['post'] ),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_loader' => EOS_UPC_PLUGIN_URL.'/assets/img/ajax-loader.gif'
		) );
	}
	if( isset( $_GET['page'] ) && 'upc_settings' === $_GET['page'] ){
		remove_all_actions( 'admin_notices' );
		wp_enqueue_style( 'upc-admin',EOS_UPC_PLUGIN_URL.'/assets/css/upc-admin.css',array(),UPC_VERSION );
	}
}

add_action( 'admin_menu','eos_upc_menu_page' );
//Add plugin menu
function eos_upc_menu_page() {
    add_menu_page(
        esc_html__( 'Update Cache', 'up-cache' ),
        esc_html__( 'Update Cache', 'up-cache' ),
        'manage_options',
        'upc_settings',
        'eos_upc_settings_do_page',
        'dashicons-update',
        60
    );
}

//Callback for plugins settings
function eos_upc_settings_do_page(){
	$post_types = get_post_types();
	$excludes = array(
		'revision',
		'customize_changeset',
		'user_request',
		'shop_order',
		'shop_order_refund'
	);
	foreach( $excludes as $exclude ){
		if( isset( $post_types[$exclude] ) ){
			unset( $post_types[$exclude] );
		}
	}
	$n = 0;
	$opts = eos_up_get_main_options_array();
	$autos = $opts && isset( $opts['autos'] ) ? $opts['autos'] : false;
	$pages = $opts && isset( $opts['pages'] ) ? $opts['pages'] : false;
	if( $autos ){
		$autos = eos_upc_reorder_array( json_decode( sanitize_text_field( stripslashes( $autos ) ),true ) );
	}
	wp_nonce_field( '_up_cache_setts_nonce','_up_cache_setts_nonce' );
	$page_lists = array();
	$dropdown_page = wp_dropdown_pages( array( 'echo' => false,'name' => 'upc-pages-list-n','id' => 'upc-pages-list-n','class' => 'upc-pages-list','show_option_none' => esc_attr__( 'Select page...','up-cache' ),'option_none_value' => 'false' ) );
	if( !eos_up_cache_check_supported_plugin() ){
	?>
	<div class="notice notice-warning is-dismissible" style="padding:20px;font-size:30px"><?php esc_html_e( 'Be careful! No caching plugin that is supported by Update Page Cache is active.','up-cache' ); ?></div>
	<?php
	}
	?>
	<h2><?php esc_html_e( 'Main settings','up-cache' ); ?></h2>
	<table id="eos-upc-table" class="wp-list-table widefat striped table-view-list">
		<thead>
			<tr>
				<td><?php esc_html_e( 'Post Type','up-cache' ); ?></td>
				<td><p style="max-width:150px"><?php esc_html_e( 'Update single post cache after saving single post','up-cache' ); ?></p></td>
				<td><p style="max-width:250px"><?php esc_html_e( 'Update the cache of these pages after saving single post','up-cache' ); ?></p></td>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach( $post_types as $post_type ){ 
				$obj = get_post_type_object( $post_type );
				if( isset( $obj->labels ) ){
					$labs = $obj->labels;
					$name = isset( $labs->name ) ? $labs->name : $post_type;
				}
				$checked = $autos && isset( $autos[$post_type] ) && $autos[$post_type] ? ' checked' : '';
			?>
			<tr id="upc-row-<?php echo $post_type; ?>" class="upc-row-post_type" data-post_type="<?php echo $post_type; ?>">
				<td><?php echo esc_html( $name ); ?></td>
				<td>
					<?php if( $obj->public ){ ?>
					<input class="upc-auto-chk" type="checkbox" value="1"<?php echo esc_attr( $checked ); ?> data-post_type="<?php echo esc_attr( $post_type ); ?>" />
					<?php } ?>
				</td>
				<td>
					<div class="upc-display-table">
						<div>
							<span id="upc-add-page-<?php echo $n; ?>" class="upc-add-page dashicons dashicons-plus-alt2" title="<?php esc_attr_e( 'Add page','up-cache' ); ?>" data-row="upc-row-<?php echo $post_type; ?>"></span>
							<span><?php echo str_replace( 'upc-pages-list-n','upc-pages-list-'.$n,$dropdown_page ); ?></span>
						</div>
						<div class="upc-pages-<?php echo $post_type; ?> upc-selected-pages" data-id="<?php echo $post_type; ?>" data-post_type="<?php echo $post_type; ?>">
						<?php if( isset( $pages[$post_type] ) && '' !== $pages[$post_type] ){ 
						$ids = explode( ',',$pages[$post_type] );
						foreach( $ids as $id ){
						?>
						<span id="upc-page-<?php echo $post_type.'-'.esc_attr( $id ); ?>" class="upc-page-title" data-id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?><span class="upc-remove-page dashicons dashicons-no-alt" data-id="<?php echo esc_attr( $id ); ?>" data-post_type="<?php echo $post_type; ?>"></span></span>
						<?php } } ?>
						</div>
					</div>
				</td>
			</tr>	
			<?php ++$n; } ?>
		</tbody>
	</table>
	<?php
	eos_upc_save_button( 'options' );
	wp_enqueue_script( 'upc-admin',EOS_UPC_PLUGIN_URL.'/assets/js/upc-admin.js',array(),UPC_VERSION,true );
	wp_localize_script( 'upc-admin','upc_js',array( 'ajax_url' => admin_url( 'admin-ajax.php' ),'page_lists' => json_encode( $page_lists ) ) );	
}

//Return saving button
function eos_upc_save_button( $id ){
	?>
	<div class="eos-up-save-btn-wrp">
		<div>
			<div id="upc-msg-success" class="notice notice-success eos-hidden"><?php esc_html_e( 'Options saved','up-cache' ); ?></div>
			<div id="upc-msg-fail" class="notice eos-hidden"><?php esc_html_e( 'Nothing changed','up-cache' ); ?></div>
			<span id="eos-up-save-<?php echo esc_attr( $id ); ?>" class="eos-up-save-btn button"><?php esc_html_e( 'Save','up-cache' ); ?></span>
		</div>
		<?php wp_nonce_field( '_upc_'.$id.'_nonce','_upc_'.$id.'_nonce' ); ?>
	</div>
	<?php
}

//Return reordered array
function eos_upc_reorder_array( $arr ){
	$return = array();
	foreach( $arr as $arr2 ){
		$return[$arr2[0]] = $arr2[1];
	}
	return $return;
}

add_action( 'save_post', 'eos_upc_update_after_save' );
//Update cache after the post is saved
function eos_upc_update_after_save( $post_id ) {
	$post_type = get_post_type( intval( $post_id ) );
	if( $post_type ){
		$opts = eos_up_get_main_options_array();
		if( $opts ){
			$pages = isset( $opts['pages'] ) ? $opts['pages'] : false;
			if( isset( $opts['autos'] ) ){
				$autos = eos_upc_reorder_array( json_decode( sanitize_text_field( stripslashes( $opts['autos'] ) ),true ) );
				if( isset( $autos[$post_type] ) && $autos[$post_type] ){
					$flushed = eos_up_cache_flush_cache_by_id( intval( $post_id ) );
					$response = wp_remote_get( get_permalink( intval( $post_id ) ) );
				}
			}
			if( $pages ){
				if( isset( $pages[$post_type] ) ){
					$ids = explode( ',',$pages[$post_type] );
					foreach( $ids as $id ){
						$flushed = eos_up_cache_flush_cache_by_id( intval( $id ) );
						$response = wp_remote_get( get_permalink( intval( $id ) ) );						
					}
				}
			}	
		}
		
	}	
}
$plugin_basename = EOS_UPC_PLUGIN_BASE_NAME;
add_filter( "plugin_action_links_$plugin_basename", 'eos_upc_plugin_add_settings_link' );
//It adds a settings link to the action links in the plugins page
function eos_upc_plugin_add_settings_link( $links ){
    $settings_link = '<a class="eos-upc-setts" href="'.admin_url( 'admin.php?page=upc_settings' ).'">' . __( 'Settings','up-cache' ). '</a>';
    array_push( $links, $settings_link );
    $support_link = '<a class="eos-upc-help" href="https://wordpress.org/support/plugin/update-page-cache/" target="_blank" rel="noopener">' . __( 'Support','up-cache' ). '</a>';
    array_push( $links, $support_link );
  	return $links;
}	
