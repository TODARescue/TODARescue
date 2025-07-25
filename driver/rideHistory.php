<?php
session_start();
require_once '../assets/shared/connect.php';

if (!isset($_SESSION['userId'])) {
  header('Location: ../login.php');
  exit;
}

$userId = $_SESSION['userId'];

// ✅ Get driverId based on current logged-in user
$driverQuery = $conn->prepare("SELECT driverId FROM drivers WHERE userId = ?");
$driverQuery->bind_param("i", $userId);
$driverQuery->execute();
$driverResult = $driverQuery->get_result();

if ($driverResult->num_rows === 0) {
  echo "Driver profile not found.";
  exit;
}

$driverId = $driverResult->fetch_assoc()['driverId'];

// ✅ Fetch ride history within past 7 days
$sql = "
  SELECT h.historyId, h.pickupTime, h.dropoffTime, u.firstName, u.lastName, u.contactNumber
  FROM history h
  JOIN users u ON h.userId = u.userId
  WHERE h.driverId = ? AND h.pickupTime >= NOW() - INTERVAL 7 DAY
  ORDER BY h.pickupTime DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driverId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Driver | Ride History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body style="font-family: 'Inter', sans-serif;">
  <?php include '../assets/shared/header.php'; ?>

  <div class="container d-flex justify-content-center align-items-center mt-5 pt-5">
    <div class="card text-center p-4 m-5 rounded-5" style="background-color: #D9D9D9; max-width: 300px;">
      <div class="fw-bold fs-5 mb-0">Ride History Expiration</div>
      <div class="text-muted">You can only see ride history within the span of 7 days.</div>
    </div>
  </div>

  <div class="container mb-5">
    <div class="row">
      <div class="col list-group list-group-flush px-0 w-100">

        <?php if ($result->num_rows === 0): ?>
          <div class="text-muted text-center mt-4">No ride history in the last 7 days.</div>
        <?php endif; ?>

        <?php while ($row = $result->fetch_assoc()): ?>
          <?php $detailsId = "rideDetails" . $row['historyId']; ?>
          <div
            class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
            data-bs-toggle="collapse" data-bs-target="#<?= $detailsId ?>">
            <?= date("F d, Y H:i:s", strtotime($row['pickupTime'])) ?> —
            <?= date("H:i:s", strtotime($row['dropoffTime'])) ?>
          </div>
          <div id="<?= $detailsId ?>" class="collapse">
            <div class="card p-3">
              <h6>Passenger: <?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></h6>
              <p>Contact Number: <?= htmlspecialchars($row['contactNumber']) ?></p>
            </div>
          </div>
        <?php endwhile; ?>

      </div>
    </div>
  </div>

  <?php include '../assets/shared/navbarDriver.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
