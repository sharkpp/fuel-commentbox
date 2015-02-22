<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Starter Template for Bootstrap</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
  </head>
  <body>

    <div class="container">

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

<div class="panel panel-default">
  <div class="panel-body">
<?php echo $commentbox_form; ?>
<?php echo $commentbox_error; ?>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-body">
<?php echo $commentbox_comments; ?>
  </div>
</div>

    </div><!-- /.container -->

  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
  </body>
</html>
