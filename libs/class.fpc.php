<?php
if (!defined('ABSPATH')) exit;
/*
* @Since 1.0.0
* Main class for FPC
*/
class Fpcmain
{
	// class instance private property
	private static $instance;
	//form properties
	private $title = "";
	private $post_content = "";
	private $tags = array();
	private $category = array();
	
	//error messages
	private $error = array();
	
	//Allowed file minetypes
	private $allowed_image_extension = array('image/jpeg', 'image/jpg', 'image/png');
	private $max_image_size = 2097152; //Max Image size 2MB
	

	/*
	* @Method - get instance
	* @Author - Mahedi Hasan
	* @Description - Instantiating a this class
	* @Return - instance of this class
	* @Since 1.0
	*/
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	/*
	* @Method - Constructor
	* @Author - Mahedi Hasan
	* @Since 1.0
	*/
	private function __construct() {

		// Actions
		add_action( 'wp_enqueue_scripts', array(&$this, 'register_resources_frontend')); 
		add_action( 'init', array(&$this, 'handle_form_submit'));
		add_action( 'wp_ajax_fpc_search_tags', array(&$this, 'search_tag') );
		add_action( 'wp_ajax_nopriv_fpc_search_tags', array(&$this, 'search_tag') );
		//Add Shortcode
		add_shortcode('fpc-form', array(&$this, 'render_shortcode'));
		//filters
		add_filter( 'wp_dropdown_cats', array(&$this, 'dropdown_cats_multiple'), 10, 2 );
		add_filter( 'fpc_sanitize_category_value', array(&$this, 'sanitize_category_value') );
		
	}
	
	/*
	* @Method - Sanitizing Categories
	* @Callback: fpc_sanitize_category_value	
	* @Author - Mahedi Hasan
	* @Since 1.0.0
	*/
	public function sanitize_category_value( $value ){
		if(!empty($value)){
			$category = array();
			foreach($value as $cat){
				$category[] = intval($cat);
			}
			return array_map('intval', $category);
		}else{	
			return array();
		}
	}
	
	/*
	* @Method - Searching Tags by Keyword
	* @Callback: wp_ajax_fpc_search_tags	
	* @Author - Mahedi Hasan
	* @Since 1.0.0
	*/
	public function search_tag(){
		$keyword = sanitize_text_field($_POST['search']['term']);
		wp_send_json_success(Fpchelper::get_tags($keyword));
		die();
	}
	
	/*
	* @Method - Category Dropdown multiple select
	* @Callback: wp_dropdown_cats
	* @Author - Mahedi Hasan
	* @Since 1.0.0
	*/
	public function dropdown_cats_multiple($output, $r){
		
		if ( ! empty( $r['multiple'] ) ) {
			$output = preg_replace( '/<select(.*?)>/i', '<select$1 multiple="multiple">', $output );
			$output = preg_replace( '/name=([\'"]{1})(.*?)\1/i', 'name=$2[]', $output );
		}
		return $output;
		
	}
	
