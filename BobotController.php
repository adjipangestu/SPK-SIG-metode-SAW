<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//untuk manggil database nya
use App\data_kepadatan;
use App\indeks_rtphbs;
use App\data_faskes;
use App\data_pasien;
use App\data_kecamatan;
use App\riwayat_pasien;
use DB;
use phpDocumentor\Reflection\Types\This;
use Storage;

class BobotController extends Controller
{
    public function penduduk() //mau bikin bobot untuk kepadatan penduduk
                                // di fungsi ini, di deklarasiin semua dulu
    {
        $data = data_kepadatan::all(); //manggil tabel data_kepadatan
        $arr = [];
        foreach ($data as $value) { 
            $padat = $value->kepadatan_penduduk;          //itung kepadatan penduudk
            $arr [] = [
                'kepadatan_penduduk' =>  $padat,        //panggil $hasil kepadatan yang udh dibuletin
                'bobot' => $this->bobot_padat($padat)   //panggil bobot dari fungsi dibawah (pake this)
            ];
        }
        return $arr; //keluarin kepadatan sama bobot nya
    }

    public function bobot_padat($hasil) //buat bobot untuk kepadatan penduduk ambil parameter $hasil dari fungsi atas
    {
        $data = $hasil;
        if($data >= 1 && $data  <= 2500){
            return 5;
        }else{
            if($data  >= 2501 && $data  <= 5000){
                return 4;
            }else {
                if ($data  >= 5001 && $data  <= 7500) {
                    return 3;
                }else {
                    if ($data  >= 7501 && $data  <=10000) {
                        return 2;
                    }else {
                        if ($data  > 10000) {
                            return 1;
                        }
                    }
                }
            }
        }
    }

    

    public function bobot_indeks ($id, $tahun) //buat bobot untuk indeks RTPHBS ambil parameter $rtphbs dari fungsi atas
    {
        $indeks = DB::table('indeks_rtphbs')    
                    ->leftjoin('data_kecamatan', 'data_kecamatan.id', '=', 'indeks_rtphbs.id_kecamatan')
                    // ->select('kepadatan_penduduk')
                    ->groupBy('indeks_rtphbs.id_kecamatan')
                    ->where('indeks_rtphbs.id_kecamatan', $id)
                    ->where('indeks_rtphbs.tahun', $tahun)
                    ->get();
        
        
        $collection = collect($indeks);
        $plucked = $collection->pluck('jumlah_rtphbs');
        $has = [];
        foreach ($plucked as $value) {
            $has  [] = [
                $value
            ];
        }
        if (!empty($has)) {
            $rtphbs = $has[0][0];
            if($rtphbs >= 0 && $rtphbs  <= 2000){
                return 1;
            }else{
                if($rtphbs >= 2001 && $rtphbs  <= 4000){
                    return 2;
                }else {
                    if ($rtphbs >= 4001 && $rtphbs  <= 6000) {
                        return 3;
                    }else {
                        if ($rtphbs >= 6001 && $rtphbs  <= 7000) {
                            return 4;
                        }else {
                            if ($rtphbs > 7000) {
                                return 5;
                            }
                        }
                    }
                }
            }
        } else {
            return 0;
        }
            
    }


    public function bobot_faskes($id,$tahun) //bikin bobot untuk jumlah faskes di tiap kecamatan
    {
        $faskess = DB::table('data_faskes')  
                    ->leftjoin('data_kecamatan', 'data_kecamatan.id', '=', 'data_faskes.id_kecamatan')
                    // ->select('kepadatan_penduduk')
                    ->groupBy('data_faskes.id_kecamatan')
                    ->where('data_faskes.id_kecamatan', $id)
                    ->where('data_faskes.tahun', $tahun)
                    //->get()
                    ->count();
        $fas=$faskess;
        if($fas == 0){
            return 1;
        }else{
            if($fas==1){
                return 2;
            }else {
                if ($fas==2) {
                    return 3;
                }else {
                    if ($fas==3) {
                        return 4;
                    }else {
                        if ($fas>3) {
                            return 5;
                        }
                    }
                }
            }
        }
    }


