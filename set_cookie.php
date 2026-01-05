<?php
if(isset($_POST['fullname'])){
    setcookie("fullname", $_POST['fullname'], time()+3600);
    setcookie("age", $_POST['age'], time()+3600);
    setcookie("smoking", $_POST['smoking'], time()+3600);
}
header("Location: visitor_info.php");
?>
