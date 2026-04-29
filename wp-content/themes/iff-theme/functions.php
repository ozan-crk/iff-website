<?php
// ICS Dosya İndirme İşleyicisi (iPhone'da otomatik açılması için)
add_action('init', function() {
    if (isset($_POST['download_ics']) && !empty($_POST['ics_content'])) {
        $content = stripslashes($_POST['ics_content']);
        $filename = "IFF_Program.ics";
        
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($content));
        header('Connection: close');
        
        echo $content;
        exit;
    }
});

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

        // Seperator Bloğu
        register_block_type( __DIR__ . '/blocks/separator' );
    }
}
add_action( 'acf/init', 'iff_register_acf_blocks' );

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

// Blok bazlı bileşen editörünü devre dışı bırak (Klasik bileşenler için)
add_filter( 'use_widgets_block_editor', '__return_false' );
/**
 * Custom Widget: IFF Son Yazılar
 */
class IFF_Recent_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'iff_recent_posts',
            'IFF: Son Yazılar',
            array( 'description' => 'Görselli son yazılar listesi (Kategori filtreli)' )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $query_args = array(
            'posts_per_page' => 5,
            'post_status'    => 'publish'
        );

        if ( ! empty( $instance['category'] ) && $instance['category'] > 0 ) {
            $query_args['cat'] = $instance['category'];
        }

        $query = new WP_Query( $query_args );

        if ( $query->have_posts() ) {
            echo '<div class="space-y-6">';
            while ( $query->have_posts() ) {
                $query->the_post();
                ?>
                <a href="<?php the_permalink(); ?>" class="flex items-center group">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-sm mr-4 bg-gray-100">
                            <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'w-full h-full object-cover transition-transform group-hover:scale-110' ) ); ?>
                        </div>
                    <?php else : ?>
                         <div class="w-16 h-16 flex-shrink-0 bg-gray-50 flex items-center justify-center mr-4 rounded-sm border border-gray-100 overflow-hidden">
                             <img src="https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" alt="IFF">
                         </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-warmgray leading-snug group-hover:text-red transition-colors line-clamp-2 uppercase font-heading">
                            <?php the_title(); ?>
                        </h4>
                        <span class="text-[10px] uppercase tracking-widest text-gray-400 mt-1 block font-heading">
                            <?php echo get_the_date(); ?>
                        </span>
                    </div>
                </a>
                <?php
            }
            echo '</div>';
            wp_reset_postdata();
        }
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Son Yazılar';
        $category = ! empty( $instance['category'] ) ? $instance['category'] : 0;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Başlık:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'category' ); ?>">Kategori Seçin:</label>
            <?php 
            wp_dropdown_categories( array(
                'show_option_all' => 'Tüm Kategoriler',
                'name'            => $this->get_field_name( 'category' ),
                'id'              => $this->get_field_id( 'category' ),
                'selected'        => $category,
                'class'           => 'widefat',
                'hierarchical'    => true,
            ) ); 
            ?>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['category'] = ( ! empty( $new_instance['category'] ) ) ? (int) $new_instance['category'] : 0;
        return $instance;
    }
}

/**
 * Register Sidebar and Widgets
 */
function iff_widgets_init() {
    register_sidebar( array(
        'name'          => 'Yazı Yan Menü',
        'id'            => 'post-sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-12 bg-white p-6 shadow-lg border-l-4 border-red">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-xl font-heading font-bold mb-4 uppercase tracking-wider text-warmgray border-b border-gray-100 pb-2">',
        'after_title'   => '</h3>',
    ) );

    register_widget( 'IFF_Recent_Posts_Widget' );
}
add_action( 'widgets_init', 'iff_widgets_init' );