    public function bobot_kasusTB ($id,$tahun,$bulan) //buat bobot untuk kasus TB ambil parameter $jumlah_pasien dari fungsi atas
    {   

        $kasus = DB::table('riwayat_pasien')     
                    ->leftjoin('data_pasien', 'data_pasien.id', '=', 'riwayat_pasien.id_pasien')
                    ->leftjoin('data_kecamatan', 'data_kecamatan.id', '=', 'data_pasien.id_kecamatan')
                    ->select(DB::raw('count(riwayat_pasien.id_pasien) as jumlah_pasien'))
                    ->groupBy('data_pasien.id_kecamatan')
                    ->where('data_pasien.id_kecamatan', $id)
                    ->where('riwayat_pasien.tahun', $tahun)
                    ->where('riwayat_pasien.bulan', $bulan)
                    ->count();
        
        if($kasus >= 0 && $kasus  <= 5){
            return 5;
        }else{
            if($kasus >= 5 && $kasus  <= 10){
                return 4;
            }else {
                if ($kasus >= 10 && $kasus  <= 15) {
                    return 3;
                }else {
                    if ($kasus >= 15 && $kasus  <= 25) {
                        return 2;
                    }else {
                        if ($kasus > 25) {
                            return 1;
                        }
                    }
                }
            }
        }
    }

    public function bobotmati($id,$tahun,$bulan)
    {
        $mati = DB::table('riwayat_pasien') 
                    ->leftjoin('data_pasien', 'data_pasien.id', '=', 'riwayat_pasien.id_pasien')
                    ->leftjoin('data_kecamatan', 'data_kecamatan.id', '=', 'data_pasien.id_kecamatan')
                    ->select(DB::raw('count(riwayat_pasien.status) as status', 'status'))
                    ->where ('status', '=', 'Meninggal')
                    ->groupBy('data_pasien.id_kecamatan')
                    ->where('data_pasien.id_kecamatan', $id)
                    ->where('riwayat_pasien.tahun', $tahun)
                    ->where('riwayat_pasien.bulan', $bulan)
                    //->get()
                    ->count();

        if($mati >= 0 && $mati  <= 25){
            return 5;
        }else{
            if($mati >= 26 && $mati <= 50){
                return 4;
            }else {
                if ($mati >= 51 && $mati <= 75) {
                    return 3;
                }else {
                    if ($mati >= 76 && $mati  <= 100) {
                        return 2;
                    }else {
                        if ($mati > 100) {
                            return 1;
                        }
                    }
                }
            }
        }
    }

    public function kepadatan($id,$tahun)
    {
        $kasus = DB::table('data_kepadatan')     
                    ->leftjoin('data_kecamatan', 'data_kecamatan.id', '=', 'data_kepadatan.id_kecamatan')
                    // ->select('kepadatan_penduduk')
                    ->groupBy('data_kepadatan.id_kecamatan')
                    ->where('data_kepadatan.id_kecamatan', $id)
                    ->where('data_kepadatan.tahun', $tahun)
                    ->get();
        
        
        $collection = collect($kasus);
        $plucked = $collection->pluck('kepadatan_penduduk');
        $hasil = [];
        foreach ($plucked as $value) {
            $hasil  [] = [
                $value
            ];
        }
        if (!empty($hasil)) {
            $data = $hasil[0][0];
            if($data >= 1 && $data  <= 2500){
                return 1;
            }else{
                if($data  >= 2501 && $data  <= 5000){
                    return 2;
                }else {
                    if ($data  >= 5001 && $data  <= 7500) {
                        return 3;
                    }else {
                        if ($data  >= 7501 && $data  <=10000) {
                            return 4;
                        }else {
                            if ($data  > 10000) {
                                return 5;
                            }
                        }
                    }
                }
            }
        } else {
            return '0';
        }
    }

