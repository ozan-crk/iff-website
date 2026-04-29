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
$className = 'city-program-block py-20 bg-cream';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
?>

<section class="<?php echo esc_attr($className); ?>">
    <div class="container mx-auto px-6">
        
        <!-- Header: Anasayfadaki ile aynı stil -->
        <div class="text-center mb-16">
            <h2 class="text-6xl font-custom font-bold text-warmgray uppercase tracking-tighter leading-none mb-4">
                <?php echo esc_html($sehir_adi ?: 'ŞEHİR SEÇİN'); ?>
            </h2>
            <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                <h3 class="text-2xl font-custom text-red uppercase tracking-tight italic">
                    <?php echo esc_html($baslik); ?>
                </h3>
                <?php if ($pdf_url): ?>
                    <a href="<?php echo esc_url($pdf_url); ?>" target="_blank"
                        class="bg-red text-white px-6 py-3 text-xs font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all inline-flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>PDF PROGRAMI İNDİR</span>
                    </a>
                <?php endif; ?>
            </div>
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

        <div class="max-w-4xl mx-auto">
            <?php if (!empty($screenings_by_date)): ?>
                
                <!-- Tarih Tabları (Anasayfa ile aynı stil) -->
                <div class="flex flex-wrap justify-center gap-2 mb-12 bg-warmgray/5 p-2 rounded-lg border border-warmgray/10" id="city-program-tabs-<?php echo esc_attr($block_id); ?>">
                    <?php 
                    $i = 0;
                    foreach ($dates as $date): 
                        $formatted_date = date('d.m.Y', strtotime($date));
                        $is_active = ($i === 0);
                    ?>
                        <button 
                            class="city-program-tab px-8 py-3 font-heading font-bold text-sm transition-all rounded-md <?php echo $is_active ? 'bg-orange text-white' : 'bg-transparent text-warmgray hover:bg-warmgray/10'; ?>"
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
                        <div class="city-program-pane space-y-12 animate-fade-in" 
                             id="pane-<?php echo esc_attr($block_id); ?>-<?php echo $i; ?>" 
                             style="display: <?php echo $is_active ? 'block' : 'none'; ?>;">
                            
                            <?php foreach ($mekanlar as $mekan_adi => $items): ?>
                                <div class="venue-group bg-white p-8 modern-shadow border-t-4 border-orange">
                                    <div class="flex items-center gap-4 mb-8 border-b border-orange/20 pb-4">
                                        <div class="w-10 h-10 bg-red/10 flex items-center justify-center rounded-full text-red">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                        <h3 class="text-2xl font-heading font-bold text-warmgray uppercase tracking-wider"><?php echo esc_html($mekan_adi); ?></h3>
                                    </div>

                                    <div class="space-y-10">
                                        <?php 
                                        $sessions = [];
                                        foreach ($items as $item) { $sessions[$item->saat][] = $item; }

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
                                                    <?php foreach ($session_items as $idx => $item): ?>
                                                        <div class="flex flex-col md:flex-row justify-between md:items-center <?php echo ($idx < count($session_items) - 1) ? 'border-b border-orange/5 pb-8' : ''; ?>">
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
                                                            
                                                            <?php if ($idx === 0): ?>
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
                    <p class="text-gray-400 font-serif italic text-lg">Bu şehir için henüz bir program eklenmemiş.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ekstra Butonlar (Varsa alta ekleyelim) -->
        <?php if (have_rows('ekstra_butonlar')): ?>
            <div class="mt-20 max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php while (have_rows('ekstra_butonlar')): the_row();
                        $metin = get_sub_field('metin');
                        $url = get_sub_field('url');
                        $stil = get_sub_field('stil');
                        $btn_class = 'w-full text-center py-5 font-heading font-bold uppercase tracking-widest transition-all modern-shadow hover-lift ';
                        if ($stil === 'red') $btn_class .= 'bg-red text-white';
                        elseif ($stil === 'gray') $btn_class .= 'bg-warmgray text-white';
                        else $btn_class .= 'bg-white text-warmgray border-2 border-orange/20';
                        ?>
                        <a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($btn_class); ?>">
                            <?php echo esc_html($metin); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>