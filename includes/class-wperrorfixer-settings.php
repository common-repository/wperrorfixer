<?php
/*function w3_disable_maintenance_mode() {
    w3_wp_delete_file(w3_get_site_root() . '/.maintenance');
}
*/
if ( ! defined( 'ABSPATH' ) ) exit;
include_once(ABSPATH . 'wp-includes/pluggable.php');
 #if ( current_user_can( $capability , $object_id )) echo 'no puede';


function wperrorfixer_comprobar_admin() {
		if ( function_exists('current_user_can') )#&& 
		if(!current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

	 #check_admin_referer( 'plugins.php' );	

		
	/*	if(!current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));
			else
			echo 'falla';*/
		#	die(__('Cheatin&#8217; uh?', 'akismet'));
		
		}
	function wperrorfixer_conectar_mysql($sql, $basedatos, $password_base,$usuario,$host)
	{

		#echo $host.' '.$usuario.' '.$password_base.' '.$basedatos;
	 #$link = mysqli_connect($host, $usuario, $password_base, $basedatos);
	 $link = mysqli_connect($host, 'root', $password_base, $basedatos);
	# var_dump($link);
	if(!$link)
	{
		
		echo 'Your configuration file wp-config.php is wrong to connect with your database.
WPerrorFixer has not been able to connect to your WordPress database.
<br>
<br>WPerrorFixer has used the data from your file wp-config.php to connect with your database, but hasn\'t been able to make the connection.
<br>
<br>Please check the file wp-config.php.  
<br>
<br>You need to check that your file has the right values. 
<br>
<br>Check the values highlighted below (in orange) against the data in your hosting.[[ more specific than ‘in your hosting\' may be needed, perhaps the 2 files/parameter names that need to be in alignment]]. 
<br> 
<br>/** The name of the database for WordPress */		
<br>define(\'DB_NAME\', \'<strong style="orange">your_database_name</strong>\');
<br><br>
<br>/** MySQL database username */
<br>define(\'DB_USER\', \'<strong style="orange">your_user_in_your_database</strong>\');
<br>/** MySQL database password */
<br>define(\'DB_PASSWORD\', \'<strong style="orange">your_password_in_your_database</strong>\');
<br><br>
<br>/** MySQL hostname */
<br>define(\'DB_HOST\', \'<strong style="orange">your_server</strong>\');#it is usually \'localhost\', if it contains any another value then your Hosting provider will be able provide it to you.  
<br>
<br>You can find more info about this file in the official website of wordpress https://codex.wordpress.org/Editing_wp-config.php
<br> 
<br>Remember - if you have changed any of the parameters set on your server (such as your database, user or password database) then your wp-config.php file needs to be modified too to keep in sync with it.  		
';
	}
	
		return mysqli_query($link, $sql ); 

	}	
   function wperrorfixer_sacar_texto_patron($str,$comienzo,$final)
{
	$str=str_replace("\n"," ",$str);
	$str=str_replace("\t"," ",$str);
	$str=str_replace("\r"," ",$str);
	$str=str_replace("   "," ",$str);
	$str=str_replace("   "," ",$str);
	$str=str_replace("  "," ",$str);
	$str=str_replace("  "," ",$str);
	$str=str_replace("(   '","('",$str);
	$str=str_replace("'   )","')",$str);
	$str=str_replace(",   '",",'",$str);
	$str=str_replace("(  '","(  '",$str);
	$str=str_replace("'  )","')",$str);
	$str=str_replace(",  '",",'",$str);
	$str=str_replace("( '","('",$str);
	$str=str_replace("' )","')",$str);
	$str=str_replace(", '",",'",$str);
	$str=str_replace("   =","=",$str);
	$str=str_replace("  =","=",$str);
	$str=str_replace(" =","=",$str);
	$str=str_replace("=   ","=",$str);
	$str=str_replace("=  ","=",$str);
	$str=str_replace("= " ,"=",$str);
	$partes_comienzo=explode($comienzo,$str);
	
	$partes_final=explode($final,$partes_comienzo[1]);
return addslashes(str_replace("'","",str_replace(",'","",str_replace("='" ,"",$partes_final[0]))));
}
class WPerrorFixer_Settings {

	/**
	 * The single instance of WPerrorFixer_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.4.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.4.0
	 */
	public $parent = null;

	/**
	 * Prefix for Plugin Information.
	 * @var     string
	 * @access  public
	 * @since   1.4.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.4.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'wpt_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register Plugin Information
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		


		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_options_page( __( 'Plugin Information', 'wperrorfixer' ) , __( 'Plugin Information', 'wperrorfixer' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.4.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Fix', 'wperrorfixer' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {
/*
		$settings['standard'] = array(
			'title'					=> __( 'Standard', 'wperrorfixer' ),
			'description'			=> __( 'These are fairly standard form input fields.', 'wperrorfixer' ),
			'fields'				=> array(
				array(
					'id' 			=> 'text_field',
					'label'			=> __( 'Some Text' , 'wperrorfixer' ),
					'description'	=> __( 'This is a standard text field.', 'wperrorfixer' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wperrorfixer' )
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'A Password' , 'wperrorfixer' ),
					'description'	=> __( 'This is a standard password field.', 'wperrorfixer' ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wperrorfixer' )
				),
				array(
					'id' 			=> 'secret_text_field',
					'label'			=> __( 'Some Secret Text' , 'wperrorfixer' ),
					'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'wperrorfixer' ),
					'type'			=> 'text_secret',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wperrorfixer' )
				),
				array(
					'id' 			=> 'text_block',
					'label'			=> __( 'A Text Block' , 'wperrorfixer' ),
					'description'	=> __( 'This is a standard text area.', 'wperrorfixer' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'wperrorfixer' )
				),
				array(
					'id' 			=> 'single_checkbox',
					'label'			=> __( 'An Option', 'wperrorfixer' ),
					'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'wperrorfixer' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'select_box',
					'label'			=> __( 'A Select Box', 'wperrorfixer' ),
					'description'	=> __( 'A standard select box.', 'wperrorfixer' ),
					'type'			=> 'select',
					'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
					'default'		=> 'wordpress'
				),
				array(
					'id' 			=> 'radio_buttons',
					'label'			=> __( 'Some Options', 'wperrorfixer' ),
					'description'	=> __( 'A standard set of radio buttons.', 'wperrorfixer' ),
					'type'			=> 'radio',
					'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
					'default'		=> 'batman'
				),
				array(
					'id' 			=> 'multiple_checkboxes',
					'label'			=> __( 'Some Items', 'wperrorfixer' ),
					'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'wperrorfixer' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
					'default'		=> array( 'circle', 'triangle' )
				)
			)
		);

		$settings['extra'] = array(
			'title'					=> __( 'Extra', 'wperrorfixer' ),
			'description'			=> __( 'These are some extra input fields that maybe aren\'t as common as the others.', 'wperrorfixer' ),
			'fields'				=> array(
				array(
					'id' 			=> 'number_field',
					'label'			=> __( 'A Number' , 'wperrorfixer' ),
					'description'	=> __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', 'wperrorfixer' ),
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '42', 'wperrorfixer' )
				),
				array(
					'id' 			=> 'colour_picker',
					'label'			=> __( 'Pick a colour', 'wperrorfixer' ),
					'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', 'wperrorfixer' ),
					'type'			=> 'color',
					'default'		=> '#21759B'
				),
				array(
					'id' 			=> 'an_image',
					'label'			=> __( 'An Image' , 'wperrorfixer' ),
					'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'wperrorfixer' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'multi_select_box',
					'label'			=> __( 'A Multi-Select Box', 'wperrorfixer' ),
					'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', 'wperrorfixer' ),
					'type'			=> 'select_multi',
					'options'		=> array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
					'default'		=> array( 'linux' )
				)
			)
		);
		$settings['extra'] = array();
		*/

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register Plugin Information
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {
		$nonce = wp_create_nonce( 'my-nonce' );
echo '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
	if(empty($_GET['_wpnonce']))
	{
		$html2 = '<h2>' . __( 'Plugin Information' , 'wperrorfixer' ) . '</h2>' . "\n";

			$html2 .=  __( "<br><br> WPerrorFixer plugins is a limited version of the our oficial site <a href=\"https://www.wperrorfixer.com/\">https://www.wperrorfixer.com</a>.
			The WPerrorFixer plugin fix these errors:<br><br>
			- Delete their message:	briefly unavailable for scheduled maintenance. check back in a minute<br><br>
			- Mysql Errors. Examples:MySQL server has gone away,WordPress database error: [Table 'wp_options' is marked as crashed and should be repaired],Error 145<br><br>
			
			WPerrorFixer <a href=\"https://www.wperrorfixer.com/\">website</a> has more errors to repair. Because the code is executed in an external computer which can save and restore the changes in the files without damage your system. 
			" , 'wperrorfixer' ) ;
			
					$html2 .= "<br><br><a href='options-general.php?page=wperrorfixer_settings&_wpnonce={$nonce}'>Fix Wordpress</a>";
		$html2 .= '</div>' . "\n";

		echo $html2;	
	}	
#$html = '<a href="'.wp_nonce_url( 'options-general.php?page=wperrorfixer_settings', 'wperrorfixer_settings' ).'">Fix</a>';

		// Build page HTML
		#check_admin_referer();
		
		echo $html;	
$nonce = $_REQUEST['_wpnonce'];
#echo $nonce ;
if ( ! wp_verify_nonce( $nonce, 'my-nonce' )  ) {

     die(); 

} else {
wperrorfixer_comprobar_admin();
     // Do stuff here.
}		
			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			/*$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'wperrorfixer' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";/**/
				$error_tipo=0;
		if(file_exists ('../.maintenance')) #maintenance problem #delete
{
	$error_tipo=1;
	unlink('../.maintenance'); #delete the maintenance to remove the error "Briefly unavailable for scheduled maintenance. Check back in a minute"
	if(file_exists ('../.maintenance'))
	$html .='The message "Briefly unavailable for scheduled maintenance. Check back in a minute" has been delete';
	else
	$html .="WPerrorFixer can't delete the .maintenance file because WPerrorFixer doesn\'t have the right permissions to delete this file.".'<br>
<br><br>you can either a) visit our site at WPerrorFixer.com, enter your FTP data and let the online tool make the fix automatically or<br>
b) make the fix yourself manually using the following simple steps: 

<br><br>
<br><h3 style="color:green">Delete the file  .maintenance</h3>
<br>Depending on your Hosting Company it may be possible to do this via your hosting menu, if not you will have to use an FTP client such as Filezilla <a href="https://wiki.filezilla-project.org/FileZilla_Client_Tutorial_%28en%29"  target="_blank"> (manual filezilla)</a>.
<br>When you are connected to the ftp server you need to find and delete the file .maintenance, this should clear the error.  The file is usually in the main directory although it depends on the set up of your server.  The most common main directories are;
<br>public_html
<br>/home/www
<br>/home/www
<br>/root/www
<br>/var/www
<br>/srv/www.
<br>(Remember you need to show any hidden files while using your filezilla)
<br>When you delete the .maintenance file you should see your Wordpress website. If you see another error, that was hidden underneath the error you just cleared, you will need to fix this one as well.  How you fix it depends on the error message you get, hopefully WPErrorfixer.com will be able to do this automatically for you.  


';
}
if(file_exists ('../wp-config.php') and $error_tipo==0) #Look for the data to connect to database and repair that
{
	
$content=file_get_contents ('../wp-config.php');
$content=str_replace('require_once','#require_once',$content);
	$basedatos=wperrorfixer_sacar_texto_patron($content,'\'DB_NAME\'',')');
				$usuario=wperrorfixer_sacar_texto_patron($content,"'DB_USER'",")");
				$password_base=wperrorfixer_sacar_texto_patron($content,"'DB_PASSWORD'",")");
				$host=wperrorfixer_sacar_texto_patron($content,"'DB_HOST'",")");
#die();
$numero_tablas=0;
$numero_tablas_ok=0;
$numero_tablas_fixed=0;
$numero_tablas_not_fix=0;
	$connected_database=0;
	if(strlen($basedatos)>0 and strlen($usuario)>0 and strlen($password_base)>0 and strlen($host)>0) 
	{
		
		$result=wperrorfixer_conectar_mysql("show tables", $basedatos, $password_base,$usuario,$host);
	while($registro_conexion= $result->fetch_assoc())
	{ 
		$numero_tablas+=1;
		$repaired=0;
		#var_dump($registro_conexion);
		#echo '<br>'.$registro_conexion['Tables_in_'.DB_NAME];
		$check=wperrorfixer_conectar_mysql('CHECK TABLE '.$registro_conexion['Tables_in_'.$basedatos], $basedatos, $password_base,$usuario,$host);
		$registro_check= $check->fetch_assoc();
		#var_dump($registro_check);
		#echo ' '.$registro_check['Msg_text'];
		#$registro_check['Msg_text']='ss';
			$connected_database=1;
		if(!stristr($registro_check['Msg_text'],'OK'))
		{
			$check=wperrorfixer_conectar_mysql('CHECK TABLE '.$registro_conexion['Tables_in_'.$basedatos], $basedatos, $password_base,$usuario,$host);
			$registro_check= $check->fetch_assoc();
		#var_dump($registro_check);
		if(stristr($registro_check['Msg_text'],'OK'))
		{
			$html .='The  '.$registro_conexion['Tables_in_'.$basedatos].' table has been fixed';
			if($repaired!=-1) $repaired=1;
			$numero_tablas_fixed-=1;
		} else
		{
			$html .='The  '.$registro_conexion['Tables_in_'.$basedatos].' table has not been fixed';
			$repaired=-1;
			
		}
		if($repaired==-1)
		$html .='You need fix the tables from '.$basedatos;
	
			#die();
		} else
		{
			$numero_tablas_ok+=1;
		#$html .=' The '.$registro_conexion['Tables_in_'.$basedatos].' table is okay';	#It better not to show names of tables which are okay  because we don't want to help an hacker to see the name of the table
		}
	}
	$html .='<br>WPerrorFixer has found '.$numero_tablas.' tables in your database and there are '.$numero_tablas_ok.' tables okay ';
	if($numero_tablas_fixed==1)$html .='<br>WPerrorFixer has fixed '.$numero_tablas_fixed.' table';
	if($numero_tablas_fixed>1)$html .='<br>WPerrorFixer has fixed '.$numero_tablas_fixed.' tables';
	if($numero_tablas_not_fix==1)$html .='<br>WPerrorFixer has not fixed '.$numero_tablas_not_fix.' table';
	if($numero_tablas_not_fix>1)$html .='<br>WPerrorFixer has not fixed '.$numero_tablas_not_fix.' tables';
	
if($numero_tablas_not_fix>1)	$html .='WperrorFixer Plugin  is not able to fix your database automatically, but don\'t worry, you can either<br>
 a) visit our site at WPerrorFixer.com, enter your FTP data and let the online tool make the fix automatically or
b) make the fix yourself manually using the following simple steps.   
<br><strong style="color:#f09090">  Repair your database with <a href="https://www.siteground.com/tutorials/phpmyadmin/"  target="_blank"> phpmyadmin</a></strong>
<div style=\'padding-left:18px;\'>
<br>Do this by using the following steps.
<div style=\'padding-left:18px;\'>
<br>1. Login to your hosting account. <br>
<br>2. Login to phpMyAdmin. (wp-config.php is the Wordpress file where your configuration is kept). <br>
<br>3. Choose the affected database. If you only have one, that\'s it.  <br>
<br>4. In the main panel, you should see a list of your database tables. Check the boxes by the tables that need repair. 
<br>To see examples of how to fix tables go to <a href="https://www.siteground.com/tutorials/phpmyadmin/phpmyadmin_optimize_database.htm"  target="_blank">https://www.siteground.com/tutorials/phpmyadmin/phpmyadmin_optimize_database.htm</a> <br>
<br>5. At the bottom of the window, just below the list of tables, there is a drop down menu. Choose "Repair Table" <br>
<br>(*) Note: If you don\'t know the password, user and database for your mysql you can find it in wp-config.php which you can get by connecting to the FTP. The entries you are looking for are in the lines \'DB_USER\', ‘DB_PASSWORD\',and \'DB_NAME\'.
</div>
</div>
<br><strong style="color:#f09090">Alternatively to repair your database without using phpmyadmin</strong>
<div style=\'padding-left:18px;\'>
<br>Firstly you need to edit your wp-config.php.  
<br>You can do this in either your hosting menu or using your FTP client:
<div style=\'padding-left:18px;\'>
<br>a) By using your hosting menu; find the menu to access your files: you should add this line at the end of file:
<br>define(\'WP_ALLOW_REPAIR\', true);<br>
<br>b)  by using your FTP client; use a client like filezilla  <a href="https://wiki.filezilla-project.org/FileZilla_Client_Tutorial_%28en%29"  target="_blank"> (manual filezilla)</a>.  
<br>Connect via FTP to your website and download the wp-config.php file. It is residing at the root folder of the WordPress installation.
</div>
<br>Open the wp-config.php file with a text-editor and insert this line at the end of file (Be careful, some window editors have problems to modify the file properly.)
<br>define(\'WP_ALLOW_REPAIR\', true);
<br>Save the changes and upload this file back to your server. Make sure you overwrite the existing copy in the server.
<br>When the file wp-config.php has been edited go to URL http://yoursite.com/wp-admin/maint/repair.php. Don\'t forget to replace "yoursite.com" with your own Domain name. If the Domain name is not the same name as your website you can change it, but remember to keep an note of any values you are replacing in case you need to back your changes out.

<ul><li>When you open the url you press the button \'repair database\'</li>
<br><li>You should now see some code, scroll down to the end of the page until you see
"Repairs complete. Please remove the following line from wp-config.php to prevent this page from being used by unauthorized users."  </li>

<br><li>Remove the "WP_ALLOW_REPAIR" line from your "wp-config.php" file in your server 
<br>That\'s it. Your database is now repaired.</li>
</ul>
<br>Note(*):we recommend to edit your files from your hosting service because some window editors can have problems modifying the files, whereas the one supplied by your hosting service should be free of problems. 
</div>
<h4 style=\'color:orange\'>If fixing your  database doesn\'t resolve your problem.</h4>
<br>You can try to make a mysql sentence in phpmyadmin. 
<ul>
<li>Login phpmyadmin </li>
<li>Select the database </li>
<li>Press sql  link </li>
<li>Write in the box:
<br>select option_value FROM `wp_options` WHERE option_name=\'siteurl\' </li>
</ul>
<br><b>if the name – what name?</b> - is not the name of your website you can change it, but remember to keep an note of the value you are replacing in case you need to back your changes out.
<br>If the name is incorrect you can change it using this sql sentence (press sql and write in the box)<br><br>
<div style=\'padding-left:18px;\'>
UPDATE wp_options SET option_value=\'http://www.your_domain.com\' WHERE option_name=\'siteurl\'
</div>
<br>If this does not fix your problem you need to change  option_value back to the original version before your fix.
<br>
<br>An additional safeguard that we would recommend is to create a back-up copy of your database with your hosting company so that you can revert to it if you fix causes anything unexpected.   
</div>
';
	

	}
	else
	$html .='The WordPress file wp-config.php has not been found.  
<br>
<br>Some Hosting Companies regularly create backups of your WordPress, some even daily.  First check your WordPress files for wp-config.php.  Some Hosting Companies split the backups in \'Files\' and \'Databases\', you need to check in \'Files\'. If you cannot find any backups check with your Hosting Company. 
<br>
<br>If you do find a backup version of wp-config.php be aware that, depending on when the backup was taken, you may lose some recent posts, so make sure you have copies of these for reposting if necessary.  
<br>
<br>Alternatively you can create your own wp-config.php in you WordPress main directory  by following these instructions. The most common main directories are: public_html , /home/www , /home/www , /root/www , /var/www and /srv/www.
<br>
<br>
<br>
<br>Before you start following our simple instructions, you might want to find out more info about this important file and how to edit it from the official Wordpress website. https://codex.wordpress.org/Editing_wp-config.php
<br>
<br>For those of you experienced in file management and editing there is the option to create the file wp-config.php strongly implying that - if you don\'t have these skills don\'t try it! 
<br>Set the following parameters.
<br> You should find the database_name_here, username_here and password_here in your hosting menu.
<br>
<br><?php
<br>/**
<br> * Custom WordPress configurations on "wp-config.php" file.
<br>
<br>
<br>
<br>/* MySQL settings */
<br>define( \'DB_NAME\',     \'database_name_mysql_here\' );
<br>define( \'DB_USER\',     \'username_mysql_here\' );
<br>define( \'DB_PASSWORD\', \'password_mysql_here\' );
<br>define( \'DB_HOST\',     \'localhost\' );#it is usually \'localhost\'.  If it contains any another value check with your Hosting Provider, they will be able to provide you with the other values. 
<br>
<br>You can find more info about this important file on the official Wordpress website. https://codex.wordpress.org/Editing_wp-config.php
<br>define( \'DB_CHARSET\',  \'utf8mb4\' );
<br>
<br>
<br>/* MySQL database table prefix. */
<br>$table_prefix = \'wp_\';
<br>
<br>
<br>/* Authentication Unique Keys and Salts. */
<br>/* https://api.wordpress.org/secret-key/1.1/salt/ */
<br>define( \'AUTH_KEY\',         \'put your unique phrase here\' );
<br>define( \'SECURE_AUTH_KEY\',  \'put your unique phrase here\' );
<br>define( \'LOGGED_IN_KEY\',    \'put your unique phrase here\' );
<br>define( \'NONCE_KEY\',        \'put your unique phrase here\' );
<br>define( \'AUTH_SALT\',        \'put your unique phrase here\' );
<br>define( \'SECURE_AUTH_SALT\', \'put your unique phrase here\' );
<br>define( \'LOGGED_IN_SALT\',   \'put your unique phrase here\' );
<br>define( \'NONCE_SALT\',       \'put your unique phrase here\' );
<br>
<br>
<br>/* Absolute path to the WordPress directory. */
<br>if ( !defined(\'ABSPATH\') )
<br>        define(\'ABSPATH\', dirname(__FILE__) . \'/\');
<br>
<br>/* Sets up WordPress vars and included files. */
<br>require_once(ABSPATH . \'wp-settings.php\');';
	

}
echo $html;

	}

	/**
	 * Main WPerrorFixer_Settings Instance
	 *
	 * Ensures only one instance of WPerrorFixer_Settings is loaded or can be loaded.
	 *
	 * @since 1.4.0
	 * @static
	 * @see WPerrorFixer()
	 * @return Main WPerrorFixer_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.4.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.4.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}
