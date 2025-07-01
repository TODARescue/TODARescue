<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TODA Rescue - accountView</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="d-flex justify-content-center align-items-center vh-100"
  style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

  <div class="container-fluid p-0 m-0 vh-100">
    <div class="row h-100 g-0">
      <div class="col-12 d-flex justify-content-center align-items-start h-100">

        <!-- Main Card -->
        <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
          style="border-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

          <!-- HEADER -->
             <?php include '../assets/shared/header.php'; ?>

          <!-- Profile Content -->
          <div class="d-flex flex-column align-items-center justify-content-start px-4 pb-5 overflow-auto" style="padding-top: 120px;">

            <!-- Profile Image -->
            <div class="rounded-circle mb-4" style="width: 100px; height: 100px; background-color: #a59e9e;"></div>

            <!-- Form -->
            <form class="w-100">
              <div class="d-flex flex-column align-items-start mb-3">
                <label class="form-label text-muted small fw-bold ">First Name</label>
                <input type="text" class="form-control border-0 border-bottom rounded-0 shadow-none p-0"
                  style="border-bottom: 2px solid #000;" />
              </div>

              <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Last Name</label>
                <input type="text" class="form-control border-0 border-bottom rounded-0 shadow-none p-0" />
              </div>

              <div class="mb-3">
                <label class="form-label text-dark small fw-bold ">Phone Number</label>
                <input type="text" class="form-control border-0 border-bottom rounded-0 shadow-none text-start p-0"
                  value="09764028761" />
              </div>

              <div class="mb-4">
                <label class="form-label text-dark small fw-bold">Email Address</label>
                <input type="email" class="form-control border-0 border-bottom rounded-0 shadow-none text-start p-0"
                  value="bryanreano@gmail.com" />
              </div>

              <!-- Save Button -->
              <div class="d-flex justify-content-center">
                <button type="submit" class="btn text-white px-5"
                  style="background-color: #24b3a7; border-radius: 15px;">Save</button>
              </div>
            </form>

          </div>

        </div>
      </div>
    </div>
  </div>

  <?php include '../assets/shared/navbarPassenger.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>