<?php
/**
 * İletişim Block Template.
 */

$bilgi_email = get_field('bilgi_email') ?: 'bilgi@iff.org.tr';
$film_email = get_field('film_email') ?: 'film@iff.org.tr';
$basin_email = get_field('basin_email') ?: 'basin@iff.org.tr';
$koordinasyon_baslik = get_field('koordinasyon_baslik') ?: 'KOORDİNASYON MERKEZİ';
$koordinasyon_metin = get_field('koordinasyon_metin') ?: '
<p class="text-lg">İşçi Filmleri Festivali\'nin merkezi bir ofisi yerine, kolektif olarak yönetilen bir yapısı vardır.</p>
<p class="leading-relaxed">Gönüllü toplantılarımız ve hazırlık süreçlerimiz için il komitelerimizle iletişime geçebilirsiniz. Her ilin kendi yerel çalışma grubu bulunmaktadır.</p>
';

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'mb-24 iletisim-block';
if ($bg_color) {
    $className .= ' p-8 md:p-12';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
        <!-- İletişim Bilgileri -->
        <div class="space-y-12">
            <div>
                <h2 class="text-xs font-bold text-red mb-6 uppercase tracking-widest font-heading border-l-4 border-red pl-4">E-POSTA ADRESLERİ</h2>
                <div class="space-y-4 font-serif text-lg">
                    <p class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-500 text-sm italic">Genel Bilgi:</span>
                        <span class="font-bold text-warmgray"><?php echo esc_html($bilgi_email); ?></span>
                    </p>
                    <p class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-500 text-sm italic">Film Başvuruları:</span>
                        <span class="font-bold text-warmgray"><?php echo esc_html($film_email); ?></span>
                    </p>
                    <p class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-500 text-sm italic">Basın:</span>
                        <span class="font-bold text-warmgray"><?php echo esc_html($basin_email); ?></span>
                    </p>
                </div>
            </div>

            <div>
                <h2 class="text-xs font-bold text-red mb-6 uppercase tracking-widest font-heading border-l-4 border-red pl-4">SOSYAL MEDYA</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" class="bg-white border-2 border-warmgray p-4 flex items-center space-x-3 hover:bg-warmgray hover:text-white transition group modern-shadow">
                        <span class="font-bold font-heading text-xs uppercase">Instagram</span>
                    </a>
                    <a href="#" class="bg-white border-2 border-warmgray p-4 flex items-center space-x-3 hover:bg-warmgray hover:text-white transition group modern-shadow">
                        <span class="font-bold font-heading text-xs uppercase">YouTube</span>
                    </a>
                    <a href="#" class="bg-white border-2 border-warmgray p-4 flex items-center space-x-3 hover:bg-warmgray hover:text-white transition group modern-shadow">
                        <span class="font-bold font-heading text-xs uppercase">X (Twitter)</span>
                    </a>
                    <a href="#" class="bg-white border-2 border-warmgray p-4 flex items-center space-x-3 hover:bg-warmgray hover:text-white transition group modern-shadow">
                        <span class="font-bold font-heading text-xs uppercase">Bluesky</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Adres / Ofis -->
        <div class="bg-white border-4 border-orange p-10 modern-shadow">
            <h2 class="text-2xl font-heading font-bold mb-8 uppercase tracking-widest text-orange"><?php echo esc_html($koordinasyon_baslik); ?></h2>
            <div class="space-y-6 font-serif text-gray-700">
                <?php echo wp_kses_post($koordinasyon_metin); ?>
                <div class="pt-8 border-t border-gray-100 italic text-sm text-gray-500">
                    "Bize ulaşmak için e-posta adreslerimizi kullanabilir veya sosyal medya üzerinden mesaj atabilirsiniz."
                </div>
            </div>
        </div>
    </div>
</section>
