<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?= isset($page_title) ? "$page_title" : 'bLoG' ?> </title>

	<link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <?php if (isset($image_name) && $image_name) : ?>
    <style>
        .background-image{
            background-image: url("<?= IMAGE_PATH_LOCALHOST.'/'. $post->id .'/'.$image_name ?>");
             background-size: cover;
        }
    </style>
    <?php endif;?>
	<script>
		var baseURL = '<?php echo BASE_URL ?>';
	</script>
</head>
<body class="<?= get_segment(1) ? plain(get_segment(1)) : 'home' ?>">

    <header>
        <?= flash() -> display() ?>
        <?php if ( logged_in() ) : $logged_in = get_user();
            $is_admin = is_admin($logged_in->uid);
            $is_editor = is_editor($logged_in->uid);?>
            <div class="navigation">
                <div class="btn-group btn-group-sm pull-left">
                    <?php  if(get_segment(1)) : ?>
                        <a href="<?= BASE_URL ?>" class="btn btn-default"> all posts hOmE </a>
                    <?php endif ?>
                    <a href="<?= BASE_URL ?>/user/<?= $logged_in->uid ?>" class="btn btn-default"> mY posts </a>
                    <a href="<?= BASE_URL ?>/post/new" class="btn btn-default"> adD new pOst </a>
                </div>

                <div class="dropdown hidden-xs open pull-right" >

                    <a href="" class="dropdown-toggle"><span class="caret"></span>
                        <?= plain( $logged_in->nickname )." ($logged_in->email)" ?>
                        </a>

                    <div class="dropdown-content" >
                        <?php if ($is_admin) : ?>
                            <a href="<?= get_admins_link($logged_in,'users')?>"
                               class="btn btn-default">edIt usErS</a>
                            <a href="<?= get_admins_link($logged_in,'tags')?>"
                               class="btn btn-default">edIt tagS</a>
                        <?php endif; ?>
                        <?php if ($is_editor) : ?>
                            <a href="<?= get_editors_link($logged_in, 'editor')?>"
                               class="btn btn-default">ediTor's paGe</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/profile/<?=$logged_in->uid?>" class="btn btn-default">youR proFilE</a>
                        <a href="<?= BASE_URL ?>/settings/<?=$logged_in->uid?>" class="btn btn-default">youR setTingS</a>
                        <hr>
                        <a href="<?= BASE_URL ?>/logout" class="btn btn-default logout">lOgoUt</a>
                    </div>

                </div>
            </div>
        <?php endif ?>
    </header>

    <main>
        <div class="container">


