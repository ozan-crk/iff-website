<?php
/**
 * Seperator Block Template.
 */

$yukseklik = get_field('yukseklik') ?: '80'; // Varsayılan 80px
$arka_plan = get_field('arka_plan_rengi') ?: 'transparent';

// Gutenberg Ek CSS Sınıfları
$className = 'separator-block w-full';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

// Stil ayarları
$style = "height: {$yukseklik}px; background-color: {$arka_plan};";
?>

<div class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>"></div>
