<?php

/*
 * Plugin Name: Image Refresh
 * Description: Show a fresh image on every page load.
 * Plugin URI: http://wpscholar.com/wordpress-plugins/mpress-image-refresh/
 * Version: 2.1.1
 * Author: Micah Wood
 * Author URI: http://wpscholar.com
 * Requires at least: 4.5
 * Requires PHP: 5.3
 * Text Domain: mpress-image-refresh
 * Domain Path: languages
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2014-2021 by Micah Wood - All rights reserved.
 */

if ( ! class_exists( 'mPress_Image_Refresh' ) ) {

	/**
	 * Class mPress_Image_Refresh
	 */
	class mPress_Image_Refresh {

		/**
		 * The shortcode name.
		 *
		 * @var string
		 */
		const SHORTCODE = 'mpress_image_refresh';

		/**
		 * The meta key where the URL is stored for images.
		 *
		 * @var string
		 */
		const META_KEY = 'image_refresh_link_url';

		/**
		 * Initialize the plugin.
		 */
		public static function initialize() {
			load_plugin_textdomain( 'mpress-image-refresh', false, dirname( __FILE__ ) . '/languages' );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
			add_filter( 'attachment_fields_to_edit', array( __CLASS__, 'add_external_url_field' ), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( __CLASS__, 'save_external_url_field' ), 10, 2 );
			add_filter( self::SHORTCODE . '-markup', array( __CLASS__, 'add_link_markup' ), 10, 2 );
			add_filter( 'widget_text', 'do_shortcode' );
			add_shortcode( self::SHORTCODE, array( __CLASS__, 'shortcode' ) );
		}

		/**
		 * Add the external URL field to the attachment fields.
		 *
		 * @param array   $img_fields Array of image fields.
		 * @param WP_Post $post       Post object.
		 *
		 * @return array
		 */
		public static function add_external_url_field( $img_fields, WP_Post $post ) {
			$url_field                    = get_post_meta( $post->ID, self::META_KEY, true );
			$img_fields[ self::META_KEY ] =
				[
					'label' => __( 'Image Refresh Link URL', 'mpress-image-refresh' ),
					'input' => 'url',
					'value' => $url_field,
				];

			return $img_fields;

		}

		/**
		 * Save the external URL field.
		 *
		 * @param array $post       Post data.
		 * @param array $attachment Attachment data.
		 *
		 * @return array
		 */
		public static function save_external_url_field( $post, $attachment ) {
			if ( isset( $attachment[ self::META_KEY ] ) ) {
				update_post_meta( $post['ID'], self::META_KEY, sanitize_text_field( $attachment[ self::META_KEY ] ) );
			} else {
				delete_post_meta( $post['ID'], self::META_KEY );
			}

			return $post;
		}

		/**
		 * Add the link markup to the image.
		 *
		 * @param string  $markup     Image markup.
		 * @param WP_Post $attachment Attachement object.
		 *
		 * @return string
		 */
		public static function add_link_markup( $markup, WP_Post $attachment ) {
			$url = get_post_meta( $attachment->ID, self::META_KEY, true );

			if ( ! empty( $url ) ) {
				$url_domain  = parse_url( $url, PHP_URL_HOST );
				$home_domain = parse_url( home_url(), PHP_URL_HOST );
				$target      = '_self';

				if ( $home_domain !== $url_domain && ! empty ( $url_domain ) ) {
					$target = '_blank';
				}

				$markup = str_replace(
					'%1$s',
					'<a href="' . esc_url( $url ) . '"  target="' . esc_attr( $target ) . '">%1$s</a>',
					$markup
				);
			}

			return $markup;
		}

		/**
		 * Enqueue the scripts.
		 */
		public static function wp_enqueue_scripts() {
			wp_register_style( self::SHORTCODE, plugins_url( '/assets/mpress-image-refresh.css', __FILE__ ) );
		}

		/**
		 * Shortcode handler
		 *
		 * @param array $atts
		 *
		 * @return bool|string
		 */
		public static function shortcode( $atts ) {

			$output = '';

			$atts = shortcode_atts(
				array(
					'attachment'     => '', // Alias of 'attachment_ids'
					'attachments'    => '', // Alias of 'attachment_ids'
					'attachment_ids' => '',
					'caption'        => 'false',
					'class'          => '',
					'exclude'        => '',
					'not'            => '', // Alias of 'exclude'
					'post_id'        => get_the_ID(),
					'size'           => 'large',
					'source'         => 'post',
				),
				$atts,
				self::SHORTCODE
			);

			// Allow 'attachment' to be an alias for 'attachment_ids'
			if ( ! empty( $atts['attachment'] ) ) {
				$atts['attachment_ids'] = $atts['attachment'];
			}
			unset( $atts['attachment'] );

			// Allow 'attachments' to be an alias for 'attachment_ids'
			if ( ! empty( $atts['attachments'] ) ) {
				$atts['attachment_ids'] = $atts['attachments'];
			}
			unset( $atts['attachments'] );

			// Allow 'not' to be an alias for 'exclude'
			if ( ! empty( $atts['not'] ) ) {
				$atts['exclude'] = $atts['not'];
			}
			unset( $atts['not'] );

			// Enforce proper data types for all attributes (that aren't strings)
			$atts['post_id']        = absint( $atts['post_id'] );
			$atts['exclude']        = self::parse_id_list( $atts['exclude'] );
			$atts['attachment_ids'] = self::parse_id_list( $atts['attachment_ids'] );
			$atts['caption']        = filter_var( $atts['caption'], FILTER_VALIDATE_BOOLEAN );

			$post = get_post( $atts['post_id'] );

			if ( $atts['post_id'] && ! $post ) {

				// If the user can edit this post, let them know they provided an invalid post ID
				if ( current_user_can( 'edit_post', get_the_ID() ) ) {
					$output = self::error(
						sprintf( __( 'Sorry, post ID "%d" is invalid. Please check your shortcode implementation.', 'mpress-image-refresh' ), $atts['post_id'] ),
						'[' . self::SHORTCODE . ' post_id="' . $atts['post_id'] . '"]'
					);
				}

				return $output;
			}

			// If there are no requested attachment IDs, fetch attached images from the post
			if ( empty( $atts['attachment_ids'] ) ) {
				if ( 'post' === $atts['source'] ) {
					$atts['attachment_ids'] = array_map( 'absint', wp_list_pluck( get_attached_media( 'image', $post ), 'ID' ) );
				} else {
					$query                  = new WP_Query( array(
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'post_status'    => 'inherit',
						'posts_per_page' => 100,
						'fields'         => 'ids',
					) );
					$atts['attachment_ids'] = $query->posts;
				}
			}

			// Remove excluded attachment IDs
			$atts['attachment_ids'] = array_diff( $atts['attachment_ids'], $atts['exclude'] );

			// Check if we have any attachment IDs
			if ( empty( $atts['attachment_ids'] ) ) {

				// If the user can edit this post, let them know they forgot to attach an image to the post or have excluded all available attachment IDs
				if ( current_user_can( 'edit_post', get_the_ID() ) ) {
					$output = self::error(
						__( 'Sorry, it looks like you forgot to attach an image to the post or have excluded all possible attachment IDs.', 'mpress-image-refresh' ),
						sprintf( __( 'Please check your %s shortcode implementation.', 'mpress-image-refresh' ), '[' . self::SHORTCODE . ']' )
					);
				}

				return $output;
			}

			// Select a random attachment ID
			$attachment_id = $atts['attachment_ids'][ array_rand( $atts['attachment_ids'] ) ];

			/**
			 * Filter the attachment ID selected for display.
			 *
			 * @param int   $attachment_id The attachment ID
			 * @param array $atts          Parsed shortcode attributes
			 */
			$attachment_id = apply_filters( self::SHORTCODE . '-attachment_id', $attachment_id, $atts );

			if ( ! wp_attachment_is_image( $attachment_id ) ) {

				// If the user can edit this post, let them know they provided an invalid image ID
				if ( current_user_can( 'edit_post', get_the_ID() ) ) {
					$output = self::error(
						sprintf( __( 'Sorry, attachment ID "%d" is invalid. Please check your shortcode implementation.', 'mpress-image-refresh' ), $attachment_id ),
						'[' . self::SHORTCODE . ' attachment="' . join( ',', $atts['attachment_ids'] ) . '"]'
					);
				}

				return $output;
			}

			$attachment = get_post( $attachment_id );

			$image_atts = empty( $atts['class'] ) ? array() : array( 'class' => $atts['class'] );

			/**
			 * Filter the image attributes.
			 *
			 * @param array   $image_atts The attributes for the image.
			 * @param WP_Post $attachment The attachment post object.
			 * @param array   $atts       Parsed shortcode attributes.
			 */
			$image_atts = apply_filters( self::SHORTCODE . '-image_atts', $image_atts, $attachment, $atts );

			// Check if we have a valid image size
			$image_sizes   = get_intermediate_image_sizes();
			$image_sizes[] = 'full'; // Allow a full size image to be used.

			if ( ! in_array( $atts['size'], $image_sizes ) ) {

				// If the user can edit this post, let them know they provided an invalid image size
				if ( current_user_can( 'edit_post', get_the_ID() ) ) {
					$output = self::error(
						sprintf( __( 'Sorry, image size "%s" is invalid. Defaulting to "%s" image size. Please check your shortcode implementation.', 'mpress-image-refresh' ), $atts['size'], 'large' ),
						'[' . self::SHORTCODE . ' size="' . $atts['size'] . '"]'
					);
				}

				// Ensure that we have a valid image size
				$atts['size'] = 'large';
			}

			// Setup markup
			$markup = '<figure>%1$s</figure>';
			if ( $atts['caption'] ) {
				$markup = '<figure>%1$s<figcaption>%2$s</figcaption></figure>';
			}

			/**
			 * Filter the markup surrounding the image.
			 *
			 * @param string  $markup     The sprintf formatted string.
			 * @param WP_Post $attachment The attachment post object.
			 * @param array   $atts       Parsed shortcode attributes.
			 */
			$markup = apply_filters( self::SHORTCODE . '-markup', $markup, $attachment, $atts );

			$output .= sprintf(
				$markup,
				wp_get_attachment_image( $attachment->ID, $atts['size'], false, $image_atts ),
				get_the_excerpt( $attachment->ID )
			);

			return $output;
		}

		/**
		 * Parse an ID list into an array.
		 *
		 * @param string $list
		 *
		 * @return int[]
		 */
		public static function parse_id_list( $list ) {
			$ids = array();
			if ( ! empty( $list ) ) {
				$ids = array_filter( array_map( 'absint', explode( ',', preg_replace( '#[^0-9,]#', '', $list ) ) ) );
			}

			return $ids;
		}

		/**
		 * Setup error message.
		 *
		 * @param string $message
		 *
		 * @return string
		 */
		public static function error( $message, $example ) {

			wp_enqueue_style( self::SHORTCODE );

			return sprintf(
				'<div class="mpress-image-refresh-error"><p>%s</p><p>%s</p><p>%s</p></div>',
				esc_html( $message ),
				esc_html( $example ),
				esc_html( 'Note: This helpful notification is only visible to logged in users who can edit this shortcode.' )
			);
		}

	}

	mPress_Image_Refresh::initialize();

}
