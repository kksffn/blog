<span class="postmetadata">
                            <small>
                                <?php $comments=count_comments($post->id);
                                if (!$comments || $comments == 0) {$comments = 'No';}
                                ?>
                                <b><a href="<?=$post->link?>#new-comment-container">
                                <?=$comments?> comment<?=($comments == 1 ? '' : 's')?>
                                </a></b>,

                                <?php $views=count_views($post->id);
                                if (!$views || $views == 0) {$views = 'No';}
                                ?>
                                &nbsp; <?=$views?> view<?=($views == 1 ? '' : 's')?>
                            </small>
                        </span>
<a class="read-more" href="<?= $post->link ?>">read more</a>
