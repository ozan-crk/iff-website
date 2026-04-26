<?php
/**
 * Poster Block Template.
 */

$baslik = get_field('baslik') ?: '19. FESTİVAL AFİŞİMİZ ÇIKTI!';
$alt_baslik = get_field('alt_baslik') ?: '"Sesinizi duymak istiyoruz, sessizliği değil."';
$aciklama = get_field('aciklama') ?: 'Bu yılın afişi, kolektif bir çabanın ürünü olarak ortaya çıktı. Sokaktaki direnişten sinema perdesine uzanan köprüyü simgeliyor.';

$buton_1_metin = get_field('buton_1_metin') ?: 'AFİŞİ İNDİR (PNG)';
$buton_1_link = get_field('buton_1_link') ?: '#';

$buton_2_metin = get_field('buton_2_metin') ?: 'PDF OLARAK GÖR';
$buton_2_link = get_field('buton_2_link') ?: '#';

$gorsel = get_field('gorsel') ?: 'https://picsum.photos/600/800?random=50';

// Gutenberg Ek CSS Sınıfları
$className = 'py-20 px-6 bg-warmgray text-white poster-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>
<!-- Aktif Festivalin Afişi -->
<section class="<?php echo esc_attr($className); ?>">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div class="relative">
            <div class="border-8 border-orange modern-shadow relative z-10 overflow-hidden">
                <img src="<?php echo esc_url($gorsel); ?>" class="w-full h-auto grayscale hover:grayscale-0 transition-all duration-700" alt="Festival Afişi">
            </div>
            <div class="absolute -top-4 -left-4 w-full h-full bg-red -z-0"></div>
        </div>
        <div class="space-y-8">
            <h2 class="text-5xl md:text-6xl font-heading font-bold leading-tight"><?php echo esc_html($baslik); ?></h2>
            <p class="text-xl font-serif text-cream italic"><?php echo esc_html($alt_baslik); ?></p>
            <p class="text-gray-400"><?php echo esc_html($aciklama); ?></p>
            <div class="flex space-x-4">
                <a href="<?php echo esc_url($buton_1_link); ?>" class="bg-orange text-white px-8 py-3 font-heading font-bold hover:bg-darkorange transition hover-lift inline-block text-center"><?php echo esc_html($buton_1_metin); ?></a>
                <a href="<?php echo esc_url($buton_2_link); ?>" target="_blank" class="border-2 border-white px-8 py-3 font-heading font-bold hover:bg-white hover:text-warmgray transition hover-lift inline-block text-center"><?php echo esc_html($buton_2_metin); ?></a>
            </div>
        </div>
    </div>
</section>
