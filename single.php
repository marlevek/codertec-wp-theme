<?php get_header(); ?>

<?php
$post_id = get_the_ID();
$published_date = get_the_date('', $post_id);
$modified_date = get_the_modified_date('', $post_id);
$reading_time = codertec_get_post_reading_time_label($post_id);
?>

<article class="container py-5 codertec-single-post">
    <header class="codertec-single-post__header mb-4">
        <?php echo codertec_get_post_categories_markup($post_id, 'mb-3'); ?>
        <h1 class="mb-3"><?php the_title(); ?></h1>

        <div class="codertec-single-post__meta">
            <span>Publicado em <?php echo esc_html($published_date); ?></span>
            <?php if ($modified_date && $modified_date !== $published_date) : ?>
                <span>Atualizado em <?php echo esc_html($modified_date); ?></span>
            <?php endif; ?>
            <span><?php echo esc_html($reading_time); ?></span>
        </div>
    </header>

    <div class="post-content">
        <?php the_content(); ?>
    </div>

    <?php echo codertec_get_post_tags_markup($post_id, 'mt-4'); ?>

    <?php codertec_render_single_post_conversion_section($post_id); ?>

    <div class="post-share mt-5 pt-4 border-top border-2 border-secondary">
        <p class="h5 mb-3">Gostou do post? Ent&atilde;o bora compartilhar!</p>
        <div class="d-flex gap-3 flex-wrap">
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
               target="_blank"
               class="btn btn-primary d-flex align-items-center gap-2">
                <i class="fab fa-facebook-f"></i>
                Facebook
            </a>

            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>"
               target="_blank"
               class="btn btn-linkedin d-flex align-items-center gap-2"
               style="background: #0077B5; color: white;">
                <i class="fab fa-linkedin-in"></i>
                LinkedIn
            </a>

            <!-- Instagram -->
            <a href="https://www.instagram.com/"
               target="_blank"
               class="btn btn-instagram d-flex align-items-center gap-2"
               style="background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D); color: white;">
                <i class="fab fa-instagram"></i>
                Instagram
            </a>
        </div>
    </div>

    <a href="/blog/" class="btn btn-secondary mt-5 mb-1">&larr; Voltar ao Blog</a>
</article>

<?php get_footer(); ?>
