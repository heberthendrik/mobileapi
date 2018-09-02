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
--Rumah Sakit--
*
*
*
*
*/

if( $_GET['action'] == 'DisplayAllRumahSakit' ){

	$query = 
	"
	SELECT 
		*
	FROM 
		public.tab_rs
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
								<h4 class="title">&nbsp;</h4>
								<div class="info">
									<strong class="amount">'.$row['nama'].'</strong>
								</div>
							</div>
							<div class="summary-footer">
								<a href="master_rumahsakit_detail.html?id='.$row['id'].'" class="text-muted text-uppercase">( Lihat Detail )</a>
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

else if( $_GET['action'] == 'DisplayAllRumahSakitDetail' ){

	$id = $_POST['id'];
	
	$query = "SELECT * FROM public.tab_rs where id = '".$id."' ";
	$result = pg_query($conn, $query);
	while($row = pg_fetch_assoc($result)){
	
		$json['display_content'] .= 
		'
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_namarumahsakit" value="'.rtrim($row['nama']).'">
			</div>
		</div>
		
		';
	
	}
	
	$json['function_result'] = 1;

	echo json_encode($json);

}

else if( $_GET['action'] == 'DisplayRumahSakitFormAdd' ){

	$json['display_content'] .= 
	'
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama Rumah Sakit *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_namarumahsakit" value="'.$row['nama'].'">
		</div>
	</div>
	
	';
	
	
	
	$json['function_result'] = 1;

	echo json_encode($json);

}

else if( $_GET['action'] == 'UpdateRumahSakit' ){

	$id = $_POST['id'];
	$nama = $_POST['namarumahsakit'];
	$link = $_POST['link'];
	
	$query = 
	"
	update public.tab_rs set 
	nama = '".$nama."'
	, link = '".$link."'
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

else if( $_GET['action'] == 'AddRumahSakit' ){

	$id = $_POST['id'];
	$nama = $_POST['namarumahsakit'];
	$link = $_POST['link'];
	
	$query_check = "select * from public.tab_rs where nama = '".$nama."' ";
	$result_check = pg_query($conn, $query_check);
	$num_rows_check = pg_num_rows($result_check);
	
	if( $num_rows_check > 0 ){
		
		$json['function_result'] = 0;
		$json['system_message'] = 'Input gagal. Nama yang sama telah digunakan.';
		
	} else {
		
		$query = "
		insert into public.tab_rs
		(nama, link, created_at)
		values
		(
		'".$nama."',
		'".$link."',
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