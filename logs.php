<?php
$conn = new mysqli("localhost", "root", "", "esp32_dashboard");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM sensor_data ORDER BY date_collected DESC";
$result = $conn->query($sql);
$maxTankHeight = 18.16; // Make sure this matches your tank height
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Sensor Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Anton', sans-serif;
        background: linear-gradient(to right, #e0c3fc, #8ec5fc);
        min-height: 100vh;
        margin: 0;
        padding: 40px 0;
    }

    .container {
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        text-transform: uppercase;
        font-size: 2.5rem;
        color: #6a1b9a;
        text-shadow: 1px 1px 0 #fff;
    }

    .table th {
        background-color: #6a1b9a;
        color: white;
        font-family: 'Anton', sans-serif; /* Keep Anton for headers */
        font-weight: bold;
    }

    .table td {
        font-family: Arial, sans-serif; /* Change the font of data rows */
        font-weight: normal;            /* Make sure it's not bold */
    }

    .btn-back {
        margin-bottom: 20px;
    }
</style>

</head>

<body>

    <div class="container">
        <a href="index.php" class="btn btn-secondary btn-back">← Back to Dashboard</a>
        <h2>Sensor Logs</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Front Distance (cm)</th>
                        <th>Top Distance (cm)</th>
                        <th>Tank Level (cm)</th>
                        <th>Water Level (%)</th>
                        <th>Temperature (°C)</th>
                        <th>Temperature Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Calculate Water Level (%)
                            $tankLevelCm = $row['tank_level'];
                            $waterPercent = round(($tankLevelCm / $maxTankHeight) * 100);

                            // Determine Water Level Status Color
                            if ($waterPercent >= 70) {
                                $levelColor = "text-success"; // Full
                            } elseif ($waterPercent >= 30) {
                                $levelColor = "text-warning"; // Almost Empty
                            } else {
                                $levelColor = "text-danger";  // Empty
                            }

                            // Determine Temperature Status Color and Text
                            $temperature = round($row['temperature'], 1);
                            if ($temperature < 22) {
                                $tempStatus = "Cold";
                                $tempColor = "text-info";
                            } elseif ($temperature <= 30) {
                                $tempStatus = "Lukewarm";
                                $tempColor = "text-primary";
                            } else {
                                $tempStatus = "Hot";
                                $tempColor = "text-danger";
                            }

                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['front_distance']}</td>
                                <td>{$row['top_distance']}</td>
                                <td>{$row['tank_level']}</td>
                                <td class='{$levelColor}'>{$waterPercent} %</td>
                                <td>{$temperature} °C</td>
                                <td class='{$tempColor}'>{$tempStatus}</td>
                                <td>{$row['date_collected']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>

<?php $conn->close(); ?>
