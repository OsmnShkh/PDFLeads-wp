<style>
	.column-page_id, .column-edit {
		text-align: center;
	}
	
	th#page_id, th.manage-column.column-page_id {
		text-align: center;
	}
	
	
	td.page_id.column-page_id div a {
		display: inline-block;
		text-align: center;
		
	}
	
	
</style>
<div class="wrap">	
	<div id="icon-users" class="icon32"><br/></div>
	<h2>PDF Pages List <a href="<?php echo admin_url('admin.php?page=pdf-bar-add') ?>" class="add-new-h2">Add New</a></h2>
	<?php if(!empty($table->notify)) { ?>
	<div id="message" class="updated below-h2">
		<p><?php echo $table->notify; ?></p>
	</div>
	<?php } ?>
	<form id="sent-sms-filter" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $table->display() ?>
	</form>
</div>