            <hr>
            <section class="comments">
                <h3 class="commentheader">
                    <?php $number=count_comments($post->id);
                    if (!$number || $number == 0) {$number = 'No';}
                    ?>
                    <?=$number?> comment<?=($number == 1 ? '' : 's')?> for "<?= $post->title ?>"
                </h3>
                <ol class="commentlist" id="commentlist">
                    <?php if ($number <> 'No') : ?>
                        <?php foreach ($comments as $comment) :?>
                            <?php $timestamp = strtotime($comment->time) ?>
                            <li class="" id="comment-<?=$comment->id?>">
                                <div class="commentnumber"><?=$i++?></div>
                                <cite><b> <?=plain($comment->nickname)?> wrote:</b> </cite>
                                <br>
                                <div class="commentcontent">
                                    <?=$comment->text?>
                                </div>
                                <small class="commentmetadata">
                                    <b><time datetime=""><?=date( 'j M Y, G:i', $timestamp)?></time> </b>
                                </small>
                            </li>
                        <?php endforeach;?>
                    <?php endif;?>
                    <container class="new-comment-container" id="new-comment-container"></container>
                    <hr>
                    <!-------------------------------------------WRITE A COMMENT------------------------------------------------->
                    <h4 class="commentheader">You can add your thoughts or ideas, too...</h4>

                    <form class="add-comment" name="add-comment" id="add-comment"
                          action="<?= BASE_URL ?>/_admin/add-comment.php" method="post" >
                        <div class="form-group">
                                        <textarea name="comment_text" id="comment_text" class="form-control comment" rows="6"
                                                  placeholder=""
                                                  required><?= isset($comment_text) ? $comment_text : ''?></textarea>
                            <input type="hidden" name="post_id" value="<?= $post->id ?>" >
                            <input type="hidden" name="post_link" value="<?= $post->link ?>" >
                            <button type="submit" class="btn btn-primary btn-comment">Add comment</button>
                        </div>
                    </form>
                </ol>

            </section>
