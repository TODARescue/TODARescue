<?php
include '../assets/shared/connect.php';

$search = $_GET['search'] ?? '';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODARescue | Drivers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-white d-flex justify-content-center align-items-start min-vh-100 pt-5">

    <div class="container px-4 pb-5" style="max-width: 500px;">
        <h3 class="fw-bold text-center mb-3">TODA Rescue</h3>
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-3">Drivers</h5>
            <button onclick="location.href='addDrivers.php'"
                class="btn rounded-pill d-flex align-items-center justify-content-center mb-3"
                style="background-color: #2EBCBC; border: none; width: 60px; height: 30px; padding: 0;">
                <i class="bi bi-plus" style="font-size: 20px; color: white;"></i>
            </button>
        </div>

        <form method="GET" class="search-bar mb-3">
            <div class="input-group shadow">
                <input type="text" name="search" class="form-control" placeholder="Search Drivers" aria-label="Search"
                    aria-describedby="search-addon" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-secondary" type="submit" id="search-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-search" viewBox="0 0 16 16">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </button>
            </div>
        </form>


        <div class="mt-4 d-flex flex-column gap-3 mb-4">
            <?php
            $sql = "SELECT * FROM users WHERE role = 'driver' AND isDeleted = 0";
            if (!empty($search)) {
                $safeSearch = mysqli_real_escape_string($conn, $search);
                $sql .= " AND (
        firstName LIKE '%$safeSearch%' 
        OR lastName LIKE '%$safeSearch%' 
        OR CONCAT(firstName, ' ', lastName) LIKE '%$safeSearch%'
    )";
            }


            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $fullName = $row['firstName'] . ' ' . $row['lastName'];
                    $photoPath = '../assets/images/drivers/' . $row['photo'];
                    ?>
                    <div class="card border-0 clickable-card"
                        style="background-color: #D9D9D9; border-radius: 30px; cursor: pointer;"
                        onclick="goToDriverView(<?php echo $row['userId']; ?>)">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <?php if (!empty($row['photo']) && file_exists($photoPath)) { ?>
                                        <img src="<?php echo $photoPath; ?>" alt="Driver Photo" class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php } else { ?>
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    <?php } ?>
                                </div>
                                <span class="text-dark fw-medium"><?php echo htmlspecialchars($fullName); ?></span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="editProfileDriver.php?userId=<?php echo (int) $row['userId']; ?>"
                                    class="btn btn-info btn-sm rounded-circle text-white" onclick="event.stopPropagation();">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-user-id="<?php echo $row['userId']; ?>"
                                    class="btn btn-danger btn-sm rounded-circle text-white"
                                    style="text-decoration: none; color: inherit;" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash-fill"></i>
                                </a>

                            </div>
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

    <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <h5 class="fw-bold mb-2" id="deleteModalLabel">Confirm Deletion</h5>
                <p class="mb-4" style="font-size: 0.95rem;">
                    Are you sure you want to delete this driver? This action cannot be undone.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn rounded-pill px-4"
                        style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <a id="confirmDeleteBtn" href="#" class="btn rounded-pill px-4 text-white"
                        style="background-color: #1cc8c8; font-weight: 600;">
                        Yes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarAdmin.php'; ?>

    <script>
        const deleteModal = document.getElementById('deleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        deleteModal.addEventListener('show.bs.modal', function (event) {
            const triggerButton = event.relatedTarget;
            const userId = triggerButton.getAttribute('data-user-id');


            confirmDeleteBtn.href = 'deleteDriver.php?userId=' + userId;
        });
    </script>

    <script>
        function goToDriverView(userId) {
            window.location.href = "driverView.php?userId=" + userId;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>