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
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sehir ASC, tarih ASC, mekan ASC, saat ASC, id ASC");
    foreach ($results as $row) {
        $sehir_slug = sanitize_title($row->sehir);
        if (!isset($sehirler[$sehir_slug])) {
            $sehirler[$sehir_slug] = $row->sehir;
        }
        
        // Şehir -> Tarih -> Mekan -> Programlar şeklinde grupla
        $programs[$sehir_slug][$row->tarih][$row->mekan][] = $row;
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
            <h2 class="text-4xl font-custom font-bold mb-4 uppercase tracking-tighter"><?php echo esc_html($baslik); ?></h2>
            <p class="text-gray-600 font-serif italic"><?php echo esc_html($aciklama); ?></p>
        </div>

        <div class="max-w-4xl mx-auto">

            <?php if (!empty($sehirler)): ?>
                <!-- Şehir Tabları -->
                <div class="flex flex-wrap justify-center gap-4 mb-12" id="city-tabs">
                    <?php $first_city = true;
                    foreach ($sehirler as $slug => $isim): ?>
                        <button
                            class="city-tab px-10 py-4 font-heading font-bold modern-shadow hover-lift transition-all text-lg <?php echo $first_city ? 'bg-red text-white' : 'bg-white text-warmgray'; ?>"
                            data-city="<?php echo esc_attr($slug); ?>">
                            <?php echo esc_html(mb_strtoupper($isim, 'UTF-8')); ?>
                        </button>
                        <?php $first_city = false; endforeach; ?>
                </div>

                <div id="program-main-container">
                    <?php $first_city = true;
                    foreach ($programs as $city_slug => $tarihler): ?>
                        <div class="city-content animate-fade-in" id="city-<?php echo esc_attr($city_slug); ?>"
                            style="display: <?php echo $first_city ? 'block' : 'none'; ?>;">
                            
                            <!-- Tarih Tabları (Şehir içindeki günler) -->
                            <div class="flex flex-wrap justify-center gap-2 mb-8 bg-warmgray/5 p-2 rounded-lg border border-warmgray/10">
                                <?php $first_date = true;
                                foreach ($tarihler as $tarih => $mekanlar): 
                                    $tarih_slug = sanitize_title($city_slug . '-' . $tarih);
                                ?>
                                    <button
                                        class="date-tab px-6 py-2 font-heading font-bold text-sm transition-all rounded-md <?php echo $first_date ? 'bg-red text-white' : 'bg-transparent text-warmgray hover:bg-warmgray/10'; ?>"
                                        data-date="<?php echo esc_attr($tarih_slug); ?>">
                                        <?php echo date('d.m.Y', strtotime($tarih)); ?>
                                    </button>
                                <?php $first_date = false; endforeach; ?>
                            </div>

                            <!-- Tarih İçerikleri -->
                            <?php $first_date = true;
                            foreach ($tarihler as $tarih => $mekanlar): 
                                $tarih_slug = sanitize_title($city_slug . '-' . $tarih);
                            ?>
                                <div class="date-content space-y-12" id="date-<?php echo esc_attr($tarih_slug); ?>"
                                     style="display: <?php echo $first_date ? 'block' : 'none'; ?>;">
                                    
                                    <?php foreach ($mekanlar as $mekan_adi => $items): ?>
                                        <div class="venue-group bg-white p-8 modern-shadow border-t-4 border-orange">
                                            <div class="flex items-center gap-4 mb-8 border-b border-orange/20 pb-4">
                                                <div class="w-10 h-10 bg-red/10 flex items-center justify-center rounded-full text-red">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                </div>
                                                <h3 class="text-2xl font-heading font-bold text-warmgray uppercase tracking-wider"><?php echo esc_html($mekan_adi); ?></h3>
                                            </div>

                                            <div class="space-y-12">
                                                <?php 
                                                // Seansları saate göre grupla (Aynı saatte başlayanları birleştir)
                                                $sessions = [];
                                                foreach ($items as $item) {
                                                    $sessions[$item->saat][] = $item;
                                                }

                                                foreach ($sessions as $saat => $session_items): 
                                                    $is_multi = count($session_items) > 1;
                                                ?>
                                                    <div class="session-block <?php echo $is_multi ? 'border-y border-orange/20 py-8 my-4' : ''; ?>">
                                                        <?php if ($is_multi): ?>
                                                            <div class="text-[10px] font-heading font-bold text-red uppercase tracking-[0.4em] mb-8 flex items-center gap-4">
                                                                <span class="flex-shrink-0">ORTAK SEANS</span>
                                                                <div class="h-[1px] w-full bg-orange/10"></div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="space-y-8">
                                                            <?php foreach ($session_items as $index => $item): ?>
                                                                <div class="flex flex-col md:flex-row justify-between md:items-center <?php echo ($index < count($session_items) - 1) ? 'border-b border-orange/5 pb-8' : ''; ?>">
                                                                    <div class="mb-4 md:mb-0">
                                                                        <div class="flex flex-wrap items-center gap-3 mb-2">
                                                                            <h4 class="font-bold text-xl font-heading text-warmgray leading-tight">
                                                                                <?php echo esc_html($item->film_adi); ?>
                                                                            </h4>
                                                                            <?php if (isset($item->is_special) && $item->is_special): ?>
                                                                                <span class="bg-red text-white text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-widest shadow-sm">Özel Gösterim</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        
                                                                        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-500 font-serif">
                                                                            <?php if (!empty($item->sure)): ?>
                                                                                <span class="flex items-center bg-cream/50 px-2 py-1 rounded">
                                                                                    <svg class="w-4 h-4 mr-2 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                                    <?php echo esc_html($item->sure); ?>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>

                                                                        <?php if (!empty($item->etkinlik)): ?>
                                                                            <div class="mt-4 inline-flex items-center bg-red/5 border-l-4 border-red px-4 py-2 text-sm text-warmgray italic font-serif rounded-r-md">
                                                                                <svg class="w-4 h-4 mr-3 text-red" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                                                                                <?php echo esc_html($item->etkinlik); ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    
                                                                    <?php if ($index === 0): ?>
                                                                        <div class="text-left md:text-right flex flex-row md:flex-col items-center md:items-end gap-3 md:gap-1">
                                                                            <span class="block font-bold text-4xl text-red font-heading tracking-tighter leading-none"><?php echo esc_html($item->saat); ?></span>
                                                                            <span class="text-[10px] text-gray-400 font-heading tracking-[0.2em] uppercase">BAŞLAYACAK</span>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="hidden md:block w-32"></div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php $first_date = false; endforeach; ?>
                        </div>
                    <?php $first_city = false; endforeach; ?>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Şehir değiştirme
                        const cityTabs = document.querySelectorAll('.city-tab');
                        const cityContents = document.querySelectorAll('.city-content');

                        cityTabs.forEach(tab => {
                            tab.addEventListener('click', function () {
                                const city = this.getAttribute('data-city');
                                
                                cityContents.forEach(c => c.style.display = 'none');
                                const targetCity = document.getElementById('city-' + city);
                                if (targetCity) targetCity.style.display = 'block';

                                cityTabs.forEach(t => {
                                    t.classList.remove('bg-red', 'text-white');
                                    t.classList.add('bg-white', 'text-warmgray');
                                });
                                this.classList.remove('bg-white', 'text-warmgray');
                                this.classList.add('bg-red', 'text-white');
                            });
                        });

                        // Tarih değiştirme
                        const dateTabs = document.querySelectorAll('.date-tab');
                        const dateContents = document.querySelectorAll('.date-content');

                        dateTabs.forEach(tab => {
                            tab.addEventListener('click', function () {
                                // Sadece aynı şehir içindeki tarihleri yönet
                                const parentCity = this.closest('.city-content');
                                const sameCityDateContents = parentCity.querySelectorAll('.date-content');
                                const sameCityDateTabs = parentCity.querySelectorAll('.date-tab');
                                
                                const date = this.getAttribute('data-date');

                                sameCityDateContents.forEach(c => c.style.display = 'none');
                                const targetDate = document.getElementById('date-' + date);
                                if (targetDate) targetDate.style.display = 'block';

                                sameCityDateTabs.forEach(t => {
                                    t.classList.remove('bg-red', 'text-white');
                                    t.classList.add('bg-transparent', 'text-warmgray', 'hover:bg-warmgray/10');
                                });
                                this.classList.remove('bg-transparent', 'text-warmgray', 'hover:bg-warmgray/10');
                                this.classList.add('bg-red', 'text-white');
                            });
                        });
                    });
                </script>
            <?php else: ?>
                <div class="bg-white border-4 border-orange p-12 modern-shadow text-center">
                    <p class="text-gray-400 font-serif italic text-lg text-warmgray">Henüz program eklenmemiş. Panelden Excel ile veri yükleyebilirsiniz.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>