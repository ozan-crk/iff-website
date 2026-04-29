<?php
/**
 * Stats Block Template.
 */

$baslik = get_field('baslik') ?: 'İşçi Filmleri Festivali 1 Mayıs – 31 Aralık 2025 · Rakamlarla';
$alt_metin = get_field('alt_metin') ?: '"Binlerce seyirciye ulaştık..."';

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-16 px-6 stats-block';
if (!$bg_color) {
    $className .= ' bg-warmgray text-white';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<!-- Rakamlarla IFF -->
<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto">
        <div class="border-l-8 border-orange pl-6 mb-12">
            <h2 class="font-custom text-4xl md:text-5xl font-bold uppercase tracking-tighter">
                <?php echo esc_html($baslik); ?>
            </h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <?php if (have_rows('rakamlar')): ?>
                <?php while (have_rows('rakamlar')):
                    the_row(); ?>
                    <div class="space-y-1">
                        <span
                            class="text-orange text-4xl font-heading font-bold"><?php echo esc_html(get_sub_field('deger')); ?></span>
                        <p class="text-sm font-serif text-cream"><?php echo esc_html(get_sub_field('aciklama')); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">81</span>
                    <p class="text-sm font-serif text-cream">Film (59 Ulusal, 22 Uluslararası)</p>
                </div>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">125</span>
                    <p class="text-sm font-serif text-cream">Gün Film Gösterimi</p>
                </div>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">20</span>
                    <p class="text-sm font-serif text-cream">Farklı İl ve İlçede</p>
                </div>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">660</span>
                    <p class="text-sm font-serif text-cream">Seans Gösterim</p>
                </div>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">68</span>
                    <p class="text-sm font-serif text-cream">Farklı Salonda</p>
                </div>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">22</span>
                    <p class="text-sm font-serif text-cream">Prömiyer</p>
                </div>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">100+</span>
                    <p class="text-sm font-serif text-cream">Gönüllü</p>
                </div>
                <div class="space-y-1"><span class="text-orange text-4xl font-heading font-bold">XXX</span>
                    <p class="text-sm font-serif text-cream">Sosyal Medya Görüntülenme</p>
                </div>
            <?php endif; ?>
        </div>
        <p class="mt-12 text-center text-xl font-serif italic text-orange"><?php echo esc_html($alt_metin); ?></p>
    </div>
</section>