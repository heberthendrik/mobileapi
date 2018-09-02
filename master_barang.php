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
--Barang--
*
*
*
*
*/

if( $_GET['action'] == 'DisplayAllBarang' ){

	$query = 
	"
	SELECT 
		t1.id as id, 
		t1.name as nama,
		t1.katalog as katalog,
		t1.id_kategori as kategori,
		t1.status as status, 
		t2.nama as namars,
		t3.nama as namakat
	FROM 
		public.tab_barang t1 
		left join public.tab_rs t2 on t2.id = t1.id_rs
		left join public.tab_kategori t3 on t3.id = t1.id_kategori
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
								<h4 class="title">'.$row['namars'].' - <span style="display:block;" class="text-primary">('.$row['namakat'].')</span></h4>
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

else if( $_GET['action'] == 'DisplayAllBarangDetail' ){

	$id = $_POST['id'];
	
	$queryrs = "SELECT * FROM public.tab_rs ORDER BY id ASC ";
	$resultrs = pg_query($conn, $queryrs);
	$numrs = pg_num_rows($resultrs);
	while( $rowrs = pg_fetch_assoc($resultrs) ){
		
		$array_rs['id'][] = $rowrs['id'];
		$array_rs['nama'][] = $rowrs['nama'];
		
	}
	
	$querykategori = "SELECT * FROM public.tab_kategori ORDER BY id ASC ";
	$resultkategori = pg_query($conn, $querykategori);
	$numkategori = pg_num_rows($resultkategori);
	while( $rowkategori = pg_fetch_assoc($resultkategori) ){
		
		$array_kategori['id'][] = $rowkategori['id'];
		$array_kategori['nama'][] = $rowkategori['nama'];
		
	}
	
	$querymerk = "SELECT * FROM public.tab_merk ORDER BY id ASC ";
	$resultmerk = pg_query($conn, $querymerk);
	$nummerk = pg_num_rows($resultmerk);
	while( $rowmerk = pg_fetch_assoc($resultmerk) ){
		
		$array_merk['id'][] = $rowmerk['id'];
		$array_merk['nama'][] = $rowmerk['nama'];
		
	}
	
	$querysatuan = "SELECT * FROM public.tab_satuan ORDER BY id ASC ";
	$resultsatuan = pg_query($conn, $querysatuan);
	$numsatuan = pg_num_rows($resultsatuan);
	while( $rowsatuan = pg_fetch_assoc($resultsatuan) ){
		
		$array_satuan['id'][] = $rowsatuan['id'];
		$array_satuan['nm_satuan'][] = $rowsatuan['nm_satuan'];
		
	}
	
	$query = "SELECT * FROM public.tab_barang where id = '".$id."' ";
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
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama Barang *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_namabarang" value="'.rtrim($row['name']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Satuan *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_satuan">
					<option value="">--Pilih Satuan--</option>
		';
		
		for( $i=0;$i<$numsatuan;$i++ ){
		
			if( $row['id_satuan'] == $array_satuan['id'][$i] ){
				$selected_satuan = ' selected ';
			} else {
				$selected_satuan = '';
			}
			
			$json['display_content'] .= 
			'
			<option value="'.rtrim($array_satuan['id'][$i]).'" '.$selected_satuan.'>'.$array_satuan['nm_satuan'][$i].'</option>
			';
			
		}
		
		$json['display_content'] .= 
		'
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Katalog *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_katalog" value="'.rtrim($row['katalog']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Kategori *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_idkategori">
					<option value="">--Pilih Kategori--</option>
		';
		
		for( $i=0;$i<$numkategori;$i++ ){
		
			if( $row['id_kategori'] == $array_kategori['id'][$i] ){
				$selected_kategori = ' selected ';
			} else {
				$selected_kategori = '';
			}
			
			$json['display_content'] .= 
			'
			<option value="'.$array_kategori['id'][$i].'" '.$selected_kategori.' >'.$array_kategori['nama'][$i].'</option>
			';
			
		}
		
		$json['display_content'] .= 
		'
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">ID Supplier *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_idsupplier" value="'.rtrim($row['id_supplier']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tgl Masuk *</label>
			<div class="col-lg-6">
				<input type="date" class="form-control" id="input_tglmasuk" value="'.rtrim($row['tgl_masuk']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Merk</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_merk">
					<option value="0">--Pilih Merk--</option>
		';
		
		for( $i=0;$i<$nummerk;$i++ ){
		
			if( $row['id_merk'] == $array_merk['id'][$i] ){
				$selected_merk = ' selected ';
			} else {
				$selected_merk = '';
			}
			
			$json['display_content'] .= 
			'
			<option value="'.$array_merk['id'][$i].'" '.$selected_merk.' >'.$array_merk['nama'][$i].'</option>
			';
			
		}
		
		$json['display_content'] .= 
		'
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tipe *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_tipe" value="'.rtrim($row['tipe']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">ID Principal *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_idprincipal" value="'.rtrim($row['id_principal']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Harga Perolehan *</label>
			<div class="col-lg-6">
				<input type="number" class="form-control" id="input_hargaperolehan" value="'.rtrim($row['hrg_perolehan']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Harga Jual *</label>
			<div class="col-lg-6">
				<input type="number" class="form-control" id="input_hargajual" value="'.rtrim($row['hrg_jual']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Status *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_status">
					<option value="0">Tidak Aktif</option>
					<option value="1">Aktif</option>
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Komputer *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_komputer" value="'.rtrim($row['komputer']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">User *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_user" value="'.rtrim($row['xuser']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tgl Entri *</label>
			<div class="col-lg-6">
				<input type="date" class="form-control" id="input_tglentri" value="'.rtrim($row['tgl_entri']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Diskonv *</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="input_diskonv" value="'.rtrim($row['diskonv']).'">
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-lg-3 control-label text-lg-right pt-2">Rumah Sakit *</label>
			<div class="col-lg-6">
				<select class="form-control mb-3" id="input_idrs">
					<option value="0">--Pilih Rumah Sakit--</option>
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

else if( $_GET['action'] == 'DisplayBarangFormAdd' ){

	$querykategori = "SELECT * FROM public.tab_kategori ORDER BY id ASC ";
	$resultkategori = pg_query($conn, $querykategori);
	$numkategori = pg_num_rows($resultkategori);
	while( $rowkategori = pg_fetch_assoc($resultkategori) ){
		
		$array_kategori['id'][] = $rowkategori['id'];
		$array_kategori['nama'][] = $rowkategori['nama'];
		
	}
	
	$querymerk = "SELECT * FROM public.tab_merk ORDER BY id ASC ";
	$resultmerk = pg_query($conn, $querymerk);
	$nummerk = pg_num_rows($resultmerk);
	while( $rowmerk = pg_fetch_assoc($resultmerk) ){
		
		$array_merk['id'][] = $rowmerk['id'];
		$array_merk['nama'][] = $rowmerk['nama'];
		
	}
	
	$querysatuan = "SELECT * FROM public.tab_satuan ORDER BY id ASC ";
	$resultsatuan = pg_query($conn, $querysatuan);
	$numsatuan = pg_num_rows($resultsatuan);
	while( $rowsatuan = pg_fetch_assoc($resultsatuan) ){
		
		$array_satuan['id'][] = $rowsatuan['id'];
		$array_satuan['nm_satuan'][] = $rowsatuan['nm_satuan'];
		
	}

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
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Nama Barang *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_namabarang" value="'.rtrim($row['name']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Satuan *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_satuan">
				<option value="">--Pilih Satuan--</option>
	';
	
	for( $i=0;$i<$numsatuan;$i++ ){
	
		if( $row['id_satuan'] == $array_satuan['id'][$i] ){
			$selected_satuan = ' selected ';
		} else {
			$selected_satuan = '';
		}
		
		$json['display_content'] .= 
		'
		<option value="'.rtrim($array_satuan['id'][$i]).'" '.$selected_satuan.'>'.$array_satuan['nm_satuan'][$i].'</option>
		';
		
	}
	
	$json['display_content'] .= 
	'
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Katalog *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_katalog" value="'.rtrim($row['katalog']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Kategori *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_idkategori">
				<option value="">--Pilih Kategori--</option>
	';
	
	for( $i=0;$i<$numkategori;$i++ ){
	
		if( $row['id_kategori'] == $array_kategori['id'][$i] ){
			$selected_kategori = ' selected ';
		} else {
			$selected_kategori = '';
		}
		
		$json['display_content'] .= 
		'
		<option value="'.$array_kategori['id'][$i].'" '.$selected_kategori.' >'.$array_kategori['nama'][$i].'</option>
		';
		
	}
	
	$json['display_content'] .= 
	'
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">ID Supplier *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_idsupplier" value="'.rtrim($row['id_supplier']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tgl Masuk *</label>
		<div class="col-lg-6">
			<input type="date" class="form-control" id="input_tglmasuk" value="'.rtrim($row['tgl_masuk']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Merk</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_merk">
				<option value="0">--Pilih Merk--</option>
	';
	
	for( $i=0;$i<$nummerk;$i++ ){
	
		if( $row['id_merk'] == $array_merk['id'][$i] ){
			$selected_merk = ' selected ';
		} else {
			$selected_merk = '';
		}
		
		$json['display_content'] .= 
		'
		<option value="'.$array_merk['id'][$i].'" '.$selected_merk.' >'.$array_merk['nama'][$i].'</option>
		';
		
	}
	
	$json['display_content'] .= 
	'
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tipe *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_tipe" value="'.rtrim($row['tipe']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">ID Principal *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_idprincipal" value="'.rtrim($row['id_principal']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Harga Perolehan *</label>
		<div class="col-lg-6">
			<input type="number" class="form-control" id="input_hargaperolehan" value="'.rtrim($row['hrg_perolehan']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Harga Jual *</label>
		<div class="col-lg-6">
			<input type="number" class="form-control" id="input_hargajual" value="'.rtrim($row['hrg_jual']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Status *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_status">
				<option value="0">Tidak Aktif</option>
				<option value="1">Aktif</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Komputer *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_komputer" value="'.rtrim($row['komputer']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">User *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_user" value="'.rtrim($row['xuser']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Tgl Entri *</label>
		<div class="col-lg-6">
			<input type="date" class="form-control" id="input_tglentri" value="'.rtrim($row['tgl_entri']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2" for="inputDefault">Diskonv *</label>
		<div class="col-lg-6">
			<input type="text" class="form-control" id="input_diskonv" value="'.rtrim($row['diskonv']).'">
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-lg-3 control-label text-lg-right pt-2">Rumah Sakit *</label>
		<div class="col-lg-6">
			<select class="form-control mb-3" id="input_idrs">
				<option value="0">--Pilih Rumah Sakit--</option>
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

else if( $_GET['action'] == 'UpdateBarang' ){

	$id = $_POST['id'];
	$nama = $_POST['nama'];
	$satuan = $_POST['satuan'];
	$katalog = $_POST['katalog'];
	$kategori = $_POST['kategori'];
	$idsupplier = $_POST['idsupplier'];
	$tglmasuk = $_POST['tglmasuk'];
	$merk = $_POST['merk'];
	$tipe = $_POST['tipe'];
	$idprincipal = $_POST['idprincipal'];
	$hargaperolehan = $_POST['hargaperolehan'];
	$hargajual = $_POST['hargajual'];
	$status = $_POST['status'];
	$komputer = $_POST['komputer'];
	$user = $_POST['user'];
	$tglentri = $_POST['tglentri'];
	$diskonv = $_POST['diskonv'];
	$idrs = $_POST['idrs'];
	
	$query = 
	"
	update public.tab_barang 
	SET 
	name = '".$nama."'
	,id_satuan = '".$satuan."'
	,katalog = '".$katalog."'
	,id_kategori = '".$kategori."'
	,id_supplier = '".$idsupplier."'
	,tgl_masuk = '".$tglmasuk."'
	,id_merk = '".$merk."'
	,tipe = '".$tipe."'
	,id_principal = '".$idprincipal."'
	,hrg_perolehan = '".$hargaperolehan."'
	,hrg_jual = '".$hargajual."'
	,status = '".$status."'
	,komputer = '".$komputer."'
	,tgl_entri = '".$tglentri."'
	,diskonv = '".$diskonv."'
	,xuser = '".$user."'
	,id_rs = '".$idrs."'
	,updated_at = '".date("Y-m-d H:i:s")."'
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
	
	$json['query'] = $query;
	
	echo json_encode($json);

}

else if( $_GET['action'] == 'AddBarang' ){

	$nama = $_POST['nama'];
	$satuan = $_POST['satuan'];
	$katalog = $_POST['katalog'];
	$kategori = $_POST['kategori'];
	$idsupplier = $_POST['idsupplier'];
	$tglmasuk = $_POST['tglmasuk'];
	$merk = $_POST['merk'];
	$tipe = $_POST['tipe'];
	$idprincipal = $_POST['idprincipal'];
	$hargaperolehan = $_POST['hargaperolehan'];
	$hargajual = $_POST['hargajual'];
	$status = $_POST['status'];
	$komputer = $_POST['komputer'];
	$user = $_POST['user'];
	$tglentri = $_POST['tglentri'];
	$diskonv = $_POST['diskonv'];
	$idrs = $_POST['idrs'];
	
	$query_check = "select * from public.tab_barang where nama = '".$nama."' and id_rs = '".$idrs."'";
	$result_check = pg_query($conn, $query_check);
	$num_rows_check = pg_num_rows($result_check);
	
	if( $num_rows_check > 0 ){
		
		$json['function_result'] = 0;
		$json['system_message'] = 'Input gagal. Nama yang sama telah digunakan.';
		
	} else {
		
		$query = "
		insert into public.tab_barang
		(
		name,
		id_satuan,
		katalog,
		id_kategori,
		id_supplier,
		tgl_masuk,
		id_merk,
		tipe,
		id_principal,
		hrg_perolehan,
		hrg_jual,
		status,
		komputer,
		xuser,
		tgl_entri,
		diskonv,
		id_rs,
		created_at
		)
		values
		(
		'".$nama."',
		'".$satuan."',
		'".$katalog."',
		'".$kategori."',
		'".$idsupplier."',
		'".$tglmasuk."',
		'".$merk."',
		'".$tipe."',
		'".$idprincipal."',
		'".$hargaperolehan."',
		'".$hargajual."',
		'".$status."',
		'".$komputer."',
		'".$user."',
		'".$tglentri."',
		'".$diskonv."',
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
		
		$json['query'] = $query;
		
	}
	
	echo json_encode($json);
	
}



?>