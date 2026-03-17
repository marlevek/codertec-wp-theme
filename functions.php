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
    wp_enqueue_script(
        'codertec-btn-topo',
        get_template_directory_uri() . '/assets/js/btn_topo.js',
        array(),
        filemtime(get_template_directory() . '/assets/js/btn_topo.js'),
        true
    );
    wp_add_inline_script('bootstrap-js', codertec_get_blog_tracking_inline_script(), 'after');
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

function codertec_get_trimmed_plain_text($text, $limit = 160) {
    $text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags((string) $text)));

    if ('' === $text) {
        return '';
    }

    return wp_html_excerpt($text, $limit, '...');
}

function codertec_get_meta_description() {
    if (is_single()) {
        $post_id = get_queried_object_id();
        $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : '';

        if (!empty($excerpt)) {
            return codertec_get_trimmed_plain_text($excerpt, 155);
        }

        return codertec_get_trimmed_plain_text(get_post_field('post_content', $post_id), 155);
    }

    if (is_category()) {
        $term = get_queried_object();
        $description = $term instanceof WP_Term ? term_description($term, 'category') : '';

        if (!empty($description)) {
            return codertec_get_trimmed_plain_text($description, 155);
        }
    }

    if (is_home() || is_front_page()) {
        return 'Conteúdos sobre IA, automação, bots, SaaS e desenvolvimento, com dicas e soluções práticas para o seu negócio.';
    }

    return 'CoderTec - Desenvolvimento de apps web, ciência de dados, IA e automação para pequenas empresas.';
}

function codertec_get_blog_tracking_surface() {
    if (is_single()) {
        return 'blog_single';
    }

    if (is_category()) {
        return 'blog_category';
    }

    if (is_search()) {
        return 'blog_search';
    }

    if (codertec_is_blog_categories_page()) {
        return 'blog_categories_page';
    }

    if (is_home() || is_front_page()) {
        return 'blog_home';
    }

    return 'blog_generic';
}

function codertec_get_blog_tracking_context() {
    $context = array(
        'pageSurface' => codertec_get_blog_tracking_surface(),
        'contentType' => '',
        'contentId' => 0,
        'contentName' => '',
        'contentSlug' => '',
        'categoryName' => '',
    );

    if (is_single()) {
        $post_id = get_queried_object_id();

        $context['contentType'] = 'post';
        $context['contentId'] = $post_id;
        $context['contentName'] = get_the_title($post_id);
        $context['contentSlug'] = get_post_field('post_name', $post_id);
    } elseif (is_category()) {
        $term = get_queried_object();

        if ($term instanceof WP_Term) {
            $context['contentType'] = 'category';
            $context['contentId'] = (int) $term->term_id;
            $context['contentName'] = $term->name;
            $context['contentSlug'] = $term->slug;
            $context['categoryName'] = $term->name;
        }
    }

    return $context;
}

