<?php function spontit_plugins_notice(){ global $pagenow; if ( $pagenow == 'plugins.php' && (get_option( 'spontit_share_url' )=='' || 
!preg_match("/^(https?:\/\/)?(www.)?spontit\.com\/\S/i", get_option( 'spontit_share_url' )))) :?>
<div class="updated notice">
<div style="height: 30px; align-items: center">
<p><a id="spontit_activate" href="<?php echo admin_url( 'options-general.php?page=spontit_settings' ); ?>" style="text-decoration: none">
Add your Spontit invitation link
</a></p>
</div>
</div>
<?php endif;}?>
<?php function spontit_options_page(){ ?>
  <div>
  <h1>Spontit Settings</h1>
  <h5> 
  	1. Log in on the Spontit website.<br/>
	2. Select the “Push” tab.<br/>
	3. Select the channel you want your visitors to follow. When they follow this channel, you can send them notifications via this channel.<br/>
	4. After selecting the channel, select “Invite.”<br/>
	5. Copy the invitation link.<br/>
	6. Paste the invitation link in the box below.<br/>
	7. Click “Save Changes.”<br/>
  </h5>
  <form method="post" action="options.php">
  <?php settings_fields( 'spontit_options_group' ); ?>
  <table>
  <tr valign="top">
  <th scope="row"><label for="spontit_share_url">Enter Your Invitation Link:</label></th>
  <td><input required type="text" id="spontit_share_url" name="spontit_share_url" value="<?php echo get_option('spontit_share_url'); ?>" /></td>
  </tr>
  <?php if ( get_option( 'spontit_share_url' )=='' || 
!preg_match("/^(https?:\/\/)?(www.)?spontit\.com\/\S/i", get_option( 'spontit_share_url' ))) :?>
  <p style="color: red;">Sorry, this does NOT seem to be a valid link for a Spontit channel, please check again.<br/>
  </p>
  <p style="color: red;">If you believe this is a mistake, please contact Spontit. Thank you!</p>
<?php endif;?>
  <tr valign="top">
  <th scope="row"><label for="spontit_dialog_text">Customize the text in pop-up dialog:</label></th>
  <td><input required type="text" id="spontit_dialog_text" name="spontit_dialog_text" value="<?php echo get_option('spontit_dialog_text'); ?>" /></td>
  </tr>
  </table>

<?php if (strpos(get_option( 'spontit_dialog_text' ), '\\') !== FALSE) :?>
  <p style="color: red;">Failed to save customized text. Please do not include special characters in the text. <br/>
  </p>
<?php endif;?>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php }?>
<?php
class Spontit {
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	private static function init_hooks() {
		self::$initiated = true;

		add_action('admin_menu', array('Spontit', 'spontit_register_options_page'));
		add_action( 'admin_init', array('Spontit','spontit_register_settings' ));
		add_action( 'admin_init', 'spontit_plugins_notice');

		add_action('updated_option', function( $option_name, $option_value ) {
			if ($option_name==='spontit_share_url') {
				$spontit_share_url = get_option( 'spontit_share_url' );
				$pattern = "/^(https?:\/\/)?(www.)?spontit\.com\/\S/i";
				if (preg_match($pattern, $spontit_share_url)) {
					return true;
				} else {
					add_settings_error('spontit_invalid_link',esc_attr('spontit_invalid_link'),__($spontit_share_url.' is not a valid link for a Spontit channel.'),'error');
					add_action('admin_notices', 'spontit_print_errors');
					return false;
				}
			} else if ($option_name==='spontit_dialog_text') {
				if (strpos(get_option( 'spontit_dialog_text' ), '\\') !== FALSE){
					add_settings_error('spontit_dialog_text',esc_attr('spontit_dialog_text'),__('Failed to save customized text. Please do not include special characters in the text.'),'error');
					return false;
				} else {
					return true;
				}
			}
		}, 10, 2);
		add_action( 'wp_enqueue_scripts', array( 'Spontit', 'enqueue_scripts' ) );
		
		add_action( 'wp_head',array('Spontit', 'spontit_alert') );
	}

	public static function spontit_alert() {
		// echo 'spontit alert';
		// if (!is_admin() && isset($_COOKIE['spontit_dialog_shown'])) {
		// 	echo 'cookie set';
		// }
		if (!is_admin() && !isset($_COOKIE['spontit_dialog_shown'])) {
			$link = get_option('spontit_share_url');
			$text = get_option('spontit_dialog_text');
			
			if (strpos($text, '\\') !== FALSE) {
				$text = "We'd like to show you notifications for the latest news and updates.";
			}

			$text = str_replace("'","\'",$text);
			if ( $link!='' && preg_match("/^(https?:\/\/)?(www.)?spontit\.com\/\S/i", get_option( 'spontit_share_url' ))) {
				echo "<script type='text/javascript'>
					const spontit_invite = function () {
					    var div = $('<div>');

					    div.text('$text');
					    
					    jQuery(document).ready(function($) {
						  div.dialog({
						  	classes: {
							    'ui-dialog-titlebar': 'hide',
							    'ui-dialog-buttonpane': 'noLine'
							},
							dialogClass: 'noTitle',
					        autoOpen: true,
					        modal: false,
					        height: 'auto',
					        width: 500,
					        draggable: false,
					        resizable: false,
					        position: { my: 'center top', at: 'center top', of: window },
					        buttons: {
					            'Go to Spontit': function () {
					            	window.open('$link');
					                $(this).dialog('close');
					                div.remove();
					            },
					            Cancel: function() {
					                $( this ).dialog('close');
					                div.remove();
					            }
					        }
					      });
					      $('.noTitle .ui-dialog-titlebar').hide();
					      $('.hide').hide()
					      $('.noTitle .ui-dialog-buttonpane').css('background', 'transparent');
					      $('.noTitle .ui-dialog-buttonpane').css('borderTop', 'none');
					      $('.noLine').css('background', 'transparent');
					      $('.noLine').css('borderTop', 'none');
						});
					    
					}; 

					window.alert = spontit_invite()
					document.cookie = 'spontit_dialog_shown = true'
					
					</script>";
			}
		}
		
	}

	public static function spontit_register_settings() {
	   add_option( 'spontit_share_url', '');
	   register_setting( 
	   	'spontit_options_group', 
	    'spontit_share_url', 
	    $defaults = array(
	        'type'              => 'string',
	        'group'             => 'spontit_options_group',
	        'description'       => '',
	        'sanitize_callback' => null,
	        'show_in_rest'      => false,
	    ) );
	   add_option( 'spontit_dialog_text', "We'd like to show you notifications for the latest news and updates.");
	   register_setting( 
	   	'spontit_options_group', 
	    'spontit_dialog_text', 
	    $defaults = array(
	        'type'              => 'string',
	        'group'             => 'spontit_options_group',
	        'description'       => '',
	        'sanitize_callback' => null,
	        'show_in_rest'      => false,
	    ) );
	}


	public static function spontit_register_options_page() {
	  add_options_page('Spontit Settings', 'Spontit', 'manage_options', 'spontit_settings', 'spontit_options_page');
	}

	static function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_add_inline_script( 'jquery-core', 'window.$ = jQuery;' );
		// wp_register_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css', true);
  //       wp_enqueue_style( 'jquery-style' );
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script('jquery-ui-dialog');

	}

	public static function spontit_print_errors(){
	    settings_errors( 'spontit_invalid_link' );
	}
}