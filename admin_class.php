<?php
use QRcode;
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		
			extract($_POST);		
			$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				if($_SESSION['login_type'] != 1 && $_SESSION['login_type'] != 2){
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2 ;
					exit;
				}
					return 1;
			}else{
				return 3;
			}
	}
	function login2(){
		
			extract($_POST);
			if(isset($email))
				$username = $email;
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if($_SESSION['login_alumnus_id'] > 0){
				$bio = $this->db->query("SELECT * FROM alumnus_bio where id = ".$_SESSION['login_alumnus_id']);
				if($bio->num_rows > 0){
					foreach ($bio->fetch_array() as $key => $value) {
						if($key != 'passwors' && !is_numeric($key))
							$_SESSION['bio'][$key] = $value;
					}
				}
			}
			if($_SESSION['bio']['status'] != 1){
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2 ;
					exit;
				}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}
	function save_user(){
		extract($_POST);
		if (isset($name) && $name === "Administrator") {
			$type = 1; // Admin
		} else {
			$type = isset($type) ? $type : '2'; // Default to Staff if undefined
		}
		$data = "name = '$name', username = '$username'";
		if (!empty($password)) {
			$data .= ", password = '".md5($password)."'";
		}
		$data .= ", type = '$type'";
		// Check for existing username
		$chk = $this->db->query("SELECT * FROM users WHERE username = '$username' AND id != '$id'")->num_rows;
		if ($chk > 0) {
			return 2;
		}
		// Insert or update user record
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users SET $data");
		} else {
			$save = $this->db->query("UPDATE users SET $data WHERE id = $id");
		}
		if ($save) {
			return 1;
		} else {
			return 0; 
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$uid = $this->db->insert_id;
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("INSERT INTO alumnus_bio set $data ");
			if($data){
				$aid = $this->db->insert_id;
				$this->db->query("UPDATE users set alumnus_id = $aid where id = $uid ");
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}
	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['settings'][$key] = $value;
		}

			return 1;
				}
	}

	
	function save_plan(){
		extract($_POST);
		$data = " plan = '$plan' ";
		$data .= ", amount = '$amount' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO plans set $data");
			}else{
				$save = $this->db->query("UPDATE plans set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_plan(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM plans where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_package(){
		extract($_POST);
		$data = " package = '$package' ";
		$data .= ", description = '$description' ";
		$data .= ", amount = '$amount' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO packages set $data");
			}else{
				$save = $this->db->query("UPDATE packages set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_package(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM packages where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_trainer(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", rate = '$rate' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO trainers set $data");
			}else{
				$save = $this->db->query("UPDATE trainers set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_trainer(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM trainers where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_member() {
		extract($_POST);
		$data = '';
	
		// Collect POST data for other fields
		foreach ($_POST as $k => $v) {
			if (!empty($v)) {
				if (!in_array($k, array('id', 'plan_id', 'package_id', 'trainer_id'))) {
					if (empty($data)) {
						$data .= " $k='{$v}' ";
					} else {
						$data .= ", $k='{$v}' ";
					}
				}
			}
		}
	
		// Handle image upload
		if (isset($_FILES['pic-file']) && $_FILES['pic-file']['error'] == UPLOAD_ERR_OK) {
			$upload_dir = 'uploads/';
			$file_name = basename($_FILES['pic-file']['name']);
			$file_path = $upload_dir . $file_name;
		
			// Ensure the upload directory exists
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}
		
			// Move the uploaded file to the upload directory
			if (move_uploaded_file($_FILES['pic-file']['tmp_name'], $file_path)) {
				$data .= ", picture='$file_name'"; // Include the filename in the database
			} else {
				echo "Error uploading the file.";
				exit;
			}
		}
		// Generate a unique member ID if not provided
		if (empty($member_id)) {
			$i = 1;
			while ($i == 1) {
				$rand = mt_rand(1, 99999999);
				$rand = sprintf("%'08d", $rand);
				$chk = $this->db->query("SELECT * FROM members WHERE member_id = '$rand' ")->num_rows;
				if ($chk <= 0) {
					$data .= ", member_id='$rand' ";
					$i = 0;
				}
			}
		}
	
		// Insert or update member information
		if (empty($id)) {
			// Check for duplicate member ID and handle insertion
			if (!empty($member_id)) {
				$chk = $this->db->query("SELECT * FROM members WHERE member_id = '$member_id' ")->num_rows;
				if ($chk > 0) {
					return 2;
					exit;
				}
			}
			$save = $this->db->query("INSERT INTO members SET $data");
			if (!$save) {
				echo "Error: " . $this->db->error;  // Debug output
				return;
			}
			if ($save) {
				$member_id = $this->db->insert_id;
				$data = " member_id ='$member_id' ";
				$data .= ", plan_id ='$plan_id' ";
				$data .= ", package_id ='$package_id' ";
				$data .= ", trainer_id ='$trainer_id' ";
				$data .= ", start_date ='".date("Y-m-d")."' ";
				$plan = $this->db->query("SELECT * FROM plans WHERE id = $plan_id")->fetch_array()['plan'];
				$data .= ", end_date ='".date("Y-m-d", strtotime(date('Y-m-d') . ' +' . $plan . ' months')) . "' ";
				$save = $this->db->query("INSERT INTO registration_info SET $data");
				if (!$save)
					$this->db->query("DELETE FROM members WHERE id = $member_id");
			}
		} else {
			// Handle update for existing member
			if (!empty($member_id)) {
				$chk = $this->db->query("SELECT * FROM members WHERE member_id = '$member_id' AND id != $id ")->num_rows;
				if ($chk > 0) {
					return 2;
					exit;
				}
			}
			$save = $this->db->query("UPDATE members SET $data WHERE id=" . $id);
		}
	
		if ($save) {
			$this->generate_custom_qr_code($rand);
			return 1;
		}
	}
	
	
	
	function generate_custom_qr_code($member_id) {
		// Include the PHP QR Code library
		include('/opt/lampp/htdocs/gym-management-system/gym/phpqrcode/phpqrcode/qrlib.php'); // Adjust the path as needed
	
		$qrContent = $member_id;
		$qrFilePath = '/opt/lampp/htdocs/gym-management-system/gym/qr_codes/' . $member_id . '.png'; // Full path to save QR code
	
		// Generate the QR code as PNG
		QRcode::png($qrContent, $qrFilePath, QR_ECLEVEL_Q, 40);

		// Load the generated QR code image
		$qrImage = imagecreatefrompng($qrFilePath);
	
		// Load the logo and resize it
		$logoPath = '/opt/lampp/htdocs/gym-management-system/gym/images/logo.png'; // Path to your logo
		$logoImage = imagecreatefrompng($logoPath);
	
		$qrWidth = imagesx($qrImage);
		$logoSize = $qrWidth * 0.2; // Set logo size to 20% of QR code size
		$logoResized = imagecreatetruecolor($logoSize, $logoSize);
	
		// Enable transparency for the resized logo
		imagesavealpha($logoResized, true);
		$transparentColor = imagecolorallocatealpha($logoResized, 0, 0, 0, 127);
		imagefill($logoResized, 0, 0, $transparentColor);
	
		// Resize the logo image
		imagecopyresampled($logoResized, $logoImage, 0, 0, 0, 0, $logoSize, $logoSize, imagesx($logoImage), imagesy($logoImage));
	
		// Apply rounded corners to the logo
		$radius = $logoSize / 2; // Radius for the rounded effect
		for ($x = 0; $x < $logoSize; $x++) {
			for ($y = 0; $y < $logoSize; $y++) {
				// Calculate distance from the center
				$distance = sqrt(pow($x - $radius, 2) + pow($y - $radius, 2));
				if ($distance > $radius) {
					imagesetpixel($logoResized, $x, $y, $transparentColor); // Set pixel to transparent if outside radius
				}
			}
		}
	
		// Overlay the rounded logo onto the QR code
		$logoX = ($qrWidth / 2) - ($logoSize / 2);
		$logoY = ($qrWidth / 2) - ($logoSize / 2);
		imagecopy($qrImage, $logoResized, $logoX, $logoY, 0, 0, $logoSize, $logoSize);
	
		// Save the final image with the logo
		imagepng($qrImage, $qrFilePath);
	
		// Clean up memory
		imagedestroy($qrImage);
		imagedestroy($logoImage);
		imagedestroy($logoResized);
	
		return $qrFilePath;
	}
	
	function delete_member(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM members where id = ".$id);
		$infoDel = $this->db->query("UPDATE registration_info SET status = 0 WHERE member_id = ".$id);
		if($delete && $infoDel){
			return 1;
		}
		if(!$delete) {
			echo "Error: " . $this->db->error;  // Debug output
			return;
		}
	}
	function save_schedule(){
		extract($_POST);
		$data = " member_id = '$member_id' ";
		$data .= ", date_from = '{$date_from}-1' ";
		$data .= ", date_to = '".(date("Y-m-d",strtotime($date_to.'-1 +1 month -1 day')))."' ";
		$data .= ", time_from = '$time_from' ";
		$data .= ", time_to = '$time_to' ";
		$data .= ", dow = '".(implode(",",$dow))."'";

		if(empty($id)){

			$save = $this->db->query("INSERT INTO schedules set ".$data);
		}else{
			$save = $this->db->query("UPDATE schedules set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_schedule(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM schedules where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function get_schecdule(){
		extract($_POST);
		$data = array();
		$qry = $this->db->query("SELECT s.*,concat(m.lastname,',',m.firstname,' ', m.middlename) as name FROM schedules s inner join members m on m.id = s.member_id");
		while($row=$qry->fetch_assoc()){
			
			$data[] = $row;
		}
			return json_encode($data);
	}
	function save_payment(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k=> $v){
			if(!empty($v)){
				if(empty($data))
				$data .= " $k='{$v}' ";
				else
				$data .= ", $k='{$v}' ";
			}
		}
			$save = $this->db->query("INSERT INTO payments set ".$data);
		if($save)
			return 1;
	}
	function renew_membership(){
		extract($_POST);
		$prev = $this->db->query("SELECT * FROM registration_info where id = $rid")->fetch_array();
		$data = '';
		foreach($prev as $k=> $v){
			if(!empty($v) && !is_numeric($k) && !in_array($k,array('id','start_date','end_date','date_created'))){
				if(empty($data))
				$data .= " $k='{$v}' ";
				else
				$data .= ", $k='{$v}' ";
				$$k=$v;
			}
		}
				$data .= ", start_date ='".date("Y-m-d")."' ";
				$plan = $this->db->query("SELECT * FROM plans where id = $plan_id")->fetch_array()['plan'];
				$data .= ", end_date ='".date("Y-m-d",strtotime(date('Y-m-d').' +'.$plan.' months'))."' ";
				$save = $this->db->query("INSERT INTO registration_info set $data");
				if($save){
					$id = $this->db->insert_id;
					$this->db->query("UPDATE registration_info set status = 0 where member_id = $member_id and id != $id ");
					return $id;
				}

	}
	function end_membership(){
		extract($_POST);
		$update = $this->db->query("UPDATE registration_info set status = 0 where id = ".$rid);
		if($update){
			return 1;
		}
	}
	
	function save_membership(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k=> $v){
		if(!empty($v)){
			if(empty($data))
			$data .= " $k='{$v}' ";
			else
			$data .= ", $k='{$v}' ";
			$$k=$v;
		}
	}
	$data .= ", start_date ='".date("Y-m-d")."' ";
	$plan = $this->db->query("SELECT * FROM plans where id = $plan_id")->fetch_array()['plan'];
	$data .= ", end_date ='".date("Y-m-d",strtotime(date('Y-m-d').' +'.$plan.' months'))."' ";
	$save = $this->db->query("INSERT INTO registration_info set $data");
	if($save){
		$id = $this->db->insert_id;
		$this->db->query("UPDATE registration_info set status = 0 where member_id = $member_id and id != $id ");
		return 1;
	}
	}
}