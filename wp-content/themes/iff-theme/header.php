<?php
/**
 * Tema Üst Kısmı (Header)
 */
$c_ana = get_field('renk_ana', 'option') ?: '#FF6B35';
$c_ikincil = get_field('renk_ikincil', 'option') ?: '#E63946';
$c_arka = get_field('renk_arka_plan', 'option') ?: '#FFF5E6';
$c_koyu = get_field('renk_koyu', 'option') ?: '#2C2C2C';

// Font Ayarları
$f_ozel_aktif = get_field('font_ozel_aktif', 'option');
$f_ozel_ad = get_field('font_ozel_ad', 'option') ?: 'CustomFont';
$f_regular = get_field('font_ozel_regular', 'option');
$f_bold = get_field('font_ozel_bold', 'option');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;500;700&family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?php echo get_template_directory_uri(); ?>/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"
        href="<?php echo get_template_directory_uri(); ?>/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?php echo get_template_directory_uri(); ?>/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96"
        href="<?php echo get_template_directory_uri(); ?>/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?php echo get_template_directory_uri(); ?>/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage"
        content="<?php echo get_template_directory_uri(); ?>/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '<?php echo $c_arka; ?>',
                        orange: '<?php echo $c_ana; ?>',
                        darkorange: '<?php echo $c_ana; ?>', // Biraz daha koyusu için JS ile de yapılabilir ama şimdilik ana renk
                        red: '<?php echo $c_ikincil; ?>',
                        warmgray: '<?php echo $c_koyu; ?>',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'heading': ['Roboto Condensed', 'sans-serif'],
                        'display': ['Playfair Display', 'serif'],
                        'custom': ['<?php echo $f_ozel_ad; ?>', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        <?php if ($f_ozel_aktif && $f_regular): ?>
            @font-face {
                font-family: '<?php echo $f_ozel_ad; ?>';
                src: url('<?php echo $f_regular; ?>');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }

        <?php endif; ?>
        <?php if ($f_ozel_aktif && $f_bold): ?>
            @font-face {
                font-family: '<?php echo $f_ozel_ad; ?>';
                src: url('<?php echo $f_bold; ?>');
                font-weight: bold;
                font-style: normal;
                font-display: swap;
            }

        <?php endif; ?>

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .modern-shadow {
            box-shadow: 8px 8px 0px rgba(0, 0, 0, 0.1);
        }

        .hover-lift:hover {
            transform: translate(-4px, -4px);
            box-shadow: 12px 12px 0px rgba(0, 0, 0, 0.15);
        }

        .slider-dot.active {
            background-color:
                <?php echo $c_ana; ?>
            ;
        }

        .popup-overlay {
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }

        #side-panel {
            transition: transform 0.3s ease-in-out;
        }

        #side-panel.closed {
            transform: translateX(-100%);
        }

        .panel-toggle {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out !important;
        }

        @media screen and (max-width: 768px) {
            #toggle-panel.minimized {
                transform: translate(-50%, -50%) !important;
                opacity: 0.6;
            }

            #toggle-panel.minimized:hover,
            #toggle-panel.minimized:active {
                transform: translate(0, -50%) !important;
                opacity: 1;
            }
        }

        /* Global Typography & WordPress Defaults */
        h1,
        .h1,
        .wp-block-post-title {
            font-family: '<?php echo $f_ozel_ad; ?>', sans-serif;
            font-size: 3.5rem;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 1.5rem;

            letter-spacing: -0.02em;
        }

        h2,
        .h2 {
            font-family: '<?php echo $f_ozel_ad; ?>', sans-serif;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1.25rem;

            letter-spacing: -0.02em;
        }

        h3,
        .h3 {
            font-family: '<?php echo $f_ozel_ad; ?>', sans-serif;
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 1rem;

        }

        h4,
        .h4 {
            font-family: '<?php echo $f_ozel_ad; ?>', sans-serif;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.75rem;
        }

        .font-serif {
            font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
        }

        .font-custom {
            font-family: '<?php echo $f_ozel_ad; ?>', sans-serif;
        }

        .nav-item:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color:
                <?php echo $c_koyu; ?>
            ;
            min-width: 200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-top: 4px solid
                <?php echo $c_ana; ?>
            ;
        }

        .dropdown-item {
            display: block;
            padding: 12px 16px;
            color: white;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .dropdown-item:hover {
            background-color:
                <?php echo $c_ana; ?>
            ;
            color: white;
        }

        /* WordPress Menu Support */
        .primary-menu {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .primary-menu .menu-item {
            position: relative;
            padding: 0.5rem 0;
        }

        .primary-menu .menu-item a {
            color: white;
            transition: color 0.3s;
        }

        .primary-menu .menu-item a:hover {
            color:
                <?php echo $c_arka; ?>
            ;
        }

        .primary-menu .menu-item-has-children>a::after {
            content: ' ▼';
            font-size: 8px;
            opacity: 0.6;
            margin-left: 4px;
        }

        .primary-menu .menu-item-has-children:hover>.sub-menu {
            display: block;
        }

        .primary-menu .sub-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 50;
            background-color:
                <?php echo $c_koyu; ?>
            ;
            min-width: 220px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-top: 4px solid white;
            list-style: none;

        }

        .primary-menu .sub-menu .menu-item {
            padding: 0;
        }

        .primary-menu .sub-menu .menu-item a {
            display: block;
            padding: 12px 16px;
            color: white;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .primary-menu .sub-menu .menu-item a:hover {
            background-color:
                <?php echo $c_ikincil; ?>
            ;
            color: white;
        }

        /* WordPress Admin Bar Fix */
        .admin-bar header {
            top: 32px !important;
        }

        .admin-bar #side-panel {
            top: 32px !important;
            height: calc(100vh - 32px);
        }

        @media screen and (max-width: 782px) {
            .admin-bar header {
                top: 46px !important;
            }

            .admin-bar #side-panel {
                top: 46px !important;
                height: calc(100vh - 46px);
            }
        }
    </style>
    <?php wp_head(); ?>
