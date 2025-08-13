<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | TODA Rescue</title>
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .team-photo {
            width: 100%;
            aspect-ratio: 1/1;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 14px;
            color: #555;
            border: 2px solid #ddd;
        }
    </style>
</head>

<body class="bg-white justify-content-center align-items-start min-vh-100 pt-3">
    <?php include '../assets/shared/navbarDriver.php'; ?>

    <div class="container px-4 pt-3" style="max-width: 600px;">

        <div class="sticky-top bg-white pb-2 pt-3 z-3">
            <h3 class="fw-bold text-center mb-3">TODA Rescue</h3>
            <div class="d-flex align-items-center gap-2">
                <a href="javascript:history.back()" class="text-dark d-flex align-items-center me-1">
                    <img src="../assets/images/arrow-left.svg" alt="Back" width="15" height="15">
                </a>
                <h5 class="mb-0 fw-semibold">About</h5>
            </div>
        </div>
    </div>

    <div>
        <img src="../assets/images/about-banner.png" alt="Banner" class="img-fluid w-100">
    </div>

    <div class="container px-4 pt-4" style="max-width: 600px;">

        <div class="text-center mb-4 px-2">
            <h5 class="fw-bold mb-2">About TODA Rescue</h5>
            <p class="mb-0">
                TODARescue is a web-based platform designed to enhance the safety and transparency of tricycle
                transportation in Barangay Janopol, Batangas. Built for both commuters and drivers, our system ensures
                accountability, trust, and security within the local TODA (Tricycle Operators and Drivers’ Association)
                community.
            </p>
        </div>


        <div class="text-center mb-4 px-2">
            <h5 class="fw-bold mb-2">Our Mission</h5>
            <p class="mb-0">
                To empower commuters with a safer and more informed commuting experience, while supporting local drivers
                with a digital system that promotes transparency and professionalism.
            </p>
        </div>

        <div class="text-center mb-3 px-2">
            <h5 class="fw-bold mb-2">Meet the Team</h5>
            <p>
                We are a group of passionate student developers and designers studying in Polytechnic University of the
                Philippines – Santo Tomas, dedicated to building innovative solutions for real-world problems.
            </p>
        </div>

        <div class="row row-cols-2 g-3 mb-5">
            <div class="col text-center">
                <img src="../assets/images/edcel.png" class="team-photo img-fluid" alt="Team Member 1">
                <div class="mt-2 small">Edcel Esquivel</div>
            </div>
            <div class="col text-center">
                <img src="../assets/images/bryan.png" class="team-photo img-fluid" alt="Team Member 2">
                <div class="mt-2 small">Jamiel Bryan Reaño</div>
            </div>
            <div class="col text-center">
                <img src="../assets/images/janna.png" class="team-photo img-fluid" alt="Team Member 3">
                <div class="mt-2 small">Janne Mae Macatangay</div>
            </div>
            <div class="col text-center">
                <img src="../assets/images/stephen.png" class="team-photo img-fluid" alt="Team Member 4">
                <div class="mt-2 small">John Stephen Galarrita</div>
            </div>
            <div class="col text-center">
                <img src="../assets/images/jomari.png" class="team-photo img-fluid" alt="Team Member 5">
                <div class="mt-2 small">Jomari Castillo</div>
            </div>
            <div class="col text-center mb-5">
                <img src="../assets/images/ken.png" class="team-photo img-fluid" alt="Team Member 6">
                <div class="mt-2 small">Ken Milorin</div>
            </div>
        </div>

    </div>
    <!-- Change status -->
    <script>
        document.addEventListener("visibilitychange", () => {
            if (document.visibilityState === "hidden") {
                updateStatus(0);
            } else {
                updateStatus(2);
            }
        });

        function updateStatus(state) {
            fetch(`../assets/php/updateStatus.php?visibility=${state}`)
                .catch(err => console.error("Failed to update status:", err));
        }
    </script>
</body>

</html>