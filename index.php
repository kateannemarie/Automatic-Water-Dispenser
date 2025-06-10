<?php
$conn = new mysqli("localhost", "root", "", "esp32_dashboard");

$latestTemperature = "--";
$latestWaterPercent = "--";
$tempStatusText = "Unknown";
$tempStatusClass = "text-secondary";
$levelStatusText = "Unknown";
$levelStatusClass = "text-secondary";

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM sensor_data ORDER BY date_collected DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $latestTemperature = round($row['temperature'], 1);
  $tankLevelCm = $row['tank_level'];
  $maxTankHeight = 18.16; // Set this to your actual tank's height (cm)
  $latestWaterPercent = round(($tankLevelCm / $maxTankHeight) * 100);

  // Determine Temperature Status
  if ($latestTemperature < 22) {
    $tempStatusText = "Cold";
    $tempStatusClass = "text-info";
  } elseif ($latestTemperature <= 30) {
    $tempStatusText = "Lukewarm";
    $tempStatusClass = "text-primary";
  } else {
    $tempStatusText = "Hot";
    $tempStatusClass = "text-danger";
  }

  // Determine Water Level Status
  if ($latestWaterPercent >= 70) {
    $levelStatusText = "Full";
    $levelStatusClass = "text-success";
  } elseif ($latestWaterPercent >= 30) {
    $levelStatusText = "Almost Empty";
    $levelStatusClass = "text-warning";
  } else {
    $levelStatusText = "Empty";
    $levelStatusClass = "text-danger";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Water Dispenser Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Anton&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: "Anton", sans-serif;
        background: linear-gradient(to right, #e0c3fc, #8ec5fc);
        min-height: 100vh;
        margin: 0;
        padding-top: 40px;
      }

      .card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }

      .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
      }

      .form-check-label {
        font-size: 1.5rem;
        margin-left: 10px;
      }

      .status-indicator {
        font-size: 1.25rem;
        margin-top: 10px;
      }

      .display-5 {
        font-size: 2.5rem;
      }

      h1,
      h3,
      h5 {
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
      }

      .dashboard-title {
        text-transform: uppercase;
        font-size: 3rem;
        color: #fff;
        text-shadow: -2px -2px 0 #6a1b9a, 2px -2px 0 #6a1b9a, -2px 2px 0 #6a1b9a,
          2px 2px 0 #6a1b9a;
        letter-spacing: 2px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h1 class="dashboard-title text-center mb-5">WATER DISPENSER DASHBOARD</h1>

      <!-- Dispenser Control -->
      <div class="row justify-content-center mb-5">
        <div class="col-md-4">
          <div class="card text-center p-4">
            <h3>Dispenser Control</h3>
            <div
              class="form-check form-switch d-flex justify-content-center align-items-center mt-3"
            >
              <input
                class="form-check-input"
                type="checkbox"
                id="toggleSwitch"
              />
              <label
                class="form-check-label"
                for="toggleSwitch"
                id="switchLabel"
                >OFF</label
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Temperature and Water Level -->
      <div class="row g-4 mb-4">
        <!-- Water Temperature -->
        <div class="col-md-6">
          <div class="card text-center p-4">
            <h3>Water Temperature</h3>
            <p id="temperature" class="display-5 mt-3"><?= $latestTemperature ?> °C</p>
            <p id="tempStatus" class="status-indicator <?= $tempStatusClass ?>">
              <?= $tempStatusText ?>
            </p>
          </div>
        </div>

        <!-- Water Level -->
        <div class="col-md-6">
          <div class="card text-center p-4">
            <h3>Water Level</h3>
            <p id="waterLevel" class="display-5 mt-3"><?= $latestWaterPercent ?> %</p>
            <p id="levelStatus" class="status-indicator <?= $levelStatusClass ?>">
              <?= $levelStatusText ?>
            </p>
          </div>
        </div>
      </div>

      <!-- View Logs Button -->
      <div class="text-center">
        <a href="logs.php" class="btn btn-secondary px-4 py-2"
          >View Sensor Logs</a
        >
      </div>
    </div>

    <script>
      const toggleSwitch = document.getElementById("toggleSwitch");
      const switchLabel = document.getElementById("switchLabel");

      toggleSwitch.addEventListener("change", () => {
        if (toggleSwitch.checked) {
          switchLabel.innerText = "ON";
          switchLabel.style.color = "green";
        } else {
          switchLabel.innerText = "OFF";
          switchLabel.style.color = "red";
        }
      });

      switchLabel.style.color = "red";
    </script>
  </body>
</html>