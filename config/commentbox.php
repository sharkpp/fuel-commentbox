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

	// use full name for Auth package
	'use_fullname' => true,

	// also be deleted without a trace
	'delete_without_trace' => false,

	// delete descendants comments
	'delete_descendants' => false,

	// delete user information in comment
	'delete_comment_avatar' => true,

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

		// Gravatar options
		//   see: http://en.gravatar.com/site/implement/images/
		'gravatar' => array(
			'd' => 'identicon', // defaults: 404, mm, identicon, monsterid, wavatar, retro, blank
		//	'f' => 'y', // forcedefault
		//	'r' => 'g', // Rating: g, pg, r, x
		),

		// RoboHash options
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

		// always use reCAPTCHA, guest and logged in
		'always_use' => false,

		// Site key
		'site_key' => 'fill your site key here',

		// Secret key
		'secret_key' => 'fill your secret key here',
	),

	// user page link
	//   availabled tags
	//     {user_id}   : user id (Auth package requierd)
	//     {user_name} : user name
	'user_page' => 'users/{user_name}',

	// the active commentbox template
	'active' => 'default',

	// default commentbox template
	'default' => array(

		// commentbox template
		//   availabled tags
		//     {form}        : form area
		//     {comments}    : comment tree area
		//     {comment_num} : comment num
		'commentbox' => <<<EOD
<div class="panel panel-default">
  <div class="panel-body">
{form}
{comments}
  </div>
</div>
EOD
, // Limitation of heredoc

		// form wrap template
		//   availabled tags
		//     {form}   : form area
		//     {errors} : submit error area
		//     {recaptcha_script} : reCAPTCHA init script
		'form_wrap' => <<<EOD
{form}
{errors}
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
{comments}
EOD
, // Limitation of heredoc

		// comment tree template
		//   availabled tags
		//     {avatar}         : user avatar
		//     {avatar_webpage} : user avatar with web page link
		//     {avatar_userpage}: user avatar with user page link
		//     {avatar_email}   : user avatar with email link
		//     {name}           : username as plain text
		//     {name_webpage}   : username with web page link
		//     {name_userpage}  : username with web user page link
		//     {name_email}     : username with email link
		//     {email}          : display email as plain text
		//     {webpage}        : display email as plain text
		//     {time}           : post time
		//     {body}           : comment body as plain text
		//     {reply_button}   : reply button, see 'comment_reply_button' template
		//     {reply_form}     : reply form area, see 'form' template
		//     {delete_button}  : delete button, see 'comment_delete_button' template
		'comments' =>  <<<EOD
<hr style="margin-top:10px; margin-bottom:10px;">
<div class="media">
	<div class="media-left">
		<span class="media-object">{avatar}</span>
	</div>
	<div class="media-body">
		<h4 class="media-heading">{name} &nbsp;<small>{time}</small></h4>
		{body}</br>
		{reply_button}
		{delete_button}
<div class="panel panel-default hidden" style="margin-top: 10px">
	<div class="panel-body">
		{reply_form}
	</div>
</div>
{child}
	</div>
</div>
EOD
, // Limitation of heredoc

		// deleted message template
		//   availabled tags
		//     {message} : message
		'deleted_message' => <<<EOD
<span class="text-muted">{message}</span>
EOD
, // Limitation of heredoc

		// comment reply button template
		//   availabled tags
		//     {comment_key}   : comment key value
		//     {reply_caption} : reply button caption
		'comment_reply_button' => <<<EOD
<a href="#" class="btn btn-default btn-sm" role="button" style="margin-top: 10px"
   id="commentbox_reply_button_{comment_key}"
   onclick="$('#commentbox_recaptcha_{comment_key}').parent().parent().parent().toggleClass('hidden');return false;"
   ><span class="glyphicon glyphicon-comment"></span> {reply_caption}</a>
EOD
, // Limitation of heredoc

		// comment delete button template
		//   availabled tags
		//     {comment_key}    : comment key value
		//     {delete_caption} : delete button caption
		//     {delete_message} : delete confirm message
		//     {delete_form}    : delete form, element is form#commentbox_delete_form_{comment_key}
		'comment_delete_button' => <<<EOD
<a href="#" class="btn btn-default btn-sm" role="button" style="margin-top: 10px"
   id="commentbox_delete_button_{comment_key}"
   onclick="if(window.confirm('{delete_message}')){\$('[id^=\'commentbox_\']').addClass('disabled');\$('#commentbox_delete_form_{comment_key}').trigger('submit');}return false;"
   ><span class="glyphicon glyphicon-trash"></span> {delete_caption}</a>
{delete_form}
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

	// disqus like commentbox template
	'disqus' => array(

		// commentbox template
		'commentbox' => <<<EOD
<div class="panel panel-default">
  <div class="panel-body">
<h4 style="margin-top:0; margin-bottom:0;">{comment_num}</h4>
<hr style="margin-top:10px; margin-bottom:10px;">
{form}
{comments}
  </div>
</div>
EOD
, // Limitation of heredoc

		// form wrap template
		'form_wrap' => <<<EOD
{form}
{errors}
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

		// form errors wrap template
		'form_errors_wrap' => <<<EOD
<div class="alert alert-danger" role="alert">
  <ul class="list-unstyled">
{errors}
  </ul>
</div>
EOD
, // Limitation of heredoc

		// form error item template
		'form_error_item' => <<<EOD
<li>{error}</li>
EOD
, // Limitation of heredoc

		// comment tree wrap template
		'comments_wrap' => <<<EOD
{comments}
EOD
, // Limitation of heredoc

		// comment tree template
		'comments' =>  <<<EOD
<div class="media">
	<div class="media-left">
		<span class="media-object">{avatar_userpage}</span>
	</div>
	<div class="media-body">
		<h4 class="media-heading"><span class="text-primary">{name_userpage}</span> &middot; <small>{time}</small></h4>
		{body}</br>
		{reply_button}
<div class="panel panel-default hidden" style="margin-top: 10px">
	<div class="panel-body">
		{reply_form}
	</div>
</div>
{child}
	</div>
</div>
EOD
, // Limitation of heredoc

		// deleted message template
		'deleted_message' => <<<EOD
<span class="text-muted">{message}</span>
EOD
, // Limitation of heredoc

		// comment reply button template
		'comment_reply_button' => <<<EOD
<a href="#" id="commentbox_reply_button_{comment_key}"
   onclick="$('#commentbox_recaptcha_{comment_key}').parent().parent().parent().toggleClass('hidden');return false;"
   >{reply_caption}</a>
EOD
, // Limitation of heredoc

		// comment delete button template
		'comment_delete_button' => <<<EOD
<a href="#"
   id="commentbox_delete_button_{comment_key}"
   onclick="if(window.confirm('{delete_message}')){\$('[id^=\'commentbox_\']').addClass('disabled');\$('#commentbox_delete_form_{comment_key}').trigger('submit');}return false;"
   >{delete_caption}</a>
{delete_form}
EOD
, // Limitation of heredoc

		// reCAPTCHA script code
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

	// stackoverflow like commentbox template
	'stackoverflow' => array(

		// commentbox template
		'commentbox' => <<<EOD
<div class="panel panel-default">
  <div class="panel-body">
{comments}
<hr>
{form}
  </div>
</div>
EOD
, // Limitation of heredoc

		// form wrap template
		'form_wrap' => <<<EOD
{form}
{errors}
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

		// form errors wrap template
		'form_errors_wrap' => <<<EOD
<div class="alert alert-danger" role="alert">
  <ul class="list-unstyled">
{errors}
  </ul>
</div>
EOD
, // Limitation of heredoc

		// form error item template
		'form_error_item' => <<<EOD
<li>{error}</li>
EOD
, // Limitation of heredoc

		// comment tree wrap template
		'comments_wrap' => <<<EOD
{comments}
EOD
, // Limitation of heredoc

		// comment tree template
		'comments' =>  <<<EOD
<hr style="margin-top: 10px; margin-bottom: 10px;">
<div>
	<div>{body}</div>
	<div class="row">
		<div class="col-xs-9">
		</div>
		<div class="col-xs-3">
			<div class="pull-right">
				{time}
				<div class="media">
					<div class="media-left">
						<span class="media-object">{avatar_userpage}</span>
					</div>
					<div class="media-body">
						<span class="media-heading">{name_userpage}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div>
		{reply_button}
		<div class="panel panel-default hidden" style="margin-top: 10px">
			<div class="panel-body">{reply_form}</div>
		</div>
	</div>
	<div style="margin-left: 48px">
{child}
	</div>
</div>
EOD
, // Limitation of heredoc

		// comment tree template (2nd level)
		'comments_2nd' =>  <<<EOD
<hr style="margin-top: 5px; margin-bottom: 5px;">
<div>
	<div>
{body} - {name_userpage} <small class="text-muted">{time}</small> {reply_button}
		<div class="panel panel-default hidden" style="margin-top: 10px">
			<div class="panel-body">{reply_form}</div>
		</div>
	</div>
	<div style="margin-left: 48px">
{child}
	</div>
</div>
EOD
, // Limitation of heredoc

		// deleted message template
		'deleted_message' => <<<EOD
<span class="text-muted">{message}</span>
EOD
, // Limitation of heredoc

		// comment reply button template
		'comment_reply_button' => <<<EOD
<a href="#" id="commentbox_reply_button_{comment_key}"
   onclick="$('#commentbox_recaptcha_{comment_key}').parent().parent().parent().toggleClass('hidden');return false;"
   >{reply_caption}</a>
EOD
, // Limitation of heredoc

		// comment delete button template
		'comment_delete_button' => <<<EOD
<a href="#"
   id="commentbox_delete_button_{comment_key}"
   onclick="if(window.confirm('{delete_message}')){\$('[id^=\'commentbox_\']').addClass('disabled');\$('#commentbox_delete_form_{comment_key}').trigger('submit');}return false;"
   >{delete_caption}</a>
{delete_form}
EOD
, // Limitation of heredoc

		// reCAPTCHA script code
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
