<?php get_header(); ?>

<div class="container py-5">
    <h1 class="mb-3"><?php the_title(); ?></h1>
    <p class="text-muted">Publicado em <?php echo get_the_date(); ?></p>
    <div class="mt-1"></div>
    <div class="post-content">
        <?php the_content(); ?>
    </div>

<div class="post-share mt-5 pt-4 border-top border-2 border-secondary">
   <p class="h5 mb-3">Gostou do post? Então bora compartilhar!</p>
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

<a href="/blog/" class="btn btn-secondary mt-5 mb-1">← Voltar ao Blog</a>
</div>

<?php get_footer(); ?>