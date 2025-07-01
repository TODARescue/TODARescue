<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TODA Rescue | Ride History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body style="font-family: 'Inter', sans-serif;">
  <!-- HEADER -->
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
        
        <div class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
          data-bs-toggle="collapse" data-bs-target="#rideDetails1">
          April 06, 2025 20:12:36 : 20:16:29
        </div>
        <div id="rideDetails1" class="collapse">
          <?php include '../passenger/rideDetailsCard.php'; ?>
        </div>

        <div class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
          data-bs-toggle="collapse" data-bs-target="#rideDetails2">
          April 05, 2025 18:45:10 : 18:52:55
        </div>
        <div id="rideDetails2" class="collapse">
          <?php include '../passenger/rideDetailsCard.php'; ?>
        </div>

        <div class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
          data-bs-toggle="collapse" data-bs-target="#rideDetails3">
          April 04, 2025 17:20:05 : 17:30:45
        </div>
        <div id="rideDetails3" class="collapse">
          <?php include '../passenger/rideDetailsCard.php'; ?>
        </div>

      </div>
    </div>
  </div>

  <?php include '../assets/shared/navbarPassenger.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
