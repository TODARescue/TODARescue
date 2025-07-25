<?php
include("../assets/shared/connect.php");   
session_start();

$_SESSION['userId']    = $_SESSION['userId'];
$_SESSION['firstName'] = $_SESSION['firstName'] ?? "";
$_SESSION['lastName']  = $_SESSION['lastName']  ?? "";
$_SESSION['email']     = $_SESSION['email']     ?? "";
$_SESSION['contact']   = $_SESSION['contact']   ?? "";

$viewerId = $_SESSION['userId'];  
$contacts = [];

if ($viewerId !== "") {

    $sql = "
    SELECT DISTINCT
            u.userId,
            CONCAT(u.firstName, ' ', u.lastName) AS fullName,
            u.contactNumber,
            c.circleName AS circleName,
            'personal' AS contactType
        FROM       circlemembers viewerCM
        JOIN       circlemembers otherCM
                ON otherCM.circleId = viewerCM.circleId
        JOIN       users u
                ON u.userId = otherCM.userId
        JOIN       circles c
                ON c.circleId = otherCM.circleId
        WHERE      viewerCM.userId = ? 
        AND        u.userId <> ?
        ORDER BY   fullName;
    ";


    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $viewerId, $viewerId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }

    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
}

$circles = [];

$circleSql = "
    SELECT c.circleId, c.circleName
    FROM circlemembers cm
    JOIN circles c ON cm.circleId = c.circleId
    WHERE cm.userId = ?
";
$circleStmt = mysqli_prepare($conn, $circleSql);
mysqli_stmt_bind_param($circleStmt, "i", $viewerId);
mysqli_stmt_execute($circleStmt);

$circleResult = mysqli_stmt_get_result($circleStmt);
while ($row = mysqli_fetch_assoc($circleResult)) {
    $circles[] = $row;
}

mysqli_free_result($circleResult);
mysqli_stmt_close($circleStmt);


$emergencyHotline = [];
$jsonPath = __DIR__ . '/../assets/data/EmergencyHotline.json';

if (file_exists($jsonPath)) {
    $json     = file_get_contents($jsonPath);
    $emergencyHotline = json_decode($json, true) ?: [];
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Passenger | Emergency Contact</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<?php include '../assets/shared/header.php'; ?>
<?php include '../assets/shared/navbarPassenger.php'; ?>

<!-- FILTER DROPDOWN -->
<div class="dropdown" style="padding-top:60px;">
  <button class="btn border border-secondary bg-transparent text-black shadow rounded-5 fw-semibold dropdown-toggle d-flex align-items-center gap-5 mt-5 mx-3 mb-3"
          type="button" id="filterButton" data-bs-toggle="dropdown" aria-expanded="false">
          Filter
  </button>
  <ul class="dropdown-menu" aria-labelledby="filterButton">
      <li><a class="dropdown-item" href="#" data-type-filter="all">All</a></li>
      <li><a class="dropdown-item" href="#" data-type-filter="toda">TODA Office</a></li>
      <li><a class="dropdown-item" href="#" data-type-filter="police">Police</a></li>
      <li><a class="dropdown-item" href="#" data-type-filter="medical">Medical Services</a></li>
      <?php foreach ($circles as $circle): ?>
        <li>
        <a class="dropdown-item" href="#" data-type-filter="<?= htmlspecialchars($circle['circleName']) ?>">
            <?= htmlspecialchars($circle['circleName']) ?>
        </a>
        </li>
        <?php endforeach; ?>

  </ul>
</div>

<!-- MAIN CONTAINER -->
<div class="container px-3 pb-5">
  <div class="row justify-content-center">

    <!-- EMERGENCY HOTLINES -->
    <?php foreach ($emergencyHotline as $h): ?>
      <div class="card contact-card col-12 col-md-8 p-4 rounded-5 mb-3"
           data-type-filter="<?= htmlspecialchars($h['type']) ?>">
          <div class="d-flex justify-content-between align-items-center mb-3">
              <h3 class="fw-bold mb-0 m-1"><?= htmlspecialchars($h['name']) ?></h3>
              <a href="tel:<?= htmlspecialchars($h['contactNumber']) ?>" onclick="changePhoneIcon(this)">
                  <img src="../assets/images/phone-white.svg" class="img-fluid" style="max-width:60px;" alt="Call Button"/>
              </a>
          </div>
          <div class="mb-2">
              <h5 class="fw-semibold mb-1">Contact Number:</h5>
              <p class="mb-2"><?= htmlspecialchars($h['contactNumber']) ?></p>
          </div>
          <div>
              <h5 class="fw-semibold mb-1">Permanent Address:</h5>
              <p><?= htmlspecialchars($h['permanentAddress']) ?></p>
          </div>
      </div>
    <?php endforeach; ?>

    <!-- PERSONAL CONTACTS -->
        <?php foreach ($contacts as $c): ?>
        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 mb-3"
            data-type-filter="<?= htmlspecialchars($c['circleName']) ?>">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1"><?= htmlspecialchars($c['fullName']) ?></h3>
                <a href="tel:<?= htmlspecialchars($c['contactNumber']) ?>" onclick="changePhoneIcon(this)">
                    <img src="../assets/images/phone-white.svg" class="img-fluid" style="max-width:60px;" alt="Call Button"/>
                </a>
            </div>
            <div class="mb-2">
                <h5 class="fw-semibold mb-1">Contact Number:</h5>
                <p class="mb-2"><?= htmlspecialchars($c['contactNumber']) ?></p>
            </div>
            <div class="mb-2">
                <h5 class="fw-semibold mb-1">Circle Name:</h5>
                <p class="mb-2"><?= htmlspecialchars($c['circleName']) ?></p>
            </div>
            <div>
                <h5 class="fw-semibold mb-1">Permanent Address:</h5>
                <p>Janopol Occidental, Tanauan City, Batangas</p>
            </div>
        </div>
        <?php endforeach; ?>


    <?php if (empty($contacts) && empty($emergencyHotline)): ?>
        <p class="text-center text-muted">No contacts available.</p>
    <?php endif; ?>
  </div>
</div>

<p id="noContactsMessage" class="text-center text-muted" style="display: none;">
    No contacts available.
</p>


<!-- JS -->
<script>
function changePhoneIcon(link){
    const img = link.querySelector('img');
    img.src = "../assets/images/phone-blue.svg";
    setTimeout(() => {
        img.src = "../assets/images/phone-white.svg";
    }, 5000);
}

$('.dropdown-item').click(function(e){
    e.preventDefault();
    const selected = $(this).data('type-filter');
    $('.dropdown-item').removeClass('active');
    $(this).addClass('active');

    let visibleCount = 0;

    $('.contact-card').each(function(){
        const type = $(this).data('type-filter');
        if (selected === 'all' || type === selected) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });

    if (visibleCount === 0) {
        $('#noContactsMessage').show();
    } else {
        $('#noContactsMessage').hide();
    }
});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
