<?php
//function to get string from keyboard
  function read_keyboard()
  {
    $fr=fopen("php://stdin","r");
    $input = fgets($fr,128);
    $input = rtrim($input);

    return $input;
  }
 
 //how to use the function
  print("what is your name ? : ");
  $name = read_keyboard();
  
  
  print("your name is : ".$name." \n");
  
  //this code will output
  //what is your name ? : [insert name from keyboard]
  //your name is : [string typed from keyboard]
  
 ?>
