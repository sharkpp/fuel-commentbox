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

  </body>
</html>