function codertec_get_blog_tracking_inline_script() {
    $tracking_context_json = wp_json_encode(codertec_get_blog_tracking_context(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    return "window.codertecBlogTracking = {$tracking_context_json};\n"
        . "(function () {\n"
        . "    function getTrackingLink(target) {\n"
        . "        if (!target || typeof target.closest !== 'function') {\n"
        . "            return null;\n"
        . "        }\n\n"
        . "        return target.closest('[data-ct-track=\"1\"]');\n"
        . "    }\n\n"
        . "    function getValue(element, attributeName, fallbackValue) {\n"
        . "        if (!element) {\n"
        . "            return fallbackValue || '';\n"
        . "        }\n\n"
        . "        return element.getAttribute(attributeName) || fallbackValue || '';\n"
        . "    }\n\n"
        . "    function getTrimmedText(element) {\n"
        . "        if (!element || !element.textContent) {\n"
        . "            return '';\n"
        . "        }\n\n"
        . "        return element.textContent.replace(/\\s+/g, ' ').trim();\n"
        . "    }\n\n"
        . "    document.addEventListener('click', function (event) {\n"
        . "        var link = getTrackingLink(event.target);\n\n"
        . "        if (!link) {\n"
        . "            return;\n"
        . "        }\n\n"
        . "        var context = window.codertecBlogTracking || {};\n"
        . "        var payload = {\n"
        . "            event: getValue(link, 'data-ct-event', 'codertec_blog_click'),\n"
        . "            event_category: 'blog',\n"
        . "            event_action: 'click',\n"
        . "            link_type: getValue(link, 'data-ct-type', 'link'),\n"
        . "            click_area: getValue(link, 'data-ct-area'),\n"
        . "            click_label: getValue(link, 'data-ct-label', getTrimmedText(link)),\n"
        . "            click_destination: getValue(link, 'data-ct-destination'),\n"
        . "            click_url: link.href || '',\n"
        . "            page_surface: context.pageSurface || '',\n"
        . "            content_type: context.contentType || '',\n"
        . "            content_id: context.contentId || 0,\n"
        . "            content_name: context.contentName || '',\n"
        . "            content_slug: context.contentSlug || '',\n"
        . "            category_name: context.categoryName || ''\n"
        . "        };\n\n"
        . "        window.dataLayer = window.dataLayer || [];\n"
        . "        window.dataLayer.push(payload);\n\n"
        . "        window.dispatchEvent(new CustomEvent('codertec:blog-click', {\n"
        . "            detail: payload\n"
        . "        }));\n"
        . "    });\n"
        . "}());";
}

function codertec_get_tracking_destination_key($url = '') {
    $url = (string) $url;

    if ('' === $url) {
        return '';
    }

    if (false !== strpos($url, 'codertec.com.br/pt/index.html#servicos')) {
        return 'servicos_codertec';
    }

    if (false !== strpos($url, 'codertec.com.br/pt/index.html#contato')) {
        return 'contato_codertec';
    }

    if (false !== strpos($url, 'codertec.com.br/pt/produtos/psicoassist/')) {
        return 'psicoassist_resumo';
    }

    if (false !== strpos($url, 'psicoassist.codertec.com.br')) {
        return 'psicoassist_landing';
    }

    if (false !== strpos($url, 'manugest.com.br')) {
        return 'manugest';
    }

    if ('https://codertec.com.br/pt' === rtrim($url, '/')) {
        return 'site_codertec';
    }

    if (false !== strpos($url, 'codertec.com.br/blog/categorias')) {
        return 'blog_categories';
    }

    if (false !== strpos($url, 'codertec.com.br/blog')) {
        return 'blog_home';
    }

    $parsed_url = wp_parse_url($url);
    $site_url = wp_parse_url(home_url('/'));

    if (!empty($parsed_url['host']) && !empty($site_url['host']) && $parsed_url['host'] === $site_url['host']) {
        return 'internal_link';
    }

    return 'external_link';
}

function codertec_get_tracking_attributes($args = array()) {
    $args = wp_parse_args($args, array(
        'url' => '',
        'event' => 'codertec_blog_click',
        'type' => 'cta',
        'area' => '',
        'label' => '',
        'destination' => '',
    ));

    if (empty($args['destination'])) {
        $args['destination'] = codertec_get_tracking_destination_key($args['url']);
    }

    $attributes = array(
        'data-ct-track="1"',
        sprintf('data-ct-event="%s"', esc_attr($args['event'])),
        sprintf('data-ct-type="%s"', esc_attr($args['type'])),
    );

    if (!empty($args['area'])) {
        $attributes[] = sprintf('data-ct-area="%s"', esc_attr($args['area']));
    }

    if (!empty($args['label'])) {
        $attributes[] = sprintf('data-ct-label="%s"', esc_attr(wp_strip_all_tags($args['label'])));
    }

    if (!empty($args['destination'])) {
        $attributes[] = sprintf('data-ct-destination="%s"', esc_attr($args['destination']));
    }

    return implode(' ', $attributes);
}

function codertec_is_blog_categories_page() {
    return '1' === get_query_var('codertec_blog_categories');
}

function codertec_get_meta_title() {
    if (is_single()) {
        return sprintf('%s | Blog CoderTec', single_post_title('', false));
    }

    if (is_category()) {
        return sprintf('%s | Categoria do Blog CoderTec', single_cat_title('', false));
    }

    if (codertec_is_blog_categories_page()) {
        return 'Categorias do Blog CoderTec';
    }

    if (is_home() || is_front_page()) {
        return 'Blog CoderTec | IA, automação e tecnologia';
    }

    return wp_get_document_title();
}

function codertec_get_canonical_url() {
    $paged = max(1, (int) get_query_var('paged'));

    if (is_search() || is_404()) {
        return '';
    }

    if (is_single()) {
        $post_id = get_queried_object_id();

        if ($post_id) {
            return wp_get_canonical_url($post_id) ?: get_permalink($post_id);
        }
    }

    if (is_category() || is_home()) {
        return get_pagenum_link($paged);
    }

    if (codertec_is_blog_categories_page()) {
        if ($paged > 1) {
            return trailingslashit(codertec_get_categories_page_url()) . user_trailingslashit('page/' . $paged, 'paged');
        }

        return codertec_get_categories_page_url();
    }

    if (is_page()) {
        return get_permalink();
    }

    return '';
}

function codertec_get_default_social_image() {
    $path = get_theme_file_path('assets/images/destaque-dashboard-organizado.png');
    $url = get_theme_file_uri('assets/images/destaque-dashboard-organizado.png');
    $dimensions = file_exists($path) ? @getimagesize($path) : false;

    return array(
        'url' => $url,
        'width' => !empty($dimensions[0]) ? (int) $dimensions[0] : 0,
        'height' => !empty($dimensions[1]) ? (int) $dimensions[1] : 0,
        'alt' => 'CoderTec - Blog sobre IA, automação e tecnologia',
    );
}

function codertec_get_social_image_data() {
    if (is_single() && has_post_thumbnail()) {
        $thumbnail_id = get_post_thumbnail_id(get_queried_object_id());
        $image_data = wp_get_attachment_image_src($thumbnail_id, 'full');

        if (!empty($image_data[0])) {
            $image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

            return array(
                'url' => $image_data[0],
                'width' => !empty($image_data[1]) ? (int) $image_data[1] : 0,
                'height' => !empty($image_data[2]) ? (int) $image_data[2] : 0,
                'alt' => $image_alt ?: get_the_title(get_queried_object_id()),
            );
        }
    }

    return codertec_get_default_social_image();
}

function codertec_get_social_meta() {
    $image = codertec_get_social_image_data();
    $canonical_url = codertec_get_canonical_url();
    $og_type = is_single() ? 'article' : 'website';

    return array(
        'title' => codertec_get_meta_title(),
        'description' => codertec_get_meta_description(),
        'url' => $canonical_url,
        'type' => $og_type,
        'image' => $image,
        'site_name' => 'CoderTec',
        'locale' => str_replace('-', '_', get_bloginfo('language')),
        'twitter_card' => !empty($image['url']) ? 'summary_large_image' : 'summary',
    );
}

function codertec_get_schema_data() {
    $canonical_url = codertec_get_canonical_url();

    if (empty($canonical_url)) {
        return array();
    }

    if (is_single()) {
        $post_id = get_queried_object_id();
        $image = codertec_get_social_image_data();
        $categories = wp_get_post_terms($post_id, 'category', array('fields' => 'names'));
        $tags = wp_get_post_terms($post_id, 'post_tag', array('fields' => 'names'));

        return array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title($post_id),
            'description' => codertec_get_meta_description(),
            'datePublished' => get_post_time('c', false, $post_id),
            'dateModified' => get_post_modified_time('c', false, $post_id),
            'mainEntityOfPage' => $canonical_url,
            'image' => !empty($image['url']) ? array($image['url']) : array(),
            'articleSection' => !empty($categories) ? array_values($categories) : array(),
            'keywords' => !empty($tags) ? implode(', ', $tags) : '',
            'author' => array(
                '@type' => 'Organization',
                'name' => 'CoderTec',
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => 'CoderTec',
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_theme_file_uri('assets/images/logo_codertec_atual.png'),
                ),
            ),
        );
    }

    if (is_home() || is_category() || codertec_is_blog_categories_page()) {
        return array(
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => codertec_get_meta_title(),
            'description' => codertec_get_meta_description(),
            'url' => $canonical_url,
            'publisher' => array(
                '@type' => 'Organization',
                'name' => 'CoderTec',
            ),
        );
    }

    return array();
}

