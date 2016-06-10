<?php 

function ds_carousel_check_main_plugin() {
	
    ?>

    <div class="update-nag">
        <p>dotstudioPRO Premium Video plugin is either not installed or is inactive.  The dotstudioPRO Premium Carousel plugin has been deactivated.</p>
    </div>

    <?php

}


function ds_carousel_owl_carousel(){
	
	wp_enqueue_script( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array('jquery') );

	wp_enqueue_style( 'owl-carousel-min', plugin_dir_url( __FILE__ ) . 'css/owl.carousel.min.css' );

	wp_enqueue_style( 'owl-carousel-theme', plugin_dir_url( __FILE__ ) . 'css/owl.theme.default.min.css' );

}

function ds_carousel_instantiate($autoplay = true, $time_to_next_slide = 3, $items_to_display = 3){

	?>
	<script>
		jQuery(function($){
			
			$('.owl-carousel').owlCarousel({
			    items: <?php echo $items_to_display; ?>,
   				nav:true,
			    loop:true,
			    margin:10,
			    autoplay:<?php echo $autoplay ? 'true' : 'false'; ?>,
			    autoplayTimeout:<?php echo $time_to_next_slide; ?>000,
			    autoplayHoverPause:true
			});

			$('.owl-carousel').mouseleave(function(){

				$('.owl-carousel').trigger('play.owl.autoplay',[<?php echo $time_to_next_slide; ?>000]);

			});

		});
	</script>
	<?php 

}

function ds_carousel_html($objects = array(), $autoplay = true, $time_to_next_slide = 3, $items_to_display = 3){

	$carousel = "<div class='owl-carousel owl-theme'>";

	foreach($objects as $o){

		$description = strlen($o->description) > 150 ? substr($o->description, 0, 150)."..." : $o->description;

		$carousel .= "<div>";

		$carousel .= "<a href='".home_url("channels/$o->slug")."'>";

		$carousel .= "<img class='img-responsive' src='$o->poster' />";

		$carousel .= "<div><strong><small>$o->title</small></strong></div>";

		$carousel .= "<div><small>".$description."</small></div>";

		$carousel .= "</a>";

		$carousel .= "</div>";

	}

	$carousel .= "</div>";

	ds_carousel_instantiate($autoplay, $time_to_next_slide, $items_to_display);

	return $carousel;

}

function ds_carousel_build_objects($ids = array()){

	$objs = array();

	foreach($ids as $id){

		$obj = grab_channel_by_id($id);

		$objs[] = $obj[0];

	}

	return $objs;

}

function ds_carousel_display_shortcode( $atts ) {

	$atts = shortcode_atts( array(

		'channels' => '',
		'autoplay' => true,
		'time_to_next_slide' => 3,
		'items_to_display' => 3

	), $atts, 'ds_carousel_display' );

	if(!count($atts['channels']))
		return;

	if(strpos($atts['channels'], ',') !== false){

		$channels = explode( ',', $atts['channels'] );

	} else {

		$channels = array($atts['channels']);

	}

	$objs = ds_carousel_build_objects( $channels );

	return ds_carousel_html( $objs, $atts['autoplay'], $atts['time_to_next_slide'], $atts['items_to_display'] );

}
add_shortcode( 'ds_carousel_display', 'ds_carousel_display_shortcode' );


function grab_channel_by_id($id){
	
	global $ds_curl;
	
	$channel = $ds_curl->curl_command( 'single-channel-by-id', array( 'channel_slug' => str_replace( " ", "", $id ) ) );
	
	return $channel;
	
}

function ds_carousel_local_channels_list(){

	global $wpdb;

	$channel_parent = get_page_by_path("channels");

	$channels = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."posts WHERE post_parent = ".$channel_parent->ID." ORDER BY post_name ASC");

	$channels_list = "";

	foreach($channels as $ch){

		$channels_list .= "<input type='checkbox' name='channel' value='$ch->post_name'> $ch->post_title<br/>";

	}

	return $channels_list;

}