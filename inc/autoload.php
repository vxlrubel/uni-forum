<?php

// directly access denied
defined('ABSPATH') || exit;

$files = [
    'template',
    'classes/class-assets',
    'classes/class-ajax',
    'classes/class-shortcode',
    'classes/class-admin-menu',
    'classes/class-admin-ajax',
];

foreach ( $files as $file ) {
    if( file_exists(  dirname( __FILE__ ) . '/' . $file . '.php' ) ){
        require_once dirname( __FILE__ ) . '/' . $file . '.php';
    }else{
        $not_found = dirname(__FILE__) . '/' . $file . '.php';
        throw new Exception("file not found {$not_found}", 1);
    }
}