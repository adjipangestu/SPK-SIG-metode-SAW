# SPK (Sistem Informasi Geografis) SIG metode SAW

## SCREENSHOT

![contoh](https://github.com/adjipangestu/SPK-SIG-metode-SAW/blob/master/Screenshot%20from%202019-10-09%2009-43-34.png)

## REST FULL API SAW 

```
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
```
