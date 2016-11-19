<?php
  //inisialisasi parameter GA
  $jumlah_gen=4;
  $jumlah_individu=6;
  $pc=0.9;
  $pm=0.1;

  //fungsi minimasi dari persamaan 1+2b+3c+4d=30
  function f_obj($x){
    $minimasi = abs(($x[0]+2*$x[1]+3*$x[2]+4*$x[3])-30);
    return $minimasi;
  }

  //fungsi untuk menghitung nilai fitness
  function hitung_fitness($individu){
        $fitness = 1/(1+f_obj($individu));
    return $fitness;
    }

  function pembangkitan_populasi($jumlah_individu,$jumlah_gen){
    for($i=0; $i<$jumlah_individu; $i++){
      for($j=0; $j<$jumlah_gen; $j++){
        $awal[$i][$j]=rand(0,30);
      }
    }
    return $awal;
  }

  function seleksi_rws($individu){
    $jumlah_individu=count($individu);
    $total_fitness=0;

    for ($i=0; $i < $jumlah_individu ; $i++) {
      $total_fitness+=hitung_fitness($individu[$i]);
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      $probabilitas[$i]=hitung_fitness($individu[$i])/$total_fitness;
    }

    $r=rand(0,10)/10; //random nilai 0 - 1
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

  function elitisme_union($seleksi,$tersilang,$termutasi){
    $jumlah_individu=count($seleksi);
    for ($i=0; $i < $jumlah_individu; $i++) {
      $union[$i]=$seleksi[$i];
      $union[$i+$jumlah_individu]=$tersilang[$i];
      $union[$i+($jumlah_individu*2)]=$termutasi[$i];
    }

    for ($i=0; $i < ($jumlah_individu*3)-1; $i++) {
      for ($j=0; $j < ($jumlah_individu*3)-1; $j++) {
        if(hitung_fitness($union[$j]) < hitung_fitness($union[$j+1])){
          $tmp = $union[$j];
          $union[$j]=$union[$j+1];
          $union[$j+1]=$tmp;
        }
      }
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      $terbaik[$i]=$union[$i];
    }

    return $terbaik;
  }

  echo "PEMBANGKITAN POPULASI AWAL : <br> <br>";
  $populasi_awal = pembangkitan_populasi($jumlah_individu,$jumlah_gen);
  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      echo "[ ".$populasi_awal[$i][$j]." ]";
    }
    echo " | Fitnessnya : ".hitung_fitness($populasi_awal[$i]);
    echo "<br>";
  }

  echo "<br>";
  echo "GENERASI KE 1";
  echo "<br>";

  echo "SELEKSI RWS : <br> <br>";
  for ($i=0; $i < $jumlah_individu; $i++) {
    $seleksi[$i]=seleksi_rws($populasi_awal);
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      echo "[ ".$seleksi[$i][$j]." ]";
    }
    echo " | Fitnessnya : ".hitung_fitness($seleksi[$i]);
    echo "<br>";
  }

  echo "<br>";

  echo "TERSILANG <br> <br>";
  for ($i=0; $i < $jumlah_individu-1; $i+=2) {
    $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
    $tersilang[$i] = $turunan[0];
    $tersilang[$i+1] = $turunan[1];
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      echo "[ ".$tersilang[$i][$j]." ]";
    }
    echo " | Fitnessnya : ".hitung_fitness($tersilang[$i]);
    echo "<br>";
  }



  echo "<br> MUTASI : <br> <br>";
  for ($i=0; $i < $jumlah_individu; $i++) {
    $termutasi[$i]=mutasi($seleksi[$i],$pm);
  }

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      echo "[ ".$termutasi[$i][$j]." ]";
    }
    echo " | Fitnessnya : ".hitung_fitness($termutasi[$i]);
    echo "<br>";
  }

  echo "<br> ELITISME : <br> <br>";
  $elitisme = elitisme_union($seleksi,$tersilang,$termutasi);

  for ($i=0; $i < $jumlah_individu; $i++) {
    for ($j=0; $j < $jumlah_gen; $j++) {
      echo "[ ".$elitisme[$i][$j]." ]";
    }
    echo " | Fitnessnya : ".hitung_fitness($elitisme[$i]);
    echo "<br>";
  }

  $individu_terbaik = $elitisme[0];
  $fitness_terbaik = hitung_fitness($individu_terbaik);
  $c=2;

  while($fitness_terbaik < 1){
    echo "<br>";
    echo "GENERASI KE $c";
    echo "<br>";

    echo "SELEKSI RWS : <br> <br>";
    for ($i=0; $i < $jumlah_individu; $i++) {
      $seleksi[$i]=seleksi_rws($elitisme);
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        echo "[ ".$seleksi[$i][$j]." ]";
      }
      echo " | Fitnessnya : ".hitung_fitness($seleksi[$i]);
      echo "<br>";
    }

    echo "<br>";

    echo "TERSILANG <br> <br>";
    for ($i=0; $i < $jumlah_individu-1; $i+=2) {
      $turunan = crossover_uniform($seleksi[$i],$seleksi[$i+1],$pc);
      $tersilang[$i] = $turunan[0];
      $tersilang[$i+1] = $turunan[1];
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        echo "[ ".$tersilang[$i][$j]." ]";
      }
      echo " | Fitnessnya : ".hitung_fitness($tersilang[$i]);
      echo "<br>";
    }



    echo "<br> MUTASI : <br> <br>";
    for ($i=0; $i < $jumlah_individu; $i++) {
      $termutasi[$i]=mutasi($seleksi[$i],$pm);
    }

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        echo "[ ".$termutasi[$i][$j]." ]";
      }
      echo " | Fitnessnya : ".hitung_fitness($termutasi[$i]);
      echo "<br>";
    }

    echo "<br> ELITISME : <br> <br>";
    $elitisme = elitisme_union($seleksi,$tersilang,$termutasi);

    for ($i=0; $i < $jumlah_individu; $i++) {
      for ($j=0; $j < $jumlah_gen; $j++) {
        echo "[ ".$elitisme[$i][$j]." ]";
      }
      echo " | Fitnessnya : ".hitung_fitness($elitisme[$i]);
      echo "<br>";
    }

    $individu_terbaik = $elitisme[0];
    $fitness_terbaik = hitung_fitness($individu_terbaik);
    $c++;
  }
  
  echo "<br>";
  echo "proses pencarian nilai dari a,b,c dan d berhenti pada generasi ke $c";



 ?>