	/*
	* @Method - Handle frontend form submision
	* @Author - Mahedi Hasan
	* @Since 1.0.0
	*/
	public function handle_form_submit(){
		
		if(isset($_POST['fpc_submit'])){

			//sanitizing post fields
			$title = sanitize_text_field($_POST['title']);
			$tags = sanitize_text_field($_POST['tags']);
			$post_content = $_POST['post_content'];
			$category = apply_filters( 'sanitize_category_value', $_POST['cat'] );
			$allowed_html = wp_kses_allowed_html( 'post' );
			$post_content = wp_unslash(wp_kses( trim($post_content), $allowed_html ));
			
			if(!$post_content=='' or strlen($post_content)>0 or !$title=='' or strlen($title)>0){
				
				//validating nonce
				if (! isset( $_POST['fpc_f_nonce_field'] ) || ! wp_verify_nonce( $_POST['fpc_f_nonce_field'], 'fpc_nonce_action' )) {
				   print 'Please do not cheating. I am clever enough to catch you :)';exit;
				} else {
					
					//constructing post array
					$post = array(
						'comment_status'  => 'open',
						'post_content' => $post_content,
						'post_title'    => $title,
						'post_status'   => 'publish',
						'post_type'   => 'post'
					);
					//adding category if exists
					if(!empty($category)){
						$post['post_category'] = $category;
					}
					
					// checking if image exists
					if (file_exists($_FILES["post_image"]["tmp_name"])) {
						// Get image file extension
						
						$mimetype = mime_content_type($_FILES['post_image']['tmp_name']);
						$response = array();
						if (! in_array($mimetype, $this->allowed_image_extension)) {
							$response[] = array(
								"type" => "error",
								"message" => esc_html__("Invalid file uploaded. Only PNG, JPG and JPEG are allowed.", "fpc")
							);
							
						}// Validate image file size
						if (($_FILES["post_image"]["size"] > $this->max_image_size)) {
							$response[] = array(
								"type" => "error",
								"message" => esc_html__("Image size exceeds.", "fpc")
							);
						}
						if(count($response)>0){
							$this->error = $response;
						}else{
							$imageStatus = true;
						}						
						
					}
				
					if(empty($this->error)){
						
						$post_id = wp_insert_post($post);
						if($post_id){
							
							//Adding image to Post if exists
							if(isset($imageStatus) && $imageStatus){
								if(!is_admin()){
									require_once(ABSPATH . 'wp-admin/includes/media.php');
									require_once(ABSPATH . 'wp-admin/includes/file.php');
									require_once(ABSPATH . 'wp-admin/includes/image.php');
								}
								$attachmentId = media_handle_sideload($_FILES["post_image"], $post_id);             
								if ( is_wp_error($attachmentId) ) {									
									@unlink($_FILES["post_image"]["tmp_name"]);									
								}else{
									set_post_thumbnail($post_id, $attachmentId);
								}
								
							}
							//Assigning tags to Post
							if($tags!='' || strlen($tags)>0){
								$tags = explode(',', $tags);
								wp_set_post_tags($post_id, $tags);
							}
							//redirect to post page after success
							wp_redirect(get_permalink($post_id));die();
						}
						
					}

				}

			}else{
				$response[] = array(
					"type" => "error",
					"message" => esc_html__("At least Title or Post Content is Required!", "fpc")
				);
				$this->error = $response;
			}
			$this->tags = explode(',',$tags);
			$this->title = $title;
			$this->post_content = $post_content;
			$this->category = $category;

		}
		
	}
	
	/*
	* @Method - Register resources
	* @Author - Mahedi Hasan
	* @Description - registering resources for frontend
	* @Since 1.0
	*/
	public function register_resources_frontend(){
		
		//registering styles
		wp_register_style( FPC_PRFX.'_form_css', FPC__PLUGIN_URL . 'assets/css/form.css', array(), FPC_VERSION, 'all' );
		wp_register_style( FPC_PRFX.'_icon_css', FPC__PLUGIN_URL . 'assets/css/font-awesome.min.css', array(), FPC_VERSION, 'all' );
		wp_register_style( FPC_PRFX.'_jui_css', FPC__PLUGIN_URL . 'assets/css/jquery-ui.css', array(), FPC_VERSION, 'all' );
		//jQuery for fronend
		wp_enqueue_script( 'jquery', 'jquery');
		wp_register_script( FPC_PRFX.'_form_js', FPC__PLUGIN_URL . 'assets/js/form.js', array('jquery', 'jquery-ui-core', 'jquery-ui-autocomplete'), FPC_VERSION, true );
		wp_localize_script(FPC_PRFX.'_form_js', FPC_PRFX.'_object',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
			)
		);

	}
	
	/*
	* @Method - render shortcode
	* @Callback: add_shortcode fpc-form
	* @Author - Mahedi Hasan
	* @Description - handleing shorcode and render form
	* @Return - html form
	* @Since 1.0
	*/
	public function render_shortcode($atts = array()){
		
		wp_enqueue_style(FPC_PRFX.'_form_css');
		wp_enqueue_style(FPC_PRFX.'_icon_css');
		wp_enqueue_style(FPC_PRFX.'_jui_css');
		wp_enqueue_script(FPC_PRFX.'_form_js');
		
		$content = $this->post_content;
		$editor_id = 'post_content';
		$settings =   array(
			'wpautop' => true,
			'media_buttons' => false, // show insert/upload button(s)
			'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
			'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
			'tabindex' => '',
			'editor_css' => '', //  extra styles for both visual and HTML editors buttons, 
			'editor_class' => '', // add extra class(es) to the editor textarea
			'teeny' => false, // output the minimal editor config used in Press This
			'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
		);
		
		$categoryArgument = array( 
			'taxonomy'   => 'category',
			'multiple'   => true,				
			'hide_empty' => false,
			//'required' => true,
			'walker'     => new Fpc_Walker_CategoryDropdown(),
		);
		if(!empty($this->category)){
			$categoryArgument['selected'] = array_map('intval', $this->category);
		}
		
		ob_start();
		if(file_exists(FPC__PLUGIN_DIR.'/templates/form-template.php')){
			include FPC__PLUGIN_DIR.'/templates/form-template.php';
		}else{
			echo esc_html('Form template is missing.');
		}
		$content = ob_get_clean();
		return $content;
		
	}
	
}