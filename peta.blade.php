<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Dinas Kesehatan - Bootstrap Admin Theme</title>

  <!-- Bootstrap Core CSS -->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

  <!-- MetisMenu CSS -->
  <link href="{{ asset('assets/css/metisMenu.min.css') }}" rel="stylesheet">

  <!-- Timeline CSS -->
  <link href="{{ asset('assets/css/timeline.css') }}" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="{{ asset('assets/css/startmin.css') }}" rel="stylesheet">

  <!-- Morris Charts CSS -->
  <link href="{{ asset('assets/css/morris.css') }}" rel="stylesheet">

  <!-- Custom Fonts -->
  <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">



  <title>Data Layer: Simple</title>
  <meta name="viewport" content="initial-scale=1.0">
  <meta charset="utf-8">
  <style>
      /* Always set the map height explicitly to define the size of the div
      * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
    
  </head>
  <body>


    <div id="wrapper">

      <!-- Page Content -->
      <div id="wrapper">
        <div class="container">

          <div class="row">
            <div class="col-lg-12">
              <h1 class="page-header">SIG ACI</h1>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              
              <label for="form-control">Tahun </label>
              <select name="tahun" id="tahun">
                <option value="2014">2014</option>
                <option value="2015">2015</option>
                <option value="2016">2016</option>
                <option value="2017">2017</option>
              </select>

              <label for="form-control">Bulan </label>
              <select name="bulan" id="bulan">
                <option value="Januari">Januari</option>
                <option value="Februari">Februari</option>
                <option value="Maret">Maret</option>
                <option value="04">April</option>
                <option value="05">Mei</option>
                <option value="06">Juni</option>
                <option value="07">Juli</option>
                <option value="08">Agustus</option>
                <option value="09">Septemer</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
              </select>
              <button class="btn btn-sm btn-success" id="btn-pilih" > Pilih</button>
              <br>
              <br>
              <div class="col-lg-12" id="box-keterangan">
              </div>
              <br>
              <br>
              <div class="col-lg-12" id="box-keterangan-kecamatan">
              </div>
              <br>
              <br>
              <div class="col-lg-12">
                <body style="font-family: Arial; border: 0 none;">
                  <div id = "map" style = "width:1000px; height:500px;"></div>


                  <!-- jQuery -->
                  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

                  <!-- Bootstrap Core JavaScript -->
                  <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

                  <!-- Metis Menu Plugin JavaScript -->
                  <script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>

                  <!-- Custom Theme JavaScript -->
                  <script src="{{ asset('assets/js/startmin.js') }}"></script>

                  <script src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBt6a6dy99jZcyrlIe7OghOsZ0khO1x4O8&callback=initMap"></script>

                  <script>

                    var map;
                    function initMap(dataLaporan) {
                      map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 12,
                        center: {lat: -5.39714, lng: 105.266789}

                      });

                      map.data.setStyle(function(feature) {
          console.log(feature.getProperty('id'));  
          console.log(dataLaporan.laporan_kecamatan[feature.getProperty('id')]); 
          
          var opacity = 0.8;            
          if(dataLaporan.laporan_kecamatan[feature.getProperty('id')] == null){
              // var color = dataLaporan.kelas[0].color;
              var color = "#ffffff";
            } else{
              var color = dataLaporan.laporan_kecamatan[feature.getProperty('id')].kelas.color;
            }
          // var color = 'rgba(255,0,0,0.9)';
          return /** @type {google.maps.Data.StyleOptions} */({
            fillColor: color,
            fillOpacity: opacity,
            strokeColor: color,
            strokeOpacity: opacity,
            strokeWeight: 3
          });
        });

        // When the user clicks, set 'isColorful', changing the color of the letters.
        map.data.addListener('click', function(event) {
          // event.feature.setProperty('isColorful', true);
          console.log(dataLaporan.laporan_kecamatan[event.feature.getProperty('id')]);
          console.log(event.feature);

        });

        map.data.addListener('mouseover', function(event) {
          var data = dataLaporan.laporan_kecamatan[event.feature.getProperty('id')];
          if(data == null){
            $("#box-keterangan-kecamatan").text("Kecamatan "+event.feature.getProperty('nama_kec')+" | Jumlah = 0 Penderita");            
          }else{
            $("#box-keterangan-kecamatan").text("Kecamatan "+event.feature.getProperty('nama_kec')+" | Jumlah = "+data.jumlah+" Penderita");            
          }
        });

        map.data.addListener('mouseout', function(event) {
          // map.data.revertStyle();
        });

        map.data.loadGeoJson("{{ route('json') }}");

      }

      var id_penyakit = $('#id_penyakit').val();
      var tahun = $('#tahun').val();
      var bulan = $('#bulan').val();
      var postUrl = "{{ route('api') }}";
      var postData = 'tahun='+tahun+'&bulan='+bulan;
      


      $.ajax({
       type: "GET",
       url: postUrl,
       data: postData,
       success: function(response){
         var jsonReponse = jQuery.parseJSON(response);   
         console.log(jsonReponse.kelas);
         var arrayLength = jsonReponse.kelas.length;
         for (var i = 0; i < arrayLength; i++) {
          $("#box-keterangan").append("<div class='col-md-2' style='background-color:"+jsonReponse.kelas[i].color+"' >"+jsonReponse.kelas[i].min+" - "+jsonReponse.kelas[i].max+"</div>");
          console.log(jsonReponse.kelas[i]);
                    //Do something
                  }
                  initMap(jsonReponse);
                }
              });

      $("#btn-pilih").click(function(){
        var id_penyakit = $('#id_penyakit').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var postUrl = "{{ route('api') }}";
        var postData = 'tahun='+tahun+'&bulan='+bulan;

        $.ajax({
         type: "GET",
         url: postUrl,
         data: postData,
         success: function(response){
           var jsonReponse = jQuery.parseJSON(response); 
           var arrayLength = jsonReponse.kelas.length;
           $("#box-keterangan").html('');
           for (var i = 0; i < arrayLength; i++) {
            $("#box-keterangan").append("<div class='col-md-2' style='background-color:"+jsonReponse.kelas[i].color+"' >"+jsonReponse.kelas[i].min+" - "+jsonReponse.kelas[i].max+"</div>");
            console.log(jsonReponse.kelas[i]);
                        //Do something
                      }  
                      console.log(jsonReponse.laporan_kecamatan);
                      initMap(jsonReponse);
                    }
                  });
      });

    </script>



  </body>
  </html>