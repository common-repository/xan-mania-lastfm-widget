<?php
/*
Plugin Name: Xan Mania LastFM Widget
Plugin URI: http://xan-mania.de
Version: 1.1 
Description: A plugin for displaying the recently played songs at LastFM in your Wordpress sidebar.
Author: Xan Mania
Author URI: http://xan-webdesign.de
*/

define('MAGPIE_CACHE_ON', 1); //1.7 Cache Bug
define('MAGPIE_CACHE_AGE', 180);
define('MAGPIE_INPUT_ENCODING', 'UTF-8');
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
$lastfm_options['widget_fields']['title'] = array('label'=>'Sidebar Title:', 'type'=>'text', 'default'=>'LastFM');
$lastfm_options['widget_fields']['username'] = array('label'=>'LastFM Username:', 'type'=>'text', 'default'=>'Xanatori');
$lastfm_options['widget_fields']['num'] = array('label'=>'Show # of songs:', 'type'=>'text', 'default'=>'3');

$lastfm_options['prefix'] = 'lastfm';

// Display lastfm messages

function lastfm_messages($username = '', $num = 1, $list = false) {

	echo '<div id="twitwrapper">';

	global $lastfm_options;

	$xml = simplexml_load_file('http://ws.audioscrobbler.com/1.0/user/'.$username.'/recenttracks.rss');

	if ($list) echo '<ul class="lastfm">';

	if ($username == '') {

		if ($list) echo '<li>';

		echo 'RSS feed not configured';

		if ($list) echo '</li>';

	} else {



$out = array();
 
$i = $num;
 
if( !isset($xml->channel[0]->item) ) {
    echo('<li>No LastFM messages.</li>');
}

foreach($xml->channel[0]->item as $item) {
    if( $i-- == 0 ) {
        break;
    }
 
    $out[] = array('title'        => (string) $item->title, 'link'        => (string) $item->link);
}
 
foreach ($out as $value) {
    echo "<li class=\"lastfm-item\">$value[title]<br />(This track on LastFM: <a href=\"$value[link]\">Link</a>)</li>";
}

    }

		if ($list) echo '</ul>';
						echo '<div class="designed" style="font-size:9px; text-align: center;">'; //client can opt in or out for copyright
                $name = "$username"; // and radio button option has been created for the user to opt in or out to display copyright information
                   $url = "http://lastfm.com/user/$username";
                   $output = 'Checkout <a href="' . $url . '">' . $name . '</a>\'s profile on LastFM';
     echo $output;
   echo '</div>';
   echo '</div>';

	}
// end lastfm display
  

// lastfm widget admin options

function widget_lastfm_init() {

	if ( !function_exists('register_sidebar_widget') )

		return;

	$check_options = get_option('widget_lastfm');

  if ($check_options['number']=='') {

    $check_options['number'] = 1;

    update_option('widget_lastfm', $check_options);

  }

	function widget_lastfm($args, $number = 1) {

		global $lastfm_options;		

		// $args is an array of strings

		// the active theme: before_widget, before_title, after_widget,

		// and after_title are the array keys. Default tags: li and h2.

		extract($args);

		// Each widget can store its own options.

		include_once(ABSPATH . WPINC . '/rss.php');

		$options = get_option('widget_lastfm');		

		// fill options with default values

		$item = $options[$number];

		foreach($lastfm_options['widget_fields'] as $key => $field) {

			if (! isset($item[$key])) {

				$item[$key] = $field['default'];

			}

		}		

		$messages = fetch_rss('http://lastfm.com/statuses/user_timeline/'.$item['username'].'.rss');

		// These lines generate our output.

    echo $before_widget . $before_title . ''. $item['title'] . '' . $after_title;

		lastfm_messages($item['username'], $item['num'], true);

		echo $after_widget;

	}

	// This is the function that outputs the form to let the users edit

	// the widget's title.

	    function widget_lastfm_control($number) {

		global $lastfm_options;

		// Get our options and see form submission.

		$options = get_option('widget_lastfm');

		if ( isset($_POST['lastfm-submit']) ) {

		foreach($lastfm_options['widget_fields'] as $key => $field) {

		$options[$number][$key] = $field['default'];

		$field_name = sprintf('%s_%s_%s', $lastfm_options['prefix'], $key, $number);

		if ($field['type'] == 'text') {

	    $options[$number][$key] = strip_tags(stripslashes($_POST[$field_name]));
		

				}
				
		elseif ($field['type'] == 'checkbox') {

		$options[$number][$key] = isset($_POST[$field_name]);
		
				}

			}

			update_option('widget_lastfm', $options);

		}

		foreach($lastfm_options['widget_fields'] as $key => $field) {			

			$field_name = sprintf('%s_%s_%s', $lastfm_options['prefix'], $key, $number);

			$field_checked = '';

			if ($field['type'] == 'text') {

				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);

			} elseif ($field['type'] == 'checkbox') {

				$field_value = 1;

				if (! empty($options[$number][$key])) {

					$field_checked = 'checked="checked"';

				}

			}			

			printf('<p style="text-align:right;" class="lastfm_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',

				$field_name, __($field['label']), $field_name, $field_name, $field['type'], $field_value, $field['type'], $field_checked);

		}

		echo '<input type="hidden" id="lastfm-submit" name="lastfm-submit" value="1" />';

	}

	function widget_lastfm_setup() {

		$options = $newoptions = get_option('widget_lastfm');

		

		if ( isset($_POST['lastfm-number-submit']) ) {

			$number = (int) $_POST['lastfm-number'];

			$newoptions['number'] = $number;

		}

		
		if ( $options != $newoptions ) {

			update_option('widget_lastfm', $newoptions);

			widget_lastfm_register();

		}

	}	

	function widget_lastfm_page() {

		$options = $newoptions = get_option('widget_lastfm');



	}	

	function widget_lastfm_register() {		

		$options = get_option('widget_lastfm');

		$dims = array('width' => 300, 'height' => 300);

		$class = array('classname' => 'widget_lastfm');
    

		for ($i = 1; $i <= 1; $i++) {

			$name = sprintf(__('Xan Mania LastFM Widget'), $i);

			$id = "lastfm-$i"; // Never translate an id

			wp_register_sidebar_widget($id, $name, $i <= $options['number'] ? 'widget_lastfm' : /* unregister */ '', $class, $i);

			wp_register_widget_control($id, $name, $i <= $options['number'] ? 'widget_lastfm_control' : /* unregister */ '', $dims, $i);

		}

		add_action('sidebar_admin_setup', 'widget_lastfm_setup'); //setup

		
		add_action('sidebar_admin_page', 'widget_lastfm_page'); //page

	}

	widget_lastfm_register();
	
}

// Run our code later in case this loads prior to any other plugins.
add_action('widgets_init', 'widget_lastfm_init');

?>