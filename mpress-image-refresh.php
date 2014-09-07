<?php

/**
 * Plugin Name: mPress Image Refresh
 * Description: Show a fresh image on every page load.
 * Plugin URI: http://wpscholar.com/wordpress-plugins/mpress-image-refresh/
 * Author: Micah Wood
 * Author URI: http://wpscholar.com
 * Version: 0.2
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2012 by Micah Wood - All rights reserved.
 */

define( 'MPRESS_IMAGE_REFRESH_VERSION', '0.2' );

if ( ! class_exists( 'mPress_Image_Refresh' ) ) {

	class mPress_Image_Refresh {

		private static $instance;

		public static function get_instance() {
			return self::$instance ? self::$instance : new self();
		}

		private function __construct() {
			self::$instance = $this;
			add_filter( 'widget_text', 'do_shortcode' );
			add_shortcode( 'mpress_image_refresh', array( $this, 'shortcode' ) );
		}

		public function shortcode( $atts ) {
			global $post;
			$atts = shortcode_atts( array(
				'post_id' => $post->ID,
				'size' => 'large',
				'class' => '',
				'not' => array(),
			), $atts );

			$exclude = empty( $atts['not'] ) ? array() : explode( ',', preg_replace( '#[^0-9,]#', '', $atts['not'] ) );

			$image = $this->get_random_attached_image( $atts['post_id'], $exclude );
			$image_atts = empty( $atts['class'] ) ? array() : array( 'class' => $atts['class'] );

			return $image ? wp_get_attachment_image( $image->ID, $atts['size'], false, $image_atts ) : false;
		}

		public function get_random_attached_image( $post_id, $exclude ) {
			$args = array(
				'post_parent' => $post_id,
				'post_mime_type' => 'image',
				'post_type' => 'attachment',
				'post_status' => 'inherit',
				'posts_per_page' => 1,
				'orderby' => 'rand',
			);
			if( ! empty( $exclude ) )
				$args['post__not_in'] = $exclude;
			$images = get_posts( $args );
			return is_array( $images ) ? array_shift( $images ) : false;
		}

	}

	add_action( 'init', array( 'mPress_Image_Refresh', 'get_instance' ) );

}