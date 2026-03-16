<form role="search" method="get" class="blog-search-form row g-2 justify-content-center" action="<?php echo esc_url(home_url('/')); ?>">
  <div class="col-12 col-md-8 col-lg-6">
    <label class="visually-hidden" for="blog-search-input">Buscar no blog</label>
    <input
      type="search"
      id="blog-search-input"
      class="form-control form-control-lg"
      placeholder="Buscar posts no blog"
      value="<?php echo esc_attr(get_search_query()); ?>"
      name="s"
    >
    <input type="hidden" name="post_type" value="post">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary btn-lg">Buscar</button>
  </div>
</form>
