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
$bg_color = get_field('arka_plan_rengi');
$className = 'py-24 px-6 program-block';
if (!$bg_color) {
    $className .= ' bg-cream';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<!-- Aylık Gösterim Programları - İl İl -->
<section id="program" class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
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
                                                <h3 class="text-2xl font-heading font-bold text-warmgray tracking-wider"><?php echo esc_html($mekan_adi); ?></h3>
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
                                                    $has_gala = false;
                                                    foreach($session_items as $si) if(isset($si->is_gala) && $si->is_gala) $has_gala = true;
                                                ?>
                                                    <div class="session-block <?php echo $is_multi ? 'border-y border-orange/20 py-8 my-4' : ''; ?> <?php echo $has_gala ? 'gala-session' : ''; ?>">
                                                        <?php if ($has_gala): ?>
                                                            <div class="bg-red text-white text-center py-2 px-4 font-heading font-bold uppercase tracking-[0.3em] text-sm mb-8 -mx-8 md:-mx-8">
                                                                <?php echo esc_html($session_items[0]->film_adi); ?>
                                                            </div>
                                                        <?php elseif ($is_multi): ?>
                                                            <div class="text-[10px] font-heading font-bold text-red uppercase tracking-[0.4em] mb-8 flex items-center gap-4">
                                                                <span class="flex-shrink-0">ORTAK SEANS</span>
                                                                <div class="h-[1px] w-full bg-orange/10"></div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="space-y-8">
                                                            <?php foreach ($session_items as $index => $item): 
                                                                 $is_item_gala = (isset($item->is_gala) && $item->is_gala);
                                                             ?>
                                                                 <div class="flex flex-col md:flex-row justify-between md:items-center <?php echo ($index < count($session_items) - 1) ? 'border-b border-orange/5 pb-8' : ''; ?>">
                                                                     <div class="mb-4 md:mb-0">
                                                                         <div class="flex flex-wrap items-center gap-3 mb-2">
                                                                             <?php if (!$has_gala): ?>
                                                                                <h4 class="font-bold text-xl font-heading text-warmgray leading-tight">
                                                                                    <?php echo esc_html($item->film_adi); ?>
                                                                                </h4>
                                                                             <?php endif; ?>
                                                                             
                                                                             <?php if ($is_item_gala && !$has_gala): ?>
                                                                                 <span class="bg-orange text-white text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-widest shadow-md flex items-center gap-2">
                                                                                     <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                                                     <?php echo mb_convert_case('FESTİVAL GALASI', MB_CASE_UPPER, "UTF-8"); ?>
                                                                                 </span>
                                                                             <?php elseif (isset($item->tur) && $item->tur === 'etkinlik'): ?>
                                                                                 <span class="bg-warmgray text-white text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-widest shadow-sm">Etkinlik</span>
                                                                             <?php elseif (isset($item->is_special) && $item->is_special): ?>
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

                                                                             <button style="display: none !important;" class="add-to-personal-program flex items-center gap-2 text-red hover:text-orange transition-colors group"
                                                                                     data-id="<?php echo esc_attr($item->id); ?>"
                                                                                     data-title="<?php echo esc_attr($item->film_adi); ?>"
                                                                                     data-date="<?php echo esc_attr($tarih); ?>"
                                                                                     data-time="<?php echo esc_attr($item->saat); ?>"
                                                                                     data-venue="<?php echo esc_attr($mekan_adi); ?>"
                                                                                     data-duration="<?php echo esc_attr($item->sure); ?>">
                                                                                 <svg class="w-5 h-5 group-[.is-added]:fill-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                                                                 <span class="font-heading font-bold text-[10px] uppercase tracking-widest">Takvime Ekle</span>
                                                                             </button>
                                                                         </div>

                                                                         <?php if (!empty($item->etkinlik)): ?>
                                                                            <div class="mt-4 inline-flex items-start bg-red/5 border-l-4 border-red px-4 py-3 text-sm text-warmgray font-serif rounded-r-md w-full">
                                                                                <svg class="w-4 h-4 mr-3 text-red mt-1 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                                                                                <div class="leading-relaxed flex-1">
                                                                                    <?php 
                                                                                    if ($has_gala) {
                                                                                        $parts = explode(',', $item->etkinlik);
                                                                                        foreach ($parts as $part) {
                                                                                            $item_parts = explode(':', $part, 2);
                                                                                            $display_text = count($item_parts) > 1 
                                                                                                ? '<strong class="font-bold">' . esc_html(trim($item_parts[0])) . ':</strong> ' . esc_html(trim($item_parts[1]))
                                                                                                : esc_html(trim($part));
                                                                                                
                                                                                            echo '<div class="flex items-start gap-3 mb-2 text-red">
                                                                                                    <div class="w-1.5 h-1.5 rounded-full bg-red mt-2 shrink-0"></div>
                                                                                                    <span class="text-[15px] leading-tight">' . $display_text . '</span>
                                                                                                  </div>';
                                                                                        }
                                                                                    } else {
                                                                                        echo wp_kses_post(str_replace("\n", '<br>', $item->etkinlik)); 
                                                                                    }
                                                                                    ?>
                                                                                </div>
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