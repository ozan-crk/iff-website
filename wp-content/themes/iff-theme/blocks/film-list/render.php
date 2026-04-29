<?php
/**
 * Film Listesi Block Template.
 */

$baslik = get_field('baslik') ?: 'FİLM LİSTESİ';
$veri_kaynagi = get_field('veri_kaynagi') ?: 'manuel';
$filmler = [];

if ($veri_kaynagi === 'json') {
    $json_raw = get_field('json_verisi');
    $filmler = json_decode($json_raw, true) ?: [];
} else {
    $filmler_raw = get_field('filmler') ?: [];
    // Manuel veriyi JSON formatıyla uyumlu hale getirelim
    foreach ($filmler_raw as $f) {
        $filmler[] = [
            'film_adi' => $f['film_adi'],
            'yapim_yili' => $f['yapim_yili'],
            'yonetmen' => $f['yonetmen']
        ];
    }
}

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-20 px-6 film-list-block';
if (!$bg_color) {
    $className .= ' bg-cream';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>

<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto">
        <div class="max-w-4xl mb-12">
            <h2 class="text-4xl font-custom font-bold text-warmgray mb-4 uppercase italic border-l-8 border-red pl-6 tracking-tighter"><?php echo esc_html($baslik); ?></h2>
        </div>

        <div class="overflow-x-auto modern-shadow">
            <table class="w-full bg-white border-4 border-warmgray text-left">
                <thead>
                    <tr class="bg-warmgray text-white uppercase text-sm tracking-widest font-heading font-bold">
                        <th class="py-4 px-6 border-r border-white/20">Film Adı</th>
                        <th class="py-4 px-6 border-r border-white/20 text-center">Yıl</th>
                        <th class="py-4 px-6">Yönetmen</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-warmgray/10">
                    <?php if (!empty($filmler)): ?>
                        <?php foreach ($filmler as $film): ?>
                            <tr class="hover:bg-red/5 transition-colors group">
                                <td class="py-4 px-6 font-bold text-warmgray uppercase group-hover:text-red transition-colors italic"><?php echo esc_html($film['film_adi'] ?? '-'); ?></td>
                                <td class="py-4 px-6 text-center font-serif text-gray-500 italic"><?php echo esc_html($film['yapim_yili'] ?? '-'); ?></td>
                                <td class="py-4 px-6 font-serif text-gray-600"><?php echo esc_html($film['yonetmen'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="py-12 text-center text-gray-400 font-serif italic bg-gray-50">Henüz film eklenmedi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($veri_kaynagi === 'json' && empty($filmler) && !empty($json_raw)): ?>
            <div class="mt-4 p-4 bg-red/10 text-red text-xs font-mono">
                Hata: JSON formatı geçersiz. Lütfen [ { "film_adi": "...", "yapim_yili": "...", "yonetmen": "..." } ] yapısını kontrol edin.
            </div>
        <?php endif; ?>
    </div>
</section>
