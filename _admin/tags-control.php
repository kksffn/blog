<?php

    $page_title = "tagS contRolL";
    $odd = 0;

    // include
    include_once "_partials/header.php"; //to be able to check if user is admin

    // just to be safe - tady nemáš co dělat
    validateUser($is_admin);

    $results = get_all_tags();

?>

<?php if ($_SERVER['REQUEST_METHOD'] !== 'POST') : ?>
<h1 class="page-header">VerY MuCh tAgs conTroll</h1>
            <header class="post-header">
                <h2>To edit tag name just click the tag and start typing...</h2>
            </header>
<!---------------------------------------------Form to add new tag--------------------------------------------->
            <span class=" new-tag coll-md-6">
                    <form action="add-tag" id="add-new-tag" method="post">
                    <span class="form-group pull-left">
                        Or you can create some new tags...

                        <input type="hidden" name="odd" id="odd" value = <?=$odd?> >
                        <input type="text" class="new-tag" name="new-tag" id="new-tag" placeholder="type new tag name">
                    </span>
                    <span class="form-group pull-right">
                        <button type="submit" class="btn btn-primary btn-add">Add new tag</button>
                    </span>
                    </form>
            </span>
<!-------------------------------------------List of existing tags---------------------------------------------->
            <span class="tag-list" id="tag-list" >
                    <?php if (count($results)) : foreach ($results as $tag) : ?>
                         <span class="pull-left col-md-3">
            <!-------------------------------edit tag--------------------------------------->
                            <form  class="pull-left" id="edit-tag-form-<?=$tag->id ?>"
                                   action="edit-tag/<?=$tag->id?>"  method="post">

                                <input type="text" name="edit-tag-<?=$tag->id ?>"
                                       id="edit-tag-<?=$tag->id ?>" onclick="this.select()"
                                   class="btn btn-warning btn-xs tag-btn pull-left input-tag" value="<?= $tag->tag ?>"
                                       onfocusin="add_tag_id_input(this)" onchange="add_tag_id_input(this)"
                                >
                                <input type="hidden" id="tagname-<?=$tag->id?>" name="tagname-<?=$tag->id?>"
                                       value="<?= $tag->tag ?>">
                                <span id="edit-container-<?=$tag->id ?>"></span>
                            </form>
            <!-------------------------------delete tag--------------------------------------->
                            <form class="pull-left " action="delete-tag/<?=$tag->id ?>"
                                  id="delete-tag-form-<?=$tag->id ?>" method="post">
                                <div class="tag-controls">(<?= $count = get_number_of_posts_for_tag($tag->id)?>
                                    <?= $count == 1 ? ' post' : ' posts'?>)
                                    <button type="submit" id="delete-tag-<?=$tag->id ?>"
                                        class="odd btn btn-xs tag-delete-btn ">
                                    <small> &times delete tag</small></button>
                                </div>
                            </form>
                         </span>

                            <?php $odd = ( ($odd + 1) % 2) ?>
                    <?php endforeach; else : ?>
                        <span class=""> nOthInG tO ShoW :(</span>
                    <?php endif; ?>
            </span>
<?php endif;?>

<?php include_once "_partials/footer.php" ?>
