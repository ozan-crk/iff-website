<?php
/**
 * Photo Slider Block Template.
 */

$baslik = get_field('baslik') ?: 'Festivalden Kareler';
$fotograflar = get_field('fotograflar'); // Return format should be Array of Image Arrays or URLs

if (empty($fotograflar)) {
    // Default fallback images
    $fotograflar = [
        ['url' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg'],
        ['url' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg'],
        ['url' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg'],
        ['url' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg'],
        ['url' => 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg'],
    ];
}

// Gutenberg Ek CSS Sınıfları
$bg_color = get_field('arka_plan_rengi');
$className = 'py-20 px-6 photo-slider-block';
if (!$bg_color) {
    $className .= ' bg-white';
}

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}

$style = $bg_color ? "background-color: {$bg_color};" : "";
?>
<!-- Mini Fotoğraf Slider'ı Bölümü -->
<section class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($style); ?>">
    <div class="container mx-auto">
        <div class="flex items-center space-x-6 mb-12">
            <h2 class="text-3xl font-heading font-bold uppercase shrink-0"><?php echo esc_html($baslik); ?></h2>
            <div class="h-1 bg-red flex-1"></div>
        </div>
        <div class="relative overflow-hidden group">
            <div id="mini-slider-track" class="flex transition-transform duration-500 space-x-4">
                <?php foreach ($fotograflar as $foto): 
                    $img_url = is_array($foto) ? $foto['url'] : $foto;
                ?>
                    <div class="min-w-[300px] h-48 border-4 border-cream modern-shadow overflow-hidden">
                        <img src="<?php echo esc_url($img_url); ?>" class="w-full h-full object-cover">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
