<?php
/**
 * Hero Block Template.
 */

$tarih = get_field('tarih') ?: '01 - 08 MAYIS 2026';
$baslik_1 = get_field('baslik_1') ?: 'KES-TİK';
$baslik_2 = get_field('baslik_2') ?: 'BAŞTAN';
$baslik_3 = get_field('baslik_3') ?: 'ÇEKİYORUZ!';
$aciklama = get_field('aciklama') ?: '21. Uluslararası İşçi Filmleri Festivali, emeğin ve direnişin sesini perdeden sokaklara taşımaya devam ediyor. Sinema bir lüks değil, haktır!';



$gorsel = get_field('gorsel') ?: 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg';

// Gutenberg Ek CSS Sınıfları
$className = 'py-12 bg-cream hero-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>
<section class="<?php echo esc_attr($className); ?>">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center min-h-[520px]">
            <div class="lg:col-span-7 space-y-8">
                <div
                    class="inline-block bg-warmgray text-white px-6 py-2 font-heading font-bold text-sm transform -rotate-2 modern-shadow">
                    <?php echo esc_html($tarih); ?>
                </div>
                <h1 class="font-heading uppercase font-black text-7xl md:text-[100px] leading-[0.9]">
                    <span class="text-red block font-custom"><?php echo esc_html($baslik_1); ?></span>
                    <span class="block text-cream font-custom"
                        style="-webkit-text-stroke: 3px #2C2C2C;"><?php echo esc_html($baslik_2); ?></span>
                    <span class="block text-warmgray font-custom"><?php echo esc_html($baslik_3); ?></span>
                </h1>
                <p class="text-lg font-bold max-w-xl leading-relaxed text-warmgray/80 font-serif">
                    <?php echo esc_html($aciklama); ?>
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <?php if (have_rows('butonlar')): ?>
                        <?php $i = 0;
                        while (have_rows('butonlar')):
                            the_row();

                            // Eğer "Alt Satıra Geç" seçeneği işaretlenmişse, genişliği %100 olan boş bir div ekleyerek satırı kırıyoruz
                            if (get_sub_field('alt_satir')): ?>
                                <div class="w-full h-0"></div>
                            <?php endif;

                            $buton_metni = get_sub_field('buton_metni');
                            $buton_linki = get_sub_field('buton_linki') ?: '#';
                            $btn_class = ($i % 2 == 0)
                                ? 'bg-red text-white border-4 border-warmgray px-10 py-4 font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all inline-block'
                                : 'bg-cream text-warmgray border-4 border-warmgray px-10 py-4 font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all inline-block';
                            ?>
                            <a href="<?php echo esc_url($buton_linki); ?>" class="<?php echo $btn_class; ?>">
                                <?php echo esc_html($buton_metni); ?>
                            </a>
                            <?php $i++; endwhile; ?>
                    <?php else: ?>
                        <a href="#"
                            class="bg-red text-white border-4 border-warmgray px-10 py-4 font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all inline-block">
                            PROGRAMI İNCELE
                        </a>
                        <a href="#"
                            class="bg-cream text-warmgray border-4 border-warmgray px-10 py-4 font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all inline-block">
                            FESTİVALİ TANI
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="lg:col-span-5 relative">
                <div
                    class="border-8 border-warmgray p-2 rotate-3 hover:rotate-0 transition-transform duration-500 modern-shadow bg-white">
                    <img src="<?php echo esc_url($gorsel); ?>"
                        class="w-full h-auto border-4 border-cream grayscale hover:grayscale-0 transition-all duration-700"
                        alt="Festival Afişi">
                </div>
                <div class="absolute -bottom-4 -right-4 w-full h-full bg-orange -z-10"></div>
            </div>
        </div>
    </div>
</section>