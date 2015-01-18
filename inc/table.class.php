<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class PDF_Pages_Table extends WP_List_Table { 
 
	public $notify='';

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'row',     
            'plural'    => 'rows',  
            'ajax'      => false        
        ) );
        
    }    
    
    function column_default($item, $column_name) {
        switch($column_name){
            case 'pdf_url':
				return '<a target="_blank" href="'.$item->$column_name.'">'.basename($item->$column_name).'</a>';
            case 'page_id':
              return '<div>
				<a href="'.admin_url('admin.php?page=pdf-bar-list&ID='.$item->id).'">Edit</a> |
				<a href="'.admin_url('admin.php?page=pdf-bar-duplicate&ID='.$item->id).'">Duplicate</a> | 
				<a target="_blank" href="'.site_url('/?page_id='.$item->$column_name).'">View</a></div>';
			case 'date':
				return date('H:m, M d, Y',strtotime($item->$column_name));
			case 'button_1_click':
				 return $this->getTracking($item);
            default:
               return $item->$column_name;
        }
    }
    private function getTracking($item) {
		
		
		if(!$item->button_1_link && !$item->button_2_link) {
			return "-";
		}
		
		$output =array();
		
		if($item->button_1_link) {
			$output[] = 'Button 1 Clicks: '.$item->button_1_click; 
		}
		
		if($item->button_2_link) {
			$output[] = 'Button 2 Clicks: '.$item->button_2_click;
		}
		
		return implode(' <br> ', $output);
	}
	
    function column_user_id($item) {
        
        $actions = array(
        );
        
        if(!$user_info=get_userdata($item->user_id))
			$user_info->user_login='Anonymous';
        return sprintf('%1$s <span style="color:silver">(ip:%2$s)</span>%3$s',
            $user_info->user_login,
            $item->ip,
            $this->row_actions($actions)
        );
    }
    
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id               
        );
    }
    
    
    function get_columns() {
        $columns = array(
            'cb'        => '<input type="checkbox" />', 
            'pdf_url' 	=> 'File',
            'title'     => 'Title',
			'sh_text'	=> 'Shared Text',
			'button_1_click' => 'Tracking',
            'date'   => 'Created',
			'page_id'=> 'Actions'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(    
            'Title'      => array('title',true),
			'Date'       => array('date',true)
        );
        return $sortable_columns;
    }
    
    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
	
	function group_filter( $which = '' ) {
	}
	
    function process_bulk_action() {
        
        if( 'delete'===$this->current_action() ) {
			$rows=$_REQUEST['row'];
			global $wpdb;
			foreach($rows as $page_id) 
			{
				$postid = $wpdb->get_var("SELECT `page_id` FROM `{$wpdb->prefix}pdf_page` WHERE id='$page_id'");
				wp_delete_post( $postid, true );
				$wpdb->query("DELETE FROM `{$wpdb->prefix}pdf_page` WHERE id='$page_id'");
			}
            $this->notify="Successfully Deleted";
			wp_redirect(admin_url('admin.php?page=pdf-bar-list'));
        }
    }
    
    function prepare_items() {
		$per_page = 25; 
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();                
    
	    global $wpdb;
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'date'; 
	    $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; 

		$query = "SELECT `id`, `pdf_url`, `page_id`, `date`, `title`, `bar_position`, `sh_text`, `button_1_link`, `button_1_click`, `button_2_click`, `button_2_link`
				FROM `{$wpdb->prefix}pdf_page` ORDER BY ". $orderby . ' ' .$order;
				
	    $data = $wpdb->get_results($query); 
		
        $current_page = $this->get_pagenum();  
        $total_items = count($data); 
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ) );
    }
}
