<?php
    $correo = $_GET["correo"];
    $password1 = $_GET["password1"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Prueba";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $sql = "SELECT * FROM usuarios where correo =  '$correo'  AND password1 = '$password1'";
    
    $result = $conn->query($sql);
    $row = $result->fetch_row();

    if(empty($row[0])){
        http_response_code(404);
        $error = '{"error": "no se pudo encontrar la informacion"}';
        header('content-type: application/json; charset=utf-8');
        echo json_encode($error);
    }else{
        // $usuario = '{"correo":' . $row[0] . ', "password1": "' . $row[1] . ', "Id": "' .$row[2] .'}';
        // header('content-type: application/json; charset=utf-8');
        // echo json_encode($usuario);
        $arr = array('alg' => 'HS256', 'typ' => 'JWT');
        $arr2 = json_encode($arr);
        $encoded_header = base64_encode($arr2);

        $arr3 = array('correo' =>  $row[0] , 'contraseña' => $row[1], 'Id' => $row[2]);
        $arr33 = json_encode($arr3);
        $encoded_payload = base64_encode($arr33);

        $header_payload = $encoded_header . '.' . $encoded_payload;
        
        $secret_key = 'clave secreta';

        $signature = base64_encode(hash_hmac('sha256', $header_payload, $secret_key, true));

        $jwt_token = $header_payload . '.' . $signature;

        $recievedJwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjb3VudHJ5IjoiVmVuZXp1ZWxhIiwibmFtZSI6Ikp1bGlvIEdvbnphbGV6IiwiZW1haWwiOiJlbWFpbEBnbWFpbC5jb20ifQ==.h3tBXSN978DPxKxgJh20mc2DaqSdWuYhKJ9O1iBV6Pk=';

        $secret_key = 'clave secreta';

        $jwt_values = explode('.', $recievedJwt);

        $recieved_signature = $jwt_values[2];

        $recievedHeaderAndPayload = $jwt_values[0] . '.' . $jwt_values[1];

        $resultedsignature = base64_encode(hash_hmac('sha256', $recievedHeaderAndPayload, $secret_key, true));

        if($resultedsignature == $recieved_signature) {
            echo "Success";
        } else {
            echo "Password no valida";
        }
        $usuario = '{"token": ' .  $jwt_token .'"}';
        header('Authorization: Bearer' . $jwt_token);
    }  
?>