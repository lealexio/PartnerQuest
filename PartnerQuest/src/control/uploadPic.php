<?php
if(isset($_POST['submit'])){
    $pic = $_FILES['file'];

    $picName = $_FILES['file']['name'];
    $picTmpName = $_FILES['file']['tmp_name'];
    $picSize = $_FILES['file']['size'];
    $uploadError = $_FILES['file']['error'];
    $picType = $_FILES['file']['type'];

    $fileExt = explode('.',$picName);
    $fileActualExt = strtolower(end($fileExt));

    $allowedExt = array('jpg','jpeg','png');
    echo "test";
    if(in_array($fileActualExt,$allowedExt)){
        if($uploadError === 0){
            echo "1";
            if($picSize < 100000){
                echo "2";
                $pic_id=uniqid('',true);

                $picNameNew = $pic_id.".".$fileActualExt;
                $uploadDestination="uploads/".$picNameNew;
                move_uploaded_file($picTmpName,$uploadDestination);
            }else{
                echo "3";
                $uploadError="<script>alert( 'big pic size error')</script>";
            }
        }else{
            echo "4";
            $uploadError="<script>alert( 'upload error')</script>";
        }
    }
}

?>