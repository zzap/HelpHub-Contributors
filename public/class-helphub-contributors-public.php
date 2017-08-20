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
		$return = $content;
		$meta = get_post_meta( get_the_ID(), 'helphub_contributors' );

		if ( is_array( $meta ) && ! empty( $meta ) ) :

			$contributors = $meta[0];

			if ( is_array( $contributors ) && ! empty( $contributors ) ) :

				$contributors_list = '<div class="contirbutors-list-wrap">';
				$contributors_list .= '<h5>' . esc_html__( 'Contributors', $this->helphub_contributors ) . '</h5>';
				$contributors_list .= '<ul>';

				foreach ( $contributors as $contributor ) :
					$contributor_item = '<li>';
					$contributor_item .= '<a href="https://profiles.wordpress.org/' . $contributor . '">@' . $contributor . '</a>';
					$contributor_item .= '</li>';
					$contributors_list .= apply_filters( 'contributor_list_item', $contributor_item );
				endforeach; // $contributors as $contributor

				$contributors_list .= '</ul>';
				$contributors_list .= '</div><!-- .contirbutors-list-wrap -->';
				$return .= apply_filters( 'contributors_list', $contributors_list );

			endif; // is_array( $contributors ) && ! empty( $contributors )

		endif; // is_array( $meta ) && ! empty( $meta )

		return $return;
	}
}
