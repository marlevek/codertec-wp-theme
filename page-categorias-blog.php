<?php get_header(); ?>

<?php
$categories = get_categories(array(
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
));
?>

<div class="container my-5">
    <h1 class="text-center mb-3">Categorias do blog</h1>
    <p class="text-dark text-center pb-4">Explore os conteúdos do blog por categoria.</p>

    <div class="row">
        <?php if (!empty($categories)) : ?>
            <?php foreach ($categories as $category) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title"><?php echo esc_html($category->name); ?></h2>
                            <p class="card-text flex-grow-1">
                                <?php
                                if (!empty($category->description)) {
                                    echo esc_html($category->description);
                                } else {
                                    echo esc_html(sprintf('%d post(s) nesta categoria.', (int) $category->count));
                                }
                                ?>
                            </p>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="btn btn-primary mt-auto">Ver posts</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-center">Nenhuma categoria encontrada no blog.</p>
        <?php endif; ?>
    </div>

    <?php codertec_render_institutional_cta(); ?>
</div>

<?php get_footer(); ?>
