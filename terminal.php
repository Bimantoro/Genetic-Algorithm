<?php
  //Tugas KOMEV Danang Aji Bimantoro
//  $persamaan="5a*2b/4c=10";//"1a+2b+3c+4d=30";

  print("\n TUGAS KOMEV : DANANG AJI BIMANTORO | terminal version \n");
  print("===================================================== \n \n");

  print("Masukkan persamaan : ");
  $persamaan = read_keyboard();

  print("Masukkan jumlah Individu : ");
  $jumlah_individu=read_keyboard();

  print("Daftar Crossover : \n");
  print("[1] Uniform \n");
  print("[2] Blend \n");
  print("[3] Uniform CTM \n");
  print("[4] Blend CTM \n");
  print("[5] Uniform ODD \n");
  print("[6] Blend ODD \n");
  print("Masukkan pilihan crossover(number) : ");
  $xover=read_keyboard();

  if($xover%2!=0){
    print("Masukkan Probability of Crossover : ");
    $pc=read_keyboard();
  }else{
    print("Masukkan alpha : ");
    $alpha=read_keyboard();
  }

  print("Masukkan Probability of Mutation : ");
  $pm=read_keyboard();

  $jumlah_gen=get_jml_gen($persamaan);
  // $pc=0.9;
  // $pm=0.1;
  // $alpha=0.5;

  $var = get_variable($persamaan);

  //mengambil nilai konstanta :
  $kons = get_konstanta($persamaan);

  //mengambil operator yang digunakan :
  $opr = get_operator($persamaan);


  //==BERIKUT INI ADALAH FUNGSI YANG SAYA BUAT==//

  //fungsi untuk membaca input keyboard di CLI
  function read_keyboard(){
    $fr=fopen("php://stdin","r");
    $input = fgets($fr,128);
    $input = rtrim($input);

    return $input;
  }

  function multiexplode ($delimiters,$string) {
      $ready = str_replace($delimiters, $delimiters[0], $string);
      $launch = explode($delimiters[0], $ready);
      return  $launch;
  }

  //fungsi minimasi dari persamaan (a+2b+3c+4d=30)
  function get_jml_gen($persamaan){
    $temp = explode("=",$persamaan);
    $temp = multiexplode(array("+","-","*","/"),$temp[0]);
    $g = count($temp);
    return $g;
  }

  function get_operator($persamaan){
    $temp = explode("=",$persamaan);
    $temp2 = multiexplode(array("+","-","*","/"),$temp[0]);
    $jml_var = count($temp2);
    for ($i=0; $i < $jml_var-1; $i++) {
      $cut=explode($temp2[$i],$temp[0]);
      $opr[$i]=substr($cut[1],0,1);
    }
    return $opr;
  }

  function get_konstanta($persamaan){
    $tmp = explode("=",$persamaan);
    $tmp = multiexplode(array("+","-","*","/"),$tmp[0]);
    $jml_var = count($tmp);

    for ($i=0; $i < $jml_var; $i++) {
      $kons[$i]=$tmp[$i]*1;
    }
    return $kons;
  }

  function get_variable($persamaan){
    $tmp = explode("=",$persamaan);
    $tmp = multiexplode(array("+","-","*","/"),$tmp[0]);
    $jml_var = count($tmp);

    for ($i=0; $i < $jml_var; $i++) {
      $kons[$i]=$tmp[$i]*1;
      $t_var = explode($kons[$i],$tmp[$i]);
      $var[$i]=$t_var[1];
    }
    return $var;
  }

  function f_obj($x,$persamaan){
    $tmp = explode("=",$persamaan);
    $target = $tmp[1]*1;

    $tmp = multiexplode(array("+","-","*","/"),$tmp[0]);
    $jml_var = count($x);

    for ($i=0; $i < $jml_var; $i++) {
      $kons[$i]=$tmp[$i]*1;
    }

    $operator = get_operator($persamaan);

    $hasil=0;
    for ($i=0; $i < $jml_var; $i++) {
      $value[$i]=$x[$i]*$kons[$i];
      if ($i==0) {
        $hasil+=$value[$i];
      }else{
        $temporary = $hasil.$operator[$i-1].$value[$i];
        $hasil = eval('return '.$temporary.';');
      }

    }

    $minimasi = abs($hasil-$target);
    return $minimasi;
  }

  //fungsi untuk menghitung nilai fitness dari individu :
  function hitung_fitness($individu,$persamaan){
        $fitness = 1/(1+f_obj($individu,$persamaan));
    return $fitness;
    }

  //fungsi untuk membangkitkan populasi awal :
  function pembangkitan_populasi($jumlah_individu,$persamaan){
    $jumlah_gen = get_jml_gen($persamaan);
    $tmp = explode("=",$persamaan);
    $operator = get_operator($persamaan);
    $jml_opr = count($operator);
    $kali=1;
    for ($i=0; $i < $jml_opr; $i++) {
      if($operator[$i]=="-" || $operator[$i]=="/"){
          $kali++;
      }
    }
    for($i=0; $i<$jumlah_individu; $i++){
      for($j=0; $j<$jumlah_gen; $j++){
        $awal[$i][$j]=rand(0,($tmp[1]*$kali));
      }
    }
    return $awal;
  }

  //fungsi untuk melakukan seleksi individu :
  function seleksi_rws($individu,$persamaan){
    $jumlah_individu=count($individu);
    $total_fitness=0;

    //menghitung total fitness
    for ($i=0; $i < $jumlah_individu ; $i++) {
      $total_fitness+=hitung_fitness($individu[$i],$persamaan);
    }

    //menhitung probabilitas tiap individu
    for ($i=0; $i < $jumlah_individu; $i++) {
      $probabilitas[$i]=hitung_fitness($individu[$i],$persamaan)/$total_fitness;
    }

    $r=rand(0,10)/10;
    $i=0;
    $sum=$probabilitas[$i];
    while($sum<$r){
      $i++;
      $sum+=$probabilitas[$i];
      if($i==$jumlah_individu-1){
        break;
      }
    }

    return $individu[$i];
  }

  function elitisme_xover($silang,$jumlah_individu,$persamaan){
    $pjg_silang = count($silang);
    for ($i=0; $i < $pjg_silang-1; $i++) {
      for ($j=0; $j < $pjg_silang-1; $j++) {
        if(hitung_fitness($silang[$j],$persamaan) < hitung_fitness($silang[$j+1],$persamaan)){
          $tmp = $silang[$j];
          $silang[$j]=$silang[$j+1];
          $silang[$j+1]=$tmp;
        }
      }
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      $tersilang[$i]=$silang[$i];
    }

    return $tersilang;
  }

  //fungsi untuk crossover_uniform :
  function crossover_uniform($induk1,$induk2,$pc){
    $anak1=$induk1;
    $anak2=$induk2;
    $jumlah_gen=count($anak1);
    for ($i=0; $i < $jumlah_gen; $i++) {
      $r = rand(0,10)/10;
      if($r<=$pc){
        $tmp=$anak1[$i];
        $anak1[$i]=$anak2[$i];
        $anak2[$i]=$tmp;
      }
    }
    $hasil[0]=$anak1;
    $hasil[1]=$anak2;

    return $hasil;
  }

  //crossover blend :
  function crossover_blend($induk1,$induk2,$alpha){

    $jumlah_gen = count($induk1);
    $alpha2 = 1 - $alpha;

    for ($i=0; $i < $jumlah_gen; $i++) {
      $anak1[$i] = ($alpha*$induk1[$i]) + ($alpha2*$induk2[$i]);
      $anak2[$i] = ($alpha*$induk2[$i]) + ($alpha2*$induk1[$i]);
    }

    $hasil[0]=$anak1;
    $hasil[1]=$anak2;

    return $hasil;
  }

  //fungsi mutasi :
  function mutasi($induk,$pm,$persamaan){
    $tmp = explode("=",$persamaan);
    $jumlah_gen=count($induk);
    $operator = get_operator($persamaan);
    $jml_opr = count($operator);
    $kali=1;
    for ($i=0; $i < $jml_opr; $i++) {
      if($operator[$i]=="-" || $operator[$i]=="/"){
          $kali++;
      }
    }
    for ($i=0; $i < $jumlah_gen; $i++) {
      $r = rand(0,10)/10;
      if($r <= $pm){
        $induk[$i]=rand(0,($tmp[1]*$kali));
      }
    }

    return $induk;
  }

  //fungsi elitisme union
  function elitisme_union($seleksi,$tersilang,$termutasi,$persamaan){
    $jumlah_individu=count($seleksi);
    //penggabungan :
    for ($i=0; $i < $jumlah_individu; $i++) {
      $union[$i]=$seleksi[$i];
      $union[$i+$jumlah_individu]=$tersilang[$i];
      $union[$i+($jumlah_individu*2)]=$termutasi[$i];
    }

    //proses shorting :
    for ($i=0; $i < ($jumlah_individu*3)-1; $i++) {
      for ($j=0; $j < ($jumlah_individu*3)-1; $j++) {
        if(hitung_fitness($union[$j],$persamaan) < hitung_fitness($union[$j+1],$persamaan)){
          $tmp = $union[$j];
          $union[$j]=$union[$j+1];
          $union[$j+1]=$tmp;
        }
      }
    }

    //seleksi individu terbaik :
    for ($i=0; $i < $jumlah_individu; $i++) {
      $terbaik[$i]=$union[$i];
    }

    return $terbaik;
  }

  print( "PEMBANGKITAN POPULASI AWAL : \n \n");
  $populasi_awal = pembangkitan_populasi($jumlah_individu,$persamaan);
  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$populasi_awal[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($populasi_awal[$i],$persamaan));
    print( "\n");
  }

  print( "\n");
  print( "GENERASI KE 1");
  print( "\n");

  print( "SELEKSI RWS : \n \n");
  for ($i=0; $i < $jumlah_individu; $i++) {
    $seleksi[$i]=seleksi_rws($populasi_awal,$persamaan);
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$seleksi[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($seleksi[$i],$persamaan));
    print( "\n");
  }

  print( "\n");

  print( "CROSSOVER : \n \n");
  if($xover==1){
    for ($i=0; $i < $jumlah_individu-1; $i+=2) {
      $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
      $tersilang[$i] = $turunan[0];
      $tersilang[$i+1] = $turunan[1];
    }
  }else if($xover==2){
    for ($i=0; $i < $jumlah_individu-1; $i+=2) {
      $turunan = crossover_blend($seleksi[$i],$seleksi[$i+1],$alpha);
      $tersilang[$i] = $turunan[0];
      $tersilang[$i+1] = $turunan[1];
    }
  }else if($xover==3){
    for ($i=0; $i < $jumlah_individu/2; $i++) {
      $turunan = crossover_uniform($seleksi[$i],$seleksi[($jumlah_individu-1)-$i],$pc);
      $tersilang[$i] = $turunan[0];
      $tersilang[($jumlah_individu-1)-$i] = $turunan[1];
    }
  }else if($xover==4){
    for ($i=0; $i < $jumlah_individu/2; $i++) {
      $turunan = crossover_blend($seleksi[$i],$seleksi[($jumlah_individu-1)-$i],$alpha);
      $tersilang[$i] = $turunan[0];
      $tersilang[($jumlah_individu-1)-$i] = $turunan[1];
    }
  }else if($xover==5){
    $a=0;
    for ($i=0; $i < $jumlah_individu-1; $i++) {
      $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
      $silang[$a] = $turunan[0];
      $silang[$a+1] = $turunan[1];
      $a++;
      // $tersilang[$i] = $turunan[0];
      // $tersilang[$i+1] = $turunan[1];
    }
    $tersilang = elitisme_xover($silang,$jumlah_individu,$persamaan);
  }else{
    $a=0;
    for ($i=0; $i < $jumlah_individu-1; $i++) {
      $turunan = crossover_blend($seleksi[$i],$seleksi[$i+1],$alpha);
      $silang[$a] = $turunan[0];
      $silang[$a+1] = $turunan[1];
      $a++;
      // $tersilang[$i] = $turunan[0];
      // $tersilang[$i+1] = $turunan[1];
    }
    $tersilang = elitisme_xover($silang,$jumlah_individu,$persamaan);
  }
  // for ($i=0; $i < $jumlah_individu/2; $i++) {
  //   # code...
  //   $turunan = crossover_blend($seleksi[$i],$seleksi[($jumlah_individu-1)-1],$alpha);
  //   $tersilang[$i] = $turunan[0];
  //   $tersilang[($jumlah_individu-1)-$i] = $turunan[1];
  // }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$tersilang[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($tersilang[$i],$persamaan));
    print( "\n");
  }



  print( "\n MUTASI : \n \n");
  for ($i=0; $i < $jumlah_individu; $i++) {
    $termutasi[$i]=mutasi($seleksi[$i],$pm,$persamaan);
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$termutasi[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($termutasi[$i],$persamaan));
    print( "\n");
  }

  print( "\n ELITISME : \n \n");
  $elitisme = elitisme_union($populasi_awal,$tersilang,$termutasi,$persamaan);

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$elitisme[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($elitisme[$i],$persamaan));
    print( "\n");
  }

  $individu_terbaik = $elitisme[0];
  $fitness_terbaik = hitung_fitness($individu_terbaik,$persamaan);
  $c=2;

  while($fitness_terbaik < 1){
    $populasi_awal = $elitisme;
    print( "\n");
    print( "GENERASI KE $c");
    print( "\n");

    print( "SELEKSI RWS : \n \n");
    for ($i=0; $i < $jumlah_individu; $i++) {
      $seleksi[$i]=seleksi_rws($populasi_awal,$persamaan);
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$seleksi[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($seleksi[$i],$persamaan));
      print( "\n");
    }

    print( "\n");

    print( "CROSSOVER :\n \n");
    // for ($i=0; $i < $jumlah_individu-1; $i+=2) {
    //   # code...
    //   $turunan = crossover_blend($seleksi[$i],$seleksi[$i+1],$alpha);
    //   $tersilang[$i] = $turunan[0];
    //   $tersilang[$i+1] = $turunan[1];
    // }

    if($xover==1){
      for ($i=0; $i < $jumlah_individu-1; $i+=2) {
        $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
        $tersilang[$i] = $turunan[0];
        $tersilang[$i+1] = $turunan[1];
      }
    }else if($xover==2){
      for ($i=0; $i < $jumlah_individu-1; $i+=2) {
        $turunan = crossover_blend($seleksi[$i],$seleksi[$i+1],$alpha);
        $tersilang[$i] = $turunan[0];
        $tersilang[$i+1] = $turunan[1];
      }
    }else if($xover==3){
      for ($i=0; $i < $jumlah_individu/2; $i++) {
        $turunan = crossover_uniform($seleksi[$i],$seleksi[($jumlah_individu-1)-$i],$pc);
        $tersilang[$i] = $turunan[0];
        $tersilang[($jumlah_individu-1)-$i] = $turunan[1];
      }
    }else if($xover==4){
      for ($i=0; $i < $jumlah_individu/2; $i++) {
        $turunan = crossover_blend($seleksi[$i],$seleksi[($jumlah_individu-1)-$i],$alpha);
        $tersilang[$i] = $turunan[0];
        $tersilang[($jumlah_individu-1)-$i] = $turunan[1];
      }
    }else if($xover==5){
      $a=0;
      for ($i=0; $i < $jumlah_individu-1; $i++) {
        $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
        $silang[$a] = $turunan[0];
        $silang[$a+1] = $turunan[1];
        $a++;
        // $tersilang[$i] = $turunan[0];
        // $tersilang[$i+1] = $turunan[1];
      }
      $tersilang = elitisme_xover($silang,$jumlah_individu,$persamaan);
    }else{
      $a=0;
      for ($i=0; $i < $jumlah_individu-1; $i++) {
        $turunan = crossover_blend($seleksi[$i],$seleksi[$i+1],$alpha);
        $silang[$a] = $turunan[0];
        $silang[$a+1] = $turunan[1];
        $a++;
        // $tersilang[$i] = $turunan[0];
        // $tersilang[$i+1] = $turunan[1];
      }
      $tersilang = elitisme_xover($silang,$jumlah_individu,$persamaan);
    }

    // for ($i=0; $i < $jumlah_individu/2; $i++) {
    //   # code...
    //   $turunan = crossover_blend($seleksi[$i],$seleksi[($jumlah_individu-1)-$i],$alpha);
    //   $tersilang[$i] = $turunan[0];
    //   $tersilang[($jumlah_individu-1)-$i] = $turunan[1];
    // }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$tersilang[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($tersilang[$i],$persamaan));
      print( "\n");
    }



    print( "\n MUTASI : \n \n");
    for ($i=0; $i < $jumlah_individu; $i++) {
      $termutasi[$i]=mutasi($seleksi[$i],$pm,$persamaan);
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$termutasi[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($termutasi[$i],$persamaan));
      print( "\n");
    }

    print( "  \n ELITISME : \n \n");
    $elitisme = elitisme_union($populasi_awal,$tersilang,$termutasi,$persamaan);

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$elitisme[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($elitisme[$i],$persamaan));
      print( "\n");
    }

    $individu_terbaik = $elitisme[0];
    $fitness_terbaik = hitung_fitness($individu_terbaik,$persamaan);
    $c++;
  }

  $c--;
  $kesimpulan="";
  for ($i=0; $i < $jumlah_gen; $i++) {
    if($i!=$jumlah_gen-1){
      $kesimpulan = $kesimpulan."( ".$kons[$i]." * ".$individu_terbaik[$i]." ) ".$opr[$i]." ";
    }else{
      $kesimpulan = $kesimpulan."( ".$kons[$i]." * ".$individu_terbaik[$i]." )";
    }

  }

  print("\n \n ");
  print($kesimpulan);
  $hasil_akhir = eval('return '.$kesimpulan.';');
  print(" = ".$hasil_akhir);

  print( "\n \n");
  print( "Solusi ditemukan pada generasi ke : $c \n \n");



 ?>
