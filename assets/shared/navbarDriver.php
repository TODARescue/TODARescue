<link href="../assets/css/style.css" rel="stylesheet" />

<!-- Mobile Bottom Navbar -->
<div class="container-fluid position-fixed bottom-0 start-0 end-0 z-1 px-0 d-xl-none bg-white">
    <div class="row custom-navbar g-0 m-0" style="height: 50px;">
        <div class="col col-navbar text-center" data-icon="home">
            <a href="../driver/homePage.php" class="d-flex flex-column justify-content-center align-items-center w-100 h-100">
                <img src="../assets/shared/navbar-icons/home.svg" alt="Home" class="img-fluid" style="height: 24px;" />
            </a>
        </div>
        <div class="col col-navbar text-center" data-icon="users">
            <a href="../driver/circle.php" class="d-flex flex-column justify-content-center align-items-center w-100 h-100">
                <img src="../assets/shared/navbar-icons/users.svg" alt="Users" class="img-fluid" style="height: 24px;" />
            </a>
        </div>
        <div class="col col-navbar text-center" data-icon="settings">
            <a href="../driver/settings.php" class="d-flex flex-column justify-content-center align-items-center w-100 h-100">
                <img src="../assets/shared/navbar-icons/settings.svg" alt="Settings" class="img-fluid" style="height: 24px;" />
            </a>
        </div>
    </div>
</div>

<script>
    const cols = document.querySelectorAll('.custom-navbar .col-navbar');
    const currentPath = window.location.pathname;

    cols.forEach(col => {
        const iconName = col.getAttribute('data-icon');
        const link = col.querySelector('a');
        const href = link.getAttribute('href');
        const img = col.querySelector('img');

        if (currentPath.endsWith(href.split('/').pop())) {
            col.classList.add('active');
            img.src = `../assets/shared/navbar-icons/${iconName}-white.svg`;
        } else {
            col.classList.remove('active');
            img.src = `../assets/shared/navbar-icons/${iconName}.svg`;
        }
    });
</script>
