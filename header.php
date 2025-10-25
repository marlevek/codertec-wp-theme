<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CoderTec - Desenvolvimento de apps web, ciência de dados, IA e automação com Python para pequenas empresas.">
    <meta name="keywords" content="desenvolvimento web, ciência de dados, IA, automação, Dashboards, pequenas empresas">
    <meta name="author" content="CoderTec">
    <title><?php bloginfo('name'); ?> | <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/889104c503.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/estilos.css">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/static/images/favicon.png" type="image/x-icon">
   




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
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/#sobre">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/#servicos">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/#missao">Missão</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/#contato">Contato</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://codertec.com.br/blog/">Blog</a></li>
                </ul>
            </div>
        </div>
    </nav>