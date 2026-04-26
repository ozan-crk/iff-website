<?php
/**
 * Anasayfa Şablonu
 */
get_header(); ?>

<?php get_template_part('components/popup'); ?>

<main class="mt-20">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>
