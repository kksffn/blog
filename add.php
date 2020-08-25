<?php

	$page_title = 'adD nEw';
	include_once "_partials/header.php";

	// refill form, maybe - $title, $text, $tags, $tags_input
	if ( isset( $_SESSION['form_data'] ) )
	{
		extract( $_SESSION['form_data'] );

		unset( $_SESSION['form_data'] );
	}

?>
    <h1 class="page-header">VerY MuCh nEw pOst mAybE</h1>
	<section class="box">
		<form class="post" action="<?= BASE_URL ?>/_admin/add-item.php"
              method="post" enctype="multipart/form-data">

			<header class="post-header">
				<h1 class="box-heading">aDd neW pOsT</h1>
			</header>

			<div class="form-group">
				<input type="text" name="title" class="form-control" value="<?= isset($title) ? $title : '' ?>" placeholder="title your masterpiece">
			</div>

			<div class="form-group">
				<textarea name="text" class="form-control" rows="16" placeholder="write the masterpiece"><?= isset($text) ? $text: '' ?></textarea>
			</div>

            <div class="form-group">
                <label for="image">You can upload a background image to your post:</label>
                <input type="file" class="form-control image" name="image" id="image">
            </div>

            <?php include "_partials/add-tags-to-post.php" ?>

			<div class="form-group">
				<button type="submit" class="btn btn-primary">Add post</button>
				<span class="or">
					or <a href="<?= BASE_URL ?>">cancel</a>
				</span>
			</div>
		</form>
	</section>

<?php include_once "_partials/footer.php" ?>