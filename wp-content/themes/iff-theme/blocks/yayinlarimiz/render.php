<?php
/**
 * Yayınlarımız Block Template.
 */

$baslik = get_field('baslik') ?: 'YAYINLARIMIZ';
$aciklama = get_field('aciklama');
$yayinlar = get_field('yayinlar');

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-20 px-6 yayinlarimiz-block';
if (!$bg_color) {
    $className .= ' bg-white';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>

<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto">
        <div class="max-w-4xl mb-16">
            <h2 class="text-5xl font-custom font-bold text-warmgray mb-6 tracking-tighter uppercase border-l-8 border-red pl-6"><?php echo esc_html($baslik); ?></h2>
            <?php if ($aciklama): ?>
                <p class="text-xl font-serif text-gray-600 leading-relaxed"><?php echo esc_html($aciklama); ?></p>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($yayinlar): ?>
                <?php foreach ($yayinlar as $yayin): 
                    $y_baslik = $yayin['baslik'];
                    $y_alt = $yayin['alt_metin'];
                    $y_link = $yayin['link'];
                    $y_tip = isset($yayin['tip']) ? $yayin['tip'] : 'pdf';
                ?>
                    <div class="group relative bg-cream border-4 border-warmgray p-8 modern-shadow hover-lift transition-all flex flex-col justify-between min-h-[250px]">
                        <div class="mb-6">
                            <div class="w-12 h-12 bg-red text-white flex items-center justify-center mb-6 shadow-md transform -rotate-3 group-hover:rotate-0 transition-transform">
                                <?php if ($y_tip === 'pdf'): ?>
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path><path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path></svg>
                                <?php endif; ?>
                            </div>
                            <h4 class="text-xl font-heading font-bold text-warmgray mb-2 uppercase leading-tight"><?php echo esc_html($y_baslik); ?></h4>
                            <?php if ($y_alt): ?>
                                <p class="text-xs text-gray-500 font-serif italic"><?php echo esc_html($y_alt); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if ($y_link): ?>
                            <a href="<?php echo esc_url($y_link['url']); ?>" 
                               target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center space-x-2 text-xs font-bold text-red uppercase tracking-widest hover:text-orange transition-colors group-hover:translate-x-2 transition-transform duration-300">
                                <span><?php echo esc_html($y_link['title'] ?: 'Görüntüle'); ?></span>
                                <span>&rarr;</span>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Dekoratif Arka Plan Yazısı -->
                        <div class="absolute bottom-4 right-4 opacity-[0.03] pointer-events-none select-none">
                            <span class="text-6xl font-custom font-bold italic"><?php echo $y_tip === 'pdf' ? 'PDF' : 'LINK'; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-12 text-center border-4 border-dashed border-gray-200">
                    <p class="text-gray-400 font-serif">Henüz yayın eklenmedi.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
