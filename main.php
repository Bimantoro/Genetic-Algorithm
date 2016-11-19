<?php
  //GA - Danang Aji Bimantoro
  //inisialiasi parameter GA
  $jumlah_gen=4;
  $jumlah_individu=6;
  $pc=0.9;
  $pm=0.1;

  //fungsi minimasi dari persamaan (a+2b+3c+4d=30)
  function f_obj($x){
    $minimasi = abs(($x[0]+2*$x[1]+3*$x[2]+4*$x[3])-30);
    return $minimasi;
  }

  //fungsi untuk menghitung nilai fitness dari individu :
  function hitung_fitness($individu){
        $fitness = 1/(1+f_obj($individu));
    return $fitness;
    }

  //fungsi untuk membangkitkan populasi awal :
  function pembangkitan_populasi($jumlah_individu,$jumlah_gen){
    for($i=0; $i<$jumlah_individu; $i++){
      for($j=0; $j<$jumlah_gen; $j++){
        $awal[$i][$j]=rand(0,30);
      }
    }
    return $awal;
  }

  //fungsi untuk melakukan seleksi individu :
  function seleksi_rws($individu){
    $jumlah_individu=count($individu);
    $total_fitness=0;

    //menghitung total fitness
    for ($i=0; $i < $jumlah_individu ; $i++) {
      $total_fitness+=hitung_fitness($individu[$i]);
    }

    //menhitung probabilitas tiap individu
    for ($i=0; $i < $jumlah_individu; $i++) {
      $probabilitas[$i]=hitung_fitness($individu[$i])/$total_fitness;
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

  //fungsi mutasi :
  function mutasi($induk,$pm){
    $jumlah_gen=count($induk);
    for ($i=0; $i < $jumlah_gen; $i++) {
      $r = rand(0,10)/10;
      if($r <= $pm){
        $induk[$i]=rand(0,30);
      }
    }

    return $induk;
  }

  //fungsi elitisme union
  function elitisme_union($seleksi,$tersilang,$termutasi){
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
        if(hitung_fitness($union[$j]) < hitung_fitness($union[$j+1])){
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
  $populasi_awal = pembangkitan_populasi($jumlah_individu,$jumlah_gen);
  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$populasi_awal[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($populasi_awal[$i]));
    print( "\n");
  }

  print( "\n");
  print( "GENERASI KE 1");
  print( "\n");

  print( "SELEKSI RWS : \n \n");
  for ($i=0; $i < $jumlah_individu; $i++) {
    $seleksi[$i]=seleksi_rws($populasi_awal);
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$seleksi[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($seleksi[$i]));
    print( "\n");
  }

  print( "\n");

  print( "TERSILANG \n \n");
  for ($i=0; $i < $jumlah_individu-1; $i+=2) {
    # code...
    $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
    $tersilang[$i] = $turunan[0];
    $tersilang[$i+1] = $turunan[1];
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$tersilang[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($tersilang[$i]));
    print( "\n");
  }



  print( "\n MUTASI : \n \n");
  for ($i=0; $i < $jumlah_individu; $i++) {
    $termutasi[$i]=mutasi($seleksi[$i],$pm);
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$termutasi[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($termutasi[$i]));
    print( "\n");
  }

  print( "\n ELITISME : \n \n");
  $elitisme = elitisme_union($seleksi,$tersilang,$termutasi);

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      print( "[ ".$elitisme[$i][$j]." ]");
    }
    print( " | Fitnessnya : ".hitung_fitness($elitisme[$i]));
    print( "\n");
  }

  $individu_terbaik = $elitisme[0];
  $fitness_terbaik = hitung_fitness($individu_terbaik);
  $c=2;

  while($fitness_terbaik < 1){
    print( "\n");
    print( "GENERASI KE $c");
    print( "\n");

    print( "SELEKSI RWS : \n \n");
    for ($i=0; $i < $jumlah_individu; $i++) {
      $seleksi[$i]=seleksi_rws($elitisme);
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$seleksi[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($seleksi[$i]));
      print( "\n");
    }

    print( "\n");

    print( "TERSILANG \n \n");
    for ($i=0; $i < $jumlah_individu-1; $i+=2) {
      # code...
      $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
      $tersilang[$i] = $turunan[0];
      $tersilang[$i+1] = $turunan[1];
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$tersilang[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($tersilang[$i]));
      print( "\n");
    }



    print( "\n MUTASI : \n \n");
    for ($i=0; $i < $jumlah_individu; $i++) {
      $termutasi[$i]=mutasi($seleksi[$i],$pm);
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$termutasi[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($termutasi[$i]));
      print( "\n");
    }

    print( "\n ELITISME : \n \n");
    $elitisme = elitisme_union($seleksi,$tersilang,$termutasi);

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        print( "[ ".$elitisme[$i][$j]." ]");
      }
      print( " | Fitnessnya : ".hitung_fitness($elitisme[$i]));
      print( "\n");
    }

    $individu_terbaik = $elitisme[0];
    $fitness_terbaik = hitung_fitness($individu_terbaik);
    $c++;
  }

  $c--;
  print( "\n");
  print( "proses pencarian nilai dari a,b,c dan d berhenti pada generasi ke $c \n");



 ?>
