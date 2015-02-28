<?php
/**
 * Part of the fuel-commentbox package.
 *
 * @package    fuel-commentbox
 * @version    1.0
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2015 sharkpp
 * @link       https://github.com/sharkpp/fuel-commentbox
 */

return array(

	// table name
	'table_name' => 'commentboxes',

	// permission of guest comment
	'guest' => true,

	// avatar config
	'avatar' => array(

		// avatar icon size
		//   gravatar:  1 - 2048
		//   robohash:  1 -  400
		//   adorable: 40 -  285
		'size' => 48,

		// avatar service
		//   none     : Do not show an avatar icon
		//   blank    : blank box
		//   gravatar : http://gravatar.com/
		//   robohash : http://robohash.org/
		//   adorable : http://avatars.adorable.io/
		'service' => 'gravatar',

		// gravatar options
		//   see: http://en.gravatar.com/site/implement/images/
		'gravatar' => array(
			'd' => 'identicon', // defaults: 404, mm, identicon, monsterid, wavatar, retro, blank
		//	'f' => 'y', // forcedefault
		//	'r' => 'g', // Rating: g, pg, r, x
		),

		// robohash options
		//   see: http://robohash.org/
		'robohash' => array(
		//	'bgset' => 'bg1', // Robots at your Location: bg1, bg2
		//	'ext' => 'png', // image extension: png, jpg
		//	'gravatar' => 'hashed', // use gravatar: yes, hashed
		),
	),

	// the active pagination template
	'active' => 'default',

	// default commentbox template
	'default' => array(

		// comment form template
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

		// comment tree template
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
