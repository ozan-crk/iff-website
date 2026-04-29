<?php
/**
 * PDF Preview Block Template.
 */

$baslik = get_field('baslik');
$pdf_url = get_field('pdf_url');
$gosterim_tipi = get_field('gosterim_tipi') ?: 'modal'; // modal veya direkt
$onizleme_gorseli = get_field('onizleme_gorseli');
$buton_metni = get_field('buton_metni') ?: 'PDF GÖRÜNTÜLE';
$g_oran = get_field('genislik_orani') ?: 16;
$y_oran = get_field('yukseklik_orani') ?: 9;
$max_genislik = get_field('max_genislik') ?: '100%';

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-12 px-6 pdf-preview-block';
if (!$bg_color) {
    $className .= ' bg-white';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
$container_style = "max-width: {$max_genislik}; margin-left: auto; margin-right: auto;";
$aspect_class = "aspect-[{$g_oran}/{$y_oran}]";
?>

<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto" style="<?php echo esc_attr($container_style); ?>">
        <?php if ($baslik): ?>
            <h2 class="text-3xl font-custom font-bold text-warmgray mb-8 uppercase tracking-tighter italic border-l-4 border-red pl-4"><?php echo esc_html($baslik); ?></h2>
        <?php endif; ?>

        <?php if ($pdf_url): ?>
            <?php if ($gosterim_tipi === 'modal'): ?>
                <!-- Modal Modu -->
                <div class="relative group cursor-pointer overflow-hidden modern-shadow border-4 border-warmgray <?php echo $aspect_class; ?>">
                    <a data-fslightbox="pdf-preview-<?php echo esc_attr($block['id']); ?>" href="<?php echo esc_url($pdf_url); ?>" class="block relative w-full h-full bg-gray-100">
                        <?php if ($onizleme_gorseli): ?>
                            <img src="<?php echo esc_url($onizleme_gorseli['url']); ?>" alt="<?php echo esc_attr($onizleme_gorseli['alt']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        <?php else: ?>
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="font-serif italic text-sm">PDF Önizlemesi</span>
                            </div>
                        <?php endif; ?>

                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-red/80 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                            <span class="bg-white text-red px-8 py-3 font-heading font-bold uppercase tracking-widest shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <?php echo esc_html($buton_metni); ?>
                            </span>
                        </div>
                    </a>
                </div>
                <script>
                    if (typeof refreshFsLightbox === 'function') {
                        refreshFsLightbox();
                    }
                </script>
            <?php else: ?>
                <!-- Direkt Mod -->
                <div class="w-full border-4 border-warmgray modern-shadow overflow-hidden bg-white <?php echo $aspect_class; ?>">
                    <iframe src="<?php echo esc_url($pdf_url); ?>#toolbar=0" class="w-full h-full border-0"></iframe>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="py-20 text-center border-4 border-dashed border-gray-200">
                <p class="text-gray-400 font-serif italic">Lütfen bir PDF dosyası seçin.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
