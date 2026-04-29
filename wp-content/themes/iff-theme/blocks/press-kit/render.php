<?php
/**
 * Press Kit Block Template.
 */

$baslik = get_field('baslik') ?: 'BASIN KİTİ';
$aciklama = get_field('aciklama') ?: 'Festivalimizin logoları, basın bültenleri ve yüksek çözünürlüklü görsellerine buradan ulaşabilirsiniz. Kurumsal kimlik rehberimiz PDF formatında aşağıda sunulmuştur.';

$dosya_1_metin = get_field('dosya_1_metin') ?: 'Tüm Dosyaları İndir (.ZIP)';
$dosya_1_link = get_field('dosya_1_link') ?: '#';

$dosya_2_metin = get_field('dosya_2_metin') ?: 'Basın Bülteni (DOCX)';
$dosya_2_link = get_field('dosya_2_link') ?: '#';

$pdf_url = get_field('pdf_url'); // İframe için PDF linki

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-20 px-6 border-t-2 border-orange/10 press-kit-block';
if (!$bg_color) {
    $className .= ' bg-cream';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<!-- Basın Kiti ve Gömülü PDF -->
<section id="press" class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-4xl font-custom font-bold mb-6 uppercase"><?php echo esc_html($baslik); ?></h2>
                <p class="text-gray-600 font-serif mb-8 leading-relaxed"><?php echo esc_html($aciklama); ?></p>
                <div class="flex flex-col space-y-4">
                    <a href="<?php echo esc_url($dosya_1_link); ?>"
                        class="inline-flex items-center space-x-3 text-red font-bold hover:text-orange transition">
                        <span>&darr;</span> <span><?php echo esc_html($dosya_1_metin); ?></span>
                    </a>
                    <a href="<?php echo esc_url($dosya_2_link); ?>"
                        class="inline-flex items-center space-x-3 text-red font-bold hover:text-orange transition">
                        <span>&darr;</span> <span><?php echo esc_html($dosya_2_metin); ?></span>
                    </a>
                </div>
            </div>
            <div class="bg-white border-4 border-warmgray modern-shadow h-96 overflow-hidden relative group">
                <?php if ($pdf_url): ?>
                    <iframe src="<?php echo esc_url($pdf_url); ?>" class="w-full h-full border-0"></iframe>
                <?php else: ?>
                    <div
                        class="absolute inset-0 flex items-center justify-center bg-gray-100 text-gray-400 font-serif text-sm">
                        <div class="text-center p-8">
                            <p class="mb-4">[PDF Önizleme Alanı]</p>
                            <button
                                class="bg-warmgray text-white px-6 py-2 text-xs font-bold font-heading uppercase tracking-tighter hover:bg-orange transition">GÖRÜNTÜLE</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>