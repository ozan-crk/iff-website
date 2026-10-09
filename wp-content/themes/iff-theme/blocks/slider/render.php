<?php
/**
 * Slider Block Template (Boxed Layout Manşet Slider).
 */

$block_id = 'slider-' . ($block['id'] ?? uniqid());

// Repeater slaytları alalım
$slaytlar = [];

if (have_rows('slaytlar')) {
    while (have_rows('slaytlar')) {
        the_row();
        $gorsel_field = get_sub_field('gorsel');
        $gorsel_url = '';
        if (is_array($gorsel_field) && !empty($gorsel_field['url'])) {
            $gorsel_url = $gorsel_field['url'];
        } elseif (is_numeric($gorsel_field)) {
            $gorsel_url = wp_get_attachment_image_url($gorsel_field, 'full');
        } elseif (is_string($gorsel_field)) {
            $gorsel_url = $gorsel_field;
        }

        $slaytlar[] = [
            'etiket'      => get_sub_field('etiket') ?: 'Gündem',
            'etiket_rengi'=> get_sub_field('etiket_rengi') ?: 'orange',
            'baslik'      => get_sub_field('baslik') ?: '',
            'aciklama'    => get_sub_field('aciklama') ?: '',
            'buton_metni' => get_sub_field('buton_metni') ?: 'OKU',
            'buton_linki' => get_sub_field('buton_linki') ?: '#',
            'gorsel'      => $gorsel_url ?: 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg',
        ];
    }
}

// Fallback (varsayılan slaytlar)
if (empty($slaytlar)) {
    $slaytlar = [
        [
            'etiket'      => 'Gündem',
            'etiket_rengi'=> 'orange',
            'baslik'      => 'Saraybosna\'da Emekçilerin Sesi Yükseldi',
            'aciklama'    => 'İşçi Filmleri ekipleri Bosna\'da sinemacılarla buluştu.',
            'buton_metni' => 'OKU',
            'buton_linki' => '#',
            'gorsel'      => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg',
        ],
        [
            'etiket'      => 'Duyuru',
            'etiket_rengi'=> 'red',
            'baslik'      => 'Yeni Afiş Tasarımı Belirlendi',
            'aciklama'    => 'Bu yılın görsel dünyasına dair ipuçlarını keşfedin.',
            'buton_metni' => 'AFİŞİ GÖR',
            'buton_linki' => '#',
            'gorsel'      => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg',
        ],
    ];
}

$bg_color = get_field('arka_plan_rengi');
$className = 'py-8 slider-block-section';
if (!$bg_color) {
    $className .= ' bg-cream';
}

if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
$oto_sure = (int)(get_field('otomatik_gecis_suresi') ?: 7000);
if ($oto_sure < 2000) $oto_sure = 7000;
?>

<!-- Slider: Boxed Layout -->
<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>" data-slider-root data-duration="<?php echo esc_attr($oto_sure); ?>">
    <div class="container mx-auto px-6">
        <div class="relative h-[480px] md:h-[520px] bg-warmgray overflow-hidden modern-shadow border-4 border-white group/slider">
            <!-- Slider Track -->
            <div class="slider-track h-full flex transition-transform duration-700 ease-in-out">
                <?php foreach ($slaytlar as $index => $slayt): 
                    $badge_bg = ($slayt['etiket_rengi'] === 'red') ? 'bg-red' : 'bg-orange';
                ?>
                    <div class="min-w-full h-full relative shrink-0">
                        <img src="<?php echo esc_url($slayt['gorsel']); ?>" class="w-full h-full object-cover opacity-60" alt="<?php echo esc_attr($slayt['baslik']); ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent pointer-events-none"></div>
                        <div class="absolute bottom-10 left-6 right-6 md:bottom-12 md:left-12 md:right-12 z-10">
                            <?php if (!empty($slayt['etiket'])): ?>
                                <span class="<?php echo esc_attr($badge_bg); ?> text-white px-4 py-1 text-xs font-bold mb-3 md:mb-4 uppercase inline-block">
                                    <?php echo esc_html($slayt['etiket']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($slayt['baslik'])): ?>
                                <h2 class="text-white text-3xl md:text-5xl font-heading font-bold mb-3 md:mb-4 leading-tight max-w-4xl">
                                    <?php echo esc_html($slayt['baslik']); ?>
                                </h2>
                            <?php endif; ?>
                            
                            <?php if (!empty($slayt['aciklama'])): ?>
                                <p class="text-cream text-base md:text-lg font-serif max-w-2xl mb-5 md:mb-6 line-clamp-2 md:line-clamp-none">
                                    <?php echo esc_html($slayt['aciklama']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($slayt['buton_metni'])): ?>
                                <a href="<?php echo esc_url($slayt['buton_linki']); ?>" class="inline-block bg-white text-red px-8 py-3 font-heading font-bold hover:bg-cream transition-all hover-lift text-sm">
                                    <?php echo esc_html($slayt['buton_metni']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Slider Nav (Dots) -->
            <?php if (count($slaytlar) > 1): ?>
                <div class="absolute bottom-6 right-6 md:bottom-6 md:right-12 flex space-x-2 z-20">
                    <?php foreach ($slaytlar as $index => $slayt): ?>
                        <button type="button" class="slider-dot w-3.5 h-3.5 rounded-full border-2 border-white transition-all <?php echo $index === 0 ? 'bg-orange active' : 'bg-transparent'; ?>" data-slide-to="<?php echo $index; ?>" aria-label="Slayt <?php echo $index + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function() {
    function initInstanceSlider() {
        const root = document.getElementById('<?php echo esc_js($block_id); ?>');
        if (!root) return;
        
        const track = root.querySelector('.slider-track');
        const dots = root.querySelectorAll('.slider-dot');
        const total = <?php echo count($slaytlar); ?>;
        const duration = parseInt(root.dataset.duration, 10) || 7000;
        
        if (!track || total <= 1) return;

        let currentIndex = 0;
        let timer = null;

        function goToSlide(index) {
            currentIndex = (index + total) % total;
            track.style.transform = `translateX(-${currentIndex * 100}%)`;
            dots.forEach((dot, i) => {
                if (i === currentIndex) {
                    dot.classList.add('bg-orange', 'active');
                    dot.classList.remove('bg-transparent');
                } else {
                    dot.classList.remove('bg-orange', 'active');
                    dot.classList.add('bg-transparent');
                }
            });
        }

        function startAuto() {
            stopAuto();
            timer = setInterval(() => {
                goToSlide(currentIndex + 1);
            }, duration);
        }

        function stopAuto() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                goToSlide(idx);
                startAuto();
            });
        });

        root.addEventListener('mouseenter', stopAuto);
        root.addEventListener('mouseleave', startAuto);

        startAuto();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInstanceSlider);
    } else {
        initInstanceSlider();
    }
})();
</script>