function codertec_render_head_meta() {
    $meta = codertec_get_social_meta();
    $schema = codertec_get_schema_data();

    if (!empty($meta['url'])) {
        printf("<link rel=\"canonical\" href=\"%s\">\n", esc_url($meta['url']));
    }

    printf("<meta property=\"og:locale\" content=\"%s\">\n", esc_attr($meta['locale']));
    printf("<meta property=\"og:type\" content=\"%s\">\n", esc_attr($meta['type']));
    printf("<meta property=\"og:title\" content=\"%s\">\n", esc_attr($meta['title']));
    printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($meta['description']));

    if (!empty($meta['url'])) {
        printf("<meta property=\"og:url\" content=\"%s\">\n", esc_url($meta['url']));
    }

    printf("<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr($meta['site_name']));

    if (!empty($meta['image']['url'])) {
        printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url($meta['image']['url']));

        if (!empty($meta['image']['width'])) {
            printf("<meta property=\"og:image:width\" content=\"%d\">\n", (int) $meta['image']['width']);
        }

        if (!empty($meta['image']['height'])) {
            printf("<meta property=\"og:image:height\" content=\"%d\">\n", (int) $meta['image']['height']);
        }

        if (!empty($meta['image']['alt'])) {
            printf("<meta property=\"og:image:alt\" content=\"%s\">\n", esc_attr($meta['image']['alt']));
        }
    }

    printf("<meta name=\"twitter:card\" content=\"%s\">\n", esc_attr($meta['twitter_card']));
    printf("<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr($meta['title']));
    printf("<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr($meta['description']));

    if (!empty($meta['image']['url'])) {
        printf("<meta name=\"twitter:image\" content=\"%s\">\n", esc_url($meta['image']['url']));

        if (!empty($meta['image']['alt'])) {
            printf("<meta name=\"twitter:image:alt\" content=\"%s\">\n", esc_attr($meta['image']['alt']));
        }
    }

    if (!empty($schema)) {
        printf(
            "<script type=\"application/ld+json\">%s</script>\n",
            wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}

function codertec_override_default_canonical() {
    remove_action('wp_head', 'rel_canonical');
}
add_action('init', 'codertec_override_default_canonical');

function codertec_get_post_terms_markup($post_id = 0, $taxonomy = 'category', $wrapper_class = '', $chip_class = 'post-category-chip', $aria_label = '') {
    $post_id = $post_id ?: get_the_ID();
    $terms = get_the_terms($post_id, $taxonomy);

    if (empty($terms) || is_wp_error($terms)) {
        return '';
    }

    $links = array();

    foreach ($terms as $term) {
        $links[] = sprintf(
            '<a class="%1$s" href="%2$s">%3$s</a>',
            esc_attr($chip_class),
            esc_url(get_term_link($term)),
            esc_html($term->name)
        );
    }

    $classes = trim('post-terms ' . $wrapper_class);

    return sprintf(
        '<div class="%1$s" aria-label="%2$s">%3$s</div>',
        esc_attr($classes),
        esc_attr($aria_label ?: sprintf('Termos de %s', $taxonomy)),
        implode('', $links)
    );
}

function codertec_get_post_categories_markup($post_id = 0, $wrapper_class = '') {
    return codertec_get_post_terms_markup(
        $post_id,
        'category',
        trim('post-categories ' . $wrapper_class),
        'post-category-chip',
        'Categorias do post'
    );
}

function codertec_get_post_tags_markup($post_id = 0, $wrapper_class = '') {
    return codertec_get_post_terms_markup(
        $post_id,
        'post_tag',
        trim('post-tags ' . $wrapper_class),
        'post-tag-chip',
        'Tags do post'
    );
}

function codertec_get_post_reading_time_label($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(wp_strip_all_tags((string) $content));
    $minutes = max(1, (int) ceil($word_count / 180));

    return sprintf(_n('%s min de leitura', '%s min de leitura', $minutes, 'codertec'), $minutes);
}

function codertec_render_institutional_cta() {
    $surface = codertec_get_blog_tracking_surface();
    $area = $surface . '_institutional_cta';
    ?>
    <section class="codertec-institutional-cta mt-5" aria-label="Conheça as soluções da CoderTec">
        <h2 class="h4 mb-3">Leve essa ideia para a prática com a CoderTec</h2>
        <p class="mb-4">Conheça nossos serviços, explore o PsicoAssist e fale com a equipe da CoderTec para transformar tecnologia em resultado no seu negócio.</p>
        <div class="codertec-institutional-cta__actions">
            <a class="btn btn-primary" href="https://codertec.com.br/pt/index.html#servicos" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/index.html#servicos', 'area' => $area, 'label' => 'Conhecer serviços')); ?>>Conhecer serviços</a>
            <a class="btn btn-outline-primary codertec-institutional-cta__secondary" href="https://codertec.com.br/pt/" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/', 'area' => $area, 'label' => 'Site da CoderTec')); ?>>Site da CoderTec</a>
            <a class="btn btn-outline-primary codertec-institutional-cta__secondary" href="https://codertec.com.br/pt/produtos/psicoassist/" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/produtos/psicoassist/', 'area' => $area, 'label' => 'Ver PsicoAssist')); ?>>Ver PsicoAssist</a>
            <a class="btn btn-outline-primary codertec-institutional-cta__secondary" href="https://manugest.com.br/" <?php echo codertec_get_tracking_attributes(array('url' => 'https://manugest.com.br/', 'area' => $area, 'label' => 'Conhecer o Manugest')); ?>>Conhecer o Manugest</a>
            <a class="btn btn-outline-primary codertec-institutional-cta__secondary" href="https://codertec.com.br/pt/index.html#contato" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/index.html#contato', 'area' => $area, 'label' => 'Entrar em contato')); ?>>Entrar em contato</a>
        </div>
    </section>
    <?php
}

function codertec_get_categories_page_url() {
    return 'https://codertec.com.br/blog/categorias/';
}

function codertec_register_categories_page_route() {
    add_rewrite_rule('^categorias/?$', 'index.php?codertec_blog_categories=1', 'top');
    add_rewrite_rule('^blog/categorias/?$', 'index.php?codertec_blog_categories=1', 'top');
}
add_action('init', 'codertec_register_categories_page_route');

function codertec_register_categories_page_query_var($query_vars) {
    $query_vars[] = 'codertec_blog_categories';

    return $query_vars;
}
add_filter('query_vars', 'codertec_register_categories_page_query_var');

function codertec_load_categories_page_template($template) {
    if ('1' !== get_query_var('codertec_blog_categories')) {
        return $template;
    }

    $categories_template = locate_template('page-categorias-blog.php');

    if (!empty($categories_template)) {
        return $categories_template;
    }

    return $template;
}
add_filter('template_include', 'codertec_load_categories_page_template');

function codertec_flush_categories_page_route() {
    $route_version = '3';
    $stored_version = get_option('codertec_categories_route_version');

    if ($stored_version === $route_version) {
        return;
    }

    codertec_register_categories_page_route();
    flush_rewrite_rules(false);
    update_option('codertec_categories_route_version', $route_version);
}
add_action('init', 'codertec_flush_categories_page_route', 20);

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
            <a class="btn btn-primary" href="<?php echo esc_url('https://codertec.com.br/pt/produtos/psicoassist/'); ?>" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/produtos/psicoassist/', 'area' => 'single_post_psicoassist_cta', 'label' => 'Ver resumo na CoderTec')); ?>>Ver resumo na CoderTec</a>
            <a class="btn btn-outline-primary codertec-product-cta__secondary" href="<?php echo esc_url('https://psicoassist.codertec.com.br/'); ?>" <?php echo codertec_get_tracking_attributes(array('url' => 'https://psicoassist.codertec.com.br/', 'area' => 'single_post_psicoassist_cta', 'label' => 'Acessar landing do PsicoAssist')); ?>>Acessar landing do PsicoAssist</a>
        </div>
    </section>
    <?php
}

function codertec_get_single_post_context_data($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();
    $categories = get_the_category($post_id);
    $values = array(
        codertec_normalize_psicoassist_context_value(get_post_field('post_name', $post_id)),
        codertec_normalize_psicoassist_context_value(get_the_title($post_id)),
    );

    foreach ($categories as $category) {
        $values[] = codertec_normalize_psicoassist_context_value($category->slug);
        $values[] = codertec_normalize_psicoassist_context_value($category->name);
    }

    $values = array_values(array_unique(array_filter($values)));
    $tokens = array();

    foreach ($values as $value) {
        $tokens = array_merge($tokens, array_filter(explode('-', $value)));
    }

    return array(
        'values' => $values,
        'tokens' => array_values(array_unique($tokens)),
        'categories' => $categories,
    );
}

function codertec_context_has_keyword_match($context_values, $keywords) {
    foreach ($context_values as $context_value) {
        foreach ($keywords as $keyword) {
            if ($context_value && false !== strpos($context_value, $keyword)) {
                return true;
            }
        }
    }

    return false;
}

function codertec_context_has_token_match($tokens, $keywords) {
    return !empty(array_intersect($tokens, $keywords));
}

function codertec_get_single_post_conversion_cta($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();
    $context_data = codertec_get_single_post_context_data($post_id);
    $context_values = $context_data['values'];
    $context_tokens = $context_data['tokens'];
    $services_url = 'https://codertec.com.br/pt/index.html#servicos';

    if (codertec_should_display_psicoassist_cta($post_id)) {
        return array(
            'title' => 'Conheça o PsicoAssist',
            'description' => 'Se este conteúdo conversa com a rotina de psicólogos e clínicas, o PsicoAssist pode ajudar a organizar atendimentos e reduzir tarefas manuais.',
            'primary_label' => 'Ver página do PsicoAssist',
            'primary_url' => 'https://codertec.com.br/pt/produtos/psicoassist/',
            'secondary_label' => 'Acessar landing do PsicoAssist',
            'secondary_url' => 'https://psicoassist.codertec.com.br/',
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('automacao', 'automatizacao', 'fluxo', 'integracao'))) {
        return array(
            'title' => 'Automação para ganhar eficiência',
            'description' => 'A CoderTec desenvolve automações e integrações sob medida para reduzir retrabalho, organizar processos e acelerar a operação.',
            'primary_label' => 'Ver soluções em automação',
            'primary_url' => $services_url,
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('dashboard', 'dashboards', 'relatorios', 'indicadores')) || codertec_context_has_token_match($context_tokens, array('bi'))) {
        return array(
            'title' => 'Dashboards e dados para decisões melhores',
            'description' => 'Transforme dados em painéis claros e úteis para acompanhar indicadores, visualizar resultados e apoiar decisões do dia a dia.',
            'primary_label' => 'Conhecer soluções em dashboards',
            'primary_url' => $services_url,
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('inteligencia-artificial', 'machine-learning', 'modelos', 'llm', 'gpt')) || codertec_context_has_token_match($context_tokens, array('ia'))) {
        return array(
            'title' => 'IA aplicada ao seu negócio',
            'description' => 'A CoderTec cria soluções com inteligência artificial para atendimento, produtividade, análise de dados e automação inteligente.',
            'primary_label' => 'Ver soluções com IA',
            'primary_url' => $services_url,
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('desenvolvimento', 'wordpress', 'sites', 'site', 'app', 'aplicativo', 'software', 'sistema', 'web'))) {
        return array(
            'title' => 'Desenvolvimento web sob medida',
            'description' => 'Se você precisa transformar uma ideia em produto digital, a CoderTec desenvolve sites, sistemas e aplicações alinhados ao seu negócio.',
            'primary_label' => 'Conhecer serviços de desenvolvimento',
            'primary_url' => $services_url,
        );
    }

    return array(
        'title' => 'Conte com a CoderTec para tirar projetos do papel',
        'description' => 'Se você quer aplicar tecnologia com foco em resultado, a CoderTec pode apoiar com desenvolvimento, automação, IA e soluções sob medida.',
        'primary_label' => 'Conhecer serviços da CoderTec',
        'primary_url' => $services_url,
    );
}

function codertec_get_related_posts_for_single($post_id = 0, $limit = 3) {
    $post_id = $post_id ?: get_the_ID();
    $category_ids = wp_get_post_categories($post_id, array('fields' => 'ids'));
    $query_args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'post__not_in' => array($post_id),
        'ignore_sticky_posts' => true,
    );

    if (!empty($category_ids)) {
        $query_args['category__in'] = $category_ids;
    }

    $related_posts = get_posts($query_args);

    if (!empty($related_posts)) {
        return $related_posts;
    }

    return get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'post__not_in' => array($post_id),
        'ignore_sticky_posts' => true,
    ));
}

