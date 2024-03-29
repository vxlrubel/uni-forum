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

 use UniForum\Inc\Classes\Shortcode;
 use UniForum\Inc\Classes\Ajax_Handle;
 use UniForum\Inc\Classes\Assets;
 use UniForum\Inc\Classes\Admin_Menu;

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

        // update user activity
        add_action('init', [ $this, 'update_user_activity' ] );

        // exicute the Shortcode class
        new Shortcode;

        // exicute the Ajax_Handle class for handle ajax request
        new Ajax_Handle;

        // execute the Assets class for register scripts
        new Assets;

        // create admin menu to modify the setings of the forum post
        new Admin_Menu;

        // register new user
        add_action( 'admin_post_uf_user_registration', [ $this, 'register_new_user' ] );

        add_action( 'admin_post_nopriv_uf_user_registration', [ $this, 'register_new_user' ] );

        // hide frontend toolbar when user is logged in
        add_action('plugins_loaded', [ $this, 'toolbar_hidden' ] );

        // WordPress version control notices
        add_action( 'admin_notices', [ $this, 'wp_version_notice' ] );

        // create forum pages
        register_activation_hook( __FILE__, [ $this, 'create_forum_pages'] );

        // create database table
        register_activation_hook( __FILE__, [ $this, 'uf_likes'] );

        // add plugins actions link
        add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );

        add_filter( 'plugin_row_meta', [ $this, 'add_row_meta' ], 10, 2 );
    }

    /**
     * Filters the array of row meta for each plugin in the Plugins list table.
     *
     * @param array $plugin_meta
     * @param [type] $plugin_file
     * @return void
     */
    public function add_row_meta( array $plugin_meta, $plugin_file ){

        if( $plugin_file !== plugin_basename( __FILE__ ) ){
            return $plugin_meta;
        }
        
        $plugin_meta[] = sprintf(
            '<a href="%1$s" target="_blank"><span class="dashicons dashicons-star-filled" style="font-size:13px;margin-top:3px;"></span>%2$s</a>',
            'https://github.com/vxlrubel/uni-forum',
            __( 'Documentation', 'uni-forum' )
        );

        return $plugin_meta;
    }

    /**
     * Add plugin actions link. It's showwing the plugin list below the plugin.
     *
     * @param [type] $links
     * @param [type] $file
     * @return $links
     */
    public function action_links( $links, $file ){

        if( $file === plugin_basename( __FILE__ ) ){

            $elements = sprintf(
                '<a href="%1$s">%2$s</a>',
                esc_url( admin_url( '/edit.php?post_type=forum&page=forum-settings' ) ),
                __( 'Settings', 'uni-forum' )
            );

            array_unshift( $links, $elements );
        }
        
        return $links;
    }

    /**
     * create database table called uf_likes
     *
     * @return void
     */
    public function uf_likes(){
        global $wpdb;
        $table           = $wpdb->prefix . 'uf_likes';
        $charset_collate = $wpdb->get_charset_collate();

        $sql   = "CREATE TABLE IF NOT EXISTS $table(
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            post_id INT NOT NULL,
            like_status BOOLEAN DEFAULT FALSE,
            PRIMARY KEY (id),
            UNIQUE KEY unique_user_post (user_id, post_id)
        ) $charset_collate;";

        $wpdb->query( $sql );
    }
    

    /**
     * create forum pages when activate the plugin
     *
     * @return void
     */
    public function create_forum_pages(){
        $pages = [
            'profile'      => [ 'profile', '[uf_user_profile]' ],
            'registration' => [ 'registration', '[uf_registration_form]' ],
            'forum'        => [ 'render-forums', '[uf_render_forum]' ]
        ];
    
        foreach ( $pages as $page_name => $page_data ) {
            $page_title   = ucwords( str_replace( '_', ' ', $page_name ) );
            $page_slug    = $page_data[0];
            $page_content = $page_data[1];
    
            if ( ! get_page_by_path( $page_slug ) ) {
                $page_args = [
                    'post_title'     => $page_title,
                    'post_name'      => $page_slug,
                    'post_content'   => $page_content,
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                ];

                wp_insert_post( $page_args );
            }
        }
    }

    /**
     * WordPress version control notice
     *
     * @return void
     */
    public function wp_version_notice(){
        global $wp_version;
        $min_required_version = '5.2';

        if( version_compare( $wp_version, $min_required_version, '<' ) ){
            $notice = sprintf(
                __( 'Uni Forum plugin requires WordPress version %1$s or higher to function properly. Please <a href="%2$s">update WordPress</a>.', 'uni-forum' ),
                $min_required_version,
                admin_url( 'update-core.php' )
            );

            printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', wp_kses_post( $notice ) );
        }
    }

    /**
     * hide the toolbar from frontend when user is logged in
     *
     * @return void
     */
    public function toolbar_hidden(){
        if ( is_user_logged_in() && current_user_can('subscriber') ) {
            show_admin_bar(false);
        }
    }

    /**
     * register new user
     *
     * @return void
     */
    public function register_new_user(){
        if( isset( $_POST['uf_user_email'] ) && isset( $_POST['uf_user_password'] ) ){
            $user_email    = sanitize_email( $_POST['uf_user_email'] );
            $user_password = esc_sql( $_POST['uf_user_password'] );

            $user_exists   = email_exists( $user_email );
            if ( $user_exists ) {
                wp_redirect( home_url() . '?registration_error=user_exists' );
                exit;
            }

            $user_id = wp_create_user( $user_email, $user_password, $user_email, [ 'role' => 'subscriber' ] );

            if( is_wp_error( $user_id ) ){
                wp_redirect( home_url() . '?registration_error=' . $user_id->get_error_message() );
                exit;
            }else{
                $profile_page = get_page_by_path( 'profile' );

                if ( $profile_page ){
                    wp_redirect( home_url('/profile?registration_success=true') );
                    exit;
                }else{
                    wp_redirect( home_url() . '?registration_success=true' );
                    exit;
                }
                
            }

        }
    }
    
    /**
     * update user activity
     *
     * @return void
     */
    public function update_user_activity(){
        $user_id = get_current_user_id();

        if ( $user_id ) {
            update_user_meta( $user_id, 'last_activity', current_time('timestamp') );
        }
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
        define( 'UF_ASSETS_ADMIN', trailingslashit( UF_ASSETS . 'admin' ) );
        define( 'UF_ASSETS_ADMIN_CSS', trailingslashit( UF_ASSETS_ADMIN . 'css' ) );
        define( 'UF_ASSETS_ADMIN_JS', trailingslashit( UF_ASSETS_ADMIN . 'js' ) );
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