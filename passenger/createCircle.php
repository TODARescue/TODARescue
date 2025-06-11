<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- HEADER -->
    <?php include '../assets/shared/header.php'; ?>

    <!-- NAVBAR -->
   <?php include '../assets/shared/navbarPassenger.php'; ?>

   <div class="container-fluid py-5 vh-100 d-flex flex-column">
        <!-- Invite members text -->
        <div class="flex-grow-1 px-3">
            <div class="mb-4">
                <h3 class="fw-bold mb-3">Name Your Circle</h3   >
                <input type="text" class="form-control border-0 rounded-pill py-3 px-4" 
                       placeholder="Enter the name for your new circle" style="background-color: #d9d9d9">
            </div>
            
            <!-- Submit button -->
            <div class="text-center mt-4">
                <button class="btn custom-hover px-4 py-2 rounded-pill">Submit</button>
            </div>
        </div>
   </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>