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
        'gorsel' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/30080213/placeholder-karagoz-sarlo.jpg',
        'renk' => 'bg-red/60'
    ],
    [
        'baslik' => 'Gündem',
        'alt_baslik' => 'FESTİVAL GAZETESİ',
        'link' => '#',
        'gorsel' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/30080213/placeholder-karagoz-sarlo.jpg',
        'renk' => 'bg-orange/60'
    ],
    [
        'baslik' => 'YAYINLAR',
        'alt_baslik' => 'FESTİVAL KİTAPLARI',
        'link' => '#',
        'gorsel' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/30080213/placeholder-karagoz-sarlo.jpg',
        'renk' => 'bg-warmgray/60'
    ]
];

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-12 px-6 banners-block';

if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<!-- Bannerlar -->
<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        <?php if (have_rows('bannerlar')): ?>
            <?php while (have_rows('bannerlar')):
                the_row();
                $baslik = get_sub_field('baslik');
                $alt_baslik = get_sub_field('alt_baslik');
                $link = get_sub_field('link') ?: '#';
                $gorsel_obj = get_sub_field('gorsel');
                $gorsel = is_array($gorsel_obj) ? $gorsel_obj['url'] : ($gorsel_obj ?: 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/30080213/placeholder-karagoz-sarlo.jpg');
                $renk = get_sub_field('renk') ?: 'bg-red/60';
                ?>
                <a href="<?php echo esc_url($link); ?>"
                    class="relative aspect-[3/2] group overflow-hidden modern-shadow border-4 border-white block">
                    <img src="<?php echo esc_url($gorsel); ?>"
                        class="w-full h-full object-cover transition-all group-hover:scale-105 duration-500">
                    <!-- Alt Degrade ve Yazılar -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity">
                    </div>

                    <div class="absolute inset-x-0 bottom-0 p-6 flex flex-col items-center text-center">
                        <h3 class="text-white text-2xl md:text-3xl font-custom font-bold uppercase leading-tight mb-1">
                            <?php echo esc_html($baslik); ?>
                        </h3>
                        <p class="text-white/80 text-[10px] font-bold uppercase tracking-[0.2em] leading-none">
                            <?php echo esc_html($alt_baslik); ?>
                        </p>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <?php foreach ($default_banners as $banner): ?>
                <a href="<?php echo esc_url($banner['link']); ?>"
                    class="relative aspect-[3/2] group overflow-hidden modern-shadow border-4 border-white block">
                    <img src="<?php echo esc_url($banner['gorsel']); ?>"
                        class="w-full h-full object-cover  transition-all group-hover:scale-105 duration-500">
                    <!-- Alt Degrade ve Yazılar -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity">
                    </div>

                    <div class="absolute inset-x-0 bottom-0 p-6 flex flex-col items-center text-center">
                        <h3 class="text-white text-2xl md:text-3xl font-custom font-bold uppercase leading-tight mb-1">
                            <?php echo esc_html($banner['baslik']); ?>
                        </h3>
                        <p class="text-white/80 text-[10px] font-bold uppercase tracking-[0.2em] leading-none">
                            <?php echo esc_html($banner['alt_baslik']); ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</section>