<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Remove Circle Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 h-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">
                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- HEADER -->
                    <?php include '../assets/shared/header.php'; ?>

                    <!-- Member List -->
                    <div class="container-fluid" style="padding-top: 70px;">
                        <div class="row">
                            <div class="col list-group list-group-flush px-0 w-100">
                                <div class="mb-1">
                                    <h4 class="fs-5 mt-5 px-4">Remove Members</h4>
                                </div>

                                <div class="container-fluid p-0">
                                    <div class="list-group">
                                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary"
                                            onclick="openRemoveModal('Juan Dela Cruz')">
                                            <span class="fw-medium">Juan Dela Cruz</span>
                                            <img src="../assets/images/remove-member.svg" alt="Remove" style="max-width: 30px;" />
                                        </div>

                                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary"
                                            onclick="openRemoveModal('Ivy Aguas')">
                                            <span class="fw-medium">Ivy Aguas</span>
                                            <img src="../assets/images/remove-member.svg" alt="Remove" style="max-width: 30px;" />
                                        </div>

                                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary"
                                            onclick="openRemoveModal('Maya Dela Rosa')">
                                            <span class="fw-medium">Maya Dela Rosa</span>
                                            <img src="../assets/images/remove-member.svg" alt="Remove" style="max-width: 30px;" />
                                        </div>

                                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary"
                                            onclick="openRemoveModal('Sir Chief Ricky')">
                                            <span class="fw-medium">Sir Chief Ricky</span>
                                            <img src="../assets/images/remove-member.svg" alt="Remove" style="max-width: 30px;" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Backdrop -->
                    <div id="modalBackdrop"
                        class="position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center z-1"
                        style="background-color: rgba(255, 255, 255, 0.4);">
                        <!-- Modal Box -->
                        <div class="bg-white p-4 rounded-5 shadow text-center" style="width: 85%; max-width: 320px;">
                            <h5 class="fw-bold mb-2">Remove Member</h5>
                            <p class="mb-4" style="font-size: 0.95rem;">
                                Are you sure you want to remove <b id="modalMemberName">[Name]</b> from this circle?
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <button class="btn rounded-pill px-4" style="background-color: #dcdcdc; font-weight: 600;"
                                    onclick="closeModal()">No</button>
                                <button class="btn rounded-pill px-4 text-white" style="background-color: #1cc8c8; font-weight: 600;">
                                    Yes
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarPassenger.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function openRemoveModal(name) {
            document.getElementById('modalMemberName').innerText = name;
            document.getElementById('modalBackdrop').classList.remove('d-none');
            document.getElementById('modalBackdrop').classList.add('d-flex');
        }

        function closeModal() {
            document.getElementById('modalBackdrop').classList.remove('d-flex');
            document.getElementById('modalBackdrop').classList.add('d-none');
        }
    </script>
</body>

</html>
