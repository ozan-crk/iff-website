<?php
/**
 * Başvuru Block Template.
 */

$baslik = get_field('baslik') ?: 'BAŞVURULAR';

$film_baslik = get_field('film_baslik') ?: 'Film Başvuruları';
$film_metin = get_field('film_metin') ?: 'Dünyanın dört bir yanından işçi ve emek temalı filmleri bekliyoruz. Festivalimiz yarışmasız ve ücretsizdir. Başvurularınızı FilmFreeway platformu üzerinden gerçekleştirebilirsiniz.';
$film_tarih = get_field('film_tarih') ?: 'Son Başvuru Tarihi: 31 Ocak 2025';
$film_link_metin = get_field('film_link_metin') ?: 'FilmFreeway Üzerinden Başvur';
$film_link_url = get_field('film_link_url') ?: 'https://filmfreeway.com';

$gonullu_baslik = get_field('gonullu_baslik') ?: 'Gönüllü Başvurusu';
$gonullu_metin = get_field('gonullu_metin') ?: '"Festival gönüllülerle var olur." Film gösterimlerinden teknik desteğe, iletişimden sosyal medya yönetimine kadar birçok alanda katkı sunmak için bize katılın.';
$gonullu_alt_metin = get_field('gonullu_alt_metin') ?: 'Başvuru formunu doldurarak ekibimize katılabilirsiniz.';
$gonullu_link_metin = get_field('gonullu_link_metin') ?: 'Gönüllü Başvuru Formu';
$gonullu_link_url = get_field('gonullu_link_url') ?: '#';

// Gutenberg Ek CSS Sınıfları
$className = 'mb-24 basvuru-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>
<section class="<?php echo esc_attr($className); ?>">
    <h2 class="text-6xl font-heading font-bold text-red mb-12 uppercase tracking-tighter"><?php echo esc_html($baslik); ?></h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Film Başvurusu -->
        <div class="bg-white border-4 border-orange p-10 modern-shadow flex flex-col h-full">
            <h3 class="text-3xl font-heading font-bold mb-6 uppercase tracking-tight"><?php echo esc_html($film_baslik); ?></h3>
            <p class="font-serif text-gray-600 mb-8 flex-1 leading-relaxed"><?php echo esc_html($film_metin); ?></p>
            <div class="space-y-4">
                <div class="bg-cream p-4 border-l-4 border-orange text-xs font-bold text-orange uppercase tracking-widest">
                    <?php echo esc_html($film_tarih); ?>
                </div>
                <a href="<?php echo esc_url($film_link_url); ?>" target="_blank" class="block w-full bg-orange text-white py-4 text-center font-heading font-bold text-xs uppercase hover:bg-darkorange transition-colors hover-lift"><?php echo esc_html($film_link_metin); ?></a>
            </div>
        </div>

        <!-- Gönüllü Başvurusu -->
        <div class="bg-warmgray border-4 border-white p-10 modern-shadow flex flex-col h-full text-white">
            <h3 class="text-3xl font-heading font-bold mb-6 uppercase tracking-tight text-orange"><?php echo esc_html($gonullu_baslik); ?></h3>
            <p class="font-serif text-gray-300 mb-8 flex-1 leading-relaxed"><?php echo esc_html($gonullu_metin); ?></p>
            <div class="space-y-4">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest"><?php echo esc_html($gonullu_alt_metin); ?></p>
                <a href="<?php echo esc_url($gonullu_link_url); ?>" class="block w-full bg-white text-warmgray py-4 text-center font-heading font-bold text-xs uppercase hover:bg-cream transition-colors hover-lift"><?php echo esc_html($gonullu_link_metin); ?></a>
            </div>
        </div>
    </div>
</section>
