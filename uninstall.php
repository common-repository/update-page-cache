<?php
if( !defined( 'WP_UNINSTALL_PLUGIN') ){
    die;
}
delete_site_option( 'eos_up_cache_main' );