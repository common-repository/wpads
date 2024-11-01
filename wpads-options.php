<?php

class WPAds_Options {
	/**
	* Show the main options menu
	*/
	static function wpads_showMainMenu() {
	    
	    $bannersManager = new WPAds_Banners();
	    $banners = $bannersManager->getBanners();
	    $zones = $bannersManager->getZones( $banners );
	    $options_url = admin_url( 'options-general.php?page=wpads_menu_page' );
	    
	    if( ! defined ( 'ADVADS_VERSION') ) :
		$advads_install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads');

		?><div class="wrap"> 
		    <h2><?php _e( 'WPAds' ); ?></h2>
		    <div class="updated notice notice-success" style="overflow: hidden; padding: 10px; ">
			<img src="<?php echo WPADS_BASE_URL . 'thomas.png'; ?>" alt="Thomas" width="80" height="115" style="float: left; margin-right: 10px; "/>
			<p><?php _e( 'This is a fixed version of WPAds. I updated this plugin to work for existing users.', 'wpads' ); ?></p>
			<p><?php _e( 'There won’t be any new features and updates. I’d suggest new users to use our continuously maintained and updated Advanced Ads ad management plugin.', 'wpads' ); ?></p>
			<p><em>Thomas & Team</em></p>
			<p>
			    <a href="<?php echo $advads_install_url; ?>" class="button button-primary" target=_"blank" onclick="location.href='https://wpadvancedads.com/wpads-alternative/##utm_source=wpads&utm_medium=link&utm_campaign=advads'"><?php _e( 'Install Advanced Ads & see migration instructions', 'wpads' ); ?></a>
			    &nbsp;<span class="dashicons dashicons-external"></span>&nbsp;<strong><a href="https://wpadvancedads.com/wpads-alternative/#utm_source=wpads&utm_medium=link&utm_campaign=advads" target=_"blank"><?php _e( 'Take a look first', 'wpads' ); ?></a></strong>
			</p>
		    </div>
	    <?php endif; ?>

		<h3><?php _e( 'Banners' ); ?> (<a href="<?php echo $options_url; ?>&amp;action=new"><?php _e( 'Add new' ); ?></a>)</h3>
		<?php if ( is_array( $banners ) ) { ?>
		    <table border="0" cellpadding="3" width="100%">
			<tr>
			    <th><?php _e( 'ID' ); ?></th>
			    <th align='left'><?php _e( 'Description' ); ?></th>
			    <th align="left"><?php _e( 'Zones' ); ?></th>
			    <th><?php _e( 'Active' ); ?></th>
			    <th><?php _e( 'Weight' ); ?></th>
			    <th><?php _e( 'Max views' ); ?></th>
			    <th><?php _e( 'Views served' ); ?></th>
			    <th>&nbsp;</th>
			    <th>&nbsp;</th>
			</tr>
			<?php 
			$class = '';
			foreach( $banners as $banner ) { 
			    $class = ( 'alternate' == $class ) ? '' : 'alternate';
			    ?>
			    <tr class='<?php echo $class; ?>'>
				<td><?php echo absint( $banner->banner_id );?></td>
				<td><?php echo htmlspecialchars( $banner->banner_description );?></td>
				<td><?php echo htmlspecialchars( $banner->banner_zones );?></td>
				<td align="center"><?php echo htmlspecialchars( $banner->banner_active );?></td>
				<td align="center"><?php echo absInt( $banner->banner_weight );?></td>
				<td align="center"><?php echo absInt( $banner->banner_maxviews );?></td>
				<td align="center"><?php echo absInt( $banner->banner_views );?></td>
				<td><a href="<?php echo $options_url; ?>&amp;action=edit&amp;id=<?php echo absint( $banner->banner_id );?>" class="edit"><?php _e( 'Edit' ); ?></a></td>
				<td><a href="<?php echo $options_url; ?>&amp;action=delete&amp;id=<?php echo absint( $banner->banner_id );?>" class="delete"><?php _e( 'Delete' ); ?></a></td>
			    </tr>	
			<?php }  ?>
		    </table>

		    <a href="<?php echo $options_url; ?>&amp;action=new"><?php _e( 'Add new banner' ); ?></a><br />
		    <?php } else { ?>
			You have not defined any banners yet. <a href="<?php echo $options_url; ?>&amp;action=new">Add a new banner</a> to begin using WPAds.
		    <?php } ?>

		    <h3><?php _e( 'Zones' ); ?></h3>
		    <?php if ( count( $zones ) > 0 ) { ?>
		    <p>These are the zones you have defined in your banners. Next to each zone you can see all the <b>banners associated with that zone</b>, together with the <b>probability</b> of each banner in that zone. The third and fourth column give you the <b>code</b> you have to copy and paste in your templates or inside your posts, wherever you want the zone to show up</p>
		    <table border="0" cellpadding="3" width="100%">
			<tr>
			    <th align="left" valign="top"><?php _e( 'Zone' ); ?></th>
			    <th align="left"><?php _e( 'Banners' ); ?> (<?php _e( 'Probability' ); ?>)</th>
			    <th align="left" valign="top"><?php _e( 'Code in templates' ); ?></th>
			    <th align="left" valign="top"><?php _e( 'Code in posts' ); ?></th>
			</tr>
			<?php foreach( $zones as $zone ) { 
			    $class = ( 'alternate' == $class ) ? '' : 'alternate';
			    ?>
			    <tr class='<?php echo $class; ?>'>
				<td valign="top"><?php echo htmlspecialchars( $zone->zone_name ); ?></td>
				<td>
				    <?php foreach( $zone->banners as $banner ) { ?>
					<a href="<?php echo $options_url; ?>&amp;action=edit&amp;id=<?php echo absInt( $banner->banner_id );?>">
					    <?php echo htmlspecialchars( $banner->banner_description ); ?> 
					</a> (<?php echo sprintf( "%d", absInt( $banner->banner_probability ) ); ?>% )
					<br/>
				    <?php } ?>
				</td>
				<td valign="top">
				    &lt;?php wpads( '<?php echo htmlspecialchars( $zone->zone_name ); ?>' ); ?&gt;<br />
				</td>
				<td valign="top">
				    &lt;!--wpads#<?php echo htmlspecialchars( $zone->zone_name ); ?>--&gt;
				</td>
			    </tr>
			<?php } ?>
		    </table>
		<?php } else { ?>
		    There are no zones because you have not defined any banners yet.
		<?php } ?>
	    </div>
	    <?php
	} // function showMainMenu


