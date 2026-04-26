<?php
/**
 * Banners Block Template.
 * Repeater ile sınırsız banner eklenebilir.
 */

$default_banners = [
    [
        'baslik' => 'ARŞİV',
        'alt_baslik' => 'FOTOĞRAFLAR',
        'link' => '#',
        'gorsel' => 'https://picsum.photos/600/400?random=40',
        'renk' => 'bg-red/60'
    ],
    [
        'baslik' => 'Gündem',
        'alt_baslik' => 'FESTİVAL GAZETESİ',
        'link' => '#',
        'gorsel' => 'https://picsum.photos/600/400?random=41',
        'renk' => 'bg-orange/60'
    ],
    [
        'baslik' => 'YAYINLAR',
        'alt_baslik' => 'FESTİVAL KİTAPLARI',
        'link' => '#',
        'gorsel' => 'https://picsum.photos/600/400?random=42',
        'renk' => 'bg-warmgray/60'
    ]
];

// Gutenberg Ek CSS Sınıfları
$className = 'py-12 px-6 banners-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>
<!-- Bannerlar -->
<section class="<?php echo esc_attr($className); ?>">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        <?php if (have_rows('bannerlar')): ?>
            <?php while (have_rows('bannerlar')):
                the_row();
                $baslik = get_sub_field('baslik');
                $alt_baslik = get_sub_field('alt_baslik');
                $link = get_sub_field('link') ?: '#';
                $gorsel_obj = get_sub_field('gorsel');
                $gorsel = is_array($gorsel_obj) ? $gorsel_obj['url'] : ($gorsel_obj ?: 'https://picsum.photos/600/400?random=40');
                $renk = get_sub_field('renk') ?: 'bg-red/60';
                ?>
                <a href="<?php echo esc_url($link); ?>"
                    class="relative h-64 group overflow-hidden modern-shadow border-4 border-white block">
                    <img src="<?php echo esc_url($gorsel); ?>"
                        class="w-full h-full object-cover grayscale transition-all group-hover:grayscale-0 group-hover:scale-105 duration-500">
                    <div
                        class="absolute inset-0 <?php echo esc_attr($renk); ?> mix-blend-multiply transition-opacity group-hover:opacity-40">
                    </div>
                    <div class="absolute inset-0 flex flex-col justify-center items-center text-center p-6">
                        <h3 class="text-white text-3xl font-custom font-bold mb-2 uppercase"><?php echo esc_html($baslik); ?>
                        </h3>
                        <p
                            class="text-white text-xs border-t border-white pt-2 font-bold uppercase tracking-widest leading-none">
                            <?php echo esc_html($alt_baslik); ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <?php foreach ($default_banners as $banner): ?>
                <a href="<?php echo esc_url($banner['link']); ?>"
                    class="relative h-64 group overflow-hidden modern-shadow border-4 border-white block">
                    <img src="<?php echo esc_url($banner['gorsel']); ?>"
                        class="w-full h-full object-cover grayscale transition-all group-hover:grayscale-0 group-hover:scale-105 duration-500">
                    <div
                        class="absolute inset-0 <?php echo esc_attr($banner['renk']); ?> mix-blend-multiply transition-opacity group-hover:opacity-40">
                    </div>
                    <div class="absolute inset-0 flex flex-col justify-center items-center text-center p-6">
                        <h3 class="text-white text-3xl font-custom font-bold mb-2 uppercase">
                            <?php echo esc_html($banner['baslik']); ?></h3>
                        <p
                            class="text-white text-xs border-t border-white pt-2 font-bold uppercase tracking-widest leading-none">
                            <?php echo esc_html($banner['alt_baslik']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</section>