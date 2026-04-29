<?php
/**
 * Tarihçe Block Template (Dynamic Children).
 */

$baslik = get_field('baslik') ?: 'TARİHÇE';
$aciklama = get_field('aciklama');
$ust_sayfa_id = get_field('ust_sayfa') ?: get_the_ID();

// Alt sayfaları çek
$args = array(
    'post_parent' => $ust_sayfa_id,
    'post_type' => 'page',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'ASC'
);
$children = get_posts($args);

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-24 px-6 history-block';
if (!$bg_color) {
    $className .= ' bg-cream';
}

if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>

<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-6xl font-custom font-bold text-warmgray mb-6 tracking-tighter uppercase italic">
                <?php echo esc_html($baslik); ?></h2>
            <?php if ($aciklama): ?>
                <p class="text-xl font-serif text-gray-600 leading-relaxed"><?php echo esc_html($aciklama); ?></p>
            <?php endif; ?>
            <div class="w-24 h-2 bg-red mx-auto mt-8"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
            <?php if ($children): ?>
                <?php foreach ($children as $child):
                    $h_baslik = get_the_title($child->ID);
                    $link = get_permalink($child->ID);
                    $afis_id = get_post_thumbnail_id($child->ID);
                    $afis_url = $afis_id ? wp_get_attachment_image_url($afis_id, 'full') : '';
                    ?>
                    <div class="group relative">
                        <a href="<?php echo esc_url($link); ?>"
                            class="block relative aspect-[9/16] overflow-hidden modern-shadow hover-lift transition-all duration-500">
                            <?php if ($afis_url): ?>
                                <img src="<?php echo esc_url($afis_url); ?>" alt="<?php echo esc_attr($h_baslik); ?>"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            <?php else: ?>
                                <div
                                    class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 font-serif italic text-sm p-4 text-center">
                                    Öne Çıkan Görsel Yok</div>
                            <?php endif; ?>

                            <!-- Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6">
                                <span
                                    class="text-white text-xs font-bold uppercase tracking-widest mb-2 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">DETAYLARI
                                    GÖR</span>
                            </div>
                        </a>

                        <div class="mt-4 text-center">
                            <h4 class="text-2xl font-custom font-bold text-warmgray group-hover:text-red transition-colors">
                                <?php echo esc_html($h_baslik); ?></h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center border-4 border-dashed border-gray-300">
                    <p class="text-gray-400 font-serif italic">Henüz alt sayfa bulunamadı. (Seçilen Üst Sayfa ID:
                        <?php echo $ust_sayfa_id; ?>)</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>