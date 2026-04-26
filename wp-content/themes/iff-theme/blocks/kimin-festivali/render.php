<?php
/**
 * Kimin Festivali Block Template.
 */

$baslik = get_field('baslik') ?: 'KİMİN FESTİVALİ?';
$metin = get_field('metin') ?: 'Katılımcı, kolektif ve yerelden örgütlenen yapıya dair kısa açıklama hazırlık aşamasındadır. Gönüllü olarak bu sürecin bir parçası olabilirsiniz.';
$buton_metin = get_field('buton_metin') ?: 'Gönüllü Formu &rarr;';
$buton_link = get_field('buton_link') ?: home_url('/basvuru#gonullu');
?>
<section class="mb-24 bg-white p-12 border-4 border-red modern-shadow kimin-festivali-block">
    <h2 class="text-4xl font-heading font-bold text-red mb-6 uppercase tracking-tighter"><?php echo esc_html($baslik); ?></h2>
    <p class="font-serif text-xl text-gray-600 mb-8 leading-relaxed"><?php echo esc_html($metin); ?></p>
    <a href="<?php echo esc_url($buton_link); ?>" class="bg-red text-white px-10 py-4 font-heading font-bold hover:bg-orange transition hover-lift inline-block uppercase text-xs tracking-widest"><?php echo wp_kses_post($buton_metin); ?></a>
</section>
