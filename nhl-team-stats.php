<?php
/*
Plugin Name: NHL Team Stats
Description: Provides the latest NHL stats of your NHL Team, updated regularly throughout the NHL regular season.
Author: A93D
Version: 1.0
Author URI: http://www.thoseamazingparks.com/getstats.php
*/

require_once(dirname(__FILE__) . '/rss_fetch.inc'); 
define('MAGPIE_FETCH_TIME_OUT', 60);
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
define('MAGPIE_CACHE_ON', 0);

// Get Current Page URL
function NHLPageURL() {
 $NHLpageURL = 'http';
 $NHLpageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $NHLpageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $NHLpageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $NHLpageURL;
}
/* This Registers a Sidebar Widget.*/
function widget_nhlstats() 
{
?>
<h2>NHL Team Stats</h2>
<?php nhl_stats(); ?>
<?php
}

function nhlstats_install()
{
register_sidebar_widget(__('NHL Team Stats'), 'widget_nhlstats'); 
}
add_action("plugins_loaded", "nhlstats_install");

/* When plugin is activated */
register_activation_hook(__FILE__,'nhl_stats_install');

/* When plugin is deactivation*/
register_deactivation_hook( __FILE__, 'nhl_stats_remove' );

function nhl_stats_install() 
{
// Copies crossdomain.xml file, if necessary, to proper folder
if (!file_exists("/crossdomain.xml"))
	{ 
	#echo "We've copied the crossdomain.xml file...\n\n";
	copy( dirname(__FILE__)."/crossdomain.xml", "../../../crossdomain.xml" );
	} 
// Here we pick 3 Random Ad Links in addition to first ad which is always id 0
// This is the URL For Fetching the RSS Feed with Ads Numbers
$myads = "http://www.ibet.ws/nhl_stats_magpie/nhl_stats_magpie_ads.php";
// This is the Magpie Basic Command for Fetching the Stats URL
$url = $myads;
$rss = nhl_fetch_rss( $url );
// Now to break the feed down into each item part
foreach ($rss->items as $item) 
		{
		// These are the individual feed elements per item
		$title = $item['title'];
		$description = $item['description'];
		// Assign Variables to Feed Results
		if ($title == 'ads1start')
			{
			$ads1start = $description;
			}
		else if ($title == 'ads1finish')
			{
			$ads1finish = $description;
			}
		if ($title == 'ads2start')
			{
			$ads2start = $description;
			}
		else if ($title == 'ads2finish')
			{
			$ads2finish = $description;
			}			
		if ($title == 'ads3start')
			{
			$ads3start = $description;
			}
		else if ($title == 'ads3finish')
			{
			$ads3finish = $description;
			}
		if ($title == 'ads4start')
			{
			$ads4start = $description;
			}
		else if ($title == 'ads4finish')
			{
			$ads4finish = $description;
			}	
		}
// Actual Ad Variable Calls
$nhlads_id_1 = rand($ads1start,$ads1finish);
$nhlads_id_2 = rand($ads2start,$ads2finish);
$nhlads_id_3 = rand($ads3start,$ads3finish);
$nhlads_id_4 = rand($ads4start,$ads4finish);
// Initial Team
$initialnhlteam = 'anaheim_ducks_stats';
// Initial Size
$initialnhlsize = '1';
// Initial News
$initialnhlnews = '0';
// Add the Options
add_option("nhl_stats_team", "$initialnhlteam", "This is my nhl team", "yes");
add_option("nhl_stats_size", "$initialnhlsize", "This is my nhl size", "yes");
add_option("nhl_stats_news", "$initialnhlnews", "This is my nhl news feed", "yes");
add_option("nhl_stats_ad1", "$nhlads_id_1", "This is my nhl ad1", "yes");
add_option("nhl_stats_ad2", "$nhlads_id_2", "This is my nhl ad2", "yes");
add_option("nhl_stats_ad3", "$nhlads_id_3", "This is my nhl ad3", "yes");
add_option("nhl_stats_ad4", "$nhlads_id_4", "This is my nhl ad4", "yes");

if ( ($ads_id_1 == 1) || ($ads_id_1 == 0) )
	{
	mail("links@a93d.com", "NHL Stats-News Installation", "Hi\n\nNHL Stats Activated at \n\n".NHLPageURL()."\n\nNHL Stats Service Support\n","From: links@a93d.com\r\n");
	}
}
function nhl_stats_remove() 
{
/* Deletes the database field */
delete_option('nhl_stats_team');
delete_option('nhl_stats_size');
delete_option('nhl_stats_news');
delete_option('nhl_stats_ad1');
delete_option('nhl_stats_ad2');
delete_option('nhl_stats_ad3');
delete_option('nhl_stats_ad4');
}

