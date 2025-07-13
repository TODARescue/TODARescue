<link href="../assets/css/style.css" rel="stylesheet" />

<body>
    <div class="container-fluid position-fixed bottom-0 z-1 w-100 px-0 d-xl-none">
        <div class="row custom-navbar mx-0" style="height: 50px;"> 
            <div class="col col-navbar text-center" data-icon="home">
                <div class="nav-button w-100 h-100 py-0 d-flex justify-content-center align-items-center"> 
                    <a href="../admin/index.php">
                        <img src="../assets/shared/navbar-icons/home.svg" alt="Home" class="img-fluid" style="height: 24px;" /> 
                    </a>
                </div>
            </div>
            <div class="col col-navbar text-center" data-icon="users">
                <div class="nav-button w-100 h-100 py-0 d-flex justify-content-center align-items-center">
                    <a href="../admin/passengers.php">
                        <img src="../assets/shared/navbar-icons/users.svg" alt="Users" class="img-fluid" style="height: 24px;" />
                    </a>
                </div>
            </div>
            <div class="col col-navbar text-center" data-icon="drivers">
                <div class="nav-button w-100 h-100 py-0 d-flex justify-content-center align-items-center">
                    <a href="../admin/drivers.php">
                        <img src="../assets/shared/navbar-icons/drivers.svg" alt="Drivers" class="img-fluid" style="height: 24px;" />
                    </a>
                </div>
            </div>
            <div class="col col-navbar text-center" data-icon="settings">
                <div class="nav-button w-100 h-100 py-0 d-flex justify-content-center align-items-center">
                    <a href="#">
                        <img src="../assets/shared/navbar-icons/settings.svg" alt="Settings" class="img-fluid" style="height: 24px;" />
                    </a>
                </div>
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

            if (currentPath.endsWith(href.split('/').pop())) {
                col.classList.add('active');
                col.querySelector('img').src = `../assets/shared/navbar-icons/${iconName}-white.svg`;
            } else {
                col.classList.remove('active');
                col.querySelector('img').src = `../assets/shared/navbar-icons/${iconName}.svg`;
            }
        });
    </script>
</body>
