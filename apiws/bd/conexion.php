<?php
    // class Database{
    //     private $host = 'localhost';
    //     private $user = 'root';
    //     private $password = '1234';
    //     private $database = 'apiws';

    //     public function getConnection(){
    //         $hostDB = "mysql:host=".$this->host.";dbname=".$this->database.";";
    //     // $hostDB= new mysqli($host,$user,$password,$database);

    //         try{
    //             $connection = new PDO($hostDB,$this->user,$this->password);
    //             $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //             return $connection;
    //         } catch(PDOException $e){
    //             die("ERROR: ".$e->getMessage());
    //         }

    //     }

        
    // }

    $mysqli = new mysqli("localhost","root","1234","apiws");

    if($mysqli->connect_errno){
        die("Conexión fallida");
    } else {
        // echo "Todo bien";
    }

    header("Content-Type: application/json");
    $metodo= $_SERVER['REQUEST_METHOD'];
    print_r($metodo);

    $path= isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/'; //recorrer el path, la url para saber el id
    $searchId = explode('/',$path); //buscar el id
    $id=($path!=='/') ? end($searchId):null;//se utiliza el id

    switch ($metodo){

        case 'GET':
            select($mysqli, $id);
            break;
        
        case 'POST':
            insertar($mysqli);
            break;
    
        case 'PUT':
            actualizar($mysqli, $id);
            break;

        case 'DELETE':
            borrar($mysqli, $id);
            break;

        default:
            echo "Método no permitido";

    
    }

    function select($mysqli, $id){
        $sql= ($id===null)? "SELECT * FROM usuarios":"SELECT * FROM usuarios WHERE id=$id";
        $resultados= $mysqli->query($sql);

        if($resultados){
            $datos= array();
            while($fila= $resultados->fetch_assoc()){
                $datos[]= $fila;
            }

            echo json_encode($datos);
        }
    }

    function insertar($mysqli){
        $dato= json_decode(file_get_contents('php://input'),true);
        $name= $dato['name'];
        $last_name= $dato['last_name'];
        $email= $dato['email'];
        $phone= $dato['phone'];

        $sql= "INSERT INTO usuarios(name,last_name,email,phone) VALUES ('$name','$last_name','$email','$phone')";
        $resultados= $mysqli->query($sql);

        if($resultados){

            $dato['id'] = $mysqli->insert_id;
            echo json_encode($dato);
        } else {
            echo json_encode(array('error'=>'Error al crear un usuario'));
        }


    }

    function borrar($mysqli,$id){
        echo "El id a borrar es: ". $id;

        $sql= "DELETE FROM usuarios WHERE id = $id";
        $resultados= $mysqli->query($sql);

        if($resultados){

            echo json_encode(array('mensaje'=>'Usuario eliminado'));
        } else {
            echo json_encode(array('error'=>'Error al borrar un usuario'));
        }
    }

    function actualizar($mysqli, $id){
        $dato= json_decode(file_get_contents('php://input'),true);
        $name= $dato['name'];
        $last_name= $dato['last_name'];
        $email= $dato['email'];
        $phone= $dato['phone'];

        echo "El id a actualizar es: ". $id. " con el dato ".$name. $last_name. $email. $phone;

        $sql= "UPDATE usuarios SET name='$name' ,last_name='$last_name',email='$email',phone='$phone' WHERE id=$id";
        $resultados= $mysqli->query($sql);

        if($resultados){

            echo json_encode(array('mensaje'=>'Usuario actualizado'));
        } else {
            echo json_encode(array('error'=>'Error al actualizar un usuario'));
        }
        
    }


?>