<?php

// Define the Tweeps4WP class
class Tweeps4WP_widget {

// can be whatever you want to call it.
private $tweeps4wp_title = "";

// Tweeps4WP only works if you provide your username and password4
private $tweeps4wp_twitter_username = "";
private $tweeps4wp_twitter_password =  "";

// can be "followers" or "friends". defaults to followers.
private $tweeps4wp_group = "";

// because twitter has limitations on how many times you can get status information, we're keeping a cache of the information
private $tweeps4wp_cache =  "";

// time when the cache was last refreshed. this will be a timestamp in seconds. Easy to do calculations.
private $tweeps4wp_lastupdate =  0;

// the update interval. default is 1 hour (3600) with a minimum time of (600)
private $tweeps4wp_updateinterval = 3600;

// the size of the photo to be displayed
private $tweeps4wp_photosize = "";


// function to show the form that will get/set the proper options
public function tweeps4wp_controls() {
	// update the options in the database with the form values
	$this->tweeps4wp_title = (isset($_POST['tweeps4wp_title']) ? $_POST['tweeps4wp_title'] : "");
	$this->tweeps4wp_group = (isset($_POST['tweeps4wp_group']) ? $_POST['tweeps4wp_group'] : "");
	$this->tweeps4wp_twitter_username = (isset($_POST['tweeps4wp_twitter_username']) ? $_POST['tweeps4wp_twitter_username'] : "");
	$this->tweeps4wp_twitter_password = (isset($_POST['tweeps4wp_twitter_password']) ? $_POST['tweeps4wp_twitter_password'] : "");
	$this->tweeps4wp_updateinterval = (isset($_POST['tweeps4wp_updateinterval']) ? $_POST['tweeps4wp_updateinterval'] : "");
	$this->tweeps4wp_photosize = (isset($_POST['tweeps4wp_updateinterval']) ? $_POST['tweeps4wp_photosize'] : "");

	if(function_exists('wp_cache_no_postid')) 
		wp_cache_no_postid(0);

	if ( $this->tweeps4wp_title != null && $this->tweeps4wp_title != "" ) 
		update_option("tweeps4wp_title", stripslashes($this->tweeps4wp_title) );

	if ( $this->tweeps4wp_group != null && $this->tweeps4wp_group != "" )  
		update_option("tweeps4wp_group", $this->tweeps4wp_group);

	if ( $this->tweeps4wp_twitter_username != null && $this->tweeps4wp_group != "" )
		update_option("tweeps4wp_twitter_username", $this->tweeps4wp_twitter_username);

	if ( $this->tweeps4wp_twitter_password != null && $this->tweeps4wp_twitter_password != "" ) 
		update_option("tweeps4wp_twitter_password", $this->tweeps4wp_twitter_password);

	if ( $this->tweeps4wp_updateinterval != null && $this->tweeps4wp_updateinterval != "" && is_numeric($this->tweeps4wp_updateinterval) ) {
		if ( $this->tweeps4wp_updateinterval < 600 ) 
			$this->tweeps4wp_updateinterval = 600;
		update_option("tweeps4wp_updateinterval", $this->tweeps4wp_updateinterval);
	}

	if ( $this->tweeps4wp_photosize != null && $this->tweeps4wp_photosize != "" )
		update_option("tweeps4wp_photosize", $this->tweeps4wp_photosize);

	// since the form was submitted, we set the lastupdate to current time
	update_option("tweeps4wp_lastupdate", time());
	update_option("tweeps4wp_cache", "");

	// load the new values and display form with new values from teh daterbase
	$this->tweeps4wp_set_default_options();

	// show the form
	$this->tweeps4wp_display_form();
}

// function to output the contents of our Dashboard Widget
public function tweeps4wp_showtweeps($args) {
	// Display whatever it is you want to show
	// the other way of doing it but $args is provided by the registration action.
	// variable that will store my output in html format to be displayed
	$output = "";

	// set the default options from the DB.
	$this->tweeps4wp_set_default_options();

	// if the cache is empty or if the cache is expired, create the cache and save it to the DB
	$currenttime = time();

	if ( $currenttime - $this->tweeps4wp_lastupdate >= $this->tweeps4wp_updateinterval || $this->tweeps4wp_cache == "" ) {
		// cache is empty or expired.
		$this->tweeps4wp_update_cache();
	}

	$output .=  $this->tweeps4wp_create_list();

	// 
	extract($args);
	echo $before_widget . $before_title . $this->tweeps4wp_title . $after_title;
	echo $output;	
	echo $after_widget;

}

public function tweeps4wp_create_list() {

	$tweeplist = "";

	if ( $this->tweeps4wp_cache != "ERROR" ) 
		$XML = simplexml_load_string($this->tweeps4wp_cache);
	else 
		return "Not available at this time.";

	//$tweeplist .= "<div style=\"width: 95%; margin: 0 auto\">\n";
	$tweeplist .= "<div style=\"text-align:justify\">";
	foreach ($XML->user as $user) {
		$name = $user->name;
		$screen_name = $user->screen_name;
		$profile_image = $user->profile_image_url;
                if ( $this->tweeps4wp_photosize == "mini" )  {
                        $profile_image = preg_replace('/_normal\.(.+)/', '_mini.\1', $profile_image);
                        $tweeplist .= "<span><a href=\"http://twitter.com/$screen_name\" title=\"$name\"><img src=\"$profile_image\" height=\"24\" width=\"24\"></a> </span>\n";
                } else if ( $this->tweeps4wp_photosize == "normal" ) {
                        $tweeplist .= "<span><a href=\"http://twitter.com/$screen_name\" title=\"$name\"><img src=\"$profile_image\" height=\"48\" width=\"48\"></a> </span>\n";

                }
	}
	$tweeplist .= "<br /><small><a href=\"http://twitter.com/$this->tweeps4wp_twitter_username/" . (( $this->tweeps4wp_group == "friends" ) ? "following" : "followers") . "\">View all</a> | ";
	$tweeplist .= "<a href=\"https://twitter.com/$this->tweeps4wp_twitter_username\">Follow @$this->tweeps4wp_twitter_username</a></small>";
	$tweeplist .= "</div>\n";
	

	return $tweeplist;

}

public function tweeps4wp_update_cache() {

	$this->tweeps4wp_set_default_options();
	// call twitter and get the cache in xml format
	// got this from brandontreb.com. 
        // set the URL to the right api call
	if ( $this->tweeps4wp_group == "friends" ) 
        	$url = "http://twitter.com/statuses/friends.xml";
	else if ( $this->tweeps4wp_group == "followers" )
		$url = "http://twitter.com/statuses/followers.xml";
        // Will store the response we get from Twitter 
        $responseInfo=array(); 
        // Initialize CURL 
        $ch = curl_init($url);
        // Set the username and password in the CURL call 
        curl_setopt($ch, CURLOPT_USERPWD, $this->tweeps4wp_twitter_username.':'.$this->tweeps4wp_twitter_password); 
        // Set some cur flags (not too important) 
        curl_setopt($ch, CURLOPT_VERBOSE, 1); 
        curl_setopt($ch, CURLOPT_NOBODY, 0); 
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        // execute the CURL call 
        $response = curl_exec($ch); 
        // Get information about the response 
        $responseInfo=curl_getinfo($ch); 
        // Close the CURL connection curl_close($ch);
                // Make sure we received a response from Twitter 
        if(intval($responseInfo['http_code'])==200) { 
                // store the cache
		$this->tweeps4wp_cache = $response;
		update_option("tweeps4wp_lastupdate", time());
        } else { 
                // Something went wrong 
                //$this->tweeps4wp_cache = "There was an error. $this->tweeps4wp_twitter_username's $this->tweeps4wp_group list isn't available at this time.";
                $this->tweeps4wp_cache = "ERROR";
        }
	
	update_option("tweeps4wp_cache", $this->tweeps4wp_cache); 
}

// Create the function use in the action hook
public function tweeps4wp_addwidget() {
	wp_register_sidebar_widget("tweeps4wp", "Tweeps 4 WP", array($this, "tweeps4wp_showtweeps"));
	register_widget_control("tweeps4wp", array($this, "tweeps4wp_controls"));
}

public function tweeps4wp_set_default_options() {

// if $admin is set to true, then this function is being called from the dashboard
// if false, then it's being called by a visitor
	// can be whatever you want to call it
	$this->tweeps4wp_title = stripslashes((get_option("tweeps4wp_title") == false) ? "My Tweeps" : get_option("tweeps4wp_title"));

	// Tweeps4WP only works if you provide your username and password4
	$this->tweeps4wp_twitter_username = (get_option("tweeps4wp_twitter_username") == false ) ? "" : get_option("tweeps4wp_twitter_username");

	// Tweeps4WP only works if you provide your username and password4
	$this->tweeps4wp_twitter_password = (get_option("tweeps4wp_twitter_password") == false ) ? "" : get_option("tweeps4wp_twitter_password");

	// can be "followers" or "friends". defaults to followers.
	$this->tweeps4wp_group = strtolower( (get_option("tweeps4wp_group") == false) ? "followers" : get_option("tweeps4wp_group"));

	// the update interval. default is 1 hour (3600) with a minimum time of (600)
	$this->tweeps4wp_updateinterval = (int)(get_option("tweeps4wp_updateinterval") == false ) ? 3600 : get_option("tweeps4wp_updateinterval");

	// because twitter has limitations on how many times you can get status information, we're keeping a cache of the information
	$this->tweeps4wp_cache = (get_option("tweeps4wp_cache") == false ) ? "" : get_option("tweeps4wp_cache");

	// get the last update time
	$this->tweeps4wp_lastupdate = (get_option("tweeps4wp_lastupdate") == false ) ? time() : get_option("tweeps4wp_lastupdate");

	// set the photosize to mini as default
	$this->tweeps4wp_photosize = (get_option("tweeps4wp_photosize") == false ) ? "mini" : get_option("tweeps4wp_photosize");
}

public function tweeps4wp_display_form() {

print<<<END
	<p>
		<label>Title:</label>
		<input class="widefat" type="text" name="tweeps4wp_title" value="$this->tweeps4wp_title">
	</p>
	<p>
		<label>Followers or Friends?</label><br />
END;
		echo '<input type="radio" name="tweeps4wp_group" value="followers" ' . (($this->tweeps4wp_group == "followers") ? 'checked' : '') . '> Followers';
		echo '&nbsp;&nbsp;';
		echo '<input type="radio" name="tweeps4wp_group" value="friends" ' . (($this->tweeps4wp_group == "friends") ? 'checked' : '' ) . '> Friends';
print<<<END
	</p>
	<p>
		<label>Twitter Username:</label>
		<input type="text" name="tweeps4wp_twitter_username" value="$this->tweeps4wp_twitter_username">
	</p>
	<p>
		<label>Twitter Password:</label>
		<input type="password" name="tweeps4wp_twitter_password" value="">
	</p>
	<p>
		<label>Update Interval:</label>
		<input type="text" name="tweeps4wp_updateinterval" value="$this->tweeps4wp_updateinterval" size="5">
	</p>
	<p>
		<label>Photo size:</label><br />
END;
		echo '<input type="radio" name="tweeps4wp_photosize" value="normal" ' . (($this->tweeps4wp_photosize == "normal") ? 'checked' : '') . '> Normal';
		echo '&nbsp;&nbsp;';
		echo '<input type="radio" name="tweeps4wp_photosize" value="mini" ' . (($this->tweeps4wp_photosize == "mini") ? 'checked' : '' ) . '> Mini';
print<<<END
	<p>
		<small>Username and password are needed to access the twitter API. Without this, it will not work. The information is stored in the WP database and no where else.</small>
	</p>

END;
}


}

?>
