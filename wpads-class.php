<?php

/**
* Classes for WPAds plugin
*/

class WPAds_Banners {
	
    var $banners_table;

    /**
    * Constructor
    */
    function __construct() {
        global $table_prefix;
        $this->banners_table = $table_prefix . "ads_banners";
    }

    /**
    * Get all the banners in the database
    */
    function getBanners( ) {
        global $wpdb;

        $sql = "SELECT * FROM " . $this->banners_table . " WHERE 1 ORDER BY banner_id ASC";

        $banners = $wpdb->get_results( $sql );
        for ( $i=0 ; $i<count( $banners ) ; $i++) {
                $banners[$i]->banner_zones = $this->zonesDBToUser( $banners[$i]->banner_zones );
        }
        return $banners;
    }

    /**
    * Convert zones from db format (#zone1#zone2#) to user format (zone1, zone2)
    */
    function zonesDBtoUser( $zones ) {
        $zones = str_replace( "#", ", ", $zones );
        $zones = preg_replace( "|^,\s+|", "", $zones );
        $zones = preg_replace( "|,\s+$|", "", $zones );
        return $zones;
    }

    /**
    * Convert zones from user format (zone1, zone2) to db format (#zone1#zone2#)
    */
    function zonesUserToDB( $zones ) {
        $zones = str_replace( ",", "#", $zones );
        $zones = preg_replace( "|^([^#])|", "#\\1", $zones );
        $zones = preg_replace( "|([^#])$|", "\\1#", $zones );
        $zones = preg_replace( "|[\s]+|", "", $zones );
        return $zones;
    }

    /**
    * Get data for a banner with a given banner_id
    */
    function getBanner( $banner_id ) {
        global $wpdb;
        $sql = $wpdb->prepare( "SELECT * FROM " . $this->banners_table . " WHERE banner_id = %d ", $banner_id );
        $banners = $wpdb->get_results( $sql );

        $banners[0]->banner_zones = $this->zonesDBToUser( $banners[0]->banner_zones );
        return $banners[0];
    }

    /**
    * Update data for a banner
    */
    function updateBanner( $banner ) {
        global $wpdb;

        if ( $banner["banner_active"] != "Y" ) {
            $banner["banner_active"] = "N";
        }
        $banner["banner_zones"] = $this->zonesUserToDB( $banner["banner_zones"] );
        $wpdb->query( 
            $wpdb->prepare(
                "UPDATE " . $this->banners_table . " SET "
                . " banner_description = %s, "
                . " banner_html = '" . $banner["banner_html"] . "', "
                . " banner_zones = %s, "
                . " banner_active = %s, "
                . " banner_weight = %d, "
                . " banner_maxviews = %d "
                . " WHERE banner_id = %d ",
                array(
                    esc_attr( $banner["banner_description"] ),
                    esc_attr( $banner["banner_zones"] ),
                    esc_attr( $banner["banner_active"] ),
                    esc_attr( $banner["banner_weight"] ),
                    esc_attr( $banner["banner_maxviews"] ),
                    esc_attr( $banner["banner_id"] )     
                )   
            )
        );
    }

    /**
    * Add a new banner to the database
    */
    function addBanner( $banner ) {
        global $wpdb;

        if ( $banner["banner_active"] != "Y" ) {
            $banner["banner_active"] = "N";
        }
        $banner["banner_zones"] = $this->zonesUserToDB( $banner["banner_zones"] );
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO " . $this->banners_table . " SET "
            . " banner_description = %s, "
            . " banner_html = '" . $banner["banner_html"] . "', "
            . " banner_zones = %s, "
            . " banner_active = %s, "
            . " banner_weight = %d, "
            . " banner_maxviews = %d ",
            array(
                esc_attr( $banner["banner_description"] ),
                esc_attr( $banner["banner_zones"] ),
                esc_attr( $banner["banner_active"] ),
                absInt( $banner["banner_weight"] ),
                absInt( $banner["banner_maxviews"] )     
            )
        ) );
    }

    /**
    * Delete a banner from the database
    */
    function deleteBanner( $banner_id ) {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM " . $this->banners_table . " WHERE banner_id = %d ",
                $banner_id
            )
        );
    }

    /**
    * Returns a random banner for a given zone
    */
    function getZoneBanner( $the_zone ) {
        global $wpdb;

        $sql = "SELECT * FROM ".$this->banners_table." WHERE 1 "; 
        $sql .= " AND ( banner_active = 'Y' )";
        $sql .= " AND ( (banner_maxviews = 0) OR (banner_views < banner_maxviews) )";
        $sql .= " AND ( banner_zones LIKE '%#".$the_zone."#%' )";

        $banners = $wpdb->get_results( $sql );

        //check if there are any active banners for the zone
        if ( $banners != null ) {
            $weighted_rand = array();
            for ( $i=0 ; $i<count($banners) ; $i++ ) {
                if ( $banners[$i]->banner_weight < 1 ) {
                    $banners[$i]->banner_weight = 1;
                }
                for ( $j=0 ; $j<$banners[$i]->banner_weight ; $j++ ) {
                    $weighted_rand[] = $i;
                }
            }
            $rand_banner = $weighted_rand[ rand( 0, count( $weighted_rand )-1 ) ];
            return $banners[$rand_banner];
        } else {
            return null;
        }
    }

    /**
    * Add a new impression for a banner
    */
    function addView( $banner_id ) {
        global $wpdb;

        $wpdb->query( 
            $wpdb->prepare(
                "UPDATE ".$this->banners_table." SET banner_views = banner_views + 1 WHERE banner_id = %d", 
                $banner_id
            )
        );
    }

    /**
    * Returns an array with all the zones and the banners for each zone (for the options page)
    */
    function getZones( $banners ) {
        $zones = array();
	
        if ( is_array( $banners ) ) {
            foreach ( $banners as $banner ) {
                if ( $banner->banner_active == "Y" ) {
                    $banner_zones = explode( ",", $banner->banner_zones );
                    foreach( $banner_zones as $the_zone ) {
                        $the_zone = trim( $the_zone );
                        $new_zone = 1;
                        for ( $i=0 ; $i<count( $zones ) && $new_zone ; $i++) {
                            if ( $the_zone == $zones[$i]->zone_name ) {
                                $zones[$i]->banners[] = ( object ) get_object_vars( $banner );
                                $zones[$i]->total_weight += $banner->banner_weight;
                                $new_zone = 0;
                            }
                        }
                        if ( $new_zone ) {
                            $temp = new stdClass;
                            $temp->zone_name = $the_zone;
                            $temp->banners = array();
                            $temp->banners[] = ( object ) get_object_vars( $banner );
                            $temp->total_weight = $banner->banner_weight;
                            $zones[] = $temp;
                        }
                    }
                }
            }
        }	
        // update % probability for each banner
        for ( $i=0 ; $i<count( $zones ) ; $i++ ) {
            if ( $zones[$i]->total_weight > 0 ) {
                for ( $j=0 ; $j<count( $zones[$i]->banners ) ; $j++ ) {
                    $zones[$i]->banners[$j]->banner_probability = 100*( $zones[$i]->banners[$j]->banner_weight / $zones[$i]->total_weight );
                }
            }
        }
        return $zones;
    }
}
