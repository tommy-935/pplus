<?php
class lybpAdmin{
	public $lybp_list_obj = null;
	public $db = null;
	public function __construct(){
		global $wpdb;
		$this->db = $wpdb;
	}

	/*
    * Admin Menu add function
    * WC sub menu
    */
    public function registerMenu() {
    	// add_submenu_page( 'woocommerce', 'Order Submit Limit', 'Order Submit Limit', 'manage_woocommerce', 'woocommerce-order-submit-limit', [$this, 'orderSubmitLimitView']);
		add_options_page( __( 'Lymos Backup', 'lybp' ), __( 'Lymos Backup', 'lybp' ), 'manage_options', 'lybp_backup', [$this, 'lybpBackup']);
	}

   	public function lybpBackup(){
		require_once LYBP_DIR . '/view/lybpList.php';
		$this->lybp_list_obj = new lybpList;
		$this->lybp_list_obj->list();
   	}

   	public function ajaxAddWolRule(){
   		$ip = addslashes(trim($_POST['ip']));
        $email = addslashes(trim($_POST['email']));
		if(! $ip && ! $email){
			return wp_send_json(['status' => 0, 'data' => __('ip or email must be filled at least one', 'lybp')]);
		}
		$data = [
			'ip' => $ip,
			'email' => $email,
			'added_by' => intval(get_current_user_id()),
			'added_date' => date('Y-m-d H:i:s')
		];
		$res = $this->db->insert($this->db->prefix . 'order_limit', $data);
		return wp_send_json(['status' => 1, 'data' => __('added success', 'lybp')]);
   	}

	public function ajaxLybpBackupDb(){
		set_time_limit(0);
		$filename = 'database-' . time() . '.sql';
		$file = $this->_genFile($filename);
		$command = 'mysqldump';
		$script = $command . ' -u' . DB_USER . ' -p' . DB_PASSWORD . ' ' . DB_NAME . ' > ' . $file;

		$status = shell_exec($script);

		$command_gz = 'tar';
		$script_gz = $command_gz . ' -czvf ' . $file . '.tar.gz ' . $file;

		shell_exec($script_gz);
		$file = $this->_genFile($filename . '.tar.gz');

	 	return wp_send_json(['status' => $status, 'data' => __('backup success ' . $file, 'lybp')]);
	}

	public function ajaxLybpBackupFile(){
		$filename = 'file-' . time() . '.tar.gz';
		$file = $this->_genFile($filename);
		$origin_file = ABSPATH;
		if(isset($_POST['is_content'])){
			$origin_file .= 'wp-content/';
		}
		set_time_limit(0);
		$command = 'tar';
		$script = $command . ' -czvf ' . $file . ' ' . $origin_file;
		$status = shell_exec($script);
	 	return wp_send_json(['status' => $status, 'data' => __('backup success ' . $file, 'lybp')]);
	}

	private function _genFile($filename){
		$dir = WP_CONTENT_DIR . '/uploads/lymos-backup';
		if(! file_exists($dir)){
			mkdir($dir, 0777);
		} 
		return $dir . '/' . $filename;
	}

   	public function getList(){
   		$page = intval($_GET['page']);
        $pagesize = intval($_GET['pagesize']);
        $page = $page ? $page : 1;
        $pagesize = $pagesize ? $pagesize : 20;
   		$where = '';
   		$sql = 'select id, ip, email, status, added_date from ' . $this->db->prefix . 'order_limit where 1=1 ' . $where . ' order by id desc limit ' . ($page - 1) * $pagesize . ',' . $pagesize;
   		$sql_count = 'select count(*) as count from ' . $this->db->prefix . 'order_limit where 1=1 ' . $where;
   		$count = $this->db->get_var($sql_count);
   		$data = $this->db->get_results($sql, ARRAY_A);
   		$disabled = __('disabled', 'lybp');
   		$ignored = __('ignored', 'lybp');
   		foreach($data as & $rs){
			$rs['status'] = $rs['status'] ? $ignored : $disabled;
   		}
		return wp_send_json(['status' => 1, 'data' => [
			'page' => $page,
			'pagesize' => $pagesize,
			'count' => $count,
			'list' => $data
		]]);
   	}

   	public function saveMessage(){
		$ip = addslashes(trim($_POST['ip_message']));
        $email = addslashes(trim($_POST['email_message']));
		if(! $ip || ! $email){
			return wp_send_json(['status' => 0, 'data' => __('ip or email message must be filled', 'lybp')]);
		}
		update_option('wol_ip_message', $ip);
		update_option('wol_email_message', $email);
		return wp_send_json(['status' => 0, 'data' => __('save success', 'lybp')]);
   	}

}