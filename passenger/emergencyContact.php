<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Inter', sans-serif;
    }

   .btn {
        border: 1px solid black;
        background-color: transparent;
        color: black;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);   
    }
    .contact-card {
        background-color: #D9D9D9;
        max-width: 600px; margin-left: auto; margin-right: auto;
    }

</style>

<body>
    <!-- HEADER -->
    <?php include '../assets/shared/header.php'; ?>

    <!-- NAVBAR -->
   <?php include '../assets/shared/navbarPassenger.php'; ?>

    <div class="dropdown">
        <button class="btn rounded-5 fw-semibold dropdown-toggle d-flex align-items-center gap-5 m-4" type="button" id="filterButton" data-bs-toggle="dropdown" aria-expanded="false">
            Filter 
        </button>

        <ul class="dropdown-menu" aria-labelledby="filterButton">
            <li><a class="dropdown-item" href="#" data-type="all">All</a></li>
            <li><a class="dropdown-item" href="#" data-type="toda">TODA Office</a></li>
            <li><a class="dropdown-item" href="#" data-type="police">Police</a></li>
            <li><a class="dropdown-item" href="#" data-type="medical">Medical Services</a></li>
            <li><a class="dropdown-item" href="#" data-type="personal">Personal Contacts</a></li>
        </ul>
    </div>

<div class="container px-3"> 
    <div class="row justify-content-center">
        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type="police">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">PNP (Philippine National Police)</h3>
                <a href="tel:097843954267">
                    <img src="../assets/images/call-button.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
                </a>
            </div>
            <div class="mb-2">
                <h5 class="fw-semibold mb-1">Contact Number:</h5>
                <p class="mb-2">0978-439-54267</p>
            </div>
            <div class="mb-3">
                <h5 class="fw-semibold mb-1">Permanent Address:</h5>
                <p>Tanauan City, Batangas</p>
            </div>
        </div>

        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type="medical">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">Tanauan Medical Center</h3>
                <a href="tel:097843954267">
                    <img src="../assets/images/call-button.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
                </a>
            </div>
            <div class="mb-2">
                <h5 class="fw-semibold mb-1">Contact Number:</h5>
                <p class="mb-2">0921-345-6789</p>
            </div>
            <div class="mb-3">
                <h5 class="fw-semibold mb-1">Permanent Address:</h5>
                <p>Tanauan City, Batangas</p>
            </div>
        </div>

        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type="toda">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">Janopol TODA Office</h3>
                <a href="tel:097843954267">
                    <img src="../assets/images/call-button.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
                </a>
            </div>
            <div class="mb-2">
                <h5 class="fw-semibold mb-1">Contact Number:</h5>
                <p class="mb-2">0998-765-4321</p>
            </div>
            <div class="mb-3">
                <h5 class="fw-semibold mb-1">Permanent Address:</h5>
                <p>Janopol East, Tanauan</p>
            </div>
        </div>

        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type="personal">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">My Brother</h3>
                <a href="tel:097843954267">
                    <img src="../assets/images/call-button.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
                </a>
            </div>
            <div class="mb-2">
                <h5 class="fw-semibold mb-1">Contact Number:</h5>
                <p class="mb-2">0917-123-4567</p>
            </div>
            <div class="mb-3">
                <h5 class="fw-semibold mb-1">Permanent Address:</h5>
                <p>Tanauan City, Batangas</p>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"
        integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>