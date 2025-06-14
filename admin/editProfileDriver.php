<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TODA Rescue - Driver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    </head>
</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex flex-column justify-content-start pt-4 position-relative z-1">

        <div class="row mb-3">
            <div class="col text-center">
                <h5 class="fw-bold m-0">TODA Rescue</h5>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col d-flex align-items-center ps-2">
            <a href="#" class="me-2 text-decoration-none">
                    <img src="../assets/images/arrow-back-admin.svg" alt="Back" style="width: 15px; height: 15px;">
                </a>
                <h5 class="fw-semibold m-0">Drivers</h5>
            </div>
        </div>
        <div class="row justify-content-center mb-4">
            <div class="col-auto">
                <img src="../assets/images/logo.png" alt="Profile" class="rounded-circle"
                    style="width:100px; height:100px;">
            </div>
        </div>
        <div class="row">
            <div class="col px-4">
            <form>
                <input type="text" class="form-control border-0 border-bottom mb-3" placeholder="First Name">
                <input type="text" class="form-control border-0 border-bottom mb-3" placeholder="Last Name">
                <input type="text" class="form-control border-0 border-bottom mb-3" placeholder="Contact Number">
                <input type="email" class="form-control border-0 border-bottom mb-4" placeholder="Email">
                <input type="text" class="form-control border-0 border-bottom mb-4" placeholder="Tricycle Number">
                <input type="text" class="form-control border-0 border-bottom mb-4" placeholder="Permanent Address">
                <input type="text" class="form-control border-0 border-bottom mb-4" placeholder="Toda Registration">
                <input type="radio" name="verification" value="unverified">
                <label for="unverified">Unverified</label>
                <input type="radio" name="verification" value="verified">
                <label for="verified">Verified</label>
            </form>
            </div>
        </div>
        <div class="row">
            <div class="col d-flex justify-content-center mt-3">
                <button class="btn w-50 rounded-pill border-0" style="background-color: #2DAAA7; color: white;">
                    Save
                </button>
            </div>
        </div>

    </div>

</body>

</html>