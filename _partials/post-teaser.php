<?php


?>

    <?php if ( count($results) ) : foreach ( $results as $post ) : ?>

    <!--------------------------On delete-users-posts page add hidden input with post_id-------------------------->
        <?php if (isset($delete_form)) : ?>
            <input name="post_id[]" value="<?=$post->id?>" type="hidden">
        <?php endif;?>
    <!------------------------------------------------------------------------------------------------------------>
        <article id="post-<?= $post->id ?>" class="post">
                <header class="post-header">
					<h2>
						<a href="<?= $post->link ?>"><?= $post->title ?></a>
                        <time datetime="<?= $post->date ?>">
                            <small> /&nbsp;<?= $post->time ?></small>
                        </time>
                    </h2>
                    <?php include '_partials/tags.php' ?>
                </header>
                <div class="post-content">
                    <p>
                        <?= $post->teaser ?>
                    </p>
                </div>
                <div class="footer post-footer">
                    <?php include "_partials/postmetadata.php"?>
                </div>
        </article>

    <?php endforeach; else : ?>

            <p>we got nothing :(</p>

    <?php endif ?>
