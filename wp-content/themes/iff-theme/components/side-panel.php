<?php
/**
 * Tema ayarlarından yönetilen Sol Panel bileşeni.
 */
$panel_varsayilan_acik = get_field('panel_varsayilan_acik', 'option');
$panel_class = $panel_varsayilan_acik ? '' : 'closed';
$panel_afis = get_field('panel_afis', 'option') ?: 'https://picsum.photos/400/560?random=50';

// Yeni Alanlar
$afis_aktif = get_field('panel_afis_aktif', 'option');
if ($afis_aktif === null) $afis_aktif = true; // Varsayılan true
$afis_baslik = get_field('panel_afis_baslik', 'option') ?: 'GÜNCEL AFİŞ';

$program_aktif = get_field('panel_program_aktif', 'option');
if ($program_aktif === null) $program_aktif = true; // Varsayılan true
$program_baslik = get_field('panel_program_baslik', 'option') ?: 'GÜNÜN PROGRAMI';
?>
<!-- Sol Panel (Afiş, Seçili Program, Hızlı Butonlar) -->
<aside id="side-panel"
    class="fixed left-0 top-0 h-screen w-80 bg-white border-r-4 border-orange z-[60] modern-shadow flex flex-col <?php echo esc_attr($panel_class); ?>">
    <!-- Toggle Button (Panel Dışında) -->
    <button id="toggle-panel"
        class="absolute -right-10 top-1/2 -translate-y-1/2 bg-orange text-white py-4 px-2 font-bold font-heading panel-toggle hover:bg-darkorange transition-colors">
        PANELİ AÇ / KAPAT
    </button>

    <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
        
        <?php if ($afis_aktif): ?>
        <!-- Güncel Afiş -->
        <div class="mb-8">
            <h4 class="text-xs font-bold text-red mb-3 uppercase tracking-widest"><?php echo esc_html($afis_baslik); ?></h4>
            <div class="border-4 border-warmgray modern-shadow overflow-hidden group cursor-pointer">
                <img src="<?php echo esc_url($panel_afis); ?>"
                    class="w-full h-auto grayscale group-hover:grayscale-0 transition-all" alt="Afiş">
            </div>
        </div>
        <?php endif; ?>

        <?php if ($program_aktif): ?>
        <!-- Program Özeti -->
        <div class="mb-8">
            <h4 class="text-xs font-bold text-red mb-3 uppercase tracking-widest"><?php echo esc_html($program_baslik); ?></h4>
            <div id="side-program-content" class="text-sm space-y-4">
                <?php 
                if (class_exists('IFF_Program_Manager')) {
                    $today = current_time('Y-m-d');
                    $programs = IFF_Program_Manager::get_programs(10, $today);
                    
                    if ($programs) {
                        foreach ($programs as $prog) {
                            ?>
                            <div class="border-l-4 border-orange pl-4 py-2 bg-cream/30">
                                <div class="font-heading font-bold text-sm leading-tight uppercase">
                                    <?php echo esc_html($prog->film_adi); ?>
                                </div>
                                <div class="text-[10px] text-gray-600 mt-1 uppercase tracking-tighter">
                                    <?php echo esc_html($prog->saat); ?> · <?php echo esc_html($prog->mekan); ?>
                                </div>
                                <div class="text-[10px] text-red mt-2 font-bold uppercase">
                                    <?php echo esc_html($prog->sehir); ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p class="text-[10px] italic text-gray-400 font-serif">Bugün için planlanmış bir gösterim bulunmuyor.</p>';
                    }
                }
                ?>
            </div>
            <a href="<?php echo home_url('/program'); ?>" class="mt-6 text-[10px] font-bold text-orange hover:underline block uppercase tracking-widest">TÜM PROGRAM &rarr;</a>
        </div>
        <?php endif; ?>

        <!-- Hızlı Erişim Butonları -->
        <div class="space-y-3">
            <?php if (have_rows('panel_hizli_butonlar', 'option')): ?>
                <?php while (have_rows('panel_hizli_butonlar', 'option')): the_row(); 
                    $metin = get_sub_field('metin');
                    $link = get_sub_field('link');
                    $stil = get_sub_field('stil') ?: 'bg-warmgray text-white';
                ?>
                    <a href="<?php echo esc_url($link); ?>"
                        class="block <?php echo esc_attr($stil); ?> p-3 text-center font-heading font-bold text-xs hover:bg-orange transition hover-lift no-underline uppercase"><?php echo esc_html($metin); ?></a>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Varsayılan Butonlar -->
                <a href="#"
                    class="block bg-warmgray text-white p-3 text-center font-heading font-bold text-xs hover:bg-orange transition hover-lift">KATALOG (PDF)</a>
                <a href="<?php echo home_url('/basvuru#gonullu'); ?>"
                    class="block bg-red text-white p-3 text-center font-heading font-bold text-xs hover:bg-orange transition hover-lift text-white no-underline">GÖNÜLLÜ OL</a>
                <a href="#"
                    class="block border-2 border-warmgray p-3 text-center font-heading font-bold text-xs hover:bg-warmgray hover:text-white transition hover-lift text-warmgray">ESKİ FİLMLER</a>
                <a href="<?php echo home_url('/basin'); ?>"
                    class="block border-2 border-red p-3 text-center font-heading font-bold text-xs hover:bg-red hover:text-white transition hover-lift text-red uppercase">Basın Bülteni</a>
            <?php endif; ?>
        </div>
    </div>
</aside>