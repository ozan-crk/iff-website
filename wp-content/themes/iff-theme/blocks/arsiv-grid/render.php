<?php
/**
 * Arsiv Grid Block Template.
 */

$secilen_kategori = get_field('arsiv_kategorisi'); // Taxonomy field (Category ID)
$gosterilecek_sayi = get_field('gosterilecek_sayi') ?: -1; // -1 tümü

$args = [
    'post_type' => 'post',
    'posts_per_page' => $gosterilecek_sayi,
    'post_status' => 'publish'
];

if (!empty($secilen_kategori)) {
    $args['cat'] = is_array($secilen_kategori) ? implode(',', $secilen_kategori) : $secilen_kategori;
}

$arsiv_query = new WP_Query($args);

// Gutenberg Ek CSS Sınıfları
$className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 arsiv-grid-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>
<!-- Arşiv Grid Bloğu -->
<div class="<?php echo esc_attr($className); ?>">
    <?php if ($arsiv_query->have_posts()): ?>
        <?php while ($arsiv_query->have_posts()): $arsiv_query->the_post(); 
            $link = get_permalink();
            $gorsel = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg';
            
            // Yıl bilgisini etiket veya ACF custom field üzerinden alabiliriz. 
            // Burada yazının yayınlanma yılı veya özel alan (yil) kullanılabilir.
            $yil = get_field('arsiv_yili') ?: get_the_date('Y');
            
            // Eğer PDF veya Dış linki varsa ona yönlendir, yoksa detaya
            $dis_link = get_field('arsiv_linki');
            $hedef_link = $dis_link ? $dis_link : $link;
        ?>
            <div class="bg-white border-4 border-warmgray modern-shadow group cursor-pointer overflow-hidden" onclick="window.location.href='<?php echo esc_url($hedef_link); ?>'">
                <div class="h-64 bg-gray-200 relative">
                    <img src="<?php echo esc_url($gorsel); ?>" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" alt="<?php the_title_attribute(); ?>">
                    <?php if ($dis_link): ?>
                    <div class="absolute inset-0 bg-red/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="bg-white text-red px-4 py-2 font-heading font-bold uppercase text-xs">PDF'İ GÖR</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <span class="bg-warmgray text-white px-2 py-0.5 text-[8px] font-bold uppercase tracking-widest"><?php echo esc_html($yil); ?></span>
                    <h3 class="text-lg font-heading font-bold mt-2 uppercase"><?php the_title(); ?></h3>
                    <p class="text-xs text-gray-500 mt-2 font-serif"><?php echo wp_trim_words(get_the_excerpt(), 10, '...'); ?></p>
                </div>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    <?php else: ?>
        <p class="text-gray-500 font-serif col-span-3">Bu kategoride henüz kayıt bulunmuyor.</p>
    <?php endif; ?>
</div>
