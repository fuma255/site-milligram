<?php namespace ProcessWire; // _main.php template file, called after a page’s template file
// trashDemoData('false'); // Put unnecessary pages into the trash ( change to true ) !!!
wireIncludeFile("inc/_head"); // ( Include header )?>

<!-- MAIN CONTENT -->
<main id='main' class='container-medium'>

    <div id='cont-main' class="grid c-main">

        <!-- CONTENT -->
        <div id='content' class='col-9_md-12 c-page'>

        <?php if(page()->template != 'home'):?>

            <!-- HEADING -->
            <h1 id='content-head'>

                <?=page()->get('headline|title')?>

            </h1>

        <?php endif; ?>

            <!-- CONTENT BODY -->
            <div id='content-body' class='c-body'>

                <?=page()->body?>

            </div>

        </div><!-- /#content -->

        <!-- SIDEBAR -->
        <aside id='sidebar' class='col sid'>

            <?php // Language Menu 
                echo langMenu($page,pages('/'))?>

            <!-- SEARCH FORM  -->
            <form id='search' class='s-form' action='<?=pages()->get('template=search')->url?>' method='get'>

                <input type='search' name='q' class='s-input' placeholder='<?=page()->ts['search'];?>&hellip;' required>

            </form>

            <?php // Show Sidebar 
            echo page()->sidebar?>

            <div id="page-children">

            <?php 
            // Include sidebar links
            wireIncludeFile('inc/_links');?>

            </div><!-- /#page-children -->

            <?php
            // Include contact form
            wireIncludeFile("inc/_c-form");?>

        </aside><!-- /#sidebar -->

    </div><!-- /#cont-main -->

</main>

<?php // ( Include footer ) https://processwire.com/blog/posts/processwire-2.5.2/
    wireIncludeFile("inc/_foot"); // Include Footer