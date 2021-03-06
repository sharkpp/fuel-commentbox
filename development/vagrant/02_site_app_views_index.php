<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>fuel-commentbox test page</title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
  <style type="text/css">
    .nav > li > a {
      font-size: 1em;
      line-height: 1em;
      padding-top: 5px;
      padding-bottom: 5px;
    }
    li > .nav > li > a {
      font-size: 0.9em;
      line-height: 0.9em;
      border-left: none;
    }
    .glyphicon-space {
      color: transparent;
    }
  </style>
</head>
<body>

  <div class="container">

    <div class="page-header">
      <h1>fuel-commentbox test page</h1>
    </div>
      
<div class="row">

<div class="col-md-3 col-md-push-9">

<nav class="hidden-print hidden-xs hidden-sm">
  <ul class="nav">
<?php
function get_url($modify_key, $modify_value)
{
	$url = \Uri::string();
	$get = \Input::get();
	if ('page' == $modify_key)
	{
		$segments = \Uri::segments();
		$segments[0] = $modify_value;
		$url = implode('/', $segments);
	}
	else
	{
		if ($modify_key)
			$get[$modify_key] = $modify_value;
	}
	return \Uri::create($url, array(), $get);
}
function is_active($key, $value, $default = '')
{
	$active = false;
	if ('page' == $key)
	{
		$active = $value == \Uri::segment(1, '');
	}
	else
	{
		$active = $value == \Input::get($key, $default);
	}
	return $active ? 'glyphicon glyphicon-ok' : 'glyphicon glyphicon-option-horizontal glyphicon-space';
}
?>

  <li class="">
    <a href="#" class="bg-primary">Pages</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('page', ''); ?>"><i class="<?php echo is_active('page', ''); ?>"></i> Main</a></li>
      <li><a href="<?php echo get_url('page', 'sub1'); ?>"><i class="<?php echo is_active('page', 'sub1'); ?>"></i> Sub 1</a></li>
      <li><a href="<?php echo get_url('page', 'sub2'); ?>"><i class="<?php echo is_active('page', 'sub2'); ?>"></i> Sub 2</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">Language</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('lang', 'en'); ?>"><i class="<?php echo is_active('lang', 'en', 'en'); ?>"></i> en</a></li>
      <li><a href="<?php echo get_url('lang', 'ja'); ?>"><i class="<?php echo is_active('lang', 'ja', 'en'); ?>"></i> ja</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">Theme</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('theme', 'default'); ?>"><i class="<?php echo is_active('theme', 'default', 'default'); ?>"></i> bootstrap3</a></li>
      <li><a href="<?php echo get_url('theme', 'disqus'); ?>"><i class="<?php echo is_active('theme', 'disqus', 'default'); ?>"></i> Disqus like</a></li>
      <li><a href="<?php echo get_url('theme', 'stackoverflow'); ?>"><i class="<?php echo is_active('theme', 'stackoverflow', 'default'); ?>"></i> StackOverflow like</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">Avatar</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('avatar', 'none'); ?>"><i class="<?php echo is_active('avatar', 'none', 'gravatar'); ?>"></i> none</a></li>
      <li><a href="<?php echo get_url('avatar', 'blank'); ?>"><i class="<?php echo is_active('avatar', 'blank', 'gravatar'); ?>"></i> blank</a></li>
      <li><a href="<?php echo get_url('avatar', 'gravatar'); ?>"><i class="<?php echo is_active('avatar', 'gravatar', 'gravatar'); ?>"></i> gravatar</a></li>
      <li><a href="<?php echo get_url('avatar', 'robohash'); ?>"><i class="<?php echo is_active('avatar', 'robohash', 'gravatar'); ?>"></i> robohash</a></li>
      <li><a href="<?php echo get_url('avatar', 'adorable'); ?>"><i class="<?php echo is_active('avatar', 'adorable', 'gravatar'); ?>"></i> adorable</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">Guest</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('guest', 'enable'); ?>"><i class="<?php echo is_active('guest', 'enable', 'enable'); ?>"></i> enable</a></li>
      <li><a href="<?php echo get_url('guest', 'disable'); ?>"><i class="<?php echo is_active('guest', 'disable', 'enable'); ?>"></i> disable</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">reCAPTCHA</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('recaptcha', 'enable'); ?>"><i class="<?php echo is_active('recaptcha', 'enable', 'enable'); ?>"></i> enable</a></li>
      <li><a href="<?php echo get_url('recaptcha', 'disable'); ?>"><i class="<?php echo is_active('recaptcha', 'disable', 'enable'); ?>"></i> disable</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">reCAPTCHA always</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('recaptcha_always', 'always'); ?>"><i class="<?php echo is_active('recaptcha_always', 'always', 'none'); ?>"></i> enable</a></li>
      <li><a href="<?php echo get_url('recaptcha_always', 'none'); ?>"><i class="<?php echo is_active('recaptcha_always', 'none', 'none'); ?>"></i> disable</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">full name</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('fullname', 'yes'); ?>"><i class="<?php echo is_active('fullname', 'yes', 'yes'); ?>"></i> yes</a></li>
      <li><a href="<?php echo get_url('fullname', 'no'); ?>"><i class="<?php echo is_active('fullname', 'no', 'yes'); ?>"></i> no</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">use delete</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('use_delete', 'true'); ?>"><i class="<?php echo is_active('use_delete', 'true', 'true'); ?>"></i> true</a></li>
      <li><a href="<?php echo get_url('use_delete', 'false'); ?>"><i class="<?php echo is_active('use_delete', 'false', 'true'); ?>"></i> false</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">delete without a trace</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('delete_without_trace', 'true'); ?>"><i class="<?php echo is_active('delete_without_trace', 'true', 'false'); ?>"></i> true</a></li>
      <li><a href="<?php echo get_url('delete_without_trace', 'false'); ?>"><i class="<?php echo is_active('delete_without_trace', 'false', 'false'); ?>"></i> false</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">delete children</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('delete_descendants', 'true'); ?>"><i class="<?php echo is_active('delete_descendants', 'true', 'false'); ?>"></i> true</a></li>
      <li><a href="<?php echo get_url('delete_descendants', 'false'); ?>"><i class="<?php echo is_active('delete_descendants', 'false', 'false'); ?>"></i> false</a></li>
    </ul>
  </li>

  <li class="">
    <a href="#" class="bg-primary">delete comment avatar</a>
    <ul class="nav">
      <li><a href="<?php echo get_url('delete_comment_avatar', 'true'); ?>"><i class="<?php echo is_active('delete_comment_avatar', 'true', 'true'); ?>"></i> true</a></li>
      <li><a href="<?php echo get_url('delete_comment_avatar', 'false'); ?>"><i class="<?php echo is_active('delete_comment_avatar', 'false', 'true'); ?>"></i> false</a></li>
    </ul>
  </li>

