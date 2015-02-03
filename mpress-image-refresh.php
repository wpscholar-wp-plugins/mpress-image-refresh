<?php

/**
 * Plugin Name: mPress Image Refresh
 * Description: Show a fresh image on every page load.
 * Plugin URI: http://wpscholar.com/wordpress-plugins/mpress-image-refresh/
 * Author: Micah Wood
 * Author URI: http://wpscholar.com
 * Version: 0.3
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Copyright 2014-2015 by Micah Wood - All rights reserved.
 */

define( 'MPRESS_IMAGE_REFRESH_VERSION', '0.3' );

if ( ! class_exists( 'mPress_Image_Refresh' ) ) {

	/**
	 * Class mPress_Image_Refresh
	 */
	class mPress_Image_Refresh {

		/**
		 * @var mPress_Image_Refresh
		 */
		private static $instance;

		/**
		 * Get class instance
		 *
		 * @return mPress_Image_Refresh
		 */
		public static function get_instance() {
			return self::$instance ? self::$instance : new self();
		}

		/**
		 * Setup plugin
		 */
		private function __construct() {
			self::$instance = $this;
			add_filter( 'widget_text', 'do_shortcode' );
			add_shortcode( 'mpress_image_refresh', array( $this, 'shortcode' ) );
		}

		/**
		 * Shortcode handler
		 *
		 * @param array $atts
		 *
		 * @return bool|string
		 */
		public function shortcode( $atts ) {
			global $post;

			$atts = shortcode_atts(
				array(
					'post_id'    => $post->ID,
					'size'       => 'large',
					'class'      => '',
					'not'        => array(),
					'attachment' => array(),
					'caption'    => false,
				),
				$atts
			);

			if ( ! empty( $atts['attachment'] ) ) {
				$attachment_ids = explode( ',', preg_replace( '#[^0-9,]#', '', $atts['attachment'] ) );
				$image = $this->get_random_image( $attachment_ids );
			} else {
				$exclude = empty( $atts['not'] ) ? array() : explode( ',', preg_replace( '#[^0-9,]#', '', $atts['not'] ) );
				$image = $this->get_random_attached_image( $atts['post_id'], $exclude );
			}

			$image_atts = empty( $atts['class'] ) ? array() : array( 'class' => $atts['class'] );

			$image_html = '';
			if ( $image ) {
				$image_html = wp_get_attachment_image( $image->ID, $atts['size'], false, $image_atts );
				$display_caption = filter_var( $atts['caption'], FILTER_VALIDATE_BOOLEAN );
				if ( $display_caption ) {
					$image_html = "<figure>{$image_html}<figcaption>{$image->post_excerpt}</figcaption></figure>";
				}
			}

			return apply_filters( 'mpress_image_refresh-image_html', $image_html, $image, $atts );
		}

		/**
		 * Get a random image from an array of attachment ids
		 *
		 * @param array $attachment_ids
		 *
		 * @return bool|mixed
		 */
		public function get_random_image( array $attachment_ids ) {
			$args = array(
				'orderby'             => 'rand',
				'ignore_sticky_posts' => true,
				'post__in'            => $attachment_ids,
				'post_mime_type'      => 'image',
				'post_status'         => 'inherit',
				'post_type'           => 'attachment',
				'posts_per_page'      => 1,
			);
			$images = get_posts( $args );

			return is_array( $images ) ? array_shift( $images ) : false;
		}

		/**
		 * Get a random image attached to a specific post
		 *
		 * @param       $post_id
		 * @param array $exclude
		 *
		 * @return bool|mixed
		 */
		public function get_random_attached_image( $post_id, $exclude = array() ) {
			$args = array(
				'orderby'        => 'rand',
				'post_mime_type' => 'image',
				'post_parent'    => $post_id,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'posts_per_page' => 1,
			);
			if ( ! empty( $exclude ) ) {
				$args['post__not_in'] = $exclude;
			}
			$images = get_posts( $args );

			return is_array( $images ) ? array_shift( $images ) : false;
		}

	}

	add_action( 'init', array( 'mPress_Image_Refresh', 'get_instance' ) );

}