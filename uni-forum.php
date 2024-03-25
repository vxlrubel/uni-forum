<?php
/*
 * Plugin Name:       Uni Forum
 * Plugin URI:        https://github.com/vxlrubel/uni-forum
 * Description:       Uni Forum is a comprehensive forum management plugin for WordPress, designed to empower website owners to effortlessly create and manage online communities. With Uni Forum, users can register on your site, initiate discussions, and seamlessly interact with others in a user-friendly environment.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rubel Mahmud ( Sujan )
 * Author URI:        https://www.linkedin.com/in/vxlrubel/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       uni-forum
 * Domain Path:       /lang
 */

//  directly access denied
 defined('ABSPATH') || exit;

 if ( file_exists( dirname(__FILE__) . '/inc/autoload.php' ) ){
    require_once dirname(__FILE__) . '/inc/autoload.php';
 }

 final class Uni_Forum{

    // instance of the class
    private static $instance;

    // plugin version
    private static $version = '1.0';

    public function __construct(){

        // define constant
        $this->constants();
        // register text domain
        add_action( 'plugins_loaded', [ $this, 'register_text_domain' ] );

        // register custom post type called forum
        add_action( 'init', [ $this, 'register_forum_post_type' ] );
    }

    /**
     * register custom post type called forum
     *
     * @return void
     */
    public function register_forum_post_type(){
        $labels = [
            'name'               => __( 'Forums', 'uni-forum' ),
            'singular_name'      => __( 'Forum', 'uni-forum' ),
            'menu_name'          => __( 'Forums', 'uni-forum' ),
            'name_admin_bar'     => __( 'Forum', 'uni-forum' ),
            'add_new'            => __( 'Add New', 'uni-forum' ),
            'add_new_item'       => __( 'Add New Forum', 'uni-forum' ),
            'new_item'           => __( 'New Forum', 'uni-forum' ),
            'edit_item'          => __( 'Edit Forum', 'uni-forum' ),
            'view_item'          => __( 'View Forum', 'uni-forum' ),
            'all_items'          => __( 'All Forums', 'uni-forum' ),
            'search_items'       => __( 'Search Forums', 'uni-forum' ),
            'parent_item_colon'  => __( 'Parent Forums:', 'uni-forum' ),
            'not_found'          => __( 'No forums found.', 'uni-forum' ),
            'not_found_in_trash' => __( 'No forums found in Trash.', 'uni-forum' )
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'forum' ],
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-buddicons-forums',
            'supports'           => [ 'title', 'editor', 'author', 'comments' ],
            'show_in_rest'       => true,
        ];

        register_post_type( 'forum', $args );
    }

    /**
     * register text domain
     *
     * @return void
     */
    public function register_text_domain(){
        load_plugin_textdomain( 
            'uni-forum',
            false,
            dirname( plugin_basename( __FILE__ ) ) . trailingslashit( '/lang' )
        );
    }

    /**
     * define constant
     *
     * @return void
     */
    public function constants(){
        define( 'UF_VERSION', self::$version );
        define( 'UF_ASSETS', trailingslashit( plugins_url( 'assets', __FILE__ ) ) );
        define( 'UF_ASSETS_CSS', trailingslashit( UF_ASSETS . 'css' ) );
        define( 'UF_ASSETS_JS', trailingslashit( UF_ASSETS . 'js' ) );
    }

    /**
     * create a new instance
     *
     * @return void
     */
    public static function init(){
        if( is_null( self::$instance ) ){
            self::$instance = new self();
        }

        return self::$instance;
    }

 }

 function uni_forum(){
    return Uni_Forum::init();
 }

 uni_forum();