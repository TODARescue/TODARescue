<?php

$isRiding = isset($_SESSION['isRiding']) ? $_SESSION['isRiding'] : false;
?>
<link href="../assets/css/style.css" rel="stylesheet" />

<div class="container-fluid position-fixed bottom-0 start-0 end-0 z-1 px-0 d-xl-none bg-white">
    <div class="row custom-navbar g-0 m-0" style="height: 50px;">
        <div class="col col-navbar text-center" data-icon="home">
            <a href="../passenger/index.php" class="d-flex flex-column justify-content-center align-items-center py-1 w-100 h-100">
                <img src="../assets/shared/navbar-icons/home.svg" alt="Home" class="img-fluid" style="height: 24px;" />
            </a>
        </div>
        <div class="col col-navbar text-center" data-icon="users">
            <a href="../passenger/groupPage.php" class="d-flex flex-column justify-content-center align-items-center py-1 w-100 h-100">
                <img src="../assets/shared/navbar-icons/users.svg" alt="Users" class="img-fluid" style="height: 24px;" />
            </a>
        </div>
        <div class="col col-navbar text-center" data-icon="circle" <?php if ($isRiding): ?>
            style="background-color: #afb0b1ff; cursor: not-allowed; opacity: 0.7;"
            <?php endif; ?>>
            <?php if (!$isRiding) { ?>
                <a href="../passenger/scanQr.php" class="d-flex flex-column justify-content-center align-items-center py-1 w-100 h-100">
                    <img src="../assets/shared/navbar-icons/circle.svg" alt="Add" class="img-fluid" style="height: 24px;" />
                </a>
            <?php } else { ?>
                <div class="d-flex flex-column justify-content-center align-items-center py-1 w-100 h-100" style="opacity: 1;">
                    <img src="../assets/shared/navbar-icons/circle.svg" alt="Add (Disabled)" class="img-fluid" style="height: 24px;" />
                </div>
            <?php } ?>
        </div>
        <div class="col col-navbar text-center" data-icon="phone">
            <a href="../passenger/emergencyContact.php" class="d-flex flex-column justify-content-center align-items-center py-1 w-100 h-100">
                <img src="../assets/shared/navbar-icons/phone.svg" alt="Phone" class="img-fluid" style="height: 24px;" />
            </a>
        </div>
        <div class="col col-navbar text-center" data-icon="settings">
            <a href="../passenger/settings.php" class="d-flex flex-column justify-content-center align-items-center py-1 w-100 h-100">
                <img src="../assets/shared/navbar-icons/settings.svg" alt="Settings" class="img-fluid" style="height: 24px;" />
            </a>
        </div>
    </div>
</div>

<script>
    const cols = document.querySelectorAll('.custom-navbar .col-navbar');
    const currentPath = window.location.pathname;
    console.log("Riding? " + <?php echo json_encode($isRiding); ?>);

    cols.forEach(col => {
        const iconName = col.getAttribute('data-icon');
        const link = col.querySelector('a');
        const href = link.getAttribute('href');

        if (currentPath.includes(href.split('/').pop())) {
            col.classList.add('active');
            col.querySelector('img').src = `../assets/shared/navbar-icons/${iconName}-white.svg`;
        } else {
            col.classList.remove('active');
            col.querySelector('img').src = `../assets/shared/navbar-icons/${iconName}.svg`;
        }
    });
</script>