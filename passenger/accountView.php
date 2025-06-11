<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODA Rescue - Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="bg-dark d-flex justify-content-center align-items-center vh-100"
    style="font-family: 'Inter', sans-serif; margin: 0;">
    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">
                <div class="card bg-white w-100 h-100 d-flex flex-column p-0 rounded-0 rounded-bottom-4 shadow-lg"
                    style="--bs-border-radius-bottom: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">
                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-start shadow px-4 bg-white"
                        style="height: 100px; border-bottom-left-radius: 43px; border-bottom-right-radius: 43px;">
                        <a href="#" class="me-2 fs-5 fw-bold text-decoration-none text-dark">←</a>
                        <h5 class="mb-0 fw-bold">Account</h5>
                    </div>

                    <!-- Account Content -->
                    <div class="list-group list-group-flush mt-2 px-0 w-100 flex-grow-1 overflow-auto">
                        <!-- Profile Section -->
                        <div class="px-3 pt-3 pb-1 text-secondary fw-bold text-uppercase small">
                            Profile
                        </div>

                        <!-- Profile Form -->
                        <form class="w-100 px-3">
                            <!-- Avatar -->
                            <div class="list-group-item list-group-item-action py-3 border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <!-- Smaller Circular Avatar (left side) -->
                                    <div class="rounded-circle me-3"
                                        style="width: 65px; height: 65px; background-color: #a59e9e;"></div>
                                    <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                                        <span>Username</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Details Section -->
                            <div class="pt-3 pb-1 text-secondary fw-bold text-uppercase small">
                                Account Details
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Phone Number</label>
                                <input type="tel" class="form-control border-0 border-bottom rounded-0 shadow-none p-0"
                                    value="+639998881234" style="border-bottom: 1px solid #dee2e6;" />
                            </div>

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Email Address</label>
                                <input type="email"
                                    class="form-control border-0 border-bottom rounded-0 shadow-none p-0"
                                    value="bryan.reano@gmail.com" style="border-bottom: 1px solid #dee2e6;" />
                            </div>

                            <!-- Ride History Section -->
                            <div class="pt-3 pb-1 text-secondary fw-bold text-uppercase small">
                                Ride History
                            </div>

                            <!-- Ride History Content -->
                            <div class="mb-4 text-center text-muted py-2">
                                No ride history available
                            </div>

                            <!-- Edit Profile Button -->
                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn text-white px-5"
                                    style="background-color: #24b3a7; border-radius: 15px;">Edit Profile</button>
                            </div>
                        </form>
                    </div>

                    <?php include '../assets/shared/navbarPassenger.php'; ?>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                    <script>
                        function handleClick(page) {
                            console.log("Navigating to: " + page);
                            // window.location.href = page + '.html';
                        }
                    </script>
</body>

</html>