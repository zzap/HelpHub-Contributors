<?php
/**
 * Frontend functionality of the plugin.
 *
 * @package HelpHub_Contributors
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * HelpHub Contributors Public Class
 *
 * The frontend functionality of the plugin.
 *
 * @since 1.0.0
 */
class HelpHub_Contributors_Public {
	/**
	 * Unique ID of plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string  $helphub_contributors Unique ID of plugin.
	 */
	private $helphub_contributors;

	/**
	 * The version of plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string  $version The current version of plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string  $helphub_contributors  The name of the plugin.
	 * @param string  $version               The version of plugin.
	 */
	public function __construct( $helphub_contributors, $version ) {
		$this->helphub_contributors = $helphub_contributors;
		$this->version = $version;
		add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue_scripts' ) );
		add_filter( 'the_content', array( $this, 'show_contributors' ) );
	}

	/**
	 * Enqueue assets for the frontend.
	 *
	 * @since 1.0.0
	 */
	public function public_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( $this->helphub_contributors, plugin_dir_url( __FILE__ ) . 'css/helphub-contributors-public.css', array(), $this->version );
		// Scripts.
		wp_enqueue_script( $this->helphub_contributors, plugin_dir_url( __FILE__ ) . 'js/helphub-contributors-public.js', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Show contributors after post content.
	 * Attached to 'the_content' filter hook.
	 *
	 * @param  string $content  Post content
	 * @return string           Returns post content with appended contributors list
	 */
	public function show_contributors( $content ) {
		$output = $content;

		$meta = get_post_meta( get_the_ID(), 'helphub_contributors' );

		if ( is_array( $meta ) && ! empty( $meta ) ) :

			$contributors = $meta[0];

			if ( is_array( $contributors ) && ! empty( $contributors ) ) :

				$contributors_list_items = '';

				foreach ( $contributors as $contributor ) :
					// Get user object.
					$contributor_object = get_user_by( 'slug', $contributor );

					if ( is_object( $contributor_object ) ) :

						$data = array(
							'user_nicename' => $contributor_object->data->user_nicename,
							'display_name'  => $contributor_object->data->display_name,
							'user_email'    => $contributor_object->data->user_email,
						);

						/**
						 * Filters retrived data.
						 *
						 * @since 1.0.0
						 *
						 * @param array  $data                Array of users data
						 * @param obj    $contributor_object  WP_User Object
						 */
						$data = apply_filters( 'helphub_contributors_user_data', $data, $contributor_object );

						$contributor_url      = 'https://profiles.wordpress.org/' . $data['user_nicename'];
						$contributor_gravatar = '<img src="' . esc_url( get_avatar_url( $data['user_email'] ) ) . '" />';
						$contributor_name     = '<span>' . esc_html( $data['display_name'] ) . '</span>';
						$contributor_username = '&#64;' . esc_html( $data['user_nicename'] );

						// Build the link
						$contributor_link = '<a href="' . esc_url( $contributor_url ) . '">' . $contributor_gravatar . $contributor_name . $contributor_username . '</a>';

						/**
						 * Filters contributor link.
						 *
						 * @since 1.0.0
						 *
						 * @param string $contributor_link      Contributor link markup.
						 * @param string $contributor_url       Contributor wp.org profile URL.
						 * @param string $contributor_gravatar  Contributor gravatar markup.
						 * @param string $contributor_name      Contributor display_name.
						 * @param string $contributor_username  Contributor wp.org username.
						 */
						$contributor_link = apply_filters( 'helphub_contributors_contributor_link', $contributor_link, $contributor_url, $contributor_gravatar, $contributor_name, $contributor_username );

						$contributor_list_item = '<li>' . $contributor_link . '</li>';

						/**
						 * Filters contributor list item.
						 *
						 * @since 1.0.0
						 *
						 * @param string $contributor_list_item  Contributor list item markup.
						 */
						$contributor_list_item = apply_filters( 'helphub_contributors_contributor_item', $contributor_list_item );

						$contributors_list_items .= $contributor_list_item;

					else :

						// Display message if no user is found with provided username.
						$contributors_list_items .= '<li>' . sprintf( __( '%s does not seem to be a valid username.', $this->helphub_contributors ), '<strong>' . $contributor . '</strong>' ) . '</li>';

					endif; // is_object( $contributor_object )

				endforeach; // $contributors as $contributor

				$contributors_heading = '<h5>' . esc_html__( 'Contributors', $this->helphub_contributors ) . '</h5>';
				$contributors_list    = '<ul>' . $contributors_list_items . '</ul>';

				// Build the markup
				$contributors_markup = '<div class="contirbutors-list-wrap">' . $contributors_heading . $contributors_list . '</div>';

				/**
				 * Filters contributors markup.
				 *
				 * @since 1.0.0
				 *
				 * @param string $contributors_markup      Contributors markup.
				 * @param string $contributors_heading     Contributors heading.
				 * @param string $contributors_list        Contributors list markup.
				 * @param string $contributors_list_items  Contributors list items markup, without '<ul>'.
				 */
				$contributors_markup = apply_filters( 'helphub_contributors', $contributors_markup, $contributors_heading, $contributors_list, $contributors_list_items );

				$output .= $contributors_markup;

			endif; // is_array( $contributors ) && ! empty( $contributors )

		endif; // is_array( $meta ) && ! empty( $meta )

		return $output;
	}
}
