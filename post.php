<?php
    //hnusný trick
    $id = get_segment( 2 );

    // add new post form
    if ( $id === 'new' ) {
        include_once 'add.php';
        die();
    }

    try {
        $post = get_post();

        //increase number of views if user didn't come from this site
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != get_post_link($post))
        {
            update_views($post->id);
        }

        $comments = get_comments_for_post($post->id);
    }
    catch ( PDOException $e ) {
        $post = false;
        $error  = date( 'j M Y, G:i' ) . PHP_EOL;
        $error .= '------------------' . PHP_EOL;
        $error .= $e->getMessage() . ' in [ '. __FILE__ .' : ' .__LINE__. ' ] ' . PHP_EOL . PHP_EOL;

        file_put_contents( APP_PATH . '/_inc/error.log', $error, FILE_APPEND );
    }

    if ( ! $post ) {
        flash()->error("doesn't exist:(");
        redirect('/');
    }

    // refill comment form, maybe - will get $comment
    if ( isset( $_SESSION['form_data'] ) )
    {
        extract( $_SESSION['form_data'] );

        unset( $_SESSION['form_data'] );
    }


    $i = 1; //to mark comments with numbers
    $page_title = $post->title;

    $image_name = get_image_name_for_post($post->id);

    include_once "_partials/header.php";
?>
<!------------------------------------ SHOW THE POST ------------------------------------------------------------->
        <h1 class="page-header">a masTerpiEcE mAybE</h1>
        <section class="box">
            <article class="post full-post ">
                <header class="post-header background-image">
                    <h1 class="box-heading">
                        <a href="<?= $post->link ?>"><?= $post->title ?></a>
                    </h1>
                    <h2 class="post-time">
                        <time datetime="<?= $post->date ?>">
                            <small><?= $post->time ?></small>
                        </time>
                    </h2>
                </header>
                <div class="links">
                    <!--------------------- Only admin, editor or the author can edit or delete the post -->
                    <?php if ( can_edit($post) || $is_admin || $is_editor) : ?>
                        <a href="<?= get_edit_link($post) ?>" class="btn btn-xs edit-link">
                            edit
                        </a>
                        <a href="<?= get_delete_link($post) ?>" class="btn btn-xs delete-link">
                            &times
                        </a>
                    <?php endif ?>
                    </div>
                    <!-------------------------------- Links to all author's posts ------------------------>
                <div class="post-content">
                    <?= $post->text ?>
                    <p class="written-by small">
                        <small>- written by <a href="<?= $post->user_link ?>"><?= $post->nickname ?></a>
                            <?= " ($post->email) "?></small>
                    </p>
                </div>
                <!------------------------- maybe some tags ------------------------------------------->
                <?php   if ($post->tags) : ?>
                    <p class="tags">
                        <?php   include '_partials/tags.php'?>
                    </p>
                <?php endif ?>
            </article>
<!------------------------------------------------- COMMENTS------------------------------------------------->
           <?php include "_partials/comments.php"?>
        </section>

<?php include_once "_partials/footer.php" ?>