function codertec_render_single_post_conversion_section($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();

    if (!$post_id || 'post' !== get_post_type($post_id)) {
        return;
    }

    $cta = codertec_get_single_post_conversion_cta($post_id);
    $related_posts = codertec_get_related_posts_for_single($post_id);
    ?>
    <section class="codertec-post-conversion mt-5" aria-label="Próximos passos após a leitura do post">
        <div class="codertec-post-conversion__header">
            <h2 class="h4 mb-3">Continue explorando este tema</h2>
            <p class="mb-0">Se este conteúdo foi útil, veja outros materiais do blog e descubra como a CoderTec pode transformar esse assunto em uma solução prática.</p>
        </div>

        <div class="codertec-post-conversion__grid mt-4">
            <div class="codertec-post-conversion__panel">
                <h3 class="h5 mb-3">Leituras relacionadas</h3>
                <?php if (!empty($related_posts)) : ?>
                    <ul class="codertec-post-conversion__related-list">
                        <?php foreach ($related_posts as $related_post) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>" <?php echo codertec_get_tracking_attributes(array('url' => get_permalink($related_post->ID), 'type' => 'related_post', 'area' => 'single_post_related_posts', 'label' => get_the_title($related_post->ID), 'destination' => 'blog_post')); ?>>
                                    <?php echo esc_html(get_the_title($related_post->ID)); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="mb-3">Explore outros conteúdos do blog para continuar a leitura.</p>
                <?php endif; ?>

                <div class="codertec-post-conversion__nav-links">
                    <a href="https://codertec.com.br/blog/" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/blog/', 'type' => 'navigation', 'area' => 'single_post_conversion_nav', 'label' => 'Ver todos os posts')); ?>>Ver todos os posts</a>
                    <a href="<?php echo esc_url(codertec_get_categories_page_url()); ?>" <?php echo codertec_get_tracking_attributes(array('url' => codertec_get_categories_page_url(), 'type' => 'navigation', 'area' => 'single_post_conversion_nav', 'label' => 'Explorar categorias')); ?>>Explorar categorias</a>
                </div>
            </div>

            <div class="codertec-post-conversion__panel codertec-post-conversion__panel--cta">
                <h3 class="h5 mb-3"><?php echo esc_html($cta['title']); ?></h3>
                <p class="mb-4"><?php echo esc_html($cta['description']); ?></p>
                <div class="codertec-post-conversion__actions">
                    <a class="btn btn-primary" href="<?php echo esc_url($cta['primary_url']); ?>" <?php echo codertec_get_tracking_attributes(array('url' => $cta['primary_url'], 'area' => 'single_post_conversion_cta', 'label' => $cta['primary_label'])); ?>>
                        <?php echo esc_html($cta['primary_label']); ?>
                    </a>
                    <?php if (!empty($cta['secondary_label']) && !empty($cta['secondary_url'])) : ?>
                        <a class="btn btn-outline-primary codertec-post-conversion__secondary" href="<?php echo esc_url($cta['secondary_url']); ?>" <?php echo codertec_get_tracking_attributes(array('url' => $cta['secondary_url'], 'area' => 'single_post_conversion_cta', 'label' => $cta['secondary_label'])); ?>>
                            <?php echo esc_html($cta['secondary_label']); ?>
                        </a>
                    <?php endif; ?>
                    <a class="btn btn-outline-primary codertec-post-conversion__secondary" href="https://codertec.com.br/pt/index.html#contato" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/index.html#contato', 'area' => 'single_post_conversion_cta', 'label' => 'Falar com a CoderTec')); ?>>
                        Falar com a CoderTec
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php
}

