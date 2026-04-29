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
$className = 'py-20 px-6 bg-white photo-slider-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>
<!-- Mini Fotoğraf Slider'ı Bölümü -->
<section class="<?php echo esc_attr($className); ?>">
    <div class="container mx-auto">
        <div class="flex items-center space-x-6 mb-12">
            <h2 class="text-3xl font-heading font-bold uppercase shrink-0"><?php echo esc_html($baslik); ?></h2>
            <div class="h-1 bg-red flex-1"></div>
        </div>
        <div class="relative overflow-hidden group">
            <div id="mini-slider-track" class="flex transition-transform duration-500 space-x-4">
                <?php foreach ($fotograflar as $foto): ?>
                    <div class="min-w-[300px] h-48 border-4 border-cream modern-shadow overflow-hidden">
                        <img src="<?php echo esc_url(is_array($foto) && isset($foto['url']) ? $foto['url'] : (is_string($foto) ? $foto : '')); ?>" class="w-full h-full object-cover">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
