<?php
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
$className = 'city-program-block mb-24';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
?>

<section class="<?php echo esc_attr($className); ?>">
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
        <!-- Sol Kolon: Program Listesi (Tab Yapısı) -->
        <div class="lg:col-span-8">
            <?php if (!empty($screenings_by_date)): ?>
                <!-- Tab Navigasyonu -->
                <div class="flex flex-wrap gap-2 mb-8" id="program-tabs-<?php echo esc_attr($block_id); ?>">
                    <?php 
                    $i = 0;
                    foreach ($dates as $date): 
                        $formatted_date = date('d.m.Y', strtotime($date));
                        $is_active = ($i === 0);
                        $btn_class = $is_active 
                            ? 'bg-red text-white' 
                            : 'bg-white text-warmgray border-2 border-orange/10';
                    ?>
                        <button 
                            class="program-tab px-6 py-3 font-heading font-bold text-sm modern-shadow hover-lift transition-all <?php echo $btn_class; ?>"
                            data-target="pane-<?php echo esc_attr($block_id); ?>-<?php echo $i; ?>">
                            <?php echo esc_html($formatted_date); ?>
                        </button>
                    <?php $i++; endforeach; ?>
                </div>

                <!-- Tab İçerikleri -->
                <div id="program-content-<?php echo esc_attr($block_id); ?>">
                    <?php 
                    $i = 0;
                    foreach ($screenings_by_date as $date => $mekanlar): 
                        $is_active = ($i === 0);
                    ?>
                        <div class="program-pane space-y-12 animate-fade-in" 
                             id="pane-<?php echo esc_attr($block_id); ?>-<?php echo $i; ?>" 
                             style="display: <?php echo $is_active ? 'block' : 'none'; ?>;">
                            
                            <?php foreach ($mekanlar as $mekan_adi => $items): ?>
                                <div class="venue-group">
                                    <div class="flex items-center gap-4 mb-6 border-b-2 border-orange/20 pb-2">
                                        <svg class="w-5 h-5 text-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <h4 class="text-xl font-heading font-bold text-warmgray uppercase tracking-wide"><?php echo esc_html($mekan_adi); ?></h4>
                                    </div>

                                    <div class="space-y-6">
                                        <?php 
                                        // Seansları saate göre grupla
                                        $sessions = [];
                                        foreach ($items as $item) {
                                            $sessions[$item->saat][] = $item;
                                        }

                                        foreach ($sessions as $saat => $session_items): 
                                            $is_multi = count($session_items) > 1;
                                        ?>
                                            <div class="session-block bg-white modern-shadow <?php echo $is_multi ? 'border-y-4 border-orange/30 py-6 px-4 my-4' : 'p-6 border-2 border-orange/5'; ?>">
                                                <?php if ($is_multi): ?>
                                                    <div class="text-[9px] font-heading font-bold text-red uppercase tracking-[0.4em] mb-6 flex items-center gap-4">
                                                        <span class="flex-shrink-0">ORTAK SEANS</span>
                                                        <div class="h-[1px] w-full bg-orange/10"></div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="space-y-6">
                                                    <?php foreach ($session_items as $idx => $item): ?>
                                                        <div class="flex flex-col md:flex-row justify-between items-center <?php echo ($idx < count($session_items) - 1) ? 'border-b border-orange/5 pb-6' : ''; ?>">
                                                            <div class="flex items-center space-x-6 w-full">
                                                                <?php if ($idx === 0): ?>
                                                                    <div class="text-2xl font-custom font-bold text-red w-24 shrink-0">
                                                                        <?php echo esc_html($item->saat); ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="w-24 shrink-0 hidden md:block"></div>
                                                                <?php endif; ?>

                                                                <div class="flex-1 <?php echo $idx === 0 ? 'border-l-2 border-orange/10 pl-6' : 'pl-6 md:pl-6'; ?>">
                                                                    <div class="flex flex-wrap items-center gap-3">
                                                                        <h5 class="text-lg font-heading font-bold text-warmgray uppercase transition-colors">
                                                                            <?php echo esc_html($item->film_adi); ?>
                                                                        </h5>
                                                                        <?php if (isset($item->is_special) && $item->is_special): ?>
                                                                            <span class="bg-red text-white text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-widest">Özel Gösterim</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="flex items-center gap-4 mt-1 text-xs text-gray-500 font-serif italic">
                                                                        <?php if (!empty($item->sure)): ?>
                                                                            <span><?php echo esc_html($item->sure); ?></span>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($item->etkinlik)): ?>
                                                                            <span class="text-red font-bold not-italic font-heading tracking-tight underline underline-offset-2">• <?php echo esc_html($item->etkinlik); ?></span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                        const container = document.getElementById('program-tabs-' + blockId);
                        if(!container) return;
                        
                        const tabs = container.querySelectorAll('.program-tab');
                        const panes = document.querySelectorAll('#program-content-' + blockId + ' .program-pane');

                        tabs.forEach(tab => {
                            tab.addEventListener('click', function() {
                                const targetId = this.getAttribute('data-target');

                                // Buton Stillerini Temizle
                                tabs.forEach(t => {
                                    t.classList.remove('bg-red', 'text-white');
                                    t.classList.add('bg-white', 'text-warmgray', 'border-2', 'border-orange/10');
                                });

                                // Aktif Buton Stilini Ekle
                                this.classList.remove('bg-white', 'text-warmgray', 'border-2', 'border-orange/10');
                                this.classList.add('bg-red', 'text-white');

                                // Tüm Panelleri Gizle
                                panes.forEach(p => p.style.display = 'none');

                                // Hedef Paneli Göster
                                const targetPane = document.getElementById(targetId);
                                if(targetPane) targetPane.style.display = 'block';
                            });
                        });
                    })();
                </script>

            <?php else: ?>
                <div class="bg-white p-12 border-4 border-dashed border-gray-200 text-center">
                    <p class="text-gray-400 font-serif italic">Bu şehir için henüz gösterim eklenmemiş veya şehir adı uyuşmuyor
                        (Lütfen eklentideki şehir adıyla aynı yazdığınızdan emin olun).</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sağ Kolon: Ekstra Butonlar -->
        <div class="lg:col-span-4 space-y-6">
            <?php if (have_rows('ekstra_butonlar')): ?>
                <h4 class="font-heading font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-6 border-b border-gray-200 pb-2">HIZLI BAĞLANTILAR</h4>
                <div class="flex flex-col space-y-4">
                    <?php while (have_rows('ekstra_butonlar')):
                        the_row();
                        $metin = get_sub_field('metin');
                        $url = get_sub_field('url');
                        $stil = get_sub_field('stil');

                        $btn_class = 'w-full text-center py-4 font-heading font-bold uppercase tracking-widest transition-all modern-shadow hover-lift ';
                        if ($stil === 'red')
                            $btn_class .= 'bg-red text-white border-4 border-white';
                        elseif ($stil === 'gray')
                            $btn_class .= 'bg-warmgray text-white border-4 border-white';
                        else
                            $btn_class .= 'bg-white text-warmgray border-4 border-warmgray';
                        ?>
                        <a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($btn_class); ?>">
                            <?php echo esc_html($metin); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>