<?php get_header(); ?>

<div class="container my-5">
  <h1 class="text-center mb-4">Postagens do Blog</h1>
  <p class="text-dark text-center pb-4">Acompanhe nossas postagens e traga esses conceitos para a realidade do seu negócio.</p>
  <div class="row">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
          <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('medium', ['class' => 'card-img-top', 'alt' => get_the_title()]); ?>
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
      <p class="text-center">Nenhum post encontrado.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>