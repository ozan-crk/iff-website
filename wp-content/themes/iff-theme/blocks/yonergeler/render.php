<?php
$baslik = get_field('baslik') ?: 'YÖNERGELER';
$metin = get_field('metin');
$btn_metin = get_field('buton_metni');
$btn_url = get_field('buton_url');
$btn_stil = get_field('buton_stili') ?: 'solid-orange';

$btn_class = '';
switch ($btn_stil) {
    case 'solid-orange':
        $btn_class = 'bg-orange text-white border-4 border-warmgray';
        break;
    case 'outline-red':
        $btn_class = 'bg-white text-red border-4 border-red';
        break;
    case 'solid-gray':
        $btn_class = 'bg-warmgray text-white border-4 border-white';
        break;
    default:
        $btn_class = 'bg-orange text-white border-4 border-warmgray';
}
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

?>

<section id="yonergeler" class="yonergeler-block mb-24 <?php echo esc_attr($className); ?>">
    <?php
    $bg_color = get_field('arka_plan_rengi');
    $bg_class = $bg_color ? '' : 'bg-cream';
    $style = $bg_color ? "background-color: {$bg_color};" : "";
    ?>
    <div class="<?php echo $bg_class; ?> p-12 border-4 border-orange modern-shadow relative overflow-hidden" style="<?php echo esc_attr($style); ?>">
        <h2 class="text-4xl font-custom mb-8 text-warmgray   tracking-tighter border-b-4 border-orange inline-block">
            <?php echo esc_html($baslik); ?>
        </h2>

        <div class="font-serif text-lg leading-relaxed text-gray-700 mb-10 max-w-4xl">
            <?php echo wp_kses_post($metin); ?>
        </div>

        <?php if ($btn_metin && $btn_url): ?>
            <a href="<?php echo esc_url($btn_url); ?>"
                class="<?php echo $btn_class; ?> px-10 py-4 font-heading font-bold uppercase tracking-widest modern-shadow hover-lift transition-all inline-block">
                <?php echo esc_html($btn_metin); ?>
            </a>
        <?php endif; ?>

        <!-- Dekoratif Arkaplan -->
        <div class="absolute -bottom-10 -right-1 opacity-5 pointer-events-none transform rotate-12 select-none">
            <span class="text-[180px] font-custom leading-none text-warmgray">İFF</span>
        </div>
    </div>
</section>