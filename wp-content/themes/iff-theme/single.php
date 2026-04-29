<?php
get_header();
?>

<main class="mt-24 py-12 px-6 min-h-screen bg-cream">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Main Content Area -->
            <div class="lg:col-span-8">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white shadow-2xl overflow-hidden border-b-8 border-red'); ?>>
                        
                        <!-- Post Title -->
                        <div class="p-8 md:p-12 pb-6">
                            <h1 class="text-4xl md:text-5xl font-heading font-bold text-warmgray leading-tight">
                                <?php the_title(); ?>
                            </h1>
                        </div>

                        <!-- Full Width Post Meta -->
                        <div class="flex flex-wrap items-center gap-6 px-8 md:px-12 text-xs font-heading tracking-widest text-gray-500 uppercase bg-gray-50 border-y border-gray-100 py-4">
                            <div class="flex items-center">
                                <svg class="w-3.5 h-3.5 mr-2 text-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <?php echo get_the_date(); ?>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-3.5 h-3.5 mr-2 text-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <?php the_category(', '); ?>
                            </div>
                        </div>
                        
                        <?php if (has_excerpt()) : ?>
                            <div class="px-8 md:px-12 mb-10">
                                <div class="text-xl text-gray-600 italic leading-relaxed border-l-4 border-orange pl-6 py-2 bg-gray-50">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Featured Image -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="mb-8">
                                <div class="bg-gray-100">
                                    <?php the_post_thumbnail('full', ['class' => 'w-full h-auto object-cover']); ?>
                                </div>
                                <?php if (get_the_post_thumbnail_caption()) : ?>
                                    <p class="mt-3 px-8 md:px-12 text-sm text-gray-500 italic text-center font-sans border-b border-gray-100 pb-2">
                                        <?php the_post_thumbnail_caption(); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Post Content -->
                        <div class="p-8 md:p-12 pt-0">
                            <div class="post-content text-warmgray prose-lg max-w-none">
                                <?php the_content(); ?>
                            </div>
                        </div>

                        <!-- Post Footer (Author) -->
                    

                    </article>
                <?php endwhile; endif; ?>
            </div>

            <!-- Sidebar Area -->
            <aside class="lg:col-span-4">
                <?php if ( is_active_sidebar( 'post-sidebar' ) ) : ?>
                    <div class="sidebar">
                        <?php dynamic_sidebar( 'post-sidebar' ); ?>
                    </div>
                <?php else : ?>
                    <div class="bg-white p-6 shadow-lg border-l-4 border-red">
                        <p class="text-gray-500 text-sm">Lütfen panelden bileşen ekleyin.</p>
                    </div>
                <?php endif; ?>
            </aside>

        </div>
    </div>
</main>

<?php
get_footer();
