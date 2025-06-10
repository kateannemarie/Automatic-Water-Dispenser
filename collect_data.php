<?php
if(isset($_GET["front"]) && isset($_GET["top"]) && isset($_GET["tank"]) && isset($_GET["temp"])) {
    $front = $_GET["front"];
    $top = $_GET["top"];
    $tank = $_GET["tank"];
    $temp = $_GET["temp"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "esp32_dashboard";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO sensor_data (front_distance, top_distance, tank_level, temperature) VALUES ($front, $top, $tank, $temp)";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "Missing parameters";
}
?>