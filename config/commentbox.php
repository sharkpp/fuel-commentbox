<?php

return array(

	// table name
	'table_name' => 'commentboxes',

	// the active pagination template
	'active' => 'default',

	// default commentbox template
	'default' => array(
		'form' => <<<EOD
{open}
<div class="form-group">{body_field}</div>
<div class="form-inline">
	<div class="form-group">{name_field}</div>
	<div class="form-group">{email_field}</div>
	<div class="form-group">{website_field}</div>
	<div class="form-group">{submit}</div>
</div>
{close}
EOD
, // Limitation of heredoc
		'comments' =>  <<<EOD
<hr>
<div class="media">
	<div class="media-left">
		<span class="media-object">{icon}</span>
	</div>
	<div class="media-body">
		<h4 class="media-heading">{name} ({email}) <small>{time}</small></h4>
		{body}</br>
		{reply_toggle}
<div class="panel panel-default hidden">
	<div class="panel-body">
		{reply_form}
	</div>
</div>
{child}
	</div>
</div>
EOD
, // Limitation of heredoc
	),
);
