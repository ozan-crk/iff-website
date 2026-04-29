<?php
/**
 * Search Results Template.
 */
get_header();
?>

<main class="mt-24 py-12 px-6 min-h-screen bg-cream">
    <div class="container mx-auto">
        
        <!-- Header -->
        <div class="mb-12 border-b-4 border-red pb-6 inline-block">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-warmgray uppercase tracking-tighter">
                <?php printf(__('ARAMA SONUÇLARI: %s', 'iff'), '<span class="text-red">' . get_search_query() . '</span>'); ?>
            </h1>
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
                        ));
                        ?>
                    </div>

                <?php else : ?>
                    <div class="bg-white p-16 text-center shadow-2xl border-t-8 border-red">
                        <div class="mb-8">
                             <svg class="w-24 h-24 text-gray-200 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-heading font-bold text-warmgray mb-4 uppercase">Üzgünüz, Sonuç Bulunamadı</h2>
                        <p class="text-gray-500 font-serif text-lg italic mb-8">Aradığınız kriterlere uygun içerik bulunamadı. Lütfen farklı anahtar kelimelerle tekrar deneyin.</p>
                        <div class="max-w-md mx-auto">
                            <?php get_search_form(); ?>
                        </div>
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
