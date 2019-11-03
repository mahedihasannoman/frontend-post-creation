<?php
if (!defined('ABSPATH')) exit;
?>
<div class="fpc-form-style-2">
	<div class="fpc-form-style-2-heading"><?php echo esc_html__('Create A New Post', 'fpc'); ?></div>
	<form action="" method="post" enctype="multipart/form-data">		
		<?php
			//Displaying error Messages
			$html = '';
			if(!empty($this->error)){
				$html .= '<div class="fpc_error_content">';
				$html .='<ul>';
				foreach($this->error as $error){
					$html .= '<li> Error: '.esc_html($error['message']).'</li>';
				}
				$html .='</ul>';
				$html .= '</div>';
				$this->error = array();
				echo $html;
			}else{
				echo $html;
			}
		?>
	
		<!--Title field -->
		<label for="title"><span><?php echo esc_html__('Title', 'fpc'); ?></span><input type="text" class="fpc-input-field" name="title" value="<?php echo esc_html($this->title) ?>" /></label>
		<!--categories field -->
		<label for="cat"><span><?php echo esc_html__('Categories', 'fpc'); ?></span>
		<?php wp_dropdown_categories($categoryArgument); ?>
		<i><?php echo esc_html__('You can choose more then one category', 'fpc'); ?></i>
		</label>
		<!--Post Content -->
		<label for="post_content"><span><?php echo esc_html__('Post Content', 'fpc'); ?></span><?php wp_editor( $content, $editor_id, $settings = array() ); ?></label>
		<!--Tags Selector -->
		<label for="tags-selector"><span><?php echo esc_html__('Tags', 'fpc'); ?></span><input type="text" class="fpc-input-field fpc-tags" name="tags-selector" value="" />&nbsp;<input type="button" class="button fpc_tagadd" value="<?php echo esc_html__('Add', 'fpc'); ?>">
		
			<div class="fpc-tag_area">
				<ul>
				<?php 
					if(!empty($this->tags)){
						foreach($this->tags as $tag){
							if(trim($tag)!=''):
						?>
							<li><button class="fpc_tag_remove"><i class="fa fa-window-close" aria-hidden="true"></i></button><?php echo esc_html($tag); ?></li>
						<?php 
							endif;
						}
					}
				?>
				</ul>
				<input type="hidden" name="tags" id="fpc-tags" value="<?php echo esc_html(implode(',', $this->tags)); ?>" />
			</div>
		</label>
		<!--Featured Image Field -->
		<label for="tags-selector"><span><?php echo esc_html__('Featured Image', 'fpc'); ?></span>
			<input type="file" name="post_image" id="fpc_post_image">
			<br>
			<br>
			<i><?php echo esc_html__('* Allow extensions are: png, jpeg, jpg. Max upload file size: 2MB', 'fpc'); ?></i>
			<br>
		</label>
		<!--Nonce Field -->
		<?php wp_nonce_field( 'fpc_nonce_action', 'fpc_f_nonce_field' ); ?>
		<div class="fpc-form-style-2-heading"><?php echo esc_html__('Publish post', 'fpc'); ?></div>
		<!--Publish button -->
		<label><input type="submit" name="fpc_submit" value="<?php echo esc_html__('Publish', 'fpc'); ?>" /></label>
	</form>
</div>