	/**
	* Show the banner edit page
	*/
	static function wpads_showEdit() {
	    
	    $options_url = admin_url( 'options-general.php?page=wpads_menu_page' );

	    if ( isset( $_REQUEST['id'] ) ) {
		$banner_id = $_REQUEST['id'];
	    } else {
		$banner_id = "";
	    }
	    $bannersManager = new WPAds_Banners();
	    $banner = $bannersManager->getBanner( $banner_id );
	    ?>
	    <div class="wrap"> 
		<h2><?php _e( 'WPAds - Edit banner' ); ?></h2> 

		<form name="banner_edit" method="post" action="<?php echo $options_url; ?>">
		    <input type="hidden" name="action" value="edit2" />
		    <input type="hidden" name="banner_id" value="<?php echo $banner->banner_id;?>" />
		    <table cellspacing="3">
			<tr>
			    <td valign="top">ID</td>
			    <td><?php echo $banner->banner_id;?></td>
			</tr>
			<tr>
			    <td valign="top">Description</td>
			    <td>
				<input name="banner_description" type="text" size="50" value="<?php echo htmlentities( $banner->banner_description );?>" /><br />
				Any text that helps you identify this banner
			    </td>
			</tr>
			<tr>
			    <td valign="top">HTML Code</td>
			    <td>
				<textarea name="banner_html" rows="6" cols="80"><?php echo htmlentities( $banner->banner_html );?></textarea><br />
				Copy and paste the HTML code to show the ad ( for example, the Google AdSense code)
			    </td>
			</tr>
			<tr>
			    <td valign="top">Zones</td>
			    <td>
				<input name="banner_zones" type="text" size="50" value="<?php echo $banner->banner_zones;?>" /><br/>
				Enter names of the zones where this banner will show, separated by commas. Example: <em>sidebar1, sidebar2</em>
			    </td>
			</tr>
			<tr>
			    <td valign="top">Active</td>
			    <td>
				<input name="banner_active" type="checkbox" value="Y" <?php echo ( $banner->banner_active == "Y" ? "checked" : "" );?> />
			    </td>
			</tr>
			<tr>
			    <td valign="top">Weight</td>
			    <td>
				<input name="banner_weight" type="text" size="10" value="<?php echo $banner->banner_weight;?>" /><br />
				Sets how much a banner is displayed in relationship with other banners in the same zone. Default: 1
			    </td>
			</tr>
			<tr>
			    <td valign="top">Max views</td>
			    <td>
				<input name="banner_maxviews" type="text" size="10" value="<?php echo $banner->banner_maxviews;?>" /><br />
				Maximum number of times this banner will be shown. Default: 0 (unlimited views)
			    </td>
			</tr>
			<tr>
			    <td>&nbsp;</td>
			    <td><input type="submit" name="submit" value="<?php _e( 'Save' ); ?>" /></td>
			</tr>
		    </table>		
		</form>
	    </div>
	    <?php
	} // function wpads_showEdit

	/**
	* Update a banner, gets the input from the "edit" form
	*/
	static function wpads_updateBanner() {
	    $banner = array();
	    $banner["banner_id"] = $_REQUEST["banner_id"];
	    $banner["banner_description"] = $_REQUEST["banner_description"];
	    $banner["banner_html"] = $_REQUEST["banner_html"];
	    $banner["banner_zones"] = $_REQUEST["banner_zones"];
	    if ( isset( $_REQUEST["banner_active"] ) ) {
		$banner["banner_active"] = $_REQUEST["banner_active"];
	    } else {
		$banner["banner_active"] = 'N';
	    }
	    $banner["banner_weight"] = $_REQUEST["banner_weight"];
	    $banner["banner_maxviews"] = $_REQUEST["banner_maxviews"];
	    if ( get_magic_quotes_gpc() ) {
		foreach( $banner as $key => $value ) {
		    $banner[$key] = stripslashes( $value );
		}
	    } 
	    $bannersManager = new WPAds_Banners();
	    $banners = $bannersManager->updateBanner( $banner );
	    echo '<div id="message" class="updated fade"><p>Banner updated</p></div>';
	}

