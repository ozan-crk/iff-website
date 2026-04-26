<?php
/**
 * Tema ayarlarından yönetilen Popup Duyuru bileşeni.
 */
$popup_aktif = get_field('popup_aktif', 'option');

if (!$popup_aktif) return;

$popup_etiket = get_field('popup_etiket', 'option') ?: 'FESTİVAL HABERİ';
$popup_baslik = get_field('popup_baslik', 'option') ?: '20. Festival Hazırlıkları Başladı!';
$popup_metin = get_field('popup_metin', 'option') ?: '"Güneşli Günler Göreceğiz" temasıyla 20. yılımızı kutlamaya hazırlanıyoruz. Detaylar çok yakında burada olacak.';
$popup_btn_metin = get_field('popup_buton_metni', 'option') ?: 'GÖNÜLLÜ OL';
$popup_btn_link = get_field('popup_buton_link', 'option') ?: home_url('/basvuru#gonullu');
?>
<!-- Popup Duyuru (Daha modern, overlay ile) -->
<div id="announcement-popup"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 popup-overlay transition-opacity duration-500 opacity-0 pointer-events-none">
    <div
        class="bg-white border-8 border-orange max-w-lg w-full p-10 relative modern-shadow transform scale-95 transition-transform duration-500">
        <button id="close-popup"
            class="absolute top-4 right-4 text-4xl font-bold leading-none hover:text-orange transition">&times;</button>
        <span
            class="bg-red text-white px-4 py-1 text-[10px] font-bold mb-6 inline-block uppercase tracking-widest"><?php echo esc_html($popup_etiket); ?></span>
        <h2 class="text-4xl font-heading font-bold mb-4 uppercase tracking-tighter leading-none"><?php echo esc_html($popup_baslik); ?></h2>
        <p class="text-gray-600 mb-8 font-serif text-lg leading-relaxed italic"><?php echo esc_html($popup_metin); ?></p>
        <div class="flex space-x-4">
            <a href="<?php echo esc_url($popup_btn_link); ?>"
                class="bg-orange text-white px-8 py-4 flex-1 font-heading font-bold hover:bg-darkorange transition-colors uppercase text-xs tracking-widest hover-lift text-center"><?php echo esc_html($popup_btn_metin); ?></a>
            <button id="close-popup-btn"
                class="border-2 border-warmgray text-warmgray px-8 py-4 flex-1 font-heading font-bold hover:bg-warmgray hover:text-white transition-all uppercase text-xs tracking-widest">KAPAT</button>
        </div>
    </div>
</div>
