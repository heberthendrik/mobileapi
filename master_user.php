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
--User--
*
*
*
*
*/

if( $_GET['action'] == 'DisplayAllUser' ){

	$query = 
	"
	SELECT 
		*
	FROM 
		public.users
	";
	$result = pg_query($conn, $query);
	
	$i=0;
	while($row = pg_fetch_assoc($result)){
		$i++;
		
		$json['display_content'] .= 
		'
		<div class="col-lg-12" style="padding:0;">
			<section class="card card-featured-left card-featured-primary mb-4">
				<div class="card-body">
					<div class="widget-summary">
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">'.$row['email'].'</h4>
								<div class="info">
									<strong class="amount">'.$row['name'].'</strong>
								</div>
							</div>
							<div class="summary-footer">
								<a href="master_ruang_detail.html?id='.$row['id'].'" class="text-muted text-uppercase">( Lihat Detail )</a>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		';
		
	}
	
	$json['function_result'] = 1;

	echo json_encode($json);

}

else if( $_GET['action'] == 'DisplayAllUserDetail' ){

	$id = $_POST['id'];
	
	$queryrs = "SELECT * FROM public.tab_rs ORDER BY id ASC ";
	$resultrs = pg_query($conn, $queryrs);
	$numrs = pg_num_rows($resultrs);
	while( $rowrs = pg_fetch_assoc($resultrs) ){
		
		$array_rs['id'][] = $rowrs['id'];
		$array_rs['nama'][] = $rowrs['nama'];
		
	}
	
	$query = "SELECT * FROM public.users where id = '".$id."' ";
	$result = pg_query($conn, $query);
	while($row = pg_fetch_assoc($result)){
	
		if( $row['status'] == 0 ){
			$status_indicator_0 = ' selected ';
			$status_indicator_1 = '';
		} else if( $row['status'] == 1 ){
			$status_indicator_0 = '';
			$status_indicator_1 = ' selected ';
		} 
		
		
		if( $row['roles'] == 'admin' ){
			$roles_admin = ' selected ';
			$roles_user = '';
			$roles_officer = '';
		} else if( $row['roles'] == 'user' ){
			$roles_admin = '';
			$roles_user = ' selected ';
			$roles_officer = '';
		} else if( $row['roles'] == 'officer' ){
			$roles_admin = '';
			$roles_user = '';
			$roles_officer = ' selected ';
		}
		
		$json['display_content'] .= 
		'
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama User *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_namauser" value="'.$row['name'].'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Email *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_email" value="'.$row['email'].'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Password *</label>
			<div class="col-lg-6">
				<input type="password" class="form-control" id="input_password" >
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Roles *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_roles">
					<option value="" >--Pilih Roles--</option>
					<option value="admin" '.$roles_admin.' >Admin</option>
					<option value="user" '.$roles_user.' >User</option>
					<option value="officer" '.$roles_officer.' >Officer</option>
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Rumah Sakit *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_idrs">
					<option value="">--Pilih Rumah Sakit--</option>
		';
					
		for($i=0;$i<$numrs;$i++){
		
			if( $array_rs['id'][$i] == $row['id_rs'] ){
				$rs_indicator = ' selected ';
			} else {
				$rs_indicator = '';
			}
		
			$json['display_content'] .= 
			'
			<option value="'.$array_rs['id'][$i].'" '.$rs_indicator.' >'.$array_rs['nama'][$i].'</option>
			';
		}
		
		$json['display_content'] .= 
		'
				</select>
			</div>
		</div>
		
		';
	
	}
	
	$json['function_result'] = 1;

	echo json_encode($json);

}

else if( $_GET['action'] == 'DisplayUserFormAdd' ){

	$queryrs = "SELECT * FROM public.tab_rs ORDER BY id ASC ";
	$resultrs = pg_query($conn, $queryrs);
	$numrs = pg_num_rows($resultrs);
	while( $rowrs = pg_fetch_assoc($resultrs) ){
		
		$array_rs['id'][] = $rowrs['id'];
		$array_rs['nama'][] = $rowrs['nama'];
		
	}
	
	
	$json['display_content'] .= 
	'
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama User *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_namauser" value="'.$row['nama'].'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Email *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_email" value="'.$row['email'].'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Password *</label>
		<div class="col-lg-6">
			<input type="password" class="form-control" id="input_password" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Roles *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_roles">
				<option value="" >--Pilih Roles--</option>
				<option value="admin">Admin</option>
				<option value="user">User</option>
				<option value="officer">Officer</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Rumah Sakit *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_idrs">
				<option value="">--Pilih Rumah Sakit--</option>
	';
				
	for($i=0;$i<$numrs;$i++){
	
		if( $array_rs['id'][$i] == $row['id_rs'] ){
			$rs_indicator = ' selected ';
		} else {
			$rs_indicator = '';
		}
	
		$json['display_content'] .= 
		'
		<option value="'.$array_rs['id'][$i].'" '.$rs_indicator.' >'.$array_rs['nama'][$i].'</option>
		';
	}
	
	$json['display_content'] .= 
	'
			</select>
		</div>
	</div>
	
	';
	
	
	
	$json['function_result'] = 1;

	echo json_encode($json);

}

else if( $_GET['action'] == 'UpdateUser' ){

	$id = $_POST['id'];
	$namauser = $_POST['namauser'];
	$email = $_POST['email'];
	$password = password_hash($_POST['password'], PASSWORD_BCRYPT, [10]);
	$roles = $_POST['roles'];
	$idrs = $_POST['idrs'];
	
	$query = 
	"
	update public.users set 
	name = '".$namauser."'
	, email = '".$email."'
	, roles = '".$roles."'
	, password = '".$password."'
	, id_rs = '".$idrs."'
	, updated_at = '".date('Y-m-d H:i:s')."'
	where id = '".$id."'
	";
	$result = pg_query($conn, $query);

	if (!$result) {
		$json['function_result'] = 0;
		$json['system_message'] = 'Update gagal. Mohon hubungi administrator.';
	} else {
		$json['function_result'] = 1;
		$json['system_message'] = 'Update berhasil.';
	}
	
	echo json_encode($json);

}

else if( $_GET['action'] == 'AddUser' ){

	$id = $_POST['id'];
	$namauser = $_POST['namauser'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$roles = $_POST['roles'];
	$idrs = $_POST['idrs'];
	
	$query_check = "select * from public.users where email = '".$email."' and id_rs = '".$idrs."'";
	$result_check = pg_query($conn, $query_check);
	$num_rows_check = pg_num_rows($result_check);
	
	if( $num_rows_check > 0 ){
		
		$json['function_result'] = 0;
		$json['system_message'] = 'Input gagal. Email yang sama telah digunakan.';
		
	} else {
	
		$value = $password;
		$encryptedpassword = password_hash($value, PASSWORD_BCRYPT, [10]);
		
		$query = "
		insert into public.users
		(name, email, password, roles, id_rs, created_at)
		values
		(
		'".$namauser."',
		'".$email."',
		'".$encryptedpassword."',
		'".$roles."',
		'".$idrs."',
		'".date('Y-m-d H:i:s')."'
		)
		";
		$result = pg_query($conn, $query);
		
		if (!$result) {
			$json['function_result'] = 0;
			$json['system_message'] = 'Input gagal. Mohon hubungi administrator.';
		} else {
			$json['function_result'] = 1;
			$json['system_message'] = 'Input berhasil.';
		}	
		
	}
	
	echo json_encode($json);
	
}



?>