	/**
	* Show the new banner page
	*/
	static function wpads_showNewBanner() {
	    
	    $options_url = admin_url( 'options-general.php?page=wpads_menu_page' );

	    ?>
	    <div class="wrap"> 
		<h2><?php _e( 'WPAds - New banner' ); ?></h2> 

		<form name="banner_edit" method="post" action="<?php echo $options_url; ?>">
		    <input type="hidden" name="action" value="new2" />
		    <table cellspacing="3">
			<tr>
			    <td valign="top">Description</td>
			    <td>
				<input name="banner_description" type="text" size="50" value="" /><br />
				Any text that helps you identify this banner
			    </td>
			</tr>
			<tr>
			    <td valign="top">HTML Code</td>
			    <td>
				<textarea name="banner_html" rows="6" cols="80"></textarea><br />
				Copy and paste the HTML code to show the ad (for example, the Google AdSense code)
			    </td>
			</tr>
			<tr>
			    <td valign="top">Zones</td>
			    <td>
				<input name="banner_zones" type="text" size="50" value="" /><br/>
				Enter names of the zones where this banner will show, separated by commas. Example: <em>sidebar1, sidebar2</em>
			    </td>
			</tr>
			<tr>
			    <td valign="top">Active</td>
			    <td>
				<input name="banner_active" type="checkbox" value="Y" checked />
			    </td>
			</tr>
			<tr>
			    <td valign="top">Weight</td>
			    <td>
				<input name="banner_weight" type="text" size="10" value="1" /><br />
				Sets how much a banner is displayed in relationship with other banners in the same zone. Default: 1
			    </td>
			</tr>
			<tr>
			    <td valign="top">Max views</td>
			    <td>
				<input name="banner_maxviews" type="text" size="10" value="0" /><br />
				Maximum number of times this banner will be shown. Default: 0 (unlimited views)
			    </td>
			</tr>
			<tr>
			    <td>&nbsp;</td>
			    <td><input type="submit" name="submit" value="<?php _e( 'Save' ); ?>" /></td>
			</tr>
		    </table>		
		</form>
	    </div>
	    <?php
	} // function wpads_showNewBanner

	/**
	* Add a banner, gets the input from the "new banner" form
	*/
	static function wpads_addBanner() {
	    $banner = array();
	    $banner["banner_description"] = $_REQUEST["banner_description"];
	    $banner["banner_html"] = $_REQUEST["banner_html"];
	    $banner["banner_zones"] = $_REQUEST["banner_zones"];
	    if ( isset( $_REQUEST["banner_active"] ) ) {
		$banner["banner_active"] = $_REQUEST["banner_active"];
	    } else {
		$banner["banner_active"] = 'N';
	    }
	    $banner["banner_weight"] = $_REQUEST["banner_weight"];
	    $banner["banner_maxviews"] = $_REQUEST["banner_maxviews"];
	    if ( get_magic_quotes_gpc() ) {
		foreach( $banner as $key => $value ) {
		    $banner[$key] = stripslashes( $value );
		}
	    } 
	    $bannersManager = new WPAds_Banners();
	    $banners = $bannersManager->addBanner( $banner );
	    echo '<div id="message" class="updated fade"><p>Banner added</p></div>';
	}

	/**
	* Delete a banner
	*/
	static function wpads_deleteBanner() {
	    $banner_id = $_REQUEST["id"];
	    if ( $banner_id > 0 ) {
		$bannersManager = new WPAds_Banners();
		$banners = $bannersManager->deleteBanner( $banner_id );
		echo '<div id="message" class="updated fade"><p>Banner deleted</p></div>';
	    }
	}

	/**
	* Check if WPAds is installed
	*/
	static function wpads_checkInstall() {
	    global $wpdb;
	    global $table_prefix;		

	    $version = get_option( 'wpads_version' );
	    if ( $version == "" ) {
		$sql = "CREATE TABLE `{$table_prefix}ads_banners` (
		      `banner_id` bigint(20) NOT NULL auto_increment,
		      `banner_active` char(1) NOT NULL default '',
		      `banner_description` varchar(255) NOT NULL default '',
		      `banner_html` mediumtext NOT NULL,
		      `banner_weight` int(11) NOT NULL default '0',
		      `banner_zones` varchar(255) NOT NULL default '',
		      `banner_maxviews` bigint(20) NOT NULL default '0',
		      `banner_views` bigint(20) NOT NULL default '0',
		      PRIMARY KEY  (`banner_id`),
		      KEY `banner_zones` (`banner_zones`)
		    ) ENGINE=MyISAM;";
		$wpdb->query( $sql );
		update_option( 'wpads_version', '0.3', 'yes' );
	    }
	}
}