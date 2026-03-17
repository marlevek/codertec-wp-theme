<?php get_header(); ?>

<?php
$category = get_queried_object();
$category_name = single_cat_title('', false);
$category_content = codertec_get_category_archive_content($category);
$total_posts = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;
?>

<div class="container my-5">
  <section class="codertec-category-hub mb-5" aria-label="Introdução da categoria">
    <p class="codertec-category-hub__eyebrow mb-2">Categoria do blog</p>
    <h1 class="mb-3"><?php echo esc_html($category_name); ?></h1>

    <?php if (!empty($category_content['description_html'])) : ?>
      <div class="codertec-category-hub__intro mb-3"><?php echo wp_kses_post($category_content['description_html']); ?></div>
    <?php else : ?>
      <p class="codertec-category-hub__intro mb-3"><?php echo esc_html($category_content['intro']); ?></p>
    <?php endif; ?>

    <p class="codertec-category-hub__context mb-0"><?php echo esc_html($category_content['supporting_text']); ?></p>

    <div class="codertec-category-hub__meta mt-4">
      <span><?php echo esc_html(sprintf(_n('%s post publicado', '%s posts publicados', $total_posts, 'codertec'), number_format_i18n($total_posts))); ?></span>
      <a href="https://codertec.com.br/blog/" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/blog/', 'type' => 'navigation', 'area' => 'category_hub_nav', 'label' => 'Ver todos os posts')); ?>>Ver todos os posts</a>
      <a href="<?php echo esc_url(codertec_get_categories_page_url()); ?>" <?php echo codertec_get_tracking_attributes(array('url' => codertec_get_categories_page_url(), 'type' => 'navigation', 'area' => 'category_hub_nav', 'label' => 'Explorar outras categorias')); ?>>Explorar outras categorias</a>
    </div>
  </section>

  <div class="blog-search-wrapper mb-4">
    <?php get_search_form(); ?>
  </div>

  <div class="codertec-category-listing-heading mb-4">
    <h2 class="h4 mb-2">Posts sobre <?php echo esc_html($category_name); ?></h2>
    <p class="mb-0">Selecione um conteúdo para continuar a leitura dentro deste tema.</p>
  </div>

  <div class="row">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
          <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>" <?php echo codertec_get_tracking_attributes(array('url' => get_permalink(), 'type' => 'post_link', 'area' => 'category_post_list', 'label' => get_the_title(), 'destination' => 'blog_post')); ?>>
              <?php the_post_thumbnail('medium', array('class' => 'card-img-top', 'alt' => get_the_title())); ?>
            </a>
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <?php echo codertec_get_post_categories_markup(get_the_ID(), 'mb-3'); ?>
            <h5 class="card-title"><?php the_title(); ?></h5>
            <p class="codertec-category-card__meta text-muted mb-3">Publicado em <?php echo esc_html(get_the_date()); ?></p>
            <p class="card-text flex-grow-1"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
            <a href="<?php the_permalink(); ?>" class="btn btn-primary mt-auto" <?php echo codertec_get_tracking_attributes(array('url' => get_permalink(), 'type' => 'post_link', 'area' => 'category_post_list', 'label' => sprintf('Ler mais: %s', get_the_title()), 'destination' => 'blog_post')); ?>>Ler mais</a>
          </div>
        </div>
      </div>
    <?php endwhile; else : ?>
      <p class="text-center blog-search-empty">Nenhum post encontrado nesta categoria.</p>
    <?php endif; ?>
  </div>

  <?php
  the_posts_pagination(array(
    'mid_size' => 1,
    'prev_text' => '&larr; Anteriores',
    'next_text' => 'Próximos &rarr;',
    'class' => 'codertec-category-pagination',
  ));
  ?>

  <?php codertec_render_category_archive_cta($category); ?>
</div>

<?php get_footer(); ?>
