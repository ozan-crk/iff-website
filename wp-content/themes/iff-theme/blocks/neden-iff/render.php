<?php
/**
 * Neden IFF Block Template.
 */

$baslik = get_field('baslik') ?: 'NEDEN İŞÇİ FİLMLERİ FESTİVALİ?';
$icerik = get_field('icerik') ?: '
<p>İnsanlık tarihi yerleşik hayata geçtiği andan itibaren kendi bünyesinde sınıfsal, cinsel, etnik çelişkiler üreterek bugüne kadar geldi. Tarihin belli dönemlerinde etnik, dinsel, ulusal çelişkiler şeklinde görünen ve bu temelde şekillenen savaşlarla bu sorunlar çözülmeye çalışılmışken bazı tarihsel aktörler de bu sorunların çözümünde etnik, dinsel, kültürel çatışmaların dışına çıkarak eşitlikçi, özgürlükçü çözümler önermiş ve bunların mücadelesini vermiştir.</p>
<p>Tarihte ilk kez işçi sınıfı mücadelesi ve bu mücadelede fikri olarak olgunlaşan sosyalizm, dünya çapında sınıfsız, sömürüsüz, devletsiz bir hayat özlemini “bilimsel” temellere oturtmuş ve gerçekleşebilir bir düşünce haline getirmiştir.</p>
<p>Bu fikrin ışığında Paris Komünü deneyimi ve ardından tarihte ilk kez sınıf egemenliğine son verilen bir sosyal düzenin kurulduğu Bolşevik Rus devrimi gerçekleşmiş ve dünyaya bunun mümkün olabileceğini göstermiştir.</p>
<p class="text-orange font-bold text-xl pt-4">21. yüzyılın gerçekliğinde, işçi sınıfı mücadelesinin sınıfsız, sömürüsüz, eşitlikçi, adil bir dünya özlemine sahip çıkıyor ve bu mirasın yeni kuşaklara aktarılmasını misyonumuz olarak kabul ediyoruz.</p>
';
?>
<section class="mb-24 neden-iff-block">
    <?php
    $bg_color = get_field('arka_plan_rengi');
    $bg_class = $bg_color ? '' : 'bg-warmgray';
    $style = $bg_color ? "background-color: {$bg_color};" : "";
    ?>
    <div class="<?php echo $bg_class; ?> text-white p-12 border-4 border-white modern-shadow" style="<?php echo esc_attr($style); ?>">
        <h2 class="text-5xl font-heading font-bold text-orange mb-12 uppercase tracking-tighter border-b-8 border-orange inline-block"><?php echo esc_html($baslik); ?></h2>
        <div class="font-serif text-lg leading-relaxed space-y-6 text-gray-300">
            <?php echo wp_kses_post($icerik); ?>
        </div>
    </div>
</section>