</head>

<body <?php body_class("bg-cream font-sans text-warmgray overflow-x-hidden pt-20 md:pt-28"); ?>>

    <?php get_template_part('components/side-panel'); ?>

    <!-- Header -->
    <?php
    $header_logo = get_field('header_logo', 'option');
    $header_logo_width = get_field('header_logo_width', 'option') ?: 120;
    ?>
    <header class="bg-red py-4 px-6 fixed w-full top-0 z-50 modern-shadow border-b-2 border-white">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="<?php echo home_url(); ?>" class="flex items-center justify-center">
                    <?php if ($header_logo): ?>
                        <img src="<?php echo esc_url($header_logo); ?>"
                            style="max-width: <?php echo esc_attr($header_logo_width); ?>px;"
                            alt="<?php bloginfo('name'); ?>">
                    <?php else: ?>
                        <div
                            class="bg-white text-red w-10 h-10 flex items-center justify-center font-bold font-heading text-lg border-2 border-white">
                            IFF</div>
                    <?php endif; ?>
                </a>
            </div>
            <nav class="hidden lg:flex text-white font-heading text-[10px] uppercase tracking-widest items-center">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'primary-menu',
                    'fallback_cb' => '__return_false',
                ));
                ?>
            </nav>

            <div class="flex items-center space-x-4">
                <!-- Desktop Buttons -->
                <div class="hidden lg:flex items-center space-x-2">
                    <?php if (have_rows('header_butonlar', 'option')): ?>
                        <?php while (have_rows('header_butonlar', 'option')):
                            the_row();
                            $metin = get_sub_field('metin');
                            $link = get_sub_field('link');
                            ?>
                            <a href="<?php echo esc_url($link); ?>"
                                class="bg-white text-red px-4 py-1.5 font-heading font-bold border-2 border-white hover:bg-cream transition text-[10px] uppercase"><?php echo esc_html($metin); ?></a>
                        <?php endwhile; ?>

                    <?php endif; ?>
                </div>

                <!-- Hamburger Button (Mobile Only) -->
                <button id="mobile-menu-toggle"
                    class="lg:hidden text-white p-2 border-2 border-white hover:bg-white hover:text-red transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu"
        class="fixed inset-0 bg-red z-[100] transform translate-x-full transition-transform duration-300 lg:hidden flex flex-col overflow-y-auto">
        <div class="p-6 flex justify-between items-center border-b border-white/20">
            <div class="text-white font-custom font-bold text-2xl tracking-tighter italic uppercase">MENÜ</div>
            <button id="mobile-menu-close"
                class="text-white p-2 border-2 border-white hover:bg-white hover:text-red transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <nav class="p-8 flex-1">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'mobile-primary-menu flex flex-col space-y-4 text-white font-heading text-xl uppercase tracking-widest',
                'fallback_cb' => '__return_false',
            ));
            ?>
        </nav>

        <style>
            .mobile-primary-menu .sub-menu {
                display: none;
                flex-direction: column;
                gap: 1rem;
                padding: 1.5rem 0 0.5rem 1rem;
                border-left: 2px solid rgba(255, 255, 255, 0.1);
                margin-top: 0.5rem;
            }

            .mobile-primary-menu .menu-item-has-children {
                position: relative;
            }

            .mobile-primary-menu .menu-item-has-children>a {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .mobile-primary-menu .menu-item-has-children>a::after {
                content: '+';
                font-size: 1.5rem;
                transition: transform 0.3s;
                display: inline-block;
                margin-left: 1rem;
                font-family: sans-serif;
            }

            .mobile-primary-menu .menu-item-has-children.open>a::after {
                content: '-';
            }

            .mobile-primary-menu .menu-item-has-children.open>.sub-menu {
                display: flex;
            }

            .mobile-primary-menu .sub-menu a {
                font-size: 1rem;
                opacity: 0.8;
                letter-spacing: 0.05em;
            }
        </style>

        <div class="p-8 bg-black/10 space-y-4">
            <h4 class="text-white/50 text-[10px] uppercase tracking-[0.3em] font-heading mb-4">HIZLI İŞLEMLER</h4>
            <?php if (have_rows('header_butonlar', 'option')): ?>
                <?php while (have_rows('header_butonlar', 'option')):
                    the_row();
                    $metin = get_sub_field('metin');
                    $link = get_sub_field('link');
                    ?>
                    <a href="<?php echo esc_url($link); ?>"
                        class="block w-full bg-white text-red text-center py-4 font-heading font-bold border-2 border-white hover:bg-cream transition text-sm uppercase modern-shadow"><?php echo esc_html($metin); ?></a>
                <?php endwhile; ?>

            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('mobile-menu-toggle');
            const close = document.getElementById('mobile-menu-close');
            const menu = document.getElementById('mobile-menu');

            if (toggle && close && menu) {
                toggle.addEventListener('click', () => {
                    menu.classList.remove('translate-x-full');
                    document.body.style.overflow = 'hidden';
                });

                close.addEventListener('click', () => {
                    menu.classList.add('translate-x-full');
                    document.body.style.overflow = '';
                });

                // Dropdown handling for mobile
                const menuWithChildren = menu.querySelectorAll('.menu-item-has-children');
                menuWithChildren.forEach(item => {
                    const link = item.querySelector('a');
                    link.addEventListener('click', function (e) {
                        if (!item.classList.contains('open')) {
                            e.preventDefault();
                            item.classList.add('open');
                        }
                    });
                });

                // Menü içindeki linklere tıklayınca kapat
                const links = menu.querySelectorAll('a:not(.menu-item-has-children > a)');
                links.forEach(link => {
                    link.addEventListener('click', () => {
                        menu.classList.add('translate-x-full');
                        document.body.style.overflow = '';
                    });
                });
            }
        });
    </script>
</body>

</html>