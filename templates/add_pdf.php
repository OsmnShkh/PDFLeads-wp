<div class="wrap">
<h2><?php echo ($action =='add_pdfpage') ? "Add PDF Page" : "Edit PDF Page" ?></h2>
<?php if(isset($_GET['error'])) : ?>
	<p style="color:red"><?php echo $_GET['error']; ?></p>
		
<?php endif; ?>

<form enctype="multipart/form-data" method="post" action="<?php echo admin_url()?>admin-post.php">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Pdf File(*)</label>
			</th>
			<td>
				<?php if($pdf_url): ?>
					<a href="<?php echo $pdf_url; ?>" ><?php echo basename($pdf_url); ?></a><br>
					<input type="file" name="pdf_file" value=""><br>
					or paste a link <input type="text" name="pdf_link" value="">
				<?php else: ?>
					<input type="file" name="pdf_file" value=""><br>
					or paste a link <input type="text" name="pdf_link" value="">
				<?php endif;?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Title(*)</label>
			</th>
			<td>
				<input type="text" name="title" value="<?php echo $title; ?>">
			</td>
		</tr>
		<tr>
		<th scope="row">Bar Position(*)</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span>Bar Position </span>
					</legend>
					<p>
						<label><input name="bar_position" type="radio" value="Top" <?php echo (!$bar_position || $bar_position == 'Top') ? 'checked="checked"': '' ?>> Top</label><br>
						<label><input name="bar_position" type="radio" value="Bottom" <?php echo ($bar_position == 'Bottom') ? 'checked="checked"': '' ?>> Bottom</label>
					</p>
				</fieldset>
			</td>
		</tr>
		<th scope="row">Add "noindex"</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span>Add "noindex"</span>
					</legend>
					<p>
						<input type="checkbox" value="1" name="noindex" <?php echo ($noindex) ? 'checked="checked"': '' ?>>
					</p>
				</fieldset>
			</td>
		</tr>
	</tbody>
</table>
<h2>Social Sharing</h2>
<table class="form-table admin_social_set">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Choose Social Buttons</label>
			</th>
			<td>
				<div class="social social_share flat">
					<a class="fb" href="#">
						<span class="icon"></span>
						<input type="checkbox" name="fb" <?php echo ($social && $social['fb']) ? 'checked="checked"': '' ?>>
					</a>
					
					<a class="gplus" href="#">
						<span class="icon"></span>
						<input type="checkbox" name="gplus" <?php echo ($social && $social['gplus']) ? 'checked="checked"': '' ?>>
					</a>
					<a class="twitter" href="#">
						<span class="icon"></span>
						<input type="checkbox" name="twitter" <?php echo ($social && $social['twitter']) ? 'checked="checked"': '' ?>>
					</a>
					<a class="linkedin" href="#">
						<span class="icon"></span>
						<input type="checkbox" name="linkedin" <?php echo ($social && $social['linkedin']) ? 'checked="checked"': '' ?>>
					</a>
					<a class="reddit" href="#">
						<span class="icon"></span>
						<input type="checkbox" name="reddit" <?php echo ($social && $social['reddit']) ? 'checked="checked"': '' ?>>
					</a>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Shared Link</label>
			</th>
			<td>
				<input type="text" name="sh_link" value="<?php echo $sh_link; ?>">
				<p class="description">Leave blank to use the default url.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Shared Text</label>
			</th>
			<td>
				<textarea name="sh_text" style="width: 300px;height: 100px;"><?php echo $sh_text; ?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Twitter account name</label>
			</th>
			<td>
				<input name="twitter_account" type="text"  value="<?php echo get_option('twitter_author');?>">
				<input type="button" name="submit" id="save_author" class="button button-secondary" value="Save">
				<p class="description">One for all pages.</p>
			</td>
		</tr>
	</tbody>
</table>
<h2>Extend Options</h2>
<table class="form-table admin_social_set">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Bar Color</label>
			</th>
			<td>
				<input type="text"  value="<?php echo ($bar_color) ? $bar_color : "#ffffff"?>" name="bar_color" class="color-field" data-default-color="#ffffff" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="bar_text">Bar Text</label>
			</th>
			<td>
				<input type="text" name="bar_text" value="<?php echo $bar_text; ?>"><br>
				<p class="description">
					Put text on  it's own line <input type="checkbox" value="1" name="textline" <?php echo ($textline) ? 'checked="checked"': '' ?>>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="text_color">Text Color</label>
			</th>
			<td>
				<input type="text" value="<?php echo ($text_color) ? $text_color : "#000000"?>" name="text_color" class="color-field" data-default-color="#000000" />
			</td>
		</tr>
	</tbody>
