<?php
function codertec_theme_setup() {
    // Suporte a menus
    add_theme_support('menus');

    // Suporte a thumbnails e imagem de destaque
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 400, true);

    // Suporte a titulo automatico da pagina
    add_theme_support('title-tag');

    // Suporte a excerpts (resumos) nos posts
    add_post_type_support('post', 'excerpt');
}
add_action('after_setup_theme', 'codertec_theme_setup');

// Forcar mostrar imagem destacada para todos os post types
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

function codertec_limit_search_to_posts($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return;
    }

    $query->set('post_type', 'post');
}
add_action('pre_get_posts', 'codertec_limit_search_to_posts');

function codertec_search_posts_by_title_only($search, $query) {
    global $wpdb;

    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return $search;
    }

    $post_type = $query->get('post_type');

    if (!empty($post_type) && 'post' !== $post_type) {
        return $search;
    }

    $search_terms = $query->get('search_terms');

    if (empty($search_terms) || !is_array($search_terms)) {
        return $search;
    }

    $title_conditions = array();

    foreach ($search_terms as $term) {
        $like = '%' . $wpdb->esc_like($term) . '%';
        $title_conditions[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", $like);
    }

    if (empty($title_conditions)) {
        return $search;
    }

    $search = ' AND (' . implode(' AND ', $title_conditions) . ') ';

    if (!is_user_logged_in()) {
        $search .= " AND ({$wpdb->posts}.post_password = '') ";
    }

    return $search;
}
add_filter('posts_search', 'codertec_search_posts_by_title_only', 10, 2);

function codertec_normalize_psicoassist_context_value($value) {
    return sanitize_title(wp_strip_all_tags((string) $value));
}

function codertec_should_display_psicoassist_cta($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();

    if (!$post_id || 'post' !== get_post_type($post_id)) {
        return false;
    }

    $keywords = array(
        'psicoassist',
        'psicologia',
        'psicologo',
        'psicologos',
        'clinica',
        'clinicas',
        'consultorio',
        'consultorios',
        'saude-mental',
        'ia-para-psicologos',
        'automacao-para-psicologos',
    );

    $excluded_keywords = array(
        'odonto',
        'odontologia',
        'odontologico',
        'odontologicos',
        'odontologica',
        'odontologicas',
        'dentista',
        'dentistas',
    );

    $post_context_values = array(
        codertec_normalize_psicoassist_context_value(get_post_field('post_name', $post_id)),
        codertec_normalize_psicoassist_context_value(get_the_title($post_id)),
    );

    foreach ($post_context_values as $context_value) {
        foreach ($excluded_keywords as $excluded_keyword) {
            if ($context_value && false !== strpos($context_value, $excluded_keyword)) {
                return false;
            }
        }
    }

    $terms = array();

    foreach (array('category', 'post_tag') as $taxonomy) {
        $taxonomy_terms = wp_get_post_terms($post_id, $taxonomy);

        if (is_wp_error($taxonomy_terms) || empty($taxonomy_terms)) {
            continue;
        }

        $terms = array_merge($terms, $taxonomy_terms);
    }

    $should_display = false;

    foreach ($terms as $term) {
        $context_values = array(
            codertec_normalize_psicoassist_context_value($term->slug),
            codertec_normalize_psicoassist_context_value($term->name),
        );

        foreach ($context_values as $context_value) {
            foreach ($excluded_keywords as $excluded_keyword) {
                if ($context_value && false !== strpos($context_value, $excluded_keyword)) {
                    return false;
                }
            }

            foreach ($keywords as $keyword) {
                if ($context_value && false !== strpos($context_value, $keyword)) {
                    $should_display = true;
                    break 3;
                }
            }
        }
    }

    return (bool) apply_filters('codertec_should_display_psicoassist_cta', $should_display, $post_id, $terms);
}

function codertec_render_psicoassist_cta($post_id = 0) {
    if (!codertec_should_display_psicoassist_cta($post_id)) {
        return;
    }
    ?>
    <section class="codertec-product-cta mt-5" aria-label="CTA do PsicoAssist">
        <h2 class="h4 mb-3">Conhe&ccedil;a o PsicoAssist</h2>
        <p class="mb-4">O PsicoAssist &eacute; uma solu&ccedil;&atilde;o com IA e automa&ccedil;&atilde;o para psic&oacute;logos e cl&iacute;nicas organizarem atendimentos, reduzirem tarefas manuais e ganharem efici&ecirc;ncia.</p>
        <div class="d-flex flex-wrap gap-3">
            <a class="btn btn-primary" href="<?php echo esc_url('https://codertec.com.br/pt/produtos/psicoassist/'); ?>">Ver resumo na CoderTec</a>
            <a class="btn btn-outline-primary codertec-product-cta__secondary" href="<?php echo esc_url('https://psicoassist.codertec.com.br/'); ?>">Acessar landing do PsicoAssist</a>
        </div>
    </section>
    <?php
}

// Redireciona paginas de autor para a home
add_action('template_redirect', function() {
    if (is_author()) {
        wp_redirect(home_url('/blog/'), 301);
        exit;
    }
});
