<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<style>

    .custom-navbar {
        background-color: #F4FAFF;
        box-shadow:
            0 -1px 6px 3px rgba(0, 0, 0, 0.1),
            0 0 18px 6px rgba(0, 0, 0, 0.15);
    }


    .col {
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        padding: 5px 0;
    }

    .col.active {
        background-color: #2DAAA7;
        box-shadow:
            inset 0 0 3px 3px rgba(64, 64, 64, 0.4),
            0 0 6px rgba(123, 123, 123, 0.15);
    }

</style>

<body>
    <div class="container-fluid position-fixed bottom-0 z-1 w-100 px-0 d-xl-none">
        <div class="row custom-navbar mx-0">
            <div class="col text-center" data-icon="home">
                <div class="nav-button w-100 h-100 p-2 d-flex justify-content-center align-items-center">
                    <img src="navbar-icons/home.svg" alt="Home" class="img-fluid" />
                </div>
            </div>
            <div class="col text-center" data-icon="users">
                <div class="nav-button w-100 h-100 p-2 d-flex justify-content-center align-items-center">
                    <img src="navbar-icons/users.svg" alt="Users" class="img-fluid" />
                </div>
            </div>
            <div class="col text-center" data-icon="circle">
                <div class="nav-button w-100 h-100 p-2 d-flex justify-content-center align-items-center">
                    <img src="navbar-icons/circle.svg" alt="Add" class="img-fluid" />
                </div>
            </div>
            <div class="col text-center" data-icon="phone">
                <div class="nav-button w-100 h-100 p-2 d-flex justify-content-center align-items-center">
                    <img src="navbar-icons/phone.svg" alt="Phone" class="img-fluid" />
                </div>
            </div>
            <div class="col text-center" data-icon="settings">
                <div class="nav-button w-100 h-100 p-2 d-flex justify-content-center align-items-center">
                    <img src="navbar-icons/settings.svg" alt="Settings" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>

    <script>
        const cols = document.querySelectorAll('.custom-navbar .col');
        cols.forEach(col => {
            col.addEventListener('click', () => {
                cols.forEach(c => {
                    c.classList.remove('active');
                    const iconName = c.getAttribute('data-icon');
                    c.querySelector('img').src = `navbar-icons/${iconName}.svg`;
                });

                col.classList.add('active');
                const iconName = col.getAttribute('data-icon');
                col.querySelector('img').src = `navbar-icons/${iconName}-white.svg`;
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"
        integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D"
        crossorigin="anonymous"></script>
</body>

</html>