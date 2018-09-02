<?php
header('Access-Control-Allow-Origin: *');
include('../../library/function_list.php');

$conn = pg_pconnect("host=localhost port=5432 dbname=ehealth");
/*
if (!$conn) {
  echo "An error occurred.\n";
  exit;
}

$result = pg_query($conn, "SELECT * FROM public.users ORDER BY id ASC ");
if (!$result) {
  echo "An error occurred.\n";
  exit;
}

while ($row = pg_fetch_row($result)) {
  echo "Author: $row[0]  E-mail: $row[1]";
  echo "<br />\n";
}
*/



/*
*
*
*
*
--My Profile--
*
*
*
*
*/

if( $_GET['action'] == 'DisplayProfileInfo' ){

	$id = $_POST['id'];
	
	$query = "select * from public.tab_notifikasi where receiver = '".$id."' order by created_at desc";
	$result = pg_query($conn, $query);
	$num_rows = pg_num_rows($result);
	
	if( $num_rows == 0 ){
	
		$json['display_content'] .=
			'
			<li>
				<div class="tm-box">
					<p>Belum ada notifikasi.</p>
				</div>
			</li>
			';
			
		$json['function_result'] = 0;
		$json['system_message'] = 'Belum ada notifikasi.';
		
	} else {
	
		while( $row = pg_fetch_assoc($result) ){
			
			$json['function_result'] = 1;
			$json['system_message'] = 'Notifikasi berhasil ditampilkan.';
			
			$json['display_content'] .=
			'
			<li>
				<div class="tm-box">
					<p class="text-muted mb-0">'.date("d F Y", strtotime($row['created_at'])).'</p>
					<p>'.$row['text'].'</p>
				</div>
			</li>
			';
			
		}
	
	}
	
	echo json_encode($json);
	
}

if( $_GET['action'] == 'UpdatePersonalInfo' ){

	$id = $_POST['id'];
	$name = $_POST['input_name'];
	$email = $_POST['input_email'];
	$password = password_hash($_POST['input_password'], PASSWORD_BCRYPT, [10]);;
	
	$query_update = "update users set name = '".$name."', email = '".$email."', password = '".$password."' where id = '".$id."'";
	$result_update = pg_query($conn, $query_update);
	
	if( $result_update ){
		$json['function_result'] = 1;
		$json['system_message'] = 'Update berhasil.';
	} else {
		$json['function_result'] = 0;
		$json['system_message'] = 'Update gagal. Silahkan hubungi Administrator.';
	}
	
	$query_newdata = "select * from users where id = '".$id."'";
	$result_newdata = pg_query($conn, $query_newdata);
	$row_newdata = pg_fetch_assoc($result_newdata);
	$newname = $row_newdata['name'];
	$newemail = $row_newdata['email'];
	
	$json['newname'] = $newname;
	$json['newemail'] = $newemail;
	
	echo json_encode($json);

}



?>