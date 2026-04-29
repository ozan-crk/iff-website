<?php
/**
 * Quick Links Block Template.
 */

$baslik = get_field('baslik') ?: 'İşçi Filmleri Festivali';
$aciklama = get_field('aciklama') ?: "2006'dan bu yana kesintisiz devam eden festivalimiz, sinemayı halkla ve sokakla buluşturmaya kararlı.";

$kurumsal_baslik = get_field('kurumsal_baslik') ?: 'KURUMSAL';
$bulten_baslik = get_field('bulten_baslik') ?: 'BÜLTEN';
$bulten_aciklama = get_field('bulten_aciklama') ?: 'Yeni haberler ve duyurular için e-posta listemize katılın.';

$default_socials = [
    ['platform_adi' => 'IG', 'link' => '#', 'renk_kodu' => 'bg-warmgray'],
    ['platform_adi' => 'YT', 'link' => '#', 'renk_kodu' => 'bg-warmgray'],
    ['platform_adi' => 'X', 'link' => '#', 'renk_kodu' => 'bg-warmgray'],
    ['platform_adi' => 'FB', 'link' => '#', 'renk_kodu' => 'bg-warmgray'],
    ['platform_adi' => 'BSKY', 'link' => '#', 'renk_kodu' => 'bg-[#0085ff]']
];

$default_links = [
    ['link_metni' => 'Hakkımızda', 'link_url' => home_url('/hakkimizda')],
    ['link_metni' => 'İlkelerimiz', 'link_url' => home_url('/hakkimizda#ilkelerimiz')],
    ['link_metni' => 'Düzenleyiciler', 'link_url' => home_url('/hakkimizda#duzenleyiciler')],
    ['link_metni' => 'İletişim', 'link_url' => home_url('/iletisim')]
];

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-20 px-6 border-t-4 border-red quick-links-block';
if (!$bg_color) {
    $className .= ' bg-white';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<!-- Hızlı Erişim Linkleri & Sosyal Medya -->
<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto footer-grid grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="md:col-span-2">
            <h3 class="text-2xl font-heading font-bold mb-6 italic uppercase tracking-tighter"><?php echo esc_html($baslik); ?></h3>
            <p class="text-gray-600 font-serif mb-8 max-w-md"><?php echo esc_html($aciklama); ?></p>
            <div class="flex flex-wrap gap-4">
                <!-- Sosyal Medya İkonları -->
                <?php if (have_rows('sosyal_medya')): ?>
                    <?php while (have_rows('sosyal_medya')): the_row(); ?>
                        <a href="<?php echo esc_url(get_sub_field('link')); ?>" target="_blank" class="w-10 h-10 <?php echo esc_attr(get_sub_field('renk_kodu') ?: 'bg-warmgray'); ?> text-white flex items-center justify-center hover:bg-orange transition rounded text-xs font-bold modern-shadow"><?php echo esc_html(get_sub_field('platform_adi')); ?></a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <?php foreach ($default_socials as $social): ?>
                        <a href="<?php echo esc_url($social['link']); ?>" class="w-10 h-10 <?php echo esc_attr($social['renk_kodu']); ?> text-white flex items-center justify-center hover:bg-orange transition rounded text-xs font-bold modern-shadow"><?php echo esc_html($social['platform_adi']); ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <h4 class="font-heading font-bold mb-6 text-red uppercase tracking-widest text-sm"><?php echo esc_html($kurumsal_baslik); ?></h4>
            <ul class="space-y-3 font-serif text-gray-700 text-sm">
                <?php if (have_rows('kurumsal_linkler')): ?>
                    <?php while (have_rows('kurumsal_linkler')): the_row(); ?>
                        <li><a href="<?php echo esc_url(get_sub_field('link_url')); ?>" class="hover:text-orange transition underline decoration-orange/20"><?php echo esc_html(get_sub_field('link_metni')); ?></a></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <?php foreach ($default_links as $link): ?>
                        <li><a href="<?php echo esc_url($link['link_url']); ?>" class="hover:text-orange transition underline decoration-orange/20"><?php echo esc_html($link['link_metni']); ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <h4 class="font-heading font-bold mb-6 text-red uppercase tracking-widest text-sm"><?php echo esc_html($bulten_baslik); ?></h4>
            <p class="text-[10px] text-gray-500 mb-4 uppercase tracking-tighter"><?php echo esc_html($bulten_aciklama); ?></p>
            <div class="flex flex-col space-y-2">
                <input type="email" placeholder="E-posta" class="px-4 py-3 border-2 border-warmgray focus:border-orange outline-none font-serif text-xs">
                <button class="bg-warmgray text-white py-3 font-heading font-bold hover:bg-darkorange transition uppercase text-[10px] tracking-widest">KABUL ET</button>
            </div>
        </div>
    </div>
</section>
