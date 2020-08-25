<?php
    if (isset($post)) {
        $post_id = $post->id;   //on edit page we may have tags checked for post
    } else {
        $post_id = 0;       //on add new post page we just need all tags unchecked
    }
?>
            <div class="form-group">
                <label for="tags-input"> You can add some tags to your post:</label>
                <input type="text" class="col-md-12" name="tags-input" value="<?= isset($tags_input) ? $tags_input :''?>"
                       placeholder="type some tags here (tags of length more than 30 will be deleted), use comma as delimiter">
                <br>

                or you can choose existing ones...
                <?php foreach ( get_all_tags( $post_id ) as $tag ) : ?>
                    <label class="checkbox">
                        <input type="checkbox" name="tags[]" value="<?= $tag->id ?>"
                            <?php
                                if($post_id <>0)
                                {
                                   if ( isset( $tag->checked ) && $tag->checked )
                                   {
                                       echo ' checked';
                                   } else
                                   {
                                       echo '';
                                   }
                                }

                                elseif ($post_id == 0)
                                {
                                    if ( isset($tag->checked) || in_array( $tag->id, isset($tags) ? $tags: [] ) )
                                    {
                                        echo ' checked';
                                    } else
                                    {
                                        echo '';
                                    }
                                }
                            ?>
                           ><?= plain( $tag->tag ) ?>
                    </label>

                <?php endforeach ?>
            </div>

