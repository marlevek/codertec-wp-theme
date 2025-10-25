<?php
function codertec_theme_setup() {
    // Suporte a menus
    add_theme_support('menus');
    
    // Suporte a thumbnails e imagem de destaque
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 400, true);
    
    // Suporte a título automático da página
    add_theme_support('title-tag');
   
    // Suporte a excerpts (resumos) nos posts
    add_post_type_support('post', 'excerpt');
}
add_action('after_setup_theme', 'codertec_theme_setup');


// Forçar mostrar Imagem Destacada para todos os post types
function force_featured_image_metabox() {
    add_meta_box('postimagediv', 'Imagem Destacada', 'post_thumbnail_meta_box', null, 'side', 'high');
}
add_action('add_meta_boxes', 'force_featured_image_metabox');


// Enqueue scripts e styles
function codertec_enqueue_assets() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
    wp_enqueue_style('codertec-style', get_stylesheet_uri());
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'codertec_enqueue_assets');

// Registrar menu principal
register_nav_menus(array(
    'primary' => 'Menu Principal',
));


function add_custom_content_styles($content) {
    if (is_single()) {
        $content = '<div class="pt-4 lh-lg">' . $content . '</div>';
    }
    return $content;
}
add_filter('the_content', 'add_custom_content_styles');

// Redireciona páginas de autor para a home
add_action('template_redirect', function() {
    if (is_author()) {
        wp_redirect(home_url('/blog/'), 301);
        exit;
    }
});
