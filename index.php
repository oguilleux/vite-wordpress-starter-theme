<?php
get_header();
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

	        <?php
	        while(have_posts()) : the_post();
		        ?>

                <section>
			        <?php the_title('<h1>', '</h1>'); ?>

			        <?php
			        the_content();
			        ?>
                </section>

            <div class="container">
              <h1>Hello!</h1>
              <p>Finally, it works!</p>
              <p>Try to type something below and save it in editor without refresh the browser!</p>

              <!-- Your turn -->
              <div style="margin: 8px 0;">
                <p>You can set background image too!</p>
              </div>
              <div id="message"></div>
              <div class="img-bg"></div>
              <div id="image-background" style="background-image: url(<?php echo get_template_directory_uri(); ?>/static/img/mono-log-unsplash.jpg);"></div>
            </div>

		        <?php
	        endwhile; // End of the loop.
	        ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();
