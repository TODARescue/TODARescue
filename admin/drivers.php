<?php
include '../assets/shared/connect.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';
$filter = $_GET['filter'] ?? 'all';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TODARescue | Drivers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-white d-flex justify-content-center align-items-start min-vh-100 pt-5">

  <div class="container px-4 pb-5" style="max-width: 500px;">
    <h3 class="fw-bold text-center mb-3">TODA Rescue</h3>

    <div class="d-flex align-items-center justify-content-between">
      <h5 class="mb-3">Drivers</h5>
      <button onclick="location.href='addDrivers.php'" class="btn rounded-pill d-flex align-items-center justify-content-center mb-3" style="background-color: #2EBCBC; border: none; width: 60px; height: 30px; padding: 0;">
        <i class="bi bi-plus" style="font-size: 20px; color: white;"></i>
      </button>
    </div>

    <!-- Search + Sort/Filter -->
    <form method="GET" class="search-bar mb-3">
      <div class="input-group shadow mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search Drivers" aria-label="Search" aria-describedby="search-addon" value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-outline-secondary" type="submit" id="search-addon">
          <i class="bi bi-search"></i>
        </button>
      </div>

      <div class="d-flex gap-2">
        <select name="sort" class="form-select rounded-5" onchange="this.form.submit()">
          <option value="">Sort</option>
          <option value="asc" <?php if ($sort === 'asc') echo 'selected'; ?>>A-Z</option>
          <option value="desc" <?php if ($sort === 'desc') echo 'selected'; ?>>Z-A</option>
        </select>

        <select name="filter" class="form-select rounded-5" onchange="this.form.submit()">
          <option value="all" <?php if ($filter === 'all') echo 'selected'; ?>>All</option>
          <option value="active" <?php if ($filter === 'active') echo 'selected'; ?>>Active</option>
          <option value="inactive" <?php if ($filter === 'inactive') echo 'selected'; ?>>Inactive</option>
        </select>
      </div>
    </form>

    <!-- Cards -->
    <div class="mt-3 d-flex flex-column gap-3 mb-4">
      <?php
      $sql = "SELECT * FROM users WHERE role = 'driver'";

      if ($filter === 'active') {
        $sql .= " AND isDeleted = 0";
      } elseif ($filter === 'inactive') {
        $sql .= " AND isDeleted = 1";
      }

      if (!empty($search)) {
        $safeSearch = mysqli_real_escape_string($conn, $search);
        $sql .= " AND (
          firstName LIKE '%$safeSearch%' 
          OR lastName LIKE '%$safeSearch%' 
          OR CONCAT(firstName, ' ', lastName) LIKE '%$safeSearch%'
        )";
      }

      if ($sort === 'asc') {
        $sql .= " ORDER BY firstName ASC";
      } elseif ($sort === 'desc') {
        $sql .= " ORDER BY firstName DESC";
      }

      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $fullName = $row['firstName'] . ' ' . $row['lastName'];
          $photoPath = '../assets/images/drivers/' . $row['photo'];
          $isDeleted = (int) $row['isDeleted'];
          $cardOpacity = $isDeleted ? "opacity-50" : "";
      ?>
          <div class="card border-0 clickable-card <?php echo $cardOpacity; ?>" style="background-color: #D9D9D9; border-radius: 30px; cursor: pointer;" onclick="goToDriverView(<?php echo $row['userId']; ?>)">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <?php if (!empty($row['photo']) && file_exists($photoPath)) { ?>
                    <img src="<?php echo $photoPath; ?>" alt="Driver Photo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                  <?php } else { ?>
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                      <i class="bi bi-person-fill"></i>
                    </div>
                  <?php } ?>
                </div>
                <span class="text-dark fw-medium"><?php echo htmlspecialchars($fullName); ?></span>
              </div>

              <?php if ($isDeleted): ?>
                <span class="badge bg-secondary px-3 py-1 rounded-pill">Inactive</span>
              <?php else: ?>
                <div class="d-flex gap-2">
                  <a href="editProfileDriver.php?userId=<?php echo (int) $row['userId']; ?>" class="btn btn-info btn-sm rounded-circle text-white" onclick="event.stopPropagation();">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" data-user-id="<?php echo $row['userId']; ?>" class="btn btn-danger btn-sm rounded-circle text-white" onclick="event.stopPropagation();">
                    <i class="bi bi-trash-fill"></i>
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='text-center text-muted'>No drivers found.</p>";
      }
      ?>
    </div>
  </div>

  <!-- Delete Modal -->
  <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0" style="width: 85%; max-width: 320px; margin: auto;">
        <h5 class="fw-bold mb-2" id="deleteModalLabel">Confirm Deletion</h5>
        <p class="mb-4" style="font-size: 0.95rem;">Are you sure you want to delete this driver? This action cannot be undone.</p>
        <div class="d-flex justify-content-center gap-3">
          <button type="button" class="btn rounded-pill px-4" style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
          <a id="confirmDeleteBtn" href="#" class="btn rounded-pill px-4 text-white" style="background-color: #1cc8c8; font-weight: 600;">Yes</a>
        </div>
      </div>
    </div>
  </div>

  <?php include '../assets/shared/navbarAdmin.php'; ?>

  <script>
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    deleteModal.addEventListener('show.bs.modal', function(event) {
      const triggerButton = event.relatedTarget;
      const userId = triggerButton.getAttribute('data-user-id');
      confirmDeleteBtn.href = 'deleteDriver.php?userId=' + userId;
    });

    function goToDriverView(userId) {
      window.location.href = "driverView.php?userId=" + userId;
    }
  </script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
