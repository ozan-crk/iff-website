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
            border-top: 4px solid
                <?php echo $c_ana; ?>
            ;
            list-style: none;
            padding: 0.5rem 0 0 0;
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
                <?php echo $c_ana; ?>
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

<body <?php body_class("bg-cream font-sans text-warmgray overflow-x-hidden"); ?>>

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

            <div class="flex items-center space-x-2">
                <?php if (have_rows('header_butonlar', 'option')): ?>
                    <?php while (have_rows('header_butonlar', 'option')):
                        the_row();
                        $metin = get_sub_field('metin');
                        $link = get_sub_field('link');
                        ?>
                        <a href="<?php echo esc_url($link); ?>"
                            class="bg-white text-red px-4 py-1.5 font-heading font-bold border-2 border-white hover:bg-cream transition text-[10px] uppercase"><?php echo esc_html($metin); ?></a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <a href="<?php echo home_url('/basvuru#gonullu'); ?>"
                        class="bg-white text-red px-4 py-1.5 font-heading font-bold border-2 border-white hover:bg-cream transition text-[10px] uppercase">GÖNÜLLÜ
                        OL</a>
                <?php endif; ?>
            </div>
        </div>
    </header>