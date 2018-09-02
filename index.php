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
--Login--
*
*
*
*
*/

if( $_GET['action'] == 'Login' ){

	$email = $_POST['input_email'];
	$password = $_POST['input_password'];
	
	$query = "select * from public.users where email = '".$email."'";
	$result = pg_query($conn, $query);
	$num_rows = pg_num_rows($result);
	
	if( $num_rows == 0 ){
	
		$json['function_result'] = 0;
		$json['system_message'] = 'Login gagal. Pastikan username dan password Anda benar.';
		
	} else {
	
		$row = pg_fetch_assoc($result);
		$validpassword = $row['password'];
		
		$value = $password;
		$passwordlaravel = $validpassword;
		$password = password_hash($value, PASSWORD_BCRYPT, [10]);
		$result = password_verify($value, $passwordlaravel);
		//var_dump($result );
		
		if( $result ){
			
			$json['function_result'] = 1;
			$json['system_message'] = 'Login berhasil.';
			
			$json['id'] = $row['id'];
			$json['name'] = $row['name'];
			$json['email'] = $row['email'];
			$json['password'] = $row['password'];
			$json['roles'] = $row['roles'];
			$json['id_rs'] = $row['id_rs'];
			$json['image'] = $row['image'];
			
			
		} else {
			
			$json['function_result'] = 0;
			$json['system_message'] = 'Login gagal. Pastikan username dan password Anda benar.';
			
		}
		
	}
	
	echo json_encode($json);
	
}

?>