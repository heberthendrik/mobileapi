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
--Pasien--
*
*
*
*
*/

if( $_GET['action'] == 'DisplayAllPasien' ){

	$query = 
	"
	SELECT 
		t1.id as id, 
		t1.nama as nama,
		t1.alamat as alamat,
		t1.no_rm as no_rm,
		t2.nama as namars
	FROM 
		public.tab_customer t1 
		left join public.tab_rs t2 on t1.id_rs = t2.id
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
								<h4 class="title">'.$row['namars'].' - <span style="display:block;" class="text-primary">('.$row['no_rm'].')</span></h4>
								<div class="info">
									<strong class="amount">'.$row['nama'].'</strong>
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

else if( $_GET['action'] == 'DisplayAllPasienDetail' ){

	$id = $_POST['id'];
	
	$queryrs = "SELECT * FROM public.tab_rs ORDER BY id ASC ";
	$resultrs = pg_query($conn, $queryrs);
	$numrs = pg_num_rows($resultrs);
	while( $rowrs = pg_fetch_assoc($resultrs) ){
		
		$array_rs['id'][] = $rowrs['id'];
		$array_rs['nama'][] = $rowrs['nama'];
		
	}
	
	$query = "SELECT * FROM public.tab_customer where id = '".$id."' ";
	$result = pg_query($conn, $query);
	while($row = pg_fetch_assoc($result)){
	
		if( $row['status'] == 0 ){
			$status_indicator_0 = ' selected ';
			$status_indicator_1 = '';
		} else if( $row['status'] == 1 ){
			$status_indicator_0 = '';
			$status_indicator_1 = ' selected ';
		} 
		
		
		if( $row['sex'] == "L" ){
			$sex_indicator_L = ' selected ';
			$sex_indicator_P = '';
		} else if( $row['sex'] == "P" ){
			$sex_indicator_L = '';
			$sex_indicator_P = ' selected ';
		} 
		
		$json['display_content'] .= 
		'
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama Pasien *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_namapasien" value="'.$row['nama'].'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Alamat *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_alamat" value="'.$row['alamat'].'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">No. Rekam Medis *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_norm" value="'.$row['no_rm'].'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Jenis Kelamin *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_jeniskelamin">
					<option value="">--Pilih Jenis Kelamin--</option>
					<option value="L" '.$sex_indicator_L.' >Laki-laki</option>
					<option value="P" '.$sex_indicator_P.' >Perempuan</option>
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tgl Lahir *</label>
			<div class="col-lg-6">
				<input type="date" class="form-control" id="input_tgllahir" value="'.$row['tgl_lahir'].'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Status *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_status">
					<option value="0" '.$status_indicator_0.' >Tidak Aktif</option>
					<option value="1" '.$status_indicator_1.' >Aktif</option>
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

else if( $_GET['action'] == 'DisplayPasienFormAdd' ){

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
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama Pasien *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_namapasien" value="'.$row['nama'].'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Alamat *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_alamat" value="'.$row['nama'].'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">No. Rekam Medis *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_norm" value="'.$row['nama'].'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Jenis Kelamin *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_jeniskelamin">
				<option value="">--Pilih Jenis Kelamin--</option>
				<option value="L" >Laki-laki</option>
				<option value="P" >Perempuan</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tgl Lahir *</label>
		<div class="col-lg-6">
			<input type="date" class="form-control" id="input_tgllahir" value="'.$row['nama'].'">
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

else if( $_GET['action'] == 'UpdatePasien' ){

	$id = $_POST['id'];
	$nama = $_POST['nama'];
	$alamat = $_POST['alamat'];
	$norm = $_POST['norm'];
	$jenis_kelamin = $_POST['jeniskelamin'];
	$idrs = $_POST['idrs'];
	$status = $_POST['status'];
	$tgllahir = $_POST['tgllahir'];
	
	$query = 
	"
	update public.tab_customer set 
	nama = '".$nama."'
	, alamat = '".$alamat."'
	, no_rm = '".$norm."'
	, sex = '".$jenis_kelamin."'
	, id_rs = '".$idrs."'
	, status = '".$status."'
	, tgl_lahir = '".$tgllahir."'
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

else if( $_GET['action'] == 'AddPasien' ){

	$id = $_POST['id'];
	$nama = $_POST['nama'];
	$alamat = $_POST['alamat'];
	$norm = $_POST['norm'];
	$jenis_kelamin = $_POST['jeniskelamin'];
	$idrs = $_POST['idrs'];
	$status = $_POST['status'];
	$tgllahir = $_POST['tgllahir'];
	
	$query_check = "select * from public.tab_customer where nama = '".$nama."' and id_rs = '".$idrs."'";
	$result_check = pg_query($conn, $query_check);
	$num_rows_check = pg_num_rows($result_check);
	
	if( $num_rows_check > 0 ){
		
		$json['function_result'] = 0;
		$json['system_message'] = 'Input gagal. Nama yang sama telah digunakan.';
		
	} else {
		
		$query = "
		insert into public.tab_customer
		(nama, alamat, no_rm, sex, id_rs, status, tgl_lahir, created_at)
		values
		(
		'".$nama."',
		'".$alamat."',
		'".$norm."',
		'".$jenis_kelamin."',
		'".$idrs."',
		'".$status."',
		'".$tgllahir."',
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