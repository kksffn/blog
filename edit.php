<?php

    try {
        $post = get_post(get_segment(2), false);
        //nechceme data upravovat, ale v tom stavu, v jakém je user vložil.

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

    // refill form, maybe - $title, $text, $tags, $tags_input - especially for $tags_input
    if ( isset( $_SESSION['form_data'] ) )
    {
        extract( $_SESSION['form_data'] );

        unset( $_SESSION['form_data'] );
    }
    $page_title = "eDit / $post->title";
    include_once "_partials/header.php";

?>

    <div class="page-header">
        <h1>VerY MuCh edIt mAybE</h1>
    </div>
    <section class="box">
        <form class="post" action="<?= BASE_URL ?>/_admin/edit-item.php" method="post" enctype="multipart/form-data">
            <header class="post-header">
                <h1 class="box-heading">
                    Edit &ldquo;<?= plain($post->title) ?>&rdquo;
                </h1>
            </header>

            <div class="form-group">
                <input type="text" name="title" class="form-control" value="<?= $post->title ?>" placeholder="title your masterpiece">
            </div>

            <div class="form-group">
                <textarea name="text" class="form-control" rows="16" placeholder="write the masterpiece"><?= $post->text ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">You can upload a background image to your post:</label>
                <input type="file" class="form-control image" name="image" id="image">
            </div>

            <?php include "_partials/add-tags-to-post.php" ?>

            <div class="form-group">
                <input name="post_id" value="<?= $post->id ?>" type="hidden">
                <button type="submit" class="btn btn-primary">Edit post</button>
                <span class="or">
					or <a href="<?= get_post_link( $post ) ?>">cancel</a>
				</span>
            </div>
        </form>
    </section>


<?php include_once "_partials/footer.php" ?>