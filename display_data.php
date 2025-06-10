<?php
$conn = new mysqli("localhost", "root", "", "esp32_dashboard");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM sensor_data ORDER BY date_collected DESC";
$result = $conn->query($sql);

echo "<h2>ESP32 Sensor Dashboard</h2>";
echo "<table border='1' cellpadding='10'>
<tr>
    <th>ID</th>
    <th>Front Distance (cm)</th>
    <th>Top Distance (cm)</th>
    <th>Tank Level (cm)</th>
    <th>Temperature (°C)</th>
    <th>Date Collected</th>
</tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$row["front_distance"]."</td>
                <td>".$row["top_distance"]."</td>
                <td>".$row["tank_level"]."</td>
                <td>".$row["temperature"]."</td>
                <td>".$row["date_collected"]."</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No data available</td></tr>";
}
echo "</table>";

$conn->close();
?>