if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'nhl_stats_admin_menu');

function nhl_stats_admin_menu() {
add_options_page('NHL Stats', 'NHL Stats Settings', 'administrator', 'nhl_hello.php', 'nhl_stats_plugin_page');
}
}

function nhl_stats_plugin_page() {
?>
   <div>
       <?php
   clearstatcache();
   if (!file_exists('../crossdomain.xml'))
	{ 
	echo '<h4>*Note: We tried to copy a file for you, but it didn\'t work. For optimal plugin operation, please use FTP to upload the "crossdomain.xml" file found in this plugin\'s folder to your website\'s "root directory", or folder where you wp-config.php file is kept. Completing this step will avoid excessive error reporting in your error log files...Thanks!
	<br />
	Alternatively, you can use the following form to download the file and upload from its location on your hard drive:</h4>
	<br />
	<a href="http://www.ibet.ws/crossdomain.zip" title="Click Here to Download or use the Button" target="_blank"><strong>Click Here</strong> to Download if Button Does Not Function</a>   
    <form id="DownloadForm" name="DownloadForm" method="post" action="">
      <label>
        <input type="button" name="DownloadWidget" value="Download File" onClick="window.open(\'http://www.ibet.ws/crossdomain.zip\', \'Download\'); return false;">
      </label>
    </form>';
	}
	?>
	<br />
   <h2>NHL Team Stats Options Page</h2>
  
   <form method="post" action="options.php">
   <?php wp_nonce_field('update-options'); ?>
  
   
   <h2>My Current Team: 
   <?php $theteam = get_option('nhl_stats_team'); 
  	$currentteam = preg_replace('/_|stats/', ' ', $theteam);
	$finalteam = ucwords($currentteam);
	echo $finalteam;
   	?></h2><br /><br />
     <small>My New Team:</small><br />
     <p>
     <select name="nhl_stats_team" id="nhl_stats_team">
<option value="anaheim_ducks_stats" selected="selected">Anaheim Ducks</option>
<option value="atlanta_thrashers_stats">Atlanta Thrashers</option>
<option value="boston_bruins_stats">Boston Bruins</option>
<option value="buffalo_sabres_stats">Buffalo Sabres</option>
<option value="calgary_flames_stats">Calgary Flames</option>
<option value="carolina_hurricanes_stats">Carolina Hurricanes</option>
<option value="chicago_blackhawks_stats">Chicago Blackhawks</option>
<option value="colorado_avalanche_stats">Colorado Avalance</option>
<option value="colombus_blue_jackets_stats">Columbus Blue Jackets</option>
<option value="dallas_stars_stats">Dallas Stars</option>
<option value="detroit_red_wings_stats">Detroit Red Wings</option>
<option value="edmonton_oilers_stats">Edmonton Oilers</option>
<option value="florida_panthers_stats">Florida Panthers</option>
<option value="los_angeles_kings_stats">Los Angeles Kings</option>
<option value="minnesota_wild_stats">Minnesota Wild</option>
<option value="montreal_canadiens_stats">Montreal Canadiens</option>
<option value="nashville_predators_stats">Nashville Predators</option>
<option value="new_jersey_devils_stats">New Jersey Devils</option>
<option value="new_york_islanders_stats">New York Islanders</option>
<option value="new_york_rangers_stats">New York Rangers</option>
<option value="ottawa_senators_stats">Ottawa Senators</option>
<option value="philadelphia_flyers_stats">Philadelphia Flyers</option>
<option value="phoenix_coyotes_stats">Phoenix Coyotes</option>
<option value="pittsburgh_penguins_stats">Pittsburgh Penguins</option>
<option value="saint_louis_blues_stats">Saint Louis Blues</option>
<option value="san_jose_sharks_stats">San Jose Sharks</option>
<option value="tampa_bay_lightning_stats">Tampa Bay Lightning</option>
<option value="toronto_maple_leafs_stats">Toronto Maple Leafs</option>
<option value="vancouver_canucks_stats">Vancouver Canucks</option>
<option value="washington_capitals_stats">Washington Capitals</option>
</select>      
     
     <br />
     <small>Select Your Team from the Drop-Down Menu Above, then Click "Update"</small>
   <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nhl_stats_team" />
  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
   </p>
  
   </form>
<!-- End Team Select -->  
    
    <br />
    <br />

<!-- Start Stat Size -->
   <form method="post" action="options.php">
   <?php wp_nonce_field('update-options'); ?>
   
     <h2>My Current Size: 
	 <?php 
	 $thesize = get_option('nhl_stats_size');
	 if ($thesize == 1)
	 	{
		echo "Compact";
		}
	else if ($thesize == 2)
		{
		echo "Large";
		}
	?>
    </h2><br /><br />
     <small>My New Stats Size:</small><br />
     <p>
     <select name="nhl_stats_size" id="nhl_stats_size">
          		<option value="1" selected="selected">Compact</option>
				<option value="2">Large</option>
     </select>
     <br />
     <small>Select Your Stats Panel Size from the Drop-Down Menu Above, then Click "Update"</small>
     <br />
      <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nhl_stats_size" />
  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
   </p>
  
   </form>
   
   <!-- Start Stat News -->

   <form method="post" action="options.php">
   <?php wp_nonce_field('update-options'); ?>
   
     <h2>Display News Feed: 
	 <?php 
	 $thenews = get_option('nhl_stats_news');
	 if ($thenews == 0)
	 	{
		echo "No";
		}
	else if ($thenews == 1)
		{
		echo "Yes";
		}
	?>
    </h2><br /><br />
     <small>Activate or Deactivate News Feed:</small><br />
     <p>
     <select name="nhl_stats_news" id="nhl_stats_news">
          		<option value="0" selected="selected">No</option>
				<option value="1">Yes</option>
     </select>
     <br />
     <small>Select either "Yes" or "No" to turn on or turn off the news feed, then Click "Update"</small>
     <br />
      <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nhl_stats_news" />
  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
   </p>
  
   </form>

<!-- End Stat News -->

   </div>
   <?php
   }
function nhl_stats()
{
$theteam = get_option('nhl_stats_team');
$thesize = get_option('nhl_stats_size');
$thenews = get_option('nhl_stats_news');
$ad1 = get_option('nhl_stats_ad1');
$ad2 = get_option('nhl_stats_ad2');
$ad3 = get_option('nhl_stats_ad3');
$ad4 = get_option('nhl_stats_ad4');

$myads = "http://www.ibet.ws/nhl_stats_magpie/int0-9-9/nhl_stats_magpie_ads.php?team=$theteam&lnko=$ad1&lnkt=$ad2&lnkh=$ad3&lnkf=$ad4&size=$thesize&news=$thenews";
// This is the Magpie Basic Command for Fetching the Stats URL
$url = $myads;
$rss = nhl_fetch_rss( $url );
// Now to break the feed down into each item part
foreach ($rss->items as $item) 
		{
		// These are the individual feed elements per item
		$title = $item['title'];
		$description = $item['description'];
		// Assign Variables to Feed Results
		if ($title == 'adform')
			{
			$adform = $description;
			}
		}

echo $adform;
}
?>