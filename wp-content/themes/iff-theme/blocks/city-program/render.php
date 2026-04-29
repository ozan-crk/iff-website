<?php
/**
 * City Program Block Template (Single City Focus).
 */

$baslik = get_field('baslik') ?: 'PROGRAM';
$sehir_adi = get_field('sehir_adi');
$pdf_url = get_field('program_pdf');

global $wpdb;
$table_name = $wpdb->prefix . 'iff_programs';
$screenings = [];

if ($sehir_adi && $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $screenings = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE sehir = %s ORDER BY tarih ASC, mekan ASC, saat ASC, id ASC",
        $sehir_adi
    ));
}

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'city-program-block mb-24';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
if ($bg_color) {
    $className .= ' p-8 md:p-12'; // Arka plan varsa iç boşluk ekle
}
?>

<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <!-- Orijinal Başlık Yapısı (Sola Yaslı) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 border-b-8 border-orange pb-6">
        <div>
            <h2 class="text-6xl font-custom font-bold text-warmgray uppercase tracking-tighter leading-none">
                <?php echo esc_html($sehir_adi ?: 'ŞEHİR SEÇİN'); ?>
            </h2>
            <h3 class="text-2xl font-custom text-red mt-2 uppercase tracking-tight">
                <?php echo esc_html($baslik); ?>
            </h3>
        </div>

        <?php if ($pdf_url): ?>
            <a href="<?php echo esc_url($pdf_url); ?>" target="_blank"
                class="mt-6 md:mt-0 bg-red text-white px-8 py-4 font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all inline-flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>PROGRAMI İNDİR</span>
            </a>
        <?php endif; ?>
    </div>

    <?php
    // Gösterimleri Tarih -> Mekan -> Seans şeklinde grupla
    $screenings_by_date = [];
    if (!empty($screenings)) {
        foreach ($screenings as $item) {
            $screenings_by_date[$item->tarih][$item->mekan][] = $item;
        }
    }
    $dates = array_keys($screenings_by_date);
    $block_id = $block['id'];
    ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Sol Kolon: Program Listesi -->
        <div class="lg:col-span-8">
            <?php if (!empty($screenings_by_date)): ?>
                
                <!-- Tarih Tabları (Anasayfa ile aynı pilli stil) -->
                <div class="flex flex-wrap gap-2 mb-8 bg-warmgray/5 p-2 rounded-lg border border-warmgray/10" id="city-program-tabs-<?php echo esc_attr($block_id); ?>">
                    <?php 
                    $i = 0;
                    foreach ($dates as $date): 
                        $formatted_date = date('d.m.Y', strtotime($date));
                        $is_active = ($i === 0);
                    ?>
                        <button 
                            class="city-program-tab px-6 py-2.5 font-heading font-bold text-xs transition-all rounded-md <?php echo $is_active ? 'bg-orange text-white' : 'bg-transparent text-warmgray hover:bg-warmgray/10'; ?>"
                            data-target="pane-<?php echo esc_attr($block_id); ?>-<?php echo $i; ?>">
                            <?php echo esc_html($formatted_date); ?>
                        </button>
                    <?php $i++; endforeach; ?>
                </div>

                <!-- Tab İçerikleri -->
                <div id="city-program-content-<?php echo esc_attr($block_id); ?>">
                    <?php 
                    $i = 0;
                    foreach ($screenings_by_date as $date => $mekanlar): 
                        $is_active = ($i === 0);
                    ?>
                        <div class="city-program-pane space-y-10 animate-fade-in" 
                             id="pane-<?php echo esc_attr($block_id); ?>-<?php echo $i; ?>" 
                             style="display: <?php echo $is_active ? 'block' : 'none'; ?>;">
                            
                            <?php foreach ($mekanlar as $mekan_adi => $items): ?>
                                <div class="venue-group bg-white p-6 md:p-8 modern-shadow border-t-4 border-orange">
                                    <div class="flex items-center gap-4 mb-8 border-b border-orange/20 pb-4">
                                        <div class="w-8 h-8 bg-red/10 flex items-center justify-center rounded-full text-red">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                        <h3 class="text-xl font-heading font-bold text-warmgray uppercase tracking-wider"><?php echo esc_html($mekan_adi); ?></h3>
                                    </div>

                                    <div class="space-y-8">
                                        <?php 
                                        $sessions = [];
                                        foreach ($items as $item) { $sessions[$item->saat][] = $item; }

                                        foreach ($sessions as $saat => $session_items): 
                                            $is_multi = count($session_items) > 1;
                                            $has_gala = false;
                                            foreach($session_items as $si) if(isset($si->is_gala) && $si->is_gala) $has_gala = true;
                                        ?>
                                            <div class="session-block <?php echo $is_multi ? 'border-y border-orange/20 py-8 my-4' : ''; ?> <?php echo $has_gala ? 'gala-session' : ''; ?>">
                                                <?php if ($has_gala): ?>
                                                    <div class="bg-red text-white text-center py-2 px-4 font-heading font-bold uppercase tracking-[0.3em] text-sm mb-8 -mx-6 md:-mx-8">
                                                        <?php echo esc_html($session_items[0]->film_adi); ?>
                                                    </div>
                                                <?php elseif ($is_multi): ?>
                                                    <div class="text-[9px] font-heading font-bold text-red uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                                                        <span class="flex-shrink-0">ORTAK SEANS</span>
                                                        <div class="h-[1px] w-full bg-orange/10"></div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="space-y-8">
                                                    <?php foreach ($session_items as $idx => $item): 
                                                        $is_item_gala = (isset($item->is_gala) && $item->is_gala);
                                                    ?>
                                                        <div class="flex flex-col md:flex-row justify-between md:items-center <?php echo ($idx < count($session_items) - 1) ? 'border-b border-orange/5 pb-8' : ''; ?>">
                                                            <div class="mb-4 md:mb-0">
                                                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                                                    <?php if (!$has_gala): ?>
                                                                        <h4 class="font-bold text-lg font-heading text-warmgray leading-tight">
                                                                            <?php echo esc_html($item->film_adi); ?>
                                                                        </h4>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php if ($is_item_gala && !$has_gala): ?>
                                                                        <span class="bg-orange text-white text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-widest shadow-md flex items-center gap-1.5">
                                                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                                            FESTİVAL GALASI
                                                                        </span>
                                                                    <?php elseif (isset($item->is_special) && $item->is_special): ?>
                                                                        <span class="bg-red text-white text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-widest shadow-sm">Özel Gösterim</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                
                                                                <div class="flex items-center gap-4 text-xs text-gray-500 font-serif italic">
                                                                    <?php if (!empty($item->sure)): ?>
                                                                        <span class="flex items-center bg-cream px-2 py-0.5 rounded-sm">
                                                                            <?php echo esc_html($item->sure); ?>
                                                                        </span>
                                                                    <?php endif; ?>

                                                                    <button style="display: none !important;" class="add-to-personal-program flex items-center gap-2 text-red hover:text-orange transition-colors group not-italic"
                                                                            data-id="<?php echo esc_attr($item->id); ?>"
                                                                            data-title="<?php echo esc_attr($item->film_adi); ?>"
                                                                            data-date="<?php echo esc_attr($date); ?>"
                                                                            data-time="<?php echo esc_attr($item->saat); ?>"
                                                                            data-venue="<?php echo esc_attr($mekan_adi); ?>"
                                                                            data-duration="<?php echo esc_attr($item->sure); ?>">
                                                                        <svg class="w-4 h-4 group-[.is-added]:fill-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                                                        <span class="font-heading font-bold text-[9px] uppercase tracking-widest">Takvime Ekle</span>
                                                                    </button>
                                                                </div>

                                                                <?php if (!empty($item->etkinlik)): ?>
                                                                    <div class="mt-4 flex items-start bg-red/5 border-l-4 border-red px-4 py-3 text-sm text-warmgray italic font-serif rounded-r-md w-full">
                                                                        <?php if (!$has_gala): ?>
                                                                            <svg class="w-4 h-4 mr-3 text-red mt-1 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                                                                        <?php endif; ?>
                                                                        <div class="leading-relaxed flex-1">
                                                                            <?php 
                                                                            if ($has_gala) {
                                                                                $parts = explode(',', $item->etkinlik);
                                                                                foreach ($parts as $part) {
                                                                                    $item_parts = explode(':', $part, 2);
                                                                                    $display_text = count($item_parts) > 1 
                                                                                        ? '<strong class="font-bold">' . esc_html(trim($item_parts[0])) . ':</strong> ' . esc_html(trim($item_parts[1]))
                                                                                        : esc_html(trim($part));
                                                                                        
                                                                                    echo '<div class="flex items-start gap-3 mb-2 text-red not-italic font-sans">
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
                                                            
                                                            <?php if ($idx === 0): ?>
                                                                <div class="text-left md:text-right flex flex-row md:flex-col items-center md:items-end gap-3 md:gap-0">
                                                                    <span class="block font-bold text-3xl text-red font-heading tracking-tighter leading-none"><?php echo esc_html($item->saat); ?></span>
                                                                    <span class="text-[9px] text-gray-400 font-heading tracking-widest uppercase">SAATİNDE</span>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="hidden md:block w-24"></div>
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
                    <?php $i++; endforeach; ?>
                </div>

                <script>
                    (function() {
                        const blockId = '<?php echo esc_js($block_id); ?>';
                        const container = document.getElementById('city-program-tabs-' + blockId);
                        if(!container) return;
                        
                        const tabs = container.querySelectorAll('.city-program-tab');
                        const panes = document.querySelectorAll('#city-program-content-' + blockId + ' .city-program-pane');

                        tabs.forEach(tab => {
                            tab.addEventListener('click', function() {
                                const targetId = this.getAttribute('data-target');

                                tabs.forEach(t => {
                                    t.classList.remove('bg-orange', 'text-white');
                                    t.classList.add('bg-transparent', 'text-warmgray', 'hover:bg-warmgray/10');
                                });
                                this.classList.remove('bg-transparent', 'text-warmgray', 'hover:bg-warmgray/10');
                                this.classList.add('bg-orange', 'text-white');

                                panes.forEach(p => p.style.display = 'none');
                                const targetPane = document.getElementById(targetId);
                                if(targetPane) targetPane.style.display = 'block';
                            });
                        });
                    })();
                </script>

            <?php else: ?>
                <div class="bg-white p-16 text-center shadow-2xl border-t-8 border-red">
                    <div class="mb-6">
                        <svg class="w-20 h-20 text-orange/20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <?php if ($pdf_url): ?>
                    
                        <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" class="inline-flex items-center space-x-3 bg-red text-white px-10 py-4 font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all">
                            <span>PDF PROGRAMI İNDİR</span>
                        </a>
                    <?php else: ?>
                        <p class="text-gray-400 font-serif italic text-lg text-warmgray">Bu şehir için henüz bir program eklenmemiş.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sağ Kolon: Ekstra Butonlar (Orijinal Şablon Korundu) -->
        <aside class="lg:col-span-4 space-y-8">
            <?php if (have_rows('ekstra_butonlar')): ?>
                <h4 class="font-heading font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-6 border-b-2 border-orange/20 pb-2">HIZLI BAĞLANTILAR</h4>
                <div class="flex flex-col space-y-4">
                    <?php while (have_rows('ekstra_butonlar')): the_row();
                        $metin = get_sub_field('metin');
                        $url = get_sub_field('url');
                        $stil = get_sub_field('stil');
                        $btn_class = 'w-full text-center py-4 font-heading font-bold uppercase tracking-widest transition-all modern-shadow hover-lift ';
                        if ($stil === 'red') $btn_class .= 'bg-red text-white';
                        elseif ($stil === 'gray') $btn_class .= 'bg-warmgray text-white';
                        else $btn_class .= 'bg-white text-warmgray border-2 border-orange/10';
                        ?>
                        <a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($btn_class); ?>">
                            <?php echo esc_html($metin); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <!-- Sidebar'a opsiyonel bir bilgi kutusu ekleyelim anasayfa havasını bozmadan -->
            <div class="bg-cream p-6 border-l-4 border-red shadow-sm">
                <p class="text-xs text-warmgray font-serif italic leading-relaxed">
                    Festival programındaki değişiklikler ve duyurular için sosyal medya hesaplarımızı takip edebilirsiniz.
                </p>
            </div>
        </aside>

    </div>
</section>