    public function Api(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $kecamatan = data_kecamatan ::all();
        $data=[];
        $laporan = [];

        foreach($kecamatan as $kec)
        {
            $data[] = [
                'id_kecamatan' => $kec->id,
                'kecamatan' => $kec->nama_kecamatan,
                'bobot_kasustb'=>$this->bobot_kasusTB($kec->id,$tahun,$bulan),
                'bobot_kepadatan' => 5,
                'bobot_kematian'=>$this->bobotmati($kec->id,$tahun,$bulan),
                'bobot_index' => $this->bobot_indeks($kec->id,$tahun),
                'bobot_faskes' => $this->bobot_faskes($kec->id,$tahun)
            ];
        }

        //aji buat
        $nilai_kasusTB = array_column($data, 'bobot_kasustb');
        $nilai_kepadatan = array_column($data, 'bobot_kepadatan');
        $nilai_kematian = array_column($data, 'bobot_kematian');
        $nilai_indeks = array_column($data, 'bobot_index');
        $nilai_faskes = array_column($data, 'bobot_faskes');

        //aji buat
        $min_kasusTB = min($nilai_kasusTB);
        $min_kepadatan = min($nilai_kepadatan);
        $min_kematian = min($nilai_kematian);
        $max_indeks = max($nilai_indeks);
        $max_faskes = max($nilai_faskes);

        $jumlah_kelas = 4;
        $warna_kelas = array('#2ecc71', '#f1c40f', '#f39c12', '#f94503');

        foreach ($data as $key => $da) {
            $laporan[] = [
                'id_kecamatan' => $da['id_kecamatan'],
                'nama_kecamatan' => $da['kecamatan'],
                'jumlah' => (($min_kasusTB / $da['bobot_kasustb'])*25) + 
                            (($min_kepadatan / $da['bobot_kepadatan'])*25) + 
                            (($min_kematian / $da['bobot_kematian'])*10) +
                            (($da['bobot_index'] / $max_indeks)*20) +
                            (($da['bobot_faskes'] / $max_faskes)*20)
            ];
        }

        $min = -1;
        $max = -1;
        $jumlah_kelas = 4;
        $warna_kelas = array('#2ecc71', '#f1c40f', '#f39c12', '#f94503');
        foreach ($laporan as $key => $value) {
            if($min == -1 && $max == -1){
                $min = $value['jumlah'];
                $max = $value['jumlah'];
            }
            if($value['jumlah'] > $max){
                $max = $value['jumlah'];
            }
            if($value['jumlah'] < $min){
                $min = $value['jumlah'];
            }
        }

        $response = array();
        $response['min'] = $min;
        $response['max'] = $max;
        $response['range'] = ($max-$min)/$jumlah_kelas;
        for($i=0; $i<$jumlah_kelas; $i++){
            $warna_kelas[$i] = array(
                "color" => $warna_kelas[$i],
                "min" => $min + (($i + 0) * $response['range']),
                "max" => $min + (($i + 1) * $response['range'])
                );
        }
        $response['kelas'] = $warna_kelas;
        $laporan_kecamatan = array();
        foreach ($laporan as $key => $value) {
                foreach ($response['kelas'] as $i => $kelas) {
                     if($kelas['min'] <= $value['jumlah'] && $value['jumlah'] < $kelas['max']){
                        $laporan_kecamatan[$value['id_kecamatan']] = array(
                                "id_kecamatan" => $value['id_kecamatan'],
                                "jumlah" => $value['jumlah'],
                                "kelas" => $kelas
                                );                        
                     }
                 } 
        }
        $response['laporan_kecamatan'] = $laporan_kecamatan;
        // return response()->json($response)->header("Access-Control-Allow-Origin",  "*");
        
        echo json_encode($response);
        // return $response;
    }


    public function matriks(Request $r)
    {
        $tahun = 2017;
        $bulan = "Maret";
        $kecamatan = data_kecamatan ::all();
        $data=[];
        $hasil=[];
        //$saw=[];

        foreach($kecamatan as $kec)
        {
            $data[] = [
                'id_kecamatan' => $kec->id,
                'kecamatan' => $kec->nama_kecamatan,
                'bobot_kasustb'=>$this->bobot_kasusTB($kec->id,$tahun,$bulan),
                'bobot_kepadatan' => $this->kepadatan($kec->id,$tahun),
                'bobot_kematian'=>$this->bobotmati($kec->id,$tahun,$bulan),
                'bobot_index' => $this->bobot_indeks($kec->id,$tahun),
                'bobot_faskes' => $this->bobot_faskes($kec->id,$tahun)
            ];
        }

        //aji buat
        $nilai_kasusTB = array_column($data, 'bobot_kasustb');
        $nilai_kepadatan = array_column($data, 'bobot_kepadatan');
        $nilai_kematian = array_column($data, 'bobot_kematian');
        $nilai_indeks = array_column($data, 'bobot_index');
        $nilai_faskes = array_column($data, 'bobot_faskes');
        
        //aji buat
        $min_kasusTB = min($nilai_kasusTB);
        $min_kepadatan = min($nilai_kepadatan);
        $min_kematian = min($nilai_kematian);
        $max_indeks = max($nilai_indeks);
        $max_faskes = max($nilai_faskes);

        return view('peta')->with([
            'data' => $data,
            'min_kasusTB' => $min_kasusTB,
            'min_kepadatan' => $min_kepadatan,
            'min_kematian' => $min_kematian,
            'max_indeks' => $max_indeks,
            'max_faskes' => $max_faskes,
        ]);


    }

    public function json()
    {

        $jsonString = file_get_contents(base_path('resources/lang/en/peta.geojson'));
        $data = json_decode($jsonString, true);

        return $data;
        # code...
    }

}
