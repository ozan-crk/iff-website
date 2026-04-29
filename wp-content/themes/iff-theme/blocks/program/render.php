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
                                <div class="flex flex-col md:flex-row justify-between md:items-center border-b border-orange/20 pb-6 pt-2">
                                    <div class="mb-4 md:mb-0">
                                        <div class="flex items-center gap-3 mb-1">
                                            <h4 class="font-bold text-lg font-heading text-warmgray">
                                                <?php echo esc_html($item->film_adi); ?>
                                            </h4>
                                            <?php if (isset($item->is_special) && $item->is_special): ?>
                                                <span class="bg-red text-white text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Özel Gösterim</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 font-serif">
                                            <span class="flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                <?php echo esc_html($item->mekan); ?>
                                            </span>
                                            <?php if (!empty($item->sure)): ?>
                                                <span class="flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <?php echo esc_html($item->sure); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($item->etkinlik)): ?>
                                            <div class="mt-2 inline-flex items-center bg-gray-50 border-l-4 border-red px-3 py-1 text-xs text-warmgray italic font-serif">
                                                <svg class="w-3 h-3 mr-2 text-red" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                                                <?php echo esc_html($item->etkinlik); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-left md:text-right flex flex-row md:flex-col items-center md:items-end gap-3 md:gap-0">
                                        <span class="block font-bold text-2xl text-red md:text-orange font-heading"><?php echo esc_html($item->saat); ?></span>
                                        <span class="text-xs text-gray-500 uppercase tracking-widest font-heading"><?php echo esc_html($item->tarih); ?></span>
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