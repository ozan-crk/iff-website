<?php
/**
 * Archive Template.
 */
get_header();
?>

<main class="mt-24 py-12 px-6 min-h-screen bg-cream">
    <div class="container mx-auto">
        
        <!-- Header -->
        <div class="mb-12 border-b-4 border-red pb-6 inline-block">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-warmgray uppercase tracking-tighter">
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_day()) {
                    echo get_the_date();
                } elseif (is_month()) {
                    echo get_the_date('F Y');
                } elseif (is_year()) {
                    echo get_the_date('Y');
                } else {
                    _e('ARŞİV', 'iff');
                }
                ?>
            </h1>
            <?php if (category_description()) : ?>
                <div class="mt-4 text-gray-600 font-serif max-w-2xl">
                    <?php echo category_description(); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Main Content Area -->
            <div class="lg:col-span-8">
                <?php if (have_posts()) : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php while (have_posts()) : the_post(); 
                            $gorsel = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://iff.fra1.digitaloceanspaces.com/wp-content/uploads/2026/04/29053444/blog-placeholder.jpg';
                        ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white shadow-lg overflow-hidden group hover-lift transition-all border-b-4 border-orange/20 hover:border-red flex flex-col'); ?>>
                                <a href="<?php the_permalink(); ?>" class="block relative h-56 overflow-hidden">
                                    <img src="<?php echo esc_url($gorsel); ?>" class="w-full h-full object-cover transform transition-transform group-hover:scale-105" alt="<?php the_title_attribute(); ?>">
                                    <div class="absolute top-4 left-4">
                                        <span class="bg-red text-white text-[10px] font-bold px-3 py-1 uppercase tracking-widest shadow-lg">
                                            <?php $cats = get_the_category(); echo !empty($cats) ? esc_html($cats[0]->name) : 'HABER'; ?>
                                        </span>
                                    </div>
                                </a>
                                
                                <div class="p-6 flex-1 flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-heading tracking-widest uppercase mb-2 block">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <h2 class="text-xl font-heading font-bold text-warmgray mb-4 leading-tight group-hover:text-red transition-colors uppercase">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="text-sm text-gray-500 font-serif line-clamp-3 mb-6">
                                        <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="mt-auto text-red font-bold text-xs uppercase tracking-widest hover:underline">
                                        DEVAMINI OKU &rarr;
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        <?php
                        the_posts_pagination(array(
                            'prev_text'          => '&larr;',
                            'next_text'          => '&rarr;',
                            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Sayfa', 'iff') . ' </span>',
                            'class'              => 'modern-pagination'
                        ));
                        ?>
                    </div>

                <?php else : ?>
                    <div class="bg-white p-12 text-center shadow-lg border-t-4 border-red">
                        <p class="text-gray-500 font-serif text-lg italic">Bu bölümde henüz bir yazı bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar Area -->
            <aside class="lg:col-span-4">
                <?php if ( is_active_sidebar( 'post-sidebar' ) ) : ?>
                    <div class="sidebar space-y-12">
                        <?php dynamic_sidebar( 'post-sidebar' ); ?>
                    </div>
                <?php else : ?>
                    <div class="bg-white p-8 shadow-lg border-l-4 border-red">
                        <p class="text-gray-500 text-sm">Lütfen panelden bileşen ekleyin.</p>
                    </div>
                <?php endif; ?>
            </aside>

        </div>
    </div>
</main>

<?php
get_footer();
