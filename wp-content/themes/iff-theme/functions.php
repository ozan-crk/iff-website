<?php
function iff_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'menus' );
    
    register_nav_menus( array(
        'primary' => __( 'Ana Menü', 'iff-theme' ),
    ) );
}
add_action( 'after_setup_theme', 'iff_theme_setup' );

function iff_theme_scripts() {
    wp_enqueue_style( 'iff-style', get_stylesheet_uri() );
    wp_enqueue_script( 'iff-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'iff_theme_scripts' );

/**
 * Register ACF Blocks
 */
function iff_register_acf_blocks() {
    // Sadece ACF yüklüyse blokları kaydet
    if ( function_exists( 'register_block_type' ) ) {
        register_block_type( __DIR__ . '/blocks/biz-kimiz' );
        register_block_type( __DIR__ . '/blocks/iletisim' );
        register_block_type( __DIR__ . '/blocks/basvuru' );
        register_block_type( __DIR__ . '/blocks/kimin-festivali' );
        register_block_type( __DIR__ . '/blocks/yonergeler' );
        register_block_type( __DIR__ . '/blocks/basin' );
        
        // Anasayfa Blokları
        register_block_type( __DIR__ . '/blocks/hero' );
        register_block_type( __DIR__ . '/blocks/stats' );
        register_block_type( __DIR__ . '/blocks/photo-slider' );
        register_block_type( __DIR__ . '/blocks/banners' );
        register_block_type( __DIR__ . '/blocks/press-kit' );
        register_block_type( __DIR__ . '/blocks/news' );
        register_block_type( __DIR__ . '/blocks/program' );
        register_block_type( __DIR__ . '/blocks/poster' );
        register_block_type( __DIR__ . '/blocks/quick-links' );
        
        // Yeni Arşiv Bloğu
        register_block_type( __DIR__ . '/blocks/arsiv-grid' );

        // Şehir Programı Bloğu
        register_block_type( __DIR__ . '/blocks/city-program' );
    }
}
add_action( 'init', 'iff_register_acf_blocks' );

/**
 * Register ACF Options Page
 */
add_action('acf/init', 'iff_register_acf_options_pages');
function iff_register_acf_options_pages() {
    if( function_exists('acf_add_options_page') ) {
        acf_add_options_page(array(
            'page_title'    => 'Tema Ayarları',
            'menu_title'    => 'Tema Ayarları',
            'menu_slug'     => 'tema-ayarlari',
            'capability'    => 'edit_posts',
            'redirect'      => false,
            'icon_url'      => 'dashicons-admin-generic',
        ));
    }
}

/**
 * Türkçe Tarih Formatı (örn: 26 Nisan 2026)
 */
function iff_get_turkish_date() {
    $months = [
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan', 
        5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos', 
        9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
    ];
    $day = date('j');
    $month = $months[(int)date('n')];
    $year = date('Y');
    return "$day $month $year";
}

/**
 * Font Dosyaları İçin Yükleme İzni (MIME Types)
 */
add_filter('upload_mimes', 'iff_add_font_mime_types');
function iff_add_font_mime_types($mimes) {
    $mimes['woff'] = 'application/x-font-woff';
    $mimes['woff2'] = 'font/woff2';
    $mimes['ttf'] = 'application/x-font-ttf';
    $mimes['otf'] = 'application/font-otf';
    return $mimes;
}


add_filter('use_block_editor_for_post_type', function($use_block_editor, $post_type) {
    if ($post_type === 'post') {
        return false; // yazılar klasik editör
    }
    if ($post_type === 'page') {
        return true; // sayfalar Gutenberg
    }
    return $use_block_editor;
}, 10, 2);