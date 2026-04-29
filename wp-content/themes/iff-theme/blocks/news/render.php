<?php
/**
 * News Block Template.
 */

$baslik = get_field('baslik') ?: 'GÜNCEL HABERLER';
$tum_haberler_metni = get_field('tum_haberler_metni') ?: 'TÜM HABERLER &rarr;';
$tum_haberler_linki = get_field('tum_haberler_linki') ?: '#';

// Seçilen kategoriyi al (ACF Taxonomy alanı, term ID döndürür)
$secilen_kategori = get_field('haber_kategorisi');

$args = [
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post_status' => 'publish'
];

if (!empty($secilen_kategori)) {
    $args['cat'] = is_array($secilen_kategori) ? implode(',', $secilen_kategori) : $secilen_kategori;
}

$news_query = new WP_Query($args);
$renkler = ['bg-orange', 'bg-red', 'bg-darkorange'];
$renk_index = 0;

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-20 px-6 border-y-2 border-orange/20 news-block';
if (!$bg_color) {
    $className .= ' bg-white';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<!-- Haberler Alanı -->
<section id="news" class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto">
        <div class="flex justify-between items-end mb-12">
            <h2 class="text-4xl font-custom font-bold uppercase tracking-tight"><?php echo esc_html($baslik); ?></h2>
            <a href="<?php echo esc_url($tum_haberler_linki); ?>"
                class="text-red font-bold hover:underline mb-1"><?php echo wp_kses_post($tum_haberler_metni); ?></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <?php if ($news_query->have_posts()): ?>
                <?php while ($news_query->have_posts()):
                    $news_query->the_post();
                    $link = get_permalink();
                    $gorsel = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg';

                    // Kategoriyi etiket olarak gösterelim
                    $categories = get_the_category();
                    $etiket = !empty($categories) ? esc_html($categories[0]->name) : 'Haber';

                    $renk = $renkler[$renk_index % 3];
                    $renk_index++;
                    ?>
                    <div class="group cursor-pointer" onclick="window.location.href='<?php echo esc_url($link); ?>'">
                        <div class="relative overflow-hidden modern-shadow mb-6 border-2 border-orange/10">
                            <img src="<?php echo esc_url($gorsel); ?>"
                                class="w-full h-64 object-cover transform transition-transform group-hover:scale-105"
                                alt="<?php the_title_attribute(); ?>">
                            <div class="absolute top-4 left-4">
                                <span
                                    class="<?php echo esc_attr($renk); ?> text-white px-3 py-1 text-xs font-bold uppercase"><?php echo $etiket; ?></span>
                            </div>
                        </div>
                        <h3 class="text-xl font-heading font-bold mb-3 group-hover:text-red transition-colors">
                            <?php the_title(); ?></h3>
                        <p class="text-gray-600 font-serif line-clamp-2">
                            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?></p>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); ?>
            <?php else: ?>
                <p class="text-gray-500 font-serif col-span-3">Henüz haber bulunmuyor.</p>
            <?php endif; ?>

        </div>
    </div>
</section>