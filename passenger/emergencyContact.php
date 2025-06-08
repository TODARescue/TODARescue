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
     <link href="../assets/css/style.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- HEADER -->
    <?php include '../assets/shared/header.php'; ?>

    <!-- NAVBAR -->
   <?php include '../assets/shared/navbarPassenger.php'; ?>

  <div class="dropdown pt-5">
    <button class="btn border border-black bg-transparent text-black shadow rounded-5 fw-semibold dropdown-toggle d-flex align-items-center gap-5 mt-5 mx-3 mb-3"
        type="button"
        id="filterButton"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        Filter
    </button>

        <ul class="dropdown-menu" aria-labelledby="filterButton">
            <li><a class="dropdown-item" href="#" data-type-filter="all">All</a></li>
            <li><a class="dropdown-item" href="#" data-type-filter="toda">TODA Office</a></li>
            <li><a class="dropdown-item" href="#" data-type-filter="police">Police</a></li>
            <li><a class="dropdown-item" href="#" data-type-filter="medical">Medical Services</a></li>
            <li><a class="dropdown-item" href="#" data-type-filter="personal">Personal Contacts</a></li>
        </ul>
    </div>

<div class="container px-3"> 
    <div class="row justify-content-center">
        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type-filter="police">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">PNP (Philippine National Police)</h3>
                <a href="tel:097843954267" onclick="changePhoneIcon(this)">
                    <img src="../assets/images/phone-white.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
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

        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type-filter="medical">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">Tanauan Medical Center</h3>
                <a href="tel:097843954267" onclick="changePhoneIcon(this)">
                    <img src="../assets/images/phone-white.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
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

        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type-filter="toda">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">Janopol TODA Office</h3>
                <a href="tel:097843954267" onclick="changePhoneIcon(this)">
                    <img src="../assets/images/phone-white.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
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

        <div class="card contact-card col-12 col-md-8 p-4 rounded-5 shadow mb-5" data-type-filter="personal">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 m-1">My Brother</h3>
                <a href="tel:097843954267" onclick="changePhoneIcon(this)">
                    <img src="../assets/images/phone-white.svg" alt="Call Button" class="img-fluid" style="max-width: 60px;" />
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

<script>
    function changePhoneIcon(link) {
        const img = link.querySelector('img');
        const originalSrc = img.src;

        img.src = "../assets/images/phone-blue.svg";

        setTimeout(() => {
            img.src = "../assets/images/phone-white.svg";
        }, 5000);
    }
</script>

<script>
   $('.dropdown-item').click(function (e) {
      e.preventDefault();

      const selectedFilter = $(this).data('type-filter');

      $('.dropdown-item').removeClass('active');
      $(this).addClass('active');

      $('.contact-card').each(function () {
         const cardType = $(this).data('type-filter');

         if (selectedFilter === 'all' || cardType === selectedFilter) {
            $(this).show();
         } else {
            $(this).hide();
         }
      });
   });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>

</body>

</html>