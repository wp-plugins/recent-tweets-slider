<?php
/*
Plugin Name: Recent Tweets Slider
Plugin URI: http://wp-time.com/recent-tweets-slider-plugin/
Description: Display recent tweets in slider or without slider, standrad slider and auto slider, responsive and retina, full customize and unlimited colors, 8 animation effects.
Version: 1.4
Author: Qassim Hassan
Author URI: http://qass.im
License: GPLv2 or later
*/

/*  Copyright 2015  Qassim Hassan  (email : qassim.pay@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// WP Time Page
if( !function_exists('WP_Time_Ghozylab_Aff') ) {
	function WP_Time_Ghozylab_Aff() {
		add_menu_page( 'WP Time', 'WP Time', 'update_core', 'WP_Time_Ghozylab_Aff', 'WP_Time_Ghozylab_Aff_Page');
		function WP_Time_Ghozylab_Aff_Page() {
			?>
            	<div class="wrap">
                	<h2>WP Time</h2>
                    
					<div class="tool-box">
                		<h3 class="title">Thanks for using our plugins!</h3>
                    	<p>For more plugins, please visit <a href="http://wp-time.com" target="_blank">WP Time Website</a> and <a href="https://profiles.wordpress.org/qassimdev/#content-plugins" target="_blank">WP Time profile on WordPress</a>.</p>
                        <p>For contact or support, please visit <a href="http://wp-time.com/contact/" target="_blank">WP Time Contact Page</a>.</p>
					</div>
                    
            	<div class="tool-box">
					<h3 class="title">Recommended Links</h3>
					<p>Get collection of 87 WordPress themes for $69 only, a lot of features and free support! <a href="http://j.mp/ET_WPTime_ref_pl" target="_blank">Get it now</a>.</p>
					<p>See also:</p>
						<ul>
							<li><a href="http://j.mp/GL_WPTime" target="_blank">Must Have Awesome Plugins.</a></li>
							<li><a href="http://j.mp/CM_WPTime" target="_blank">Premium WordPress themes on CreativeMarket.</a></li>
							<li><a href="http://j.mp/TF_WPTime" target="_blank">Premium WordPress themes on Themeforest.</a></li>
							<li><a href="http://j.mp/CC_WPTime" target="_blank">Premium WordPress plugins on Codecanyon.</a></li>
							<li><a href="http://j.mp/BH_WPTime" target="_blank">Unlimited web hosting for $3.95 only.</a></li>
						</ul>
					<p><a href="http://j.mp/GL_WPTime" target="_blank"><img src="<?php echo plugins_url( '/banner/global-aff-img.png', __FILE__ ); ?>" width="728" height="90"></a></p>
					<p><a href="http://j.mp/ET_WPTime_ref_pl" target="_blank"><img src="<?php echo plugins_url( '/banner/570x100.jpg', __FILE__ ); ?>"></a></p>
                    <p><a href="http://j.mp/Avada_WP_Theme" target="_blank"><img src="<?php echo plugins_url( '/banner/avada.jpg', __FILE__ ); ?>"></a></p>
				</div>
                
                </div>
			<?php
		}
	}
	add_action( 'admin_menu', 'WP_Time_Ghozylab_Aff' );
}


// Include JavaScript And CSS
function RecentTweetsSlider_Include_JS_CSS() {
	wp_enqueue_script('RecentTweetsSlider-JS', plugins_url('/js/slider-script.js', __FILE__), array( 'jquery' ), null, false);
	wp_enqueue_style( 'RecentTweetsSlider-ICONS', plugins_url('/css/fontello.css', __FILE__), false, null );
	wp_enqueue_style( 'RecentTweetsSlider-CSS', plugins_url('/css/slider-style.css', __FILE__), false, null );
}
add_action('wp_enqueue_scripts', 'RecentTweetsSlider_Include_JS_CSS');


// Include Twitter API
require_once( plugin_dir_path( __FILE__ ) . '/twitter.class.php' );


// Recent Tweets Slider Widget
class RecentTweetsSliderWidget extends WP_Widget {
	function RecentTweetsSliderWidget() {
		parent::__construct( false, 'Recent Tweets Slider', array('description' => 'Display recent tweets in slider.') );
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$twitter_consumer_key = $instance['twitter_consumer_key'];
		$twitter_consumer_secret = $instance['twitter_consumer_secret'];
		$tweets_number = $instance['tweets_number'];
		$username = $instance['username'];
		$font_color = $instance['font_color'];
		$background_color = $instance['background_color'];
		$height = $instance['height'];
		$animation = $instance['animation'];
		$auto_slider = $instance['auto_slider'];
		$disable_slider = $instance['disable_slider'];
		$time = $instance['time'];
		
		if( empty($tweets_number) ){
			$tweets_number = 6;
		}
		
		if( empty($font_color) ){
			$font_color = '#ffffff';
		}
		
		if( empty($background_color) ){
			$background_color = '#1acae1';
		}
		
		if( empty($height) ){
			$height = '200';
		}
		
		if( $auto_slider ){
			$auto_slider = 'tw_auto_slider';
		}else{
			$auto_slider = 'tw_standard_slider';
		}
		
		if( empty($time) ){
			$time = 3;
		}
		
		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; 
		if( !empty($twitter_consumer_key) and !empty($twitter_consumer_secret) and !empty($username) ){
			
			$credentials = array(
				'consumer_key'    =>  $twitter_consumer_key,
				'consumer_secret' =>  $twitter_consumer_secret
			);
			
			$twitter_api 	  = new WpTwitterApi($credentials);
			$twitter_username =	str_replace('@', '', $username);
			$query = 'screen_name='.$twitter_username.'&count='.$tweets_number;
			
			$twitter_args = array(
				'type' => 'statuses/user_timeline',
				'cache' => ( 3600 ) // default cache is 1 hour, 3600 second = 1 hour.
			);
			
			$result = $twitter_api->query( $query, $twitter_args );
			
			?>
            <?php if( !$disable_slider ) : # if not disabled slider ?>
            	<?php if($auto_slider == 'tw_auto_slider') : ?>
                <script type="text/javascript">
					setInterval(function() { 
						if( !jQuery('#<?php echo $this->id; ?>-auto.tw_auto_slider').is(':hover') ){
							jQuery('#<?php echo $this->id; ?>-auto.tw_auto_slider ul li:first-child')
							.addClass('tw_animated animate_right')
    						.next().addClass('tw_animated animate_right')
    						.end()
    						.appendTo('#<?php echo $this->id; ?>-auto.tw_auto_slider ul');
						}
					}, <?php echo $time; ?>000);
				</script>
                <?php endif; ?>
				<div id="<?php echo $this->id; ?>-auto" class="tw_slider_wrap <?php echo $auto_slider.' '.$animation; ?>" style="background:<?php echo str_replace(';', '', $background_color); ?>;">
                    <i class="tw_slider_icon"><a style="color:<?php echo $font_color; ?>;" href="<?php echo "http://twitter.com/$twitter_username"; ?>" target="_blank" rel="nofollow" title="Follow Me"></a></i>
    				<div class="tw_slider_content" style="height:<?php echo $height; ?>px;">
    					<ul id="tw_slider" class="tw_slider_list">
                        <?php 
							foreach ( $result as $tweet ){
								$emoji_regex = array( // array for emoji icons
									'/[\x{1F600}-\x{1F64F}]/u',
									'/[\x{1F300}-\x{1F5FF}]/u',
									'/[\x{1F680}-\x{1F6FF}]/u',
									'/[\x{2600}-\x{26FF}]/u',
									'/[\x{2700}-\x{27BF}]/u'
								);
								$clean_tweet = preg_replace($emoji_regex, '', $tweet->text); // remove emoji icons from tweets
    							echo '<li><a style="color:'.$font_color.';" href="https://twitter.com/'.$twitter_username.'/status/'.$tweet->id_str.'" rel="nofollow" target="_blank" title="Open tweet">'.$clean_tweet.'</a></li>';
							}//end foreach
						 ?>
                        </ul>
    				</div>
                	<i class="tw_slider_next" style="color:<?php echo $font_color; ?>;"></i>
            		<i class="tw_slider_prev" style="color:<?php echo $font_color; ?>;"></i>
    			</div>
                <?php else : # if disabled slider ?>
                	<ul>
                	<?php 
						foreach ( $result as $tweet ){
							$emoji_regex = array( // array for emoji icons
								'/[\x{1F600}-\x{1F64F}]/u',
								'/[\x{1F300}-\x{1F5FF}]/u',
								'/[\x{1F680}-\x{1F6FF}]/u',
								'/[\x{2600}-\x{26FF}]/u',
								'/[\x{2700}-\x{27BF}]/u'
							);
							$clean_tweet = preg_replace($emoji_regex, '', $tweet->text); // remove emoji icons from tweets
    						echo '<li><a href="https://twitter.com/'.$twitter_username.'/status/'.$tweet->id_str.'" rel="nofollow" target="_blank" title="Open tweet">'.$clean_tweet.'</a></li>';
						}//end foreach
					?>
                    </ul>
                <?php endif; ?>
            <?php
			
		}// end if !empty($twitter_consumer_key)
		echo  $args['after_widget'];
	}//widget
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['twitter_consumer_key'] = strip_tags($new_instance['twitter_consumer_key']);
		$instance['twitter_consumer_secret'] = strip_tags($new_instance['twitter_consumer_secret']);
		$instance['tweets_number'] = strip_tags($new_instance['tweets_number']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['font_color'] = strip_tags($new_instance['font_color']);
		$instance['background_color'] = strip_tags($new_instance['background_color']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['animation'] = strip_tags($new_instance['animation']);
		$instance['auto_slider'] = strip_tags($new_instance['auto_slider']);
		$instance['disable_slider'] = strip_tags($new_instance['disable_slider']);
		$instance['time'] = strip_tags($new_instance['time']);
		return $instance;
	}//update
	
	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance
		);
		
		$defaults = array(
			'title' => 'Recent Tweets',
			'twitter_consumer_key' => '',
			'twitter_consumer_secret' => '',
			'tweets_number' => '6',
			'username' => '',
			'font_color' => '#ffffff',
			'background_color' => '#1acae1',
			'height' => '200',
			'animation' => 'tw_fade_right_and_left',
			'auto_slider' => '',
			'disable_slider' => '',
			'time' => '3'
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$twitter_consumer_key = $instance['twitter_consumer_key'];
		$twitter_consumer_secret = $instance['twitter_consumer_secret'];
		$tweets_number = $instance['tweets_number'];
		$username = $instance['username'];
		$font_color = $instance['font_color'];
		$background_color = $instance['background_color'];
		$height = $instance['height'];
		$animation = $instance['animation'];
		$auto_slider = $instance['auto_slider'];
		$disable_slider = $instance['disable_slider'];
		$time = $instance['time'];
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('twitter_consumer_key'); ?>">Twitter Consumer Key:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('twitter_consumer_key'); ?>" name="<?php echo $this->get_field_name('twitter_consumer_key'); ?>" type="text" value="<?php echo $twitter_consumer_key; ?>" />
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('twitter_consumer_secret'); ?>">Twitter Consumer Secret:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('twitter_consumer_secret'); ?>" name="<?php echo $this->get_field_name('twitter_consumer_secret'); ?>" type="text" value="<?php echo $twitter_consumer_secret; ?>" />
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>">Username:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('tweets_number'); ?>">Tweets Number:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('tweets_number'); ?>" name="<?php echo $this->get_field_name('tweets_number'); ?>" type="text" value="<?php echo $tweets_number; ?>" />
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('font_color'); ?>">Font Color:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('font_color'); ?>" name="<?php echo $this->get_field_name('font_color'); ?>" type="text" value="<?php echo $font_color; ?>" />
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('background_color'); ?>">Background Color:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('background_color'); ?>" name="<?php echo $this->get_field_name('background_color'); ?>" type="text" value="<?php echo $background_color; ?>" />
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('height'); ?>">Wrap Height:</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
			</p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('animation'); ?>">Animation Effect:</label> 
            	<select class="widefat" name="<?php echo $this->get_field_name('animation'); ?>" id="<?php echo $this->get_field_id('animation'); ?>">
            		<option value="tw_fade_right_and_left" <?php if ( $animation == 'tw_fade_right_and_left' ) echo 'selected="selected"'; ?>>Fade Right And Left</option>
                    <option value="tw_fade" <?php if ( $animation == 'tw_fade' ) echo 'selected="selected"'; ?>>Fade</option>
                    <option value="tw_zoomIn" <?php if ( $animation == 'tw_zoomIn' ) echo 'selected="selected"'; ?>>Zoom In</option>
                    <option value="tw_bounce" <?php if ( $animation == 'tw_bounce' ) echo 'selected="selected"'; ?>>Bounce</option>
                    <option value="tw_bounceIn" <?php if ( $animation == 'tw_bounceIn' ) echo 'selected="selected"'; ?>>Bounce In</option>
                    <option value="tw_rubberBand" <?php if ( $animation == 'tw_rubberBand' ) echo 'selected="selected"'; ?>>Rubber Band</option>
                    <option value="tw_flipInX" <?php if ( $animation == 'tw_flipInX' ) echo 'selected="selected"'; ?>>Flip In X</option>
                    <option value="tw_flipInY" <?php if ( $animation == 'tw_flipInY' ) echo 'selected="selected"'; ?>>Flip In Y</option>
            	</select>
            </p>
            
			<p>
				<input class="widefat" id="<?php echo $this->get_field_id('auto_slider'); ?>" name="<?php echo $this->get_field_name('auto_slider'); ?>" type="checkbox" value="1" <?php checked( $auto_slider ); ?>/>
                <label for="<?php echo $this->get_field_id('auto_slider'); ?>">Enable Auto Slider</label> 
			</p>
            
			<p>
				<label for="<?php echo $this->get_field_id('time'); ?>">Auto Slider Time (seconds):</label> 
				<input class="widefat" id="<?php echo $this->get_field_id('time'); ?>" name="<?php echo $this->get_field_name('time'); ?>" type="text" value="<?php echo $time; ?>" />
			</p>
            
			<p>
				<input class="widefat" id="<?php echo $this->get_field_id('disable_slider'); ?>" name="<?php echo $this->get_field_name('disable_slider'); ?>" type="checkbox" value="1" <?php checked( $disable_slider ); ?>/>
                <label for="<?php echo $this->get_field_id('disable_slider'); ?>">Disable Slider (will be display standard list)</label> 
			</p>
            <p>Note: twitter tweets will be refreshed every 1 hour.</p>
        <?php
		
	}//form
	
}
add_action('widgets_init', create_function('', 'return register_widget("RecentTweetsSliderWidget");') );

?>