<?php

echo "<h2>Cookie Bilgileri</h2>";

if(isset($_COOKIE['fullname'])){
    echo "Ad Soyad: ".$_COOKIE['fullname']."<br>";
    echo "Yaş: ".$_COOKIE['age']."<br>";
    echo "Sigara: ".($_COOKIE['smoking'] ? "Evet" : "Hayır");
}else{
    echo "Cookie bulunamadı.";
}
?>