</table>
<h2>CTA Buttons</h2>
<table class="form-table admin_social_set">
	<tbody>	
		<tr valign="top">
			<th scope="row">
				<label for="button_1">Button 1</label>
			</th>
			<td>
				<input type="checkbox" name="button_1" class="button_enable" <?php echo ($button_1_text || $button_1_link) ? 'checked="checked"': '' ?>>
			</td>
		</tr>
		<tr valign="top" class="button_1_tr">
			<th scope="row">
				<label for="pdf_file">Button Text(*)</label>
			</th>
			<td>
				<input type="text" name="button_1_text" value="<?php echo $button_1_text; ?>" disabled="disabled" >
			</td>
		</tr>
		<tr valign="top" class="button_1_tr">
			<th scope="row">
				<label for="pdf_file">Button Link(*)</label>
			</th>
			<td>
				<input type="text" name="button_1_link" value="<?php echo $button_1_link; ?>" disabled="disabled">
			</td>
		</tr>
		<tr valign="top" class="button_1_tr">
			<th scope="row">
				<label for="pdf_file">Button Color(*)</label>
			</th>
			<td>
				<input type="text" value="<?php echo ($button_1_color) ? $button_1_color : "#49afcd"?>" name="button_1_color" class="color-field" data-default-color="#49afcd" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pdf_file">Button 2</label>
			</th>
			<td>
				<input type="checkbox" name="button_2"  class="button_enable" <?php echo ($button_2_text || $button_2_link) ? 'checked="checked"': '' ?>>
			</td>
		</tr>
		<tr valign="top" class="button_2_tr">
			<th scope="row">
				<label for="pdf_file">Button Text(*)</label>
			</th>
			<td>
				<input type="text" value="<?php echo $button_2_text; ?>" name="button_2_text" disabled="disabled">
			</td>
		</tr>
		<tr valign="top" class="button_2_tr">
			<th scope="row">
				<label for="pdf_file">Button Link(*)</label>
			</th>
			<td>
				<input type="text" name="button_2_link" value="<?php echo $button_2_link; ?>" disabled="disabled">
			</td>
		</tr>
		<tr valign="top" class="button_2_tr">
			<th scope="row">
				<label for="pdf_file">Button Color(*)</label>
			</th>
			<td>
				<input type="text" value="<?php echo ($button_2_color) ? $button_2_color : "#49afcd"?>" name="button_2_color" class="color-field" data-default-color="#49afcd" />
			
			</td>
		</tr>
	</tbody>
</table>
<h2>Add Form To Bar <input type="checkbox" name="form" class="button_enable" <?php echo ($form) ? 'checked="checked"': '' ?>></h2>
<table class="form-table admin_social_set">
	<tbody>
		<tr valign="top" class="form_tr">
			<th scope="row">
				<label for="pdf_file">Form HTML</label>
			</th>
			<td>
				<textarea name="form_text" disabled="disabled"  style="width: 80%;height: 150px;"><?php echo stripslashes_deep($form); ?></textarea>
			</td>
		</tr>
	</tbody>
</table>
<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="Publish" style="margin-right: 10px;">
	<?php if ($page_id && get_post_status( $page_id ) == 'draft') : ?>
		<input type="submit" name="draft" class="button button-default" value="Saved as Draft">
	<?php else :?>
		<input type="submit" name="draft" id="draft" class="button button-default" value="Save as Draft">
	<?php endif; ?>
</p>
</form>
</div>
<script>
	jQuery(function($){
		$('.button_enable').each(function() {
			var name = $(this).attr('name');
			if ($(this).attr("checked")) {
				$('.'+name+'_tr input, .'+name+'_tr textarea').removeAttr('disabled');
				$('.'+name+'_tr').show();
			}
		});
		
		$('.button_enable').on('click',function() {
			var name = $(this).attr('name');
			
			if ($(this).attr("checked")) {
				$('.'+name+'_tr input, .'+name+'_tr textarea').removeAttr('disabled');
				$('.'+name+'_tr').show();
			} else {
				$('.'+name+'_tr input, .'+name+'_tr textarea').val('');
				$('.'+name+'_tr input, .'+name+'_tr textarea').attr('disabled', 'disabled');
				$('.'+name+'_tr').hide();
			}
		});
		
		$('.social_share > a').on('click',function(e) {
			if( $(e.target).prop("tagName") == 'INPUT' ) 
				return;
			
			e.preventDefault();
			var name = $(this).attr('class');
			$input = $(this).find('input').click();
			
		});
		
		$('#save_author').click(function(){
			var name = $('[name="twitter_account"]').val();
			jQuery.post(
				ajaxurl, 
				{
					'action': 'save_author',
					'twit_author':   name
				}, 
				function(response){
					alert(response);
				}
			);
		})
		
		 $('.color-field').wpColorPicker();
	});
</script>
