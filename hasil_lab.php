<?php
header('Access-Control-Allow-Origin: *');
include('../../library/function_list.php');
date_default_timezone_set("Asia/Bangkok");

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
--HasilLab--
*
*
*
*
*/

if( $_GET['action'] == 'DisplayAllHasilLab' ){

	$query = "SELECT * FROM tab_lab_master";
	$result = pg_query($conn, $query);
	
	$i=0;
	while($row = pg_fetch_assoc($result)){
		$i++;
		
		$idlab = $row['id'];
		
		$query_getprosesvalidasi = "select SUM(status::int) as jumlah_parameter, SUM(kd_acc::int) as jumlah_acc from tab_lab_detil where id_master = '".$idlab."'";
		$result_getprosesvalidasi = pg_query($conn, $query_getprosesvalidasi);
		$row_getprosesvalidasi = pg_fetch_assoc($result_getprosesvalidasi);
		$total_parameter = $row_getprosesvalidasi['jumlah_parameter'];
		$acc_parameter = $row_getprosesvalidasi['jumlah_acc'];
		
		if( $total_parameter > 0 ){
			$display_parameter = $total_parameter;
		} else {
			$display_parameter = 0;
		}
		
		if( $acc_parameter > 0 ){
			$display_acc_parameter = $acc_parameter;
		} else {
			$display_acc_parameter = 0;
		}
		
		$tanggal_timsetamp = $row['created_at'];
		$display_tanggalan = date("d F Y", strtotime($tanggal_timsetamp));
		
		$json['display_content'] .= 
		'
		<div class="col-lg-12" style="padding:0;">
			<section class="card card-featured-left card-featured-primary mb-4">
				<div class="card-body">
					<div class="widget-summary">
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">'.$row['nama'].' - <span style="display:block;" class="text-primary">('.$display_tanggalan.')</span></h4>
								<div class="info">
									<strong class="amount">'.$row['no_lab'].'</strong>
								</div>
							</div>
							<div class="summary-footer">
								<a href="hasil_lab_detail.html?id='.$row['id'].'" class="text-muted text-uppercase">( Lihat Detail )</a>
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

else if( $_GET['action'] == 'DisplayAllHasilLabDetail' ){

	$id = $_POST['id'];
	
	$queryrs = "SELECT * FROM public.tab_rs ORDER BY id ASC ";
	$resultrs = pg_query($conn, $queryrs);
	$numrs = pg_num_rows($resultrs);
	while( $rowrs = pg_fetch_assoc($resultrs) ){
		
		$array_rs['id'][] = $rowrs['id'];
		$array_rs['nama'][] = $rowrs['nama'];
		
	}
	
	$query = "SELECT * FROM public.tab_hasillab where id = '".$id."' ";
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
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama HasilLab *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_namahasillab" value="'.rtrim($row['nama']).'">
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

else if( $_GET['action'] == 'DisplayHasilLabFormAdd' ){

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
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tanggal *</label>
		<div class="col-lg-6">
			<input type="date" class="form-control" id="input_tanggal" value="'.date('Y-m-d').'" disabled >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">No. Rekam medis *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_norm" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_nama" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tgl lahir *</label>
		<div class="col-lg-6">
			<input type="date" class="form-control" id="input_tgllahir" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Jenis Kelamin *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_jeniskelamin">
				<option value="">--Pilih Jenis Kelamin--</option>
				<option value="L" >Laki-Laki</option>
				<option value="P" >Perempuan</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Alamat *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_alamat" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Ruang *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_idruang">
				<option value="">--Pilih Ruang--</option>
				';
	
				$query_loopruang = "select * from tab_ruang";
				$result_loopruang = pg_query($conn, $query_loopruang);
				while( $row_loopruang = pg_fetch_assoc($result_loopruang) ){
					
					$json['display_content'] .= '<option value="'.$row_loopruang['id'].'">'.$row_loopruang['nama'].'</option>';
					
				}
	
	$json['display_content'] .= '
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Kelas *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_idkelas">
				<option value="">--Pilih Kelas--</option>
				';
	
				$query_loopkelas = "select * from tab_kelas";
				$result_loopkelas = pg_query($conn, $query_loopkelas);
				while( $row_loopkelas = pg_fetch_assoc($result_loopkelas) ){
					
					$json['display_content'] .= '<option value="'.$row_loopkelas['id'].'">'.$row_loopkelas['nama'].'</option>';
					
				}
	
	$json['display_content'] .= '
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Status *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_idstatus">
				<option value="">--Pilih Status--</option>
				';
	
				$query_loopstatus = "select * from tab_status";
				$result_loopstatus = pg_query($conn, $query_loopstatus);
				while( $row_loopstatus = pg_fetch_assoc($result_loopstatus) ){
					
					$json['display_content'] .= '<option value="'.$row_loopstatus['id'].'">'.$row_loopstatus['nama'].'</option>';
					
				}
	
	$json['display_content'] .= '
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Dr. Pengirim *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_iddokter">
				<option value="">--Pilih Status--</option>
				';
	
				$query_loopdokter = "select * from tab_dokter";
				$result_loopdokter = pg_query($conn, $query_loopdokter);
				while( $row_loopdokter = pg_fetch_assoc($result_loopdokter) ){
					
					$json['display_content'] .= '<option value="'.$row_loopdokter['id'].'">'.$row_loopdokter['nama'].'</option>';
					
				}
	
	$json['display_content'] .= '
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Alamat Dokter</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_alamatdokter" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Ket Klinik</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_ketklinik" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Catatan 1</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_catatan1" >
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Catatan 2</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_catatan2" >
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

else if( $_GET['action'] == 'UpdateHasilLab' ){

	$id = $_POST['id'];
	$nama = $_POST['nama'];
	$status = $_POST['status'];
	$kode = $_POST['kode'];
	$idrs = $_POST['idrs'];
	
	$query = 
	"
	update public.tab_hasillab set 
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

else if( $_GET['action'] == 'AddHasilLab' ){

	$id = $_POST['id'];
	$norm = $_POST['input_norm'];
	$nama = $_POST['input_nama'];
	$tgllahir = $_POST['input_tgllahir'];
	$jeniskelamin = $_POST['input_jeniskelamin'];
	$alamat = $_POST['input_alamat'];
	$idruang = $_POST['input_idruang'];
	$idkelas = $_POST['input_idkelas'];
	$status = $_POST['input_idstatus'];
	$iddrpengirim = $_POST['input_iddokter'];
	$alamatdokter = $_POST['input_alamatdokter'];
	$ketklinik = $_POST['input_ketklinik'];
	$catatan1 = $_POST['input_catatan1'];
	$catatan2 = $_POST['input_catatan2'];
	$idrs = $_POST['input_idrs'];
	
	$datetime1 = new DateTime('1988-03-10');
	$datetime2 = new DateTime(date('Y-m-d'));
	$interval = $datetime1->diff($datetime2);
	$usia = $interval->format('%y Tahun %m Bulan and %d Hari');
	$usia_round = $interval->format('%y');
	
	$query_getmaxid = "select id as lastid from tab_lab_master order by id desc";
	$result_getmaxid = pg_query($conn, $query_getmaxid);
	$row_getmaxid = pg_fetch_assoc($result_getmaxid);
	$maxid = $row_getmaxid['lastid'];
	
	if( substr($maxid, 0, 6) == date('ymd') ){
		$idsubstring = date('ymd');
		$substring_lastid = substr($maxid, 6, 4);
		$substring_newid = $substring_lastid+1;
		
		if( strlen($substring_newid) == 1 ){
			$substring_newid = '000'.$substring_newid;
		} else if( strlen($substring_newid) == 2 ){
			$substring_newid = '00'.$substring_newid;
		} else if( strlen($substring_newid) == 3 ){
			$substring_newid = '0'.$substring_newid;
		} else if( strlen($substring_newid) == 4 ){
			$substring_newid = $substring_newid;
		} 
		
		$newid = $idsubstring.$substring_newid;
	} else {
		$newid = date('ymd').'0001';
	}
	
	
	
	$query_getnmruang = "select * from tab_ruang where id = '".$idruang."'";
	$result_getnmruang = pg_query($conn, $query_getnmruang);
	$row_getnmruang = pg_fetch_assoc($result_getnmruang);
	$display_nmruang = $row_getnmruang['nama'];
	
	$query_getnmkelas = "select * from tab_kelas where id = '".$idkelas."'";
	$result_getnmkelas = pg_query($conn, $query_getnmkelas);
	$row_getnmkelas = pg_fetch_assoc($result_getnmkelas);
	$display_nmkelas = $row_getnmkelas['nama'];
	
	$query_getnmstatus = "select * from tab_status where id = '".$status."'";
	$result_getnmstatus = pg_query($conn, $query_getnmstatus);
	$row_getnmstatus = pg_fetch_assoc($result_getnmstatus);
	$display_nmstatus = $row_getnmstatus['nama'];
	
	$query_getnmdokter = "select * from tab_dokter where id = '".$iddrpengirim."'";
	$result_getnmdokter = pg_query($conn, $query_getnmdokter);
	$row_getnmdokter = pg_fetch_assoc($result_getnmdokter);
	$display_nmdokter = $row_getnmdokter['nama'];
	
	$query = "
	insert into public.tab_lab_master
	(
	id,
	no_lab,
	no_rm,
	umur,
	umur_sat,
	usia,
	nama,
	sex,
	alamat,
	tgl_lahir,
	id_ruang,
	nm_ruang,
	id_kelas,
	nm_kelas,
	id_status,
	nm_status,
	id_dokter,
	nm_dokter,
	alamat_dokter,
	ket_klinik,
	catatan_1,
	catatan_2,
	id_pengentri,
	nm_pengentri,
	id_pemeriksa,
	nm_pemeriksa,
	dt_pemeriksa,
	id_dokter_acc,
	nm_dokter_acc,
	kd_acc,
	dt_acc,
	dt_print,
	id_rs,
	created_at,
	kd_pemeriksa
	)
	values
	(
	'".$newid."',
	'".$newid."',
	'".$norm."',
	'".$usia_round."',
	'Tahun',
	'".$usia."',
	'".$nama."',
	'".$jeniskelamin."',
	'".$alamat."',
	'".$tgllahir."',
	'".$idruang."',
	'".$display_nmruang."',
	'".$idkelas."',
	'".$display_nmkelas."',
	'".$status."',
	'".$display_nmstatus."',
	'".$iddrpengirim."',
	'".$display_nmdokter."',
	'".$alamatdokter."',
	'".$ketklinik."',
	'".$catatan1."',
	'".$catatan2."',
	null,
	null,
	null,
	null,
	null,
	null,
	null,
	null,
	null,
	null,
	'".$idrs."',
	'".date('Y-m-d H:i:s')."',
	null
	)
	";
	//$result = pg_query($conn, $query);
	
	$json['query'] = $query;
	
	if (!$result) {
		$json['function_result'] = 0;
		$json['system_message'] = 'Input gagal. Mohon hubungi administrator.';
	} else {
		$json['function_result'] = 1;
		$json['system_message'] = 'Input berhasil.';
	}	
	
	echo json_encode($json);
	
}



?>