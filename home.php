<?php


    try {
        $results = get_posts();
    }
    catch ( PDOException $e ) {
        // also handle error maybe
        $results = [];
        $error  = date( 'j M Y, G:i' ) . PHP_EOL;
        $error .= '------------------' . PHP_EOL;
        $error .= $e->getMessage() . ' in [ '. __FILE__ .' : ' .__LINE__. ' ] ' . PHP_EOL . PHP_EOL;

        file_put_contents( APP_PATH . '/_inc/error.log', $error, FILE_APPEND );

    }
    include_once "_partials/header.php"

?>

    <h1 class="page-header">VerY MuCh hoMepAgE mAybE</h1>
    <section class="box post-list">
        <?php if (count($results)) : foreach ($results as $post) : ?>

            <article id="post-<?= $post->id ?>" class="post">
                <header class="post-header">
                    <h2>
                        <a href="<?= $post->link ?>"><?= $post->title ?></a>
                    </h2>
                    <h2>
                        <time datetime="<?= $post->date ?>">
                            <small><?= $post->time ?></small>
                        </time>
                    </h2>

                    <?php   include '_partials/tags.php'?>
                </header>

                <div class="post-content">
                    <p><?= $post->teaser ?></p>
                </div>
                <p class="written-by small">
                    <small>- written by <a href="<?= $post->user_link ?>"><?= $post->nickname ?></a>
                        <?= " ($post->email) "?>
                    </small>
                </p>
                <div>
                    <footer class="footer post-footer">
                        <?php include "_partials/postmetadata.php"?>
                    </footer>
                </div>

            </article>
        <?php endforeach; else : ?>
            <p>nOthInG tO ShoW :(</p>
        <?php endif; ?>
    </section>


<?php include_once "_partials/footer.php" ?>