<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo esc_attr(codertec_get_meta_description()); ?>">
    <meta name="author" content="CoderTec">
    <?php codertec_render_head_meta(); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/889104c503.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/estilos.css">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="https://codertec.com.br/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo_codertec_atual.png" alt="logotipo CoderTec"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/blog/">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo esc_url(codertec_get_categories_page_url()); ?>">Categorias</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/pt/" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/', 'type' => 'navigation', 'area' => 'header_navigation', 'label' => 'Site da CoderTec')); ?>>Site da CoderTec</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/pt/index.html#contato" <?php echo codertec_get_tracking_attributes(array('url' => 'https://codertec.com.br/pt/index.html#contato', 'type' => 'navigation', 'area' => 'header_navigation', 'label' => 'Contato')); ?>>Contato</a></li>
                </ul>
            </div>
        </div>
    </nav>
