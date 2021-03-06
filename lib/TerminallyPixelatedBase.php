<?php
/**
 * Basic setup for the WordPress Theme
 */
class TerminallyPixelatedBase {

	function __construct() {
		$this->add_routes();
		$this->add_support();
		$this->add_menus();
		$this->add_image_sizes();
		$this->add_niceties();
		add_action( 'widgets_init', array( $this, 'add_sidebars' ) );
		add_action( 'init', array( $this, 'remove_crap' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'wp_footer', array( $this, 'google_analytics' ), 100 );
		add_filter( 'style_loader_tag', array( $this, 'tidy_style_tag' ) );
		// add_filter( 'clean_url', array( $this, 'add_require_attributes' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'remove_image_links' ) );
		add_filter( 'timber_context', array( $this, 'timber_context' ) );
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
	}

	private function add_support() {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'menus' );
		add_theme_support( 'html5' );

		// add_theme_support( 'custom-header', array(
		// 	'default-image'          => '',
		// 	'random-default'         => false,
		// 	'width'                  => 0,
		// 	'height'                 => 0,
		// 	'flex-height'            => false,
		// 	'flex-width'             => false,
		// 	'default-text-color'     => '',
		// 	'header-text'            => true,
		// 	'uploads'                => true,
		// 	'wp-head-callback'       => '',
		// 	'admin-head-callback'    => '',
		// 	'admin-preview-callback' => '',
		// );

		// add_theme_support( 'custom-background', array(
		// 	'default-color'          => '',
		// 	'default-image'          => '',
		// 	'wp-head-callback'       => '_custom_background_cb',
		// 	'admin-head-callback'    => '',
		// 	'admin-preview-callback' => ''
		// );

		// add_theme_support( 'post-formats', array( 'note', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

	}

	public function init() {
		add_editor_style( TPHelpers::get_theme_resource_uri( '/editor-style.css' ) );
	}

	private function add_niceties() {
		// Add shortcode support to widgets
		add_filter( 'widget_text', 'do_shortcode' );
	}

	public function excerpt_more( $more ) {
		return '&hellip;';
	}

	public function remove_crap() {
		if (!is_admin()) {
		    remove_action( 'wp_head', 'feed_links_extra'); // Display the links to the extra feeds such as category feeds
		    remove_action( 'wp_head', 'feed_links'); // Display the links to the general feeds: Post and Comment Feed
		    remove_action( 'wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
		    remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
		    remove_action( 'wp_head', 'index_rel_link' ); // index link
		    remove_action( 'wp_head', 'parent_post_rel_link', 10); // prev link
		    remove_action( 'wp_head', 'start_post_rel_link', 10); // start link
		    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10); // Display relational links for the posts adjacent to the current post.
		    remove_action( 'wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
		}
	}

	public function tidy_style_tag( $input ) {
		// Nabbed from the roots theme
		preg_match_all( "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches );
		// Only display media if it is meaningful
		$media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
		return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
	}

	public function remove_image_links() {
		$image_set = get_option( 'image_default_link_type' );

		if ($image_set !== 'none') {
			update_option('image_default_link_type', 'none');
		}
	}

	public static function number_footer_widgets() {
		return 0;
	}

	public function add_styles() {
		if ( !SCRIPT_DEBUG ) {
			wp_enqueue_style( 'style', TPHelpers::get_theme_resource_uri( '/main.css' ), false, 1 );
		} else {
			wp_enqueue_style( 'style', TPHelpers::get_theme_resource_uri( '/style.css' ), false, 0 );
		}

		wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900,300italic', false, 0 );
	}

	public function add_scripts() {
		wp_register_script( 'footable', TPHelpers::get_theme_resource_uri( 'js/vendor/footable/footable.js' ), array( 'jquery' ), false, true );
		wp_register_script( 'footable.sort', TPHelpers::get_theme_resource_uri( 'js/vendor/footable/footable.sort.js' ), array( 'jquery', 'footable' ), false, true );
		wp_register_script( 'footable.filter', TPHelpers::get_theme_resource_uri( 'js/vendor/footable/footable.filter.js' ), array( 'jquery', 'footable' ), false, true );
		wp_register_script( 'footable.paginate', TPHelpers::get_theme_resource_uri( 'js/vendor/footable/footable.paginate.js' ), array( 'jquery', 'footable' ), false, true );
		wp_register_script( 'slideout', TPHelpers::get_theme_resource_uri( 'js/vendor/slideout.js' ), array( 'jquery' ), false, true );
		wp_enqueue_script( 'main', TPHelpers::get_theme_resource_uri( 'js/main.js' ), array( 'jquery', 'slideout', 'footable', 'footable.paginate', 'footable.sort', 'footable.filter' ), false, true );
	}

	function add_require_attributes( $good_protocol_url, $original_url, $_context ) {
		if ( false !== strpos($original_url, 'require.js' ) ) {
			remove_filter( 'clean_url', array( $this, 'add_require_attributes' ), 10, 3 );
			$url_parts = parse_url( $good_protocol_url );
			if ( !SCRIPT_DEBUG ) {
				return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' data-main='" . TPHelpers::get_theme_resource_uri( 'js/main.min' );
			} else {
				return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' data-main='" . TPHelpers::get_theme_resource_uri( 'js/main' );
			}
		}
		return $good_protocol_url;
	}

	public function add_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Homepage Widgets', 'terminally_pixelated' ),
			'id'            => 'home_widgets',
			'class'         => 'home-widgets',
			'description'   => 'The homepage widgets',
			'before_widget' => '<div id="%1$s" class="widget home-widgets__widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h1 class="widgettitle home-widgets__widget__title">',
			'after_title'   => '</h1>'
		) );

