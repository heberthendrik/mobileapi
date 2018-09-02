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
--Petugas--
*
*
*
*
*/

if( $_GET['action'] == 'DisplayAllPetugas' ){

	$query = 
	"
	SELECT 
		t1.id as id, 
		t1.nama as nama,
		t1.status as status, 
		t1.kode as kode,
		t2.nama as namars
	FROM 
		public.tab_petugas t1 
		left join public.tab_rs t2 on t1.id_rs = t2.id
	";
	$result = pg_query($conn, $query);
		
	$i=0;
	while($row = pg_fetch_assoc($result)){
		$i++;
		
		if( strlen($row['kode']) > 0 ){
			
			$display_kode = '- <span style="display:block;" class="text-primary">('.$row['kode'].')</span>';
			
		} else {
			$display_kode = '';
		}
		
		$json['display_content'] .= 
		'
		<div class="col-lg-12" style="padding:0;">
			<section class="card card-featured-left card-featured-primary mb-4">
				<div class="card-body">
					<div class="widget-summary">
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">'.$row['nama'].' '.$display_kode.'</h4>
								<div class="info">
									<strong class="amount">'.$row['nama'].'</strong>
								</div>
							</div>
							<div class="summary-footer">
								<a href="master_petugas_detail.html?id='.$row['id'].'" class="text-muted text-uppercase">( Lihat Detail )</a>
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

else if( $_GET['action'] == 'DisplayAllPetugasDetail' ){

	$id = $_POST['id'];
	
	$queryrs = "SELECT * FROM public.tab_rs ORDER BY id ASC ";
	$resultrs = pg_query($conn, $queryrs);
	$numrs = pg_num_rows($resultrs);
	while( $rowrs = pg_fetch_assoc($resultrs) ){
		
		$array_rs['id'][] = $rowrs['id'];
		$array_rs['nama'][] = $rowrs['nama'];
		
	}
	
	$query = "SELECT * FROM public.tab_petugas where id = '".$id."' ";
	$result = pg_query($conn, $query);
	while($row = pg_fetch_assoc($result)){
	
		if( $row['status'] == 0 ){
			$status_indicator_0 = ' selected ';
			$status_indicator_1 = '';
		} else if( $row['status'] == 1 ){
			$status_indicator_0 = '';
			$status_indicator_1 = ' selected ';
		} 
		
		$json['display_content'] .= 
		'
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama Petugas *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_namapetugas" value="'.rtrim($row['nama']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Status *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_status">
					<option value="0" '.$status_indicator_0.'>Tidak Aktif</option>
					<option value="1" '.$status_indicator_1.'>Aktif</option>
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Kode *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_kode" value="'.rtrim($row['kode']).'">
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

else if( $_GET['action'] == 'DisplayPetugasFormAdd' ){

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
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama Petugas *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_namapetugas" value="'.$row['nama'].'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Status *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_status">
				<option value="0" >Tidak Aktif</option>
				<option value="1" >Aktif</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Kode *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_kode" value="'.$row['nama'].'">
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

else if( $_GET['action'] == 'UpdatePetugas' ){

	$id = $_POST['id'];
	$nama = $_POST['nama'];
	$status = $_POST['status'];
	$kode = $_POST['kode'];
	$idrs = $_POST['idrs'];
	
	$query = 
	"
	update public.tab_petugas set 
	nama = '".$nama."'
	, status = '".$status."'
	, kode = '".$kode."'
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

else if( $_GET['action'] == 'AddPetugas' ){

	$id = $_POST['id'];
	$nama = $_POST['nama'];
	$status = $_POST['status'];
	$kode = $_POST['kode'];
	$idrs = $_POST['idrs'];
	
	$query_check = "select * from public.tab_petugas where nama = '".$nama."' and id_rs = '".$idrs."'";
	$result_check = pg_query($conn, $query_check);
	$num_rows_check = pg_num_rows($result_check);
	
	if( $num_rows_check > 0 ){
		
		$json['function_result'] = 0;
		$json['system_message'] = 'Input gagal. Nama yang sama telah digunakan.';
		
	} else {
		
		$query = "
		insert into public.tab_petugas
		(nama, status, kode, id_rs, created_at)
		values
		(
		'".$nama."',
		'".$status."',
		'".$kode."',
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