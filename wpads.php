<?php
/* 
Plugin Name: WPAds
Version: 0.3.1
Description: Ad management for WordPress
Author: Team webgilde
Author URI: https://webgilde.com
*/ 

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WPADS_BASE_URL', plugin_dir_url( __FILE__ ) );

if ( !class_exists( 'WPAds_Banners' ) ){
    include_once( 'wpads-class.php' );
}

/**
* Print the html code for a random banner of the given zone
*/
function wpads( $the_zone ) {
    print get_wpads( $the_zone );
}

/**
* Get the html code for a random banner of the given zone
*/
function get_wpads( $the_zone ) {
    global $doing_rss;    

    if ( $the_zone == "" ) {
        return;
    }
    // No ads in RSS feeds
    if ( $doing_rss ) {
        return;
    }	
    // are we in wp-admin editing the post? 
    if ( strstr( $_SERVER['PHP_SELF'], 'post.php' ) ) {
        // **TODO**: show placeholders
        return;
    }
    $banners = new WPAds_Banners();
    $theBanner = $banners->getZoneBanner( $the_zone );
    if ( $theBanner != null){
        $banners->addView( $theBanner->banner_id );
        return $theBanner->banner_html;
    } else {
        //return an empty String if there are no active banners in the zone,
        //return $data; may make sense for some users too.
        return '';
    }
}


/**
* Content filter: replaces all ocurrences of
* <!-- wpads#zone_name -->
* in the post content for a random banner for that zone
*/
function wpads_content_filter( $data ) {
    if ( preg_match_all( "|<!--\s*wpads#(.*?)\s*-->|", $data, $matches ) ) {
        for ( $i=0 ; $i<count( $matches[0] ) ; $i++ ) {
            $banner = get_wpads( $matches[1][$i] );
            $data = preg_replace( "|".$matches[0][$i]."|", $banner, $data );
        }
    }
    return $data;
}

// WPAds Menu
add_action( 'admin_menu', 'wpads_menu' );

function wpads_menu() {
    if ( function_exists( 'add_submenu_page' ) ) {
        $hook = add_submenu_page( 'options-general.php', __( 'WPAds' ), __( 'WPAds' ), 'edit_themes', 'wpads_menu_page', 'wpads_menu_page' );
    }
}

function wpads_menu_page(){
    
	// load basic options class
	include_once( 'wpads-options.php' );
	
	WPAds_Options::wpads_checkInstall();

	if ( isset( $_REQUEST['action'] ) ) {
	    $action = $_REQUEST['action'];
	} else {
	    $action = "";
	}

	switch( $action ) {
	    case 'edit':
		    WPAds_Options::wpads_showEdit();
		    break;
	    case 'edit2';
		    WPAds_Options::wpads_updateBanner();
		    WPAds_Options::wpads_showMainMenu();
		    break;
	    case 'new':
		    WPAds_Options::wpads_showNewBanner();
		    break;
	    case 'new2':
		    WPAds_Options::wpads_addBanner();
		    WPAds_Options::wpads_showMainMenu();
		    break;
	    case 'delete';
		    WPAds_Options::wpads_deleteBanner();
		    WPAds_Options::wpads_showMainMenu();
		    break;
	    default:
		    WPAds_Options::wpads_showMainMenu();
	}
}


if ( function_exists( 'add_filter' ) ) {
    add_filter( 'the_content', 'wpads_content_filter' ); 
}