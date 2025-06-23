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

   <div class="container-fluid py-5 mt-5 d-flex justify-content-center">
        <!-- Invite members text -->
        <div class="flex-grow-1 px-3 pt-5">
            
            <div class="card border-0 mb-4 rounded-5" style="background-color: #D9D9D9">
                <div class="card-body text-center py-5">
                    <div class="card-text mb-3 fs-3 fw-bold">Enter the Invite Code</div>

                    <!-- Input field -->
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                        <input type="text" class="form-control custom-hover text-center fw-bold fs-6 py-3 px-3 border-0 rounded-4" maxlength="1" value="" style="width: 45px; height: 55px;">
                        <input type="text" class="form-control custom-hover text-center fw-bold fs-6 py-3 px-3 border-0 rounded-4" maxlength="1" value="" style="width: 45px; height: 55px;">
                        <input type="text" class="form-control custom-hover text-center fw-bold fs-6 py-3 px-3 border-0 rounded-4" maxlength="1" value="" style="width: 45px; height: 55px;">
                        <span class="fw-bold fs-3 mx-2 text-dark">-</span>
                        <input type="text" class="form-control custom-hover text-center fw-bold fs-6 py-3 px-3 border-0 rounded-4" maxlength="1" value="" style="width: 45px; height: 55px;">
                        <input type="text" class="form-control custom-hover text-center fw-bold fs-6 py-3 px-3 border-0 rounded-4" maxlength="1" value="" style="width: 45px; height: 55px;">
                        <input type="text" class="form-control custom-hover text-center fw-bold fs-6 py-3 px-3 border-0 rounded-4" maxlength="1" value="" style="width: 45px; height: 55px;">
                    </div>

                    <p class="text-muted small mb-3">Get the code from the person<br>setting up your circle.</p>
                    
                    <!-- Join button -->
                    <button class="btn custom-hover text-black px-4 py-2 rounded-pill fw-semibold">JOIN</button>
                </div>
            </div>
        </div>
   </div>

   <script>
    //Auto next to bih
    document.querySelectorAll('input[maxlength="1"]').forEach((input, index, inputs) => {
        input.addEventListener('input', function() {
            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
    
        input.addEventListener('keydown', function(press) {
            if (press.key === 'Backspace' && this.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>