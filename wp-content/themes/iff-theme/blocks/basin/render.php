<?php
/**
 * Basın Block Template.
 */

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'basin-block';
if ($bg_color) {
    $className .= ' p-8 md:p-12';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <h2 class="text-6xl font-heading font-bold text-red mb-16 uppercase tracking-tighter">BASIN ODASI</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
        <!-- Sol: Basın Bültenleri -->
        <div class="md:col-span-2 space-y-12">
            <section>
                <h3 class="text-xs font-bold text-orange mb-6 uppercase tracking-[0.3em] font-heading border-b-2 border-orange pb-2 inline-block">SON BÜLTENLER</h3>
                <div class="space-y-8">
                    <?php if (have_rows('bultenler')): ?>
                        <?php while (have_rows('bultenler')): the_row(); ?>
                            <article class="border-b border-gray-200 pb-8 hover:pl-4 transition-all group">
                                <time class="text-xs text-gray-400 font-serif lowercase italic"><?php echo esc_html(get_sub_field('tarih')); ?></time>
                                <h4 class="text-2xl font-heading font-bold mt-2 group-hover:text-red transition-colors cursor-pointer"><?php echo esc_html(get_sub_field('baslik')); ?></h4>
                                <p class="text-gray-600 mt-4 leading-relaxed font-serif text-sm"><?php echo esc_html(get_sub_field('ozet')); ?></p>
                                <a href="<?php echo esc_url(get_sub_field('dosya_linki')); ?>" class="inline-block mt-4 text-[10px] font-bold text-red border-b-2 border-red pb-1 uppercase tracking-widest">PDF'İ İNDİR</a>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <article class="border-b border-gray-200 pb-8 hover:pl-4 transition-all group">
                            <time class="text-xs text-gray-400 font-serif lowercase italic">12 Nisan 2024</time>
                            <h4 class="text-2xl font-heading font-bold mt-2 group-hover:text-red transition-colors cursor-pointer">19. İşçi Filmleri Festivali "Güneşli Günler Göreceğiz" Diyor!</h4>
                            <p class="text-gray-600 mt-4 leading-relaxed font-serif text-sm">Bu yıl 19. kez izleyiciyle buluşacak olan festivalin programı açıklandı. 15 ülkeden 62 film, 20 farklı noktada ücretsiz gösterilecek...</p>
                            <a href="#" class="inline-block mt-4 text-[10px] font-bold text-red border-b-2 border-red pb-1 uppercase tracking-widest">PDF'İ İNDİR</a>
                        </article>
                        <article class="border-b border-gray-200 pb-8 hover:pl-4 transition-all group">
                            <time class="text-xs text-gray-400 font-serif lowercase italic">05 Mart 2024</time>
                            <h4 class="text-2xl font-heading font-bold mt-2 group-hover:text-red transition-colors cursor-pointer">Festival Afiş Yarışması Sonuçlandı</h4>
                            <p class="text-gray-600 mt-4 leading-relaxed font-serif text-sm">Yüzlerce başvuru arasından seçilen bu yılın afişi, emeğin ve umudun renklerini taşıyor. Yarışmanın kazananı...</p>
                            <a href="#" class="inline-block mt-4 text-[10px] font-bold text-red border-b-2 border-red pb-1 uppercase tracking-widest">BASIN BÜLTENİ</a>
                        </article>
                    <?php endif; ?>
                </div>
            </section>

            <section>
                <h3 class="text-xs font-bold text-warmgray mb-6 uppercase tracking-[0.3em] font-heading border-b-2 border-warmgray pb-2 inline-block">BASINDA BİZ</h3>
                <ul class="space-y-4 font-serif text-sm">
                    <?php if (have_rows('basinda_biz')): ?>
                        <?php while (have_rows('basinda_biz')): the_row(); ?>
                            <li class="flex items-start space-x-3">
                                <span class="font-bold text-red">[<?php echo esc_html(get_sub_field('kaynak')); ?>]</span>
                                <a href="<?php echo esc_url(get_sub_field('link')); ?>" class="hover:underline" target="_blank"><?php echo esc_html(get_sub_field('baslik')); ?></a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="flex items-start space-x-3"><span class="font-bold text-red">[SENDİKA.ORG]</span><a href="#" class="hover:underline">İşçi sınıfı sinemasına ücretsiz ve sponsorsuz bir yolculuk.</a></li>
                        <li class="flex items-start space-x-3"><span class="font-bold text-red">[BİRGÜN]</span><a href="#" class="hover:underline">İşçi Filmleri Festivali başlıyor: Emeğin perdesi 19. kez açılıyor.</a></li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>

        <!-- Sağ: Basın Kiti & İletişim -->
        <div class="md:col-span-1 space-y-10">
            <div class="bg-orange p-8 modern-shadow text-white">
                <h3 class="font-heading font-bold text-xl mb-4 uppercase"><?php echo esc_html(get_field('kit_baslik') ?: 'BASIN KİTİ 2024'); ?></h3>
                <p class="text-sm font-serif mb-6 opacity-90"><?php echo esc_html(get_field('kit_aciklama') ?: 'Logo, afiş, yüksek çözünürlüklü fotoğraflar ve bülten içeriğini toplu olarak indirebilirsiniz.'); ?></p>
                <a href="<?php echo esc_url(get_field('kit_link') ?: '#'); ?>" class="block bg-white text-orange p-4 text-center font-heading font-bold text-xs hover:bg-cream transition-colors border-2 border-white"><?php echo esc_html(get_field('kit_buton_metin') ?: 'KİTİ İNDİR (ZIP - 45MB)'); ?></a>
            </div>

            <div class="bg-white p-8 border-4 border-warmgray modern-shadow">
                <h3 class="font-heading font-bold text-xl mb-4 uppercase"><?php echo esc_html(get_field('iletisim_baslik') ?: 'BASIN İLETİŞİM'); ?></h3>
                <div class="space-y-4 text-sm font-serif">
                    <p><strong>E-posta:</strong> <?php echo esc_html(get_field('iletisim_eposta') ?: 'basin@iff.org.tr'); ?></p>
                    <p><strong>Tel:</strong> <?php echo esc_html(get_field('iletisim_tel') ?: '+90 (XXX) XXX XX XX'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
