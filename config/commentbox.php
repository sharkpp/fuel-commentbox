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

	// reCAPTCHA config
	'recaptcha' => array(
		'enable' => false,
		'site_key' => 'fill your site key here',
		'secret_key' => 'fill your secret key here',
	),

	// the active pagination template
	'active' => 'default',

	// default commentbox template
	'default' => array(

		'form_wrap' => <<<EOD
<div class="panel panel-default">
  <div class="panel-body">
{form}
{errors}
  </div>
</div>
{recaptcha_script}
EOD
, // Limitation of heredoc

		// comment form template
		'form' => <<<EOD
{open}
<div class="form-group">{body_field}</div>
<div id="commentbox_recaptcha_{comment_key}"></div>
<div class="form-inline">
	<div class="form-group">{name_field}</div>
	<div class="form-group">{email_field}</div>
	<div class="form-group">{website_field}</div>
	<div class="form-group pull-right">{submit}</div>
</div>
{close}
EOD
, // Limitation of heredoc

		'form_errors_wrap' => <<<EOD
<div class="alert alert-danger" role="alert">
  <ul>
{errors}
  </ul>
</div>
EOD
, // Limitation of heredoc

		'form_error_item' => <<<EOD
<li>{error}</li>
EOD
, // Limitation of heredoc

		'comments_wrap' => <<<EOD
<div class="panel panel-default">
  <div class="panel-body">
{comments}
  </div>
</div>
EOD
, // Limitation of heredoc

		// comment tree template
		'comments' =>  <<<EOD
<hr>
<div class="media">
	<div class="media-left">
		<span class="media-object">{avatar}</span>
	</div>
	<div class="media-body">
		<h4 class="media-heading">{name} ({email}) <small>{time}</small></h4>
		{body}</br>
		{reply_button}
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

		'comment_reply_button' => <<<EOD
<a href="#" id="commentbox_reply_button_{comment_key}" onclick="$(this).next().toggleClass('hidden');return false;">Reply</a>
EOD
, // Limitation of heredoc

		// reCAPTCHA onload event script
		'recaptcha_script' => <<<EOD
<script type="text/javascript">
	var cbRecaptchaRender = function(id) {
		grecaptcha.render(id, {
			'sitekey': '{recaptcha_site_key}',
			'callback': function(){
				$('[id="'+id.replace('commentbox_recaptcha_', 'commentbox_submit_')+'"]')
					.removeAttr('disabled');
			}
		});
	};
	var cbRecaptchaOnload = function() {
		$('[id^="commentbox_recaptcha_"]:first')
			.each(function(){
				cbRecaptchaRender($(this).attr('id'));
			});
		$('[id^="commentbox_reply_button_"]')
			.on('click', function(){
				var id = $(this).attr('id').replace('commentbox_reply_button_', 'commentbox_recaptcha_');
				$('[id="'+id+'"]:empty')
					.each(function(){
						cbRecaptchaRender(id);
					});
			});
	};
</script>
<script src="//www.google.com/recaptcha/api.js?onload=cbRecaptchaOnload&render=explicit" async defer></script>
EOD
, // Limitation of heredoc
	),
);
