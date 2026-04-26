<?php
/**
 * Standart Sayfa Şablonu (Page Template)
 */
get_header();
?>
<main class="pt-32 pb-20 px-6">
    <div class="container mx-auto">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                the_content(); // ACF Blokları veya metinler burada render edilecek
            endwhile;
        endif;
        ?>
    </div>
</main>
<?php
get_footer();
