<?php namespace ProcessWire;
// Render Hero Image
echo wireRenderFile("render/hero", // Render Hero Content
      [ // Enable Hero Content
          'enable' =>  true,
          'height' => 70, // Height Hero Content
        // Intro
          'intro' =>  page()->title,
          'content' =>  page()->headline,
        // Bottom text
          'b_first_txt' => __('A friendly and powerful open source CMS'),
          'b_url' => 'https://processwire.com/',
          'b_url_txt' => __('Processwire'),
          'b_last_txt' => __('  With an exceptionally strong API -- '),
        // Some Icons
          'icon' => 'github', // https://feathericons.com/
          'icon_url' => 'https://github.com/processwire'
      ]);?>

<div id='content-body'>

<section id='home-grid' class="container-fluid">

  <?php echo wireRenderFile("render/grid",
        [ // Enable Content Grid
          'enable_grid' =>  true, // Enable Grid Content
        // Render Grid from this page
          'item' => pages('about'),
        ]);?>

</section><!-- /#home-grid -->

<?php echo page('body');?>

</div><!-- /#content-body -->

<div id="page-children" pw-append>

<?=pageChildren($pages('/news/'), __('Last News'));?>

</div>