<?php
$cardnumber = $_SESSION['cardNumber'];
$type_de_carte = array(
    '34'=>'American Express',
    '37'=>'American Express',
    '5'=>'Master Card',
    '4'=>'Visa',
    '30'=>'Blue Card',
    '38'=>'Blue Card',
    '35'=>'JCB',
    '6'=>'Discover'
);

if(substr($cardnumber,0,2) == "34"){
    $type_de_cartes =   $type_de_carte[34];
}else if(substr($cardnumber,0,2) == "37"){
    $type_de_cartes =  $type_de_carte[37];
}
else if(substr($cardnumber,0,2)== "30"){
    $type_de_cartes =  $type_de_carte[30];
}
else if(substr($cardnumber,0,2)== "38"){
    $type_de_cartes =   $type_de_carte[38];
}
else if(substr($cardnumber,0,2)== "35"){
    $type_de_cartes =  $type_de_carte[35];
}
else if(substr($cardnumber,0,1)== "6"){
    $type_de_cartes =   $type_de_carte[6];
}
else if(substr($cardnumber,0,1)== "5"){
    $type_de_cartes =   $type_de_carte[5];
}
else if(substr($cardnumber,0,1) == "4"){
    $type_de_cartes =  $type_de_carte[4];
}
