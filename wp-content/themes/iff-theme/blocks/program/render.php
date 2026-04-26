<?php
/**
 * Program Block Template.
 */

$baslik = get_field('baslik') ?: 'GÖSTERİM PROGRAMI';
$aciklama = get_field('aciklama') ?: 'Şehrinizi seçin ve film programını keşfedin.';

global $wpdb;
$table_name = $wpdb->prefix . 'iff_programs';
$programs = [];
$sehirler = [];

// Eklenti yüklü ve tablo varsa verileri çek
if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sehir ASC, tarih ASC, saat ASC");
    foreach ($results as $row) {
        $sehir_slug = sanitize_title($row->sehir);
        if (!in_array($row->sehir, $sehirler)) {
            $sehirler[$sehir_slug] = $row->sehir;
        }
        $programs[$sehir_slug][] = $row;
    }
}

// Gutenberg Ek CSS Sınıfları
$className = 'py-24 px-6 bg-cream program-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>
<!-- Aylık Gösterim Programları - İl İl -->
<section id="program" class="<?php echo esc_attr($className); ?>">
    <div class="container mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-custom font-bold mb-4"><?php echo esc_html($baslik); ?></h2>
            <p class="text-gray-600 font-serif"><?php echo esc_html($aciklama); ?></p>
        </div>

        <div class="max-w-4xl mx-auto">

            <?php if (!empty($sehirler)): ?>
                <div class="flex flex-wrap justify-center gap-4 mb-12" id="program-tabs">
                    <?php $first = true;
                    foreach ($sehirler as $slug => $isim): ?>
                        <button
                            class="city-tab px-8 py-3 font-heading font-bold modern-shadow hover-lift transition-all <?php echo $first ? 'bg-red text-white' : 'bg-white text-warmgray'; ?>"
                            data-city="<?php echo esc_attr($slug); ?>">
                            <?php echo esc_html(mb_strtoupper($isim, 'UTF-8')); ?>
                        </button>
                        <?php $first = false; endforeach; ?>
                </div>

                <div id="program-content-container" class="bg-white border-4 border-orange p-8 modern-shadow">
                    <?php $first = true;
                    foreach ($programs as $slug => $items): ?>
                        <div class="space-y-6 city-content" id="city-<?php echo esc_attr($slug); ?>"
                            style="display: <?php echo $first ? 'block' : 'none'; ?>;">
                            <?php foreach ($items as $item): ?>
                                <div class="flex justify-between items-center border-b border-orange/20 pb-4">
                                    <div>
                                        <h4 class="font-bold text-lg font-heading">Film: <?php echo esc_html($item->film_adi); ?>
                                        </h4>
                                        <p class="text-gray-600 font-serif text-sm">Yer: <?php echo esc_html($item->mekan); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="block font-bold text-orange"><?php echo esc_html($item->saat); ?></span>
                                        <span class="text-xs text-gray-500 uppercase"><?php echo esc_html($item->tarih); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php $first = false; endforeach; ?>
                </div>

                <!-- Tab değiştirme için basit JS -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const tabs = document.querySelectorAll('.city-tab');
                        const contents = document.querySelectorAll('.city-content');

                        tabs.forEach(tab => {
                            tab.addEventListener('click', function () {
                                const target = this.getAttribute('data-city');

                                // Tüm içerikleri gizle
                                contents.forEach(c => c.style.display = 'none');
                                // Seçileni göster
                                const targetContent = document.getElementById('city-' + target);
                                if (targetContent) targetContent.style.display = 'block';

                                // Buton stillerini güncelle
                                tabs.forEach(t => {
                                    t.classList.remove('bg-red', 'text-white');
                                    t.classList.add('bg-white', 'text-warmgray');
                                });
                                this.classList.remove('bg-white', 'text-warmgray');
                                this.classList.add('bg-red', 'text-white');
                            });
                        });
                    });
                </script>
            <?php else: ?>
                <div class="bg-white border-4 border-orange p-8 modern-shadow text-center">
                    <p class="text-gray-500 font-serif">Henüz program eklenmemiş. Panelden "Program Yönetimi" kısmından
                        Excel ile veri yükleyebilirsiniz.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>