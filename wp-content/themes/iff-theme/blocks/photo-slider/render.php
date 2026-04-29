<?php
/**
 * Photo Slider Block Template.
 */

$baslik = get_field('baslik') ?: 'Festivalden Kareler';
$fotograflar = get_field('fotograflar'); // Return format should be Array of Image Arrays or URLs
$img_w = get_field('genislik') ?: 320;
$img_h = get_field('yukseklik') ?: 213;

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
                    if (empty($img_url)) continue;
                ?>
                    <div style="min-width: <?php echo $img_w; ?>px; height: <?php echo $img_h; ?>px;" class="border-4 border-cream modern-shadow overflow-hidden group/item relative">
                        <a data-fslightbox="gallery-<?php echo $block['id']; ?>" data-type="image" href="<?php echo esc_url($img_url); ?>" class="block w-full h-full">
                            <img src="<?php echo esc_url($img_url); ?>" class="w-full h-full object-cover transform group-hover/item:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover/item:opacity-100 transition-opacity flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.4.1/index.min.js"></script>
<script>
    if (typeof refreshFsLightbox === 'function') {
        refreshFsLightbox();
    }
</script>