function codertec_get_category_context_data($term = null) {
    $term = $term ?: get_queried_object();

    if (!($term instanceof WP_Term) || 'category' !== $term->taxonomy) {
        return array(
            'values' => array(),
            'tokens' => array(),
        );
    }

    $values = array(
        codertec_normalize_psicoassist_context_value($term->slug),
        codertec_normalize_psicoassist_context_value($term->name),
        codertec_normalize_psicoassist_context_value(wp_strip_all_tags(term_description($term, 'category'))),
    );

    $values = array_values(array_unique(array_filter($values)));
    $tokens = array();

    foreach ($values as $value) {
        $tokens = array_merge($tokens, array_filter(explode('-', $value)));
    }

    return array(
        'values' => $values,
        'tokens' => array_values(array_unique($tokens)),
    );
}

function codertec_get_category_archive_content($term = null) {
    $term = $term ?: get_queried_object();

    if (!($term instanceof WP_Term) || 'category' !== $term->taxonomy) {
        return array(
            'description_html' => '',
            'intro' => '',
            'supporting_text' => '',
        );
    }

    $context_data = codertec_get_category_context_data($term);
    $context_values = $context_data['values'];
    $context_tokens = $context_data['tokens'];
    $description_html = term_description($term, 'category');

    $content = array(
        'description_html' => $description_html,
        'intro' => sprintf(
            'Explore os posts da categoria %s e aprofunde este tema com aplicações práticas, tendências e ideias para o dia a dia do negócio.',
            $term->name
        ),
        'supporting_text' => 'Use a listagem abaixo para continuar a leitura, encontrar conteúdos relacionados e avançar para a próxima etapa quando fizer sentido.',
    );

    if (codertec_context_has_keyword_match($context_values, array('automacao', 'automatizacao', 'fluxo', 'integracao'))) {
        $content['intro'] = 'Reunimos nesta categoria conteúdos sobre automação, integrações e melhoria de processos para reduzir retrabalho e ganhar escala.';
        $content['supporting_text'] = 'Se você está pesquisando esse tema, os posts abaixo ajudam a entender por onde começar e como transformar processo em eficiência.';
    } elseif (codertec_context_has_keyword_match($context_values, array('dashboard', 'dashboards', 'relatorios', 'indicadores')) || codertec_context_has_token_match($context_tokens, array('bi'))) {
        $content['intro'] = 'Aqui você encontra conteúdos sobre dashboards, indicadores e organização de dados para apoiar decisões com mais clareza.';
        $content['supporting_text'] = 'A sequência de posts desta categoria ajuda a evoluir da análise do problema até a visualização prática das informações.';
    } elseif (codertec_context_has_keyword_match($context_values, array('inteligencia-artificial', 'machine-learning', 'modelos', 'llm', 'gpt')) || codertec_context_has_token_match($context_tokens, array('ia'))) {
        $content['intro'] = 'Esta categoria reúne conteúdos sobre inteligência artificial aplicada a produtividade, atendimento, análise de dados e automações inteligentes.';
        $content['supporting_text'] = 'Os posts abaixo funcionam como um hub para explorar oportunidades reais de IA antes de levar a ideia para a operação.';
    } elseif (codertec_context_has_keyword_match($context_values, array('desenvolvimento', 'wordpress', 'sites', 'site', 'app', 'aplicativo', 'software', 'sistema', 'web'))) {
        $content['intro'] = 'Veja nesta categoria conteúdos sobre desenvolvimento web, criação de sistemas, sites e aplicações alinhadas às necessidades do negócio.';
        $content['supporting_text'] = 'Navegue pelos posts para comparar abordagens, entender boas práticas e identificar caminhos viáveis para seu projeto digital.';
    } elseif (codertec_context_has_keyword_match($context_values, array('psicologia', 'psicologo', 'psicologos', 'clinica', 'clinicas', 'consultorio', 'consultorios', 'saude-mental'))) {
        $content['intro'] = 'Esta categoria concentra conteúdos sobre psicologia, rotina clínica, produtividade e uso de tecnologia no contexto de psicólogos e clínicas.';
        $content['supporting_text'] = 'Os materiais abaixo ajudam a aprofundar o tema e identificar oportunidades de organizar processos, atendimentos e operação.';
    }

    return $content;
}

