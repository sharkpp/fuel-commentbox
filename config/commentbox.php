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
	//   https://developers.google.com/recaptcha/
	'recaptcha' => array(

		// enable or disable reCAPTCHA
		'enable' => false,

		// always use reCAPTCHA, guest and loggdin
		'always_use' => false,

		// Site key
		'site_key' => 'fill your site key here',

		// Secret key
		'secret_key' => 'fill your secret key here',
	),

	// the active commentbox template
	'active' => 'default',

	// default commentbox template
	'default' => array(

		// form wrap template
		//   availabled tags
		//     {form}   : form area
		//     {errors} : submit error area
		//     {recaptcha_script} : reCAPTCHA init script
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
		//   availabled tags
		//     {open}          : form open
		//     {close}         : form close
		//     {body_field}    : comment body input field
		//     {name_field}    : username input field (optional)
		//     {email_field}   : email input field (optional)
		//     {website_field} : website input field (optional)
		//     {submit}        : submit button
		//     {comment_key}   : comment key value
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

		// form errors wrap template
		//   availabled tags
		//     {errors} : submit errors
		'form_errors_wrap' => <<<EOD
<div class="alert alert-danger" role="alert">
  <ul class="list-unstyled">
{errors}
  </ul>
</div>
EOD
, // Limitation of heredoc

		// form error item template
		//   availabled tags
		//     {error} : submit error
		'form_error_item' => <<<EOD
<li>{error}</li>
EOD
, // Limitation of heredoc

		// comment tree wrap template
		//   availabled tags
		//     {comments} : comment tree area
		'comments_wrap' => <<<EOD
<div class="panel panel-default">
  <div class="panel-body">
{comments}
  </div>
</div>
EOD
, // Limitation of heredoc

		// comment tree template
		//   availabled tags
		//     {avatar} : user avatar area
		//     {name}   : username input area
		//     {email}  : email input area
		//     {time}   : post time
		//     {body}   : comment body input area
		//     {reply_button} : reply button, see 'reply_button' template
		//     {reply_form}   : reply form area, see 'form' template
		'comments' =>  <<<EOD
<hr>
<div class="media">
	<div class="media-left">
		<span class="media-object">{avatar}</span>
	</div>
	<div class="media-body">
		<h4 class="media-heading">{name} <small>{time}</small></h4>
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

		// comment reply button template
		//   availabled tags
		//     {comment_key} : comment key value
		'comment_reply_button' => <<<EOD
<a href="#" id="commentbox_reply_button_{comment_key}" onclick="$(this).next().toggleClass('hidden');return false;">Reply</a>
EOD
, // Limitation of heredoc

		// reCAPTCHA script code
		//   availabled tags
		//     {recaptcha_site_key} : reCAPTCHA site key value
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
