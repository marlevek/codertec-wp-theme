<?php get_header(); ?>

<div class="container my-5">
  <h1 class="text-center mb-3">Resultados da busca</h1>
  <p class="text-dark text-center pb-3">Você pesquisou por: <strong><?php echo esc_html(get_search_query()); ?></strong></p>

  <div class="blog-search-wrapper mb-5">
    <?php get_search_form(); ?>
  </div>

  <div class="row">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
          <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('medium', array('class' => 'card-img-top', 'alt' => get_the_title())); ?>
            </a>
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php the_title(); ?></h5>
            <p class="card-text flex-grow-1"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
            <a href="<?php the_permalink(); ?>" class="btn btn-primary mt-auto">Ler mais</a>
          </div>
        </div>
      </div>
    <?php endwhile; else : ?>
      <p class="text-center blog-search-empty">Nenhum post encontrado para sua busca. Tente outro termo.</p>
    <?php endif; ?>
  </div>

  <?php codertec_render_institutional_cta(); ?>
</div>

<?php get_footer(); ?>