		for ( $i=1; $i <= $this->number_footer_widgets(); $i++ ) {
			register_sidebar( array(
				'name'          => __( 'Footer Widgets ' . $i, 'terminally_pixelated' ),
				'id'            => 'footer-widgets-' . $i,
				'class'         => 'footer-widgets-' . $i,
				'description'   => 'Footer widgets set ' . $i,
				'before_widget' => '<div id="%1$s" class="footer-widget widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h1 class="widgettitle footer-widgettitle">',
				'after_title'   => '</h1>'
			) );
		}
	}

	private function add_menus() {
		register_nav_menu( 'main', 'The main site navigation' );
		register_nav_menu( 'footer', 'The footer links' );
	}

	private function add_image_sizes() {
		// add_image_size( 'post-thumbnail', 715, 400, true );
	}

	public function timber_context( $data ) {
		// Add menus
		$data['main_menu'] = new TimberMenu( 'main' );
		$data['footer_menu'] = new TimberMenu( 'footer' );

		// Add sidebar
		// $data['main_sidebar'] = Timber::get_widgets( 'main-sidebar' );

		// Add footer widgets
		if ( $this->number_footer_widgets() ) {
			for ( $i=1; $i <= $this->number_footer_widgets(); $i++ ) {
				$data['footer_widgets'][$i + 1] = Timber::get_widgets( 'footer-widgets-' . $i );
			}
		}

		// Add title
		if ( is_archive() ) {
			$data['title'] = 'Archive';
			if ( is_day() ) {
				$data['title'] = 'Archive: ' . get_the_date( 'D M Y' );
			} else if ( is_month() ) {
				$data['title'] = 'Archive: ' . get_the_date( 'M Y' );
			} else if ( is_year() ) {
				$data['title'] = 'Archive: ' . get_the_date( 'Y' );
			} else if ( is_tag() ) {
				$data['title'] = single_tag_title( '', false );
			} else if ( is_category() ) {
				$data['title'] = single_cat_title( '', false );
			} else if (is_post_type_archive()) {
				$data['title'] = post_type_archive_title( '', false );
			} else if ( is_tax() ) {
				$data['title'] = single_tag_title( '', false );
			}
		} elseif ( is_singular() ) {
			$data['title'] = get_the_title();
		}

		if ( is_single() ) {
			$data['title'] = false;
		}

		// Logo
		if ( $logo = get_theme_mod( 'terminally_pixelated_logo' ) ) {
		    $data['logo'] = new TimberImage( $logo );
		}

		// Icon path
		$data['icon_path'] = TPHelpers::get_theme_resource_uri( '/icons/' );

		// Add breadcrumbs
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			$data['breadcrumbs'] = yoast_breadcrumb('<nav id="breadcrumbs" class="main-breadcrumbs">','</nav>', false );
		}

		return $data;
	}

	public function add_routes() {
		// Timber::add_route( ':var/:var2', function( $params ) {
		// 	$query = array( 'var' => $params['var'], 'var2' => $params['var2'] );
		// 	Timber::load_template( 'index.php', $query );
		// });
	}

	public function google_analytics() {
		if ( $ga_id = get_option( 'terminally_pixelated_googleanalytics' ) ) : ?>
			<script>
	            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
	            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
	            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
	            e.src='//www.google-analytics.com/analytics.js';
	            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
	            ga('create','<?php echo $ga_id; ?>');ga('send','pageview');
	        </script>
		<?php endif;
	}
}