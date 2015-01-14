<?php
/**
 * Plugin Name: One Page Layout
 * Description: Create and manage your one-page site's sections via the Customizer.
 * Best with Menu Customizer plugin
 */

class OnePage_Manager {

	/**
	 * Singleton
	 */
	private static $__instance = null;

	/**
	 * Class variables
	 */
	private $sections = array();

	/**
	 * Implement singleton
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! is_a( self::$__instance, __CLASS__ ) ) {
			self::$__instance = new self;
		}
		return self::$__instance;
	}

	/**
	 * Register actions and filters.
	 *
	 * @uses add_action
	 * @return null
	 */
	function __construct() {
		// Make sure to run *after* the theme's run.
		add_action( 'after_setup_theme', array( $this, 'register_menu' ), 99 );
	}

	/**
	 * Grab settings from the current theme.
	 */
	function get_settings() {
		$defaults = array(
			'menu-name'        => __( 'One Page Layout', 'one-page' ),
			//'max_pages'        => 5, // Can we enforce this in the Menu UI?
			'posts-template'   => 'content',
			'custom-format'    => '<div id="%1$s" class="hentry custom"><h1 class="entry-title"><a href="%2$s">%3$s</a></h1> <div class="entry-summary"><p>%4$s</p></div></div>',
			'archive-template' => 'content',
			'archive-before'   => '<div id="%1$s" class="archive %2$s"><h1 class="page-title">%3$s</h1>',
			'archive-after'    => '</div>',
			'archive-count'    => '6',
		);
		$args = get_theme_support( 'one-page-layout' );
		if ( $args && isset( $args[0] ) ) {
			return wp_parse_args( $args[0], $defaults );
		}
		return false;
	}

	/**
	 * Register our menu, if the theme supports it.
	 */
	function register_menu() {
		if ( ! current_theme_supports( 'one-page-layout' ) ) {
			// Bail.
			return;
		}

		$args = $this->get_settings();

		$slug = sanitize_key( $args['menu-name'] );

		// Register the theme-defined menu
		register_nav_menus( array(
			$slug => $args['menu-name']
		) );
	}

	/**
	 * Grab items in our menu, and display them using the templates defined.
	 */
	function do_layout(){
		$args = $this->get_settings();
		if ( ! $args || ! is_array( $args ) ) {
			return;
		}
		$slug = sanitize_key( $args['menu-name'] );
		if ( ! has_nav_menu( $slug ) ) {
			return;
		}

		// Get our menu, grab the referenced IDs, and overwrite the query with a new, these-posts-specific query.
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $slug ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $slug ] );

			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			foreach ( $menu_items as $item ) {
				if ( 'post_type' == $item->type ) {
					$query = new WP_Query( array(
						'p' => $item->object_id,
						'post_type' => $item->object,
					) );
					while ( $query->have_posts() ) {
						$query->the_post();
						get_template_part( $args['posts-template'] );
					}

				} elseif ( 'taxonomy' == $item->type ) {
					$id = $item->object . '-' . $item->object_id;
					printf( $args['archive-before'], $id, $item->type, $item->title );
					$query = new WP_Query( array(
						'tax_query' => array( array(
							'taxonomy' => $item->object,
							'terms' => $item->object_id,
						) ),
						'posts_per_page' => $args['archive-count'],
					) );
					while ( $query->have_posts() ) {
						$query->the_post();
						get_template_part( $args['archive-template'] );
					}
					printf( $args['archive-after'] );

				} elseif ( 'custom' == $item->type ) {
					printf( $args['custom-format'],
						'custom-' . absint( $item->object_id ),
						esc_url( $item->url ),
						$item->title,
						$item->description
					);
				}
			}
		}

		wp_reset_postdata();
	}
}

global $onepage;
$onepage = OnePage_Manager::get_instance();

/**
 * Display our menu as posts/pages/etc
 *
 * @return void
 */
function onepage_layout(){
	global $onepage;
	$onepage->do_layout();
}
