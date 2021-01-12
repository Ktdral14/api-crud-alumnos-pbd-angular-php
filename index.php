<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

require "./vendor/autoload.php";

$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true
]];
$app = new Slim\App($config);

$host = "sql138.main-hosting.eu";
$db = "u224428987_pbd_kt_chino";
$mysqlConnectionString = "mysql:host=$host;dbname=$db;charset=utf8mb4;";
$dbOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
$user = "u224428987_pbd_kt_chino";
$pass = "TecDevs2017";

$app->post('/registrar-alumno', function ($request, $response) use ($mysqlConnectionString, $user, $pass, $dbOptions) {
    $matricula = $request->getParam("matricula");
    $nombre = $request->getParam("nombre");
    $carrera = $request->getParam("carrera");
    $semestre = $request->getParam("semestre");
    try {
        $connection = new PDO($mysqlConnectionString, $user, $pass, $dbOptions);
        $sql = "INSERT INTO alumnos (matricula, nombre, carrera, semestre) VALUES (:matricula, :nombre, :carrera, :semestre)";
        $statement = $connection->prepare($sql);
        $statement->bindParam(":matricula", $matricula);
        $statement->bindParam(":nombre", $nombre);
        $statement->bindParam(":carrera", $carrera);
        $statement->bindParam(":semestre", $semestre);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            return $response->withJson(["error" => false, "body" => "Registro exitoso"])->withStatus(200);
        } else {
            return $response->withJson(["error" => true, "body" => "La matrícula ya existe"])->withStatus(200);
        }
    } catch (Exception $ex) {
        return $response->withJson(["error" => true, "body" => $ex->getMessage()])->withStatus(200);
    }
});

$app->put('/actualizar-alumno', function ($request, $response) use ($mysqlConnectionString, $user, $pass, $dbOptions) {
    $matricula = $request->getParam("matricula");
    $nombre = $request->getParam("nombre");
    $carrera = $request->getParam("carrera");
    $semestre = $request->getParam("semestre");
    try {
        $connection = new PDO($mysqlConnectionString, $user, $pass, $dbOptions);
        $sql = "UPDATE alumnos SET nombre = :nombre, carrera = :carrera, semestre = :semestre WHERE matricula = :matricula";
        $statement = $connection->prepare($sql);
        $statement->bindParam(":matricula", $matricula);
        $statement->bindParam(":nombre", $nombre);
        $statement->bindParam(":carrera", $carrera);
        $statement->bindParam(":semestre", $semestre);
        $statement->execute();
        return $response->withJson(["error" => false, "body" => "Actualización exitosa"])->withStatus(200);
    } catch (Exception $ex) {
        return $response->withJson(["error" => true, "body" => $ex->getMessage()])->withStatus(200);
    }
});

$app->delete('/eliminar-alumno', function ($request, $response) use ($mysqlConnectionString, $user, $pass, $dbOptions) {
    $matricula = $request->getParam("matricula");
    try {
        $connection = new PDO($mysqlConnectionString, $user, $pass, $dbOptions);
        $sql = "DELETE FROM alumnos WHERE matricula = :matricula";
        $statement = $connection->prepare($sql);
        $statement->bindParam(":matricula", $matricula);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            return $response->withJson(["error" => false, "body" => "Eliminación exitosa"])->withStatus(200);
        } else {
            return $response->withJson(["error" => true, "body" => "El alumno no existe"])->withStatus(200);
        }
    } catch (Exception $ex) {
        return $response->withJson(["error" => true, "body" => $ex->getMessage()])->withStatus(200);
    }
});

$app->get('/obtener-uno', function ($request, $response) use ($mysqlConnectionString, $user, $pass, $dbOptions) {
    $matricula = $request->getParam("matricula");
    try {
        $connection = new PDO($mysqlConnectionString, $user, $pass, $dbOptions);
        $sql = "SELECT * FROM alumnos WHERE matricula = :matricula";
        $statement = $connection->prepare($sql);
        $statement->bindParam(":matricula", $matricula);
        $statement->execute();
        if ($statement && $statement->rowCount() > 0) {
            return $response->withJson(["error" => false, "body" => $statement->fetch(PDO::FETCH_ASSOC)])->withStatus(200);
        } else {
            return $response->withJson(["error" => true, "body" => []])->withStatus(200);
        }
    } catch (Exception $ex) {
        return $response->withJson(["error" => true, "body" => $ex->getMessage()])->withStatus(200);
    }
});

$app->get('/obtener-todos', function ($request, $response) use ($mysqlConnectionString, $user, $pass, $dbOptions) {
    try {
        $connection = new PDO($mysqlConnectionString, $user, $pass, $dbOptions);
        $sql = "SELECT * FROM alumnos";
        $statement = $connection->query($sql);
        if ($statement && $statement->rowCount() > 0) {
            return $response->withJson(["error" => false, "body" => $statement->fetchAll(PDO::FETCH_ASSOC)])->withStatus(200);
        } else {
            return $response->withJson(["error" => true, "body" => []])->withStatus(200);
        }
    } catch (Exception $ex) {
        return $response->withJson(["error" => true, "body" => $ex->getMessage()])->withStatus(200);
    }
});

$app->run();
