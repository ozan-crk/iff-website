<?php
get_header();
?>
<main class="mt-20 py-12 px-6">
    <div class="container mx-auto">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        else :
            echo '<p>İçerik bulunamadı.</p>';
        endif;
        ?>
    </div>
</main>
<?php
get_footer();