function codertec_get_category_archive_cta($term = null) {
    $term = $term ?: get_queried_object();

    if (!($term instanceof WP_Term) || 'category' !== $term->taxonomy) {
        return array();
    }

    $context_data = codertec_get_category_context_data($term);
    $context_values = $context_data['values'];
    $context_tokens = $context_data['tokens'];
    $services_url = 'https://codertec.com.br/pt/index.html#servicos';

    if (codertec_context_has_keyword_match($context_values, array('psicoassist', 'psicologia', 'psicologo', 'psicologos', 'clinica', 'clinicas', 'consultorio', 'consultorios', 'saude-mental'))) {
        return array(
            'title' => 'Quer levar esse tema para a rotina da clínica?',
            'description' => 'O PsicoAssist ajuda psicólogos e clínicas a organizar atendimentos, reduzir tarefas manuais e ganhar mais fluidez no dia a dia.',
            'primary_label' => 'Conhecer o PsicoAssist',
            'primary_url' => 'https://codertec.com.br/pt/produtos/psicoassist/',
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('automacao', 'automatizacao', 'fluxo', 'integracao'))) {
        return array(
            'title' => 'Automações podem destravar sua operação',
            'description' => 'Se este é um tema importante para o seu negócio, a CoderTec pode desenhar automações e integrações sob medida para sua rotina.',
            'primary_label' => 'Ver soluções em automação',
            'primary_url' => $services_url,
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('dashboard', 'dashboards', 'relatorios', 'indicadores')) || codertec_context_has_token_match($context_tokens, array('bi'))) {
        return array(
            'title' => 'Transforme dados em visão de negócio',
            'description' => 'A CoderTec desenvolve dashboards e painéis sob medida para acompanhar indicadores com clareza e apoiar decisões melhores.',
            'primary_label' => 'Conhecer soluções em dashboards',
            'primary_url' => $services_url,
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('inteligencia-artificial', 'machine-learning', 'modelos', 'llm', 'gpt')) || codertec_context_has_token_match($context_tokens, array('ia'))) {
        return array(
            'title' => 'IA aplicada com foco em resultado',
            'description' => 'Quando fizer sentido avançar, a CoderTec pode ajudar a transformar esse tema em soluções práticas para produtividade, atendimento e análise.',
            'primary_label' => 'Ver soluções com IA',
            'primary_url' => $services_url,
        );
    }

    if (codertec_context_has_keyword_match($context_values, array('desenvolvimento', 'wordpress', 'sites', 'site', 'app', 'aplicativo', 'software', 'sistema', 'web'))) {
        return array(
            'title' => 'Seu projeto digital pode sair do papel',
            'description' => 'A CoderTec desenvolve sites, sistemas e aplicações sob medida para estruturar melhor sua presença digital e operação.',
            'primary_label' => 'Conhecer serviços de desenvolvimento',
            'primary_url' => $services_url,
        );
    }

    return array(
        'title' => 'Quer aplicar esse tema no seu negócio?',
        'description' => 'A CoderTec apoia empresas com desenvolvimento, automação, IA e soluções sob medida para transformar conteúdo em ação.',
        'primary_label' => 'Conhecer serviços da CoderTec',
        'primary_url' => $services_url,
    );
}

function codertec_render_category_archive_cta($term = null) {
    $cta = codertec_get_category_archive_cta($term);

    if (empty($cta)) {
        return;
    }
    ?>
    <section class="codertec-category-cta mt-5" aria-label="Próximo passo após explorar a categoria">
        <h2 class="h4 mb-3"><?php echo esc_html($cta['title']); ?></h2>
        <p class="mb-4"><?php echo esc_html($cta['description']); ?></p>
        <div class="codertec-category-cta__actions">
            <a class="btn btn-primary" href="<?php echo esc_url($cta['primary_url']); ?>" <?php echo codertec_get_tracking_attributes(array('url' => $cta['primary_url'], 'area' => 'category_archive_cta', 'label' => $cta['primary_label'])); ?>>
                <?php echo esc_html($cta['primary_label']); ?>
            </a>
            <a class="btn btn-outline-primary codertec-category-cta__secondary" href="https://codertec.com.br/pt/index.html#contato" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/index.html#contato', 'area' => 'category_archive_cta', 'label' => 'Falar com a CoderTec')); ?>>
                Falar com a CoderTec
            </a>
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