</ul>
          </nav>

</div><!-- /col-md-3 col-md-push-9 -->
<div class="col-md-9 col-md-pull-3">

<?php $error = \Session::get_flash('error');
      if ($error): ?>
<div class="alert alert-danger" role="alert">
  <?php echo e($error); ?>
</div>
<?php endif; ?>

<div class="panel panel-default">
  <div class="panel-body">
    <form class="form-inline pull-right" method="post">
      <?php echo \Form::csrf(); ?>
<?php if ( ! \Auth::check()): ?>
      <div class="form-group">
        <label class="sr-only" for="inputUsername">Username</label>
        <input type="username" name="username" class="form-control" id="inputUsername" placeholder="Enter username">
      </div>
      <div class="form-group">
        <label class="sr-only" for="inputPassword">Password</label>
        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
      </div>
      <button type="submit" class="btn btn-default" name="signin">Sign in</button>
<?php else: ?>
      Logged in : <?php echo Auth::get('username'); ?>
      <button type="submit" class="btn btn-default" name="signout" value="Sign out">Sign out</button>
<?php endif; ?>
    </form>
  </div>
</div>

<?php echo $commentbox; ?>

</div><!-- /col-md-9 col-md-pull-3 -->

</div><!-- /row -->

    </div><!-- /.container -->

  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  </body>
</html>
