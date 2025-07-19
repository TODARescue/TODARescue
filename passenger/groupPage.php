<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Group Dropdown</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .group-selector {
            background-color: #009688;
            color: #fff;
            border-radius: 999px;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            user-select: none;
            display: inline-flex;
            align-items: center;
        }

        .group-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.5rem;
        }

        .action-button {
            background-color: #D9D9D9;
            border: none;
            padding: 0.4rem 1rem;
        }

        .group-container {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            padding: 2.5rem;
            margin-top: 3rem;
            z-index: 3;
            display: none;
        }

        #member-container {
            position: fixed;
            top: 100vh;
            left: 0;
            right: 0;
            height: 100vh;
            overflow-y: auto;
            background: white;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #ccc;
            transition: top 0.3s ease-in-out;
            z-index: 2;
            padding-top: 70px;
            padding-bottom: 100px;
        }

        #toggle-button {
            position: fixed;
            background-color: #2EBCBC;
            bottom: 10vh;
            right: 10px;
            z-index: 6;
        }
    </style>
</head>

<body>
    <div class="position-absolute top-0 start-0 w-100 h-100 z-1" id="map-container">
        <div id="map" class="w-100 h-100" style="pointer-events: auto;"></div>
    </div>

    <button id="toggle-button"
        class="btn btn-primary rounded-circle glass-toggle p-4 text-dark d-flex align-items-center justify-content-center"
        style="width: 48px; height: 48px;">
        <i class="bi bi-people-fill"></i>
    </button>

    <div class="py-3 py-sm-1 container-fluid position-fixed top-0 text-center start-0 end-0 bg-transparent"
        style="z-index: 4;" id="header-color">
        <div class="d-inline-flex align-items-center px-5 py-1 rounded-pill glass-selector"
            style="background-color: #2ebcbc!important; cursor: pointer; user-select: none;" id="group-selector">
            <h4 class="m-0" id="selected-group-name">Select Group ▼</h4>
        </div>
    </div>

    <div class="pt-2 pt-lg-5 group-container shadow rounded-bottom-5 bg-white" id="group-container">
        <div id="group-list" class="mb-3 text-center">Loading...</div>
        <div class="d-flex mt-3">
            <a href="./createCircle.php" class="text-decoration-none">
                <button type="button" class="btn rounded-pill action-button mx-3" style="font-size: 16px;">
                    Create Circle
                </button>
            </a>
            <a href="./joinCircle.php" class="text-decoration-none">
                <button type="button" class="btn rounded-pill action-button position-absolute end-3"
                    style="font-size: 16px;">
                    Join Circle
                </button>
            </a>
        </div>
    </div>

    <div class="container-fluid px-2" id="member-container">
        <div id="member-content"></div>
    </div>

    <div class="position-relative" style="z-index: 5">
        <?php include '../assets/shared/navbarPassenger.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="../assets/js/sharedMap.js"></script>
    <script>
        const groupContainer = document.getElementById('group-container');
        const toggleButton = document.getElementById('toggle-button');
        const groupSelector = document.getElementById('group-selector');
        const groupList = document.getElementById('group-list');
        const selectedGroupName = document.getElementById('selected-group-name');
        const memberContainer = document.getElementById('member-container');
        const memberContent = document.getElementById('member-content');

        toggleButton.addEventListener('click', () => {
            memberContainer.style.top = memberContainer.style.top === '0px' ? '100vh' : '0px';
        });

        groupSelector.addEventListener('click', async () => {
            groupContainer.style.display = groupContainer.style.display === 'block' ? 'none' : 'block';
            groupList.innerHTML = 'Loading...';
            try {
                const res = await fetch('./getUserCircles.php');
                const data = await res.json();
                groupList.innerHTML = '';

                if (!data.length) {
                    groupList.innerHTML = '<div class="text-muted">You are not yet part of any circle. You may join or create one.</div>';
                    return;
                }

                data.forEach((circle, index) => {
                    const btn = document.createElement('button');
                    btn.className = 'd-flex align-items-center my-2 p-0 border-0 bg-transparent';
                    btn.innerHTML = `
                        <img src="../assets/images/group-photo.png" class="group-image">
                        <div class="ms-2">${circle.circleName}</div>
                    `;
                    btn.addEventListener('click', () => {
                        selectedGroupName.textContent = `${circle.circleName} ▼`;
                        groupContainer.style.display = 'none';
                        loadMembers(circle.circleId);
                    });
                    groupList.appendChild(btn);

                    if (index === 0) {
                        selectedGroupName.textContent = `${circle.circleName} ▼`;
                        loadMembers(circle.circleId);
                    }
                });
            } catch (error) {
                groupList.innerHTML = '<div class="text-danger">Failed to fetch circles. Please try again later.</div>';
            }
        });

        async function loadMembers(circleId) {
            try {
                const res = await fetch(`./getCircleMembers.php?circleId=${circleId}`);
                const html = await res.text();
                memberContent.innerHTML = html;
                memberContainer.style.top = '0';
            } catch (error) {
                memberContent.innerHTML = '<p class="text-danger">Unable to load members.</p>';
            }
        }
    </script>
</body>

</html>