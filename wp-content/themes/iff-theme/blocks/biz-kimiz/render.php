<?php
$baslik = get_field('baslik');
$icerik = get_field('metin');
$tasarim = get_field('tasarim_stili') ?: 'beyaz';

// Varsayılan Stil Ayarları
$bg_class = 'bg-white';
$title_class = 'text-red';
$border_class = 'border-orange';
$text_class = 'text-gray-700';

if ($tasarim === 'koyu') {
    $bg_class = 'bg-warmgray text-white';
    $title_class = 'text-orange';
    $border_class = 'border-white';
    $text_class = 'text-gray-300';
}

// Özel Renk Ayarları
$ozel_renk = get_field('ozel_renk_aktif');
$container_style = '';
$title_style = '';

if ($ozel_renk) {
    $bg_custom = get_field('arka_plan_rengi');
    $title_custom = get_field('baslik_rengi');
    $border_custom = get_field('kenarlik_rengi');

    if ($bg_custom) {
        $container_style .= "background-color: $bg_custom; ";
        $bg_class = ''; // Tailwind sınıfını devre dışı bırak
    }
    if ($border_custom) {
        $container_style .= "border-color: $border_custom; ";
        $title_style .= "border-bottom-color: $border_custom; ";
        $border_class = '';
    }
    if ($title_custom) {
        $title_style .= "color: $title_custom; ";
        $title_class = '';
    }
}

// Gutenberg Ek CSS Sınıfları
$className = 'mb-24 content-box-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>

<section class="<?php echo esc_attr($className); ?>">
    <div class="<?php echo $bg_class; ?> p-12 border-4 <?php echo $border_class; ?> modern-shadow" style="<?php echo $container_style; ?>">
        <h2 class="text-5xl font-custom mb-10 uppercase tracking-tighter border-b-8 <?php echo $border_class; ?> inline-block <?php echo $title_class; ?>" style="<?php echo $title_style; ?>">
            <?php echo esc_html($baslik); ?>
        </h2>
        <div class="font-serif text-xl leading-relaxed space-y-8 <?php echo $text_class; ?>">
            <?php echo wp_kses_post($icerik); ?>
        </div>
    </div>
</section>