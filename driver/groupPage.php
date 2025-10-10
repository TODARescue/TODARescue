<?php
include "../assets/shared/connect.php";
session_start();
include '../assets/php/checkLogin.php';

$userId = $_SESSION['userId'];

$hasArrived = isset($_GET['arrived']) && $_GET['arrived'] == '1';

$getPhotoQuery = "SELECT photo, role FROM users WHERE userId = $userId;";
$getPhotoResult = executeQuery($getPhotoQuery);

if (mysqli_num_rows($getPhotoResult) > 0) {
    $row = mysqli_fetch_assoc($getPhotoResult);
    $userRole = !empty($row['role']) ? $row['role'] : 'passenger';
    if ($userRole === 'passenger') {
        $profilePicture = !empty($row['photo'])
            ? '../assets/images/passengers/' . $row['photo']
            : '../assets/images/profile-default.png';
    } else {
        $profilePicture = !empty($row['photo'])
            ? '../assets/images/drivers/' . $row['photo']
            : '../assets/images/profile-default.png';
    }
}

$checkRidingQuery = "SELECT driverId, historyId
FROM history
WHERE userId = $userId
  AND dropoffTime IS NULL;";
$checkRidingResult = executeQuery($checkRidingQuery);

$checkPreviousDriverQuery = "
    SELECT driverId 
    FROM history 
    WHERE userId = $userId 
      AND dropoffTime IS NOT NULL 
    ORDER BY dropoffTime DESC 
    LIMIT 1;
";
$checkPreviousDriverResult = executeQuery($checkPreviousDriverQuery);

if (mysqli_num_rows($checkPreviousDriverResult) > 0) {
    $driver = mysqli_fetch_assoc($checkPreviousDriverResult);
    $previousDriverId = $driver['driverId'];

    $getDriverProfileQuery = "SELECT 
    driver.userId, 
    users.*
    FROM 
    drivers driver
    JOIN 
    users users ON driver.userId = users.userId
    WHERE
    driver.driverId = $previousDriverId;";
    $getDriverProfileResult = executeQuery($getDriverProfileQuery);

    // For storing of driver profile details
    if (mysqli_num_rows($getDriverProfileResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverProfileResult);
        $previousDriverName = $row['firstName'] . " " . $row['lastName'];
    }

    $getDriverQuery = "SELECT plateNumber FROM drivers WHERE driverId = $previousDriverId;";
    $getDriverResult = executeQuery($getDriverQuery);

    if (mysqli_num_rows($getDriverResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverResult);
        $previousPlateNumber = $row['plateNumber'];
    }
} else {
    $previousDriverId = null;
}

// Checker to determine if the user is currently riding
if (mysqli_num_rows($checkRidingResult) > /* == */ 0) {
    $row = mysqli_fetch_assoc($checkRidingResult);
    $driverId = $row['driverId'];
    $historyId = $row['historyId'];
    $setRidingQuery = "UPDATE users SET isRiding=1 WHERE userId = $userId;";
    $setRidingResult = executeQuery($setRidingQuery);
    $isRiding = true;

    $isRiding = $_SESSION['isRiding'] = true;


    $getDriverQuery = "SELECT d.plateNumber, d.todaRegistration, d.model, u.photo FROM drivers d JOIN users u ON d.userId = u.userId WHERE driverId = $driverId;";
    $getDriverResult = executeQuery($getDriverQuery);

    $getDriverProfileQuery = "SELECT 
    driver.userId, 
    users.*
    FROM 
    drivers driver
    JOIN 
    users users ON driver.userId = users.userId
    WHERE
    driver.driverId = $driverId;";
    $getDriverProfileResult = executeQuery($getDriverProfileQuery);

    // For storing of driver profile details
    if (mysqli_num_rows($getDriverProfileResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverProfileResult);
        $name = $row['firstName'] . " " . $row['lastName'];
        $contact = $row['contactNumber'];
    }

    if (mysqli_num_rows($getDriverResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverResult);
        $plateNumber = $row['plateNumber'];
        $model = $row['model'];
        $todaRegistration = $row['todaRegistration'];
        $photo = $row['photo'];
    }
} else {
    $setIdleQuery = "UPDATE users SET isRiding=2 WHERE userId = $userId;";
    $setIdleResult = executeQuery($setIdleQuery);
    $isRiding = false;

    $isRiding = $_SESSION['isRiding'] = false;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver | Group Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap" rel="stylesheet">
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
    <div class="modal fade" id="gpsWarningModal" tabindex="-1" aria-labelledby="gpsWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2 border-teal">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="gpsWarningModalLabel"> <i class="bi bi-exclamation-triangle-fill me-2"></i>Location Outside Map Bounds</h5>
                </div>
                <div class="modal-body text-center">
                    Showing default location on the map.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-ok" data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100 z-1" id="map-container">
        <div id="map" class="w-100 h-100" style="pointer-events: auto;"></div>
    </div>

    <!-- Toggle Button -->
    <button id="toggle-button" class="btn btn-primary rounded-circle glass-toggle p-4 text-dark d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
        <i class="bi bi-people-fill"></i>
    </button>

    <div class=" py-3 py-sm-1 container-fluid position-fixed top-0 text-center start-0 end-0 bg-transparent" style="z-index: 5;" id="header-color">
        <div class="d-inline-flex align-items-center px-5 py-1 rounded-pill glass-selector"
            style="background-color: #2ebcbc!important; cursor: pointer; user-select: none;"
            id="group-selector">
            <h4 class="m-0" id="selected-group-name">
                Select Circle
                <i class="bi bi-caret-down-fill ms-2" id="caret-icon"></i>
        </div>
    </div>

    <div class="pt-4 pt-lg-5 group-container shadow rounded-bottom-5 bg-white" id="group-container">
        <div id="group-list" class="mb-3 text-center">Loading...</div>
        <div class="pt-2 pb-0 mt-4 mb-0 gap-4 d-flex flex-align-center justify-content-center text-center">
            <a href="./createCircle.php" class="text-decoration-none">
                <button type="button" class="btn rounded-pill action-button" style="font-size: 16px;">
                    Create Circle
                </button>
            </a>
            <a href="./joinCircle.php" class="text-decoration-none">
                <button type="button" class="btn rounded-pill action-button" style="font-size: 16px;">
                    Join Circle
                </button>
            </a>
        </div>
    </div>

    <div class="container-fluid px-2" id="member-container">
        <div id="member-content" class="d-flex flex-column text-muted">
            <i class="bi bi-people text-center" style="font-size: 3rem;"></i>
            <p class="mt-3 fs-5 text-center">Please select a group to view members.</p>
        </div>
    </div>

    <div class="position-relative" style="z-index: 5">
        <?php include '../assets/shared/navbarDriver.php'; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- For storing of user IDs -->
    <script>
        const userId = <?php echo $_SESSION['userId'] ?? 1; ?>;
        const sessionUserId = <?php echo json_encode($_SESSION['userId']); ?>;
        window.hasArrived = <?php echo isset($hasArrived) && $hasArrived ? 'true' : 'false'; ?>;
        window.profilePicture = '<?php echo $profilePicture; ?>';
    </script>

    <!-- Turf js to handle polygons -->
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

    <script src="../assets/js/sharedMap.js"></script>

    <!-- Get Location -->
    <script src="../assets/js/groupPage/members.js"></script>

    <!-- Change status -->
    <script>
        document.addEventListener("visibilitychange", () => {
            if (document.visibilityState === "hidden") {
                updateStatus(0);
            } else {
                updateStatus(2);
            }
        });

        function updateStatus(state) {
            fetch(`../assets/php/updateStatus.php?visibility=${state}`)
                .catch(err => console.error("Failed to update status:", err));
        }
    </script>

    <!-- Buttons -->
    <script>
        const groupContainer = document.getElementById('group-container');
        const toggleButton = document.getElementById('toggle-button');
        const groupSelector = document.getElementById('group-selector');
        const groupList = document.getElementById('group-list');
        const selectedGroupName = document.getElementById('selected-group-name');
        const memberContainer = document.getElementById('member-container');
        const memberContent = document.getElementById('member-content');

        let selectedGroupId = null;
        let selectedCircleName = null;
        let isViewed = false;
        let isToggled = false;

        let membersInterval;
        let locationInterval;

        function openMemberContainer() {
            memberContainer.style.top = "0px";
            if (!isViewed) {
                resetStyling();
            }
            if (selectedGroupId !== null) {
                loadMembers(selectedGroupId, selectedCircleName);
            }
        }

        function closeMemberContainer() {
            memberContainer.style.top = "100vh";
            // mapContainer.classList.add("h-100");
            // mapContainer.classList.add("w-100");
            // mapContainer.classList.remove("h-50");
            isViewed = false;

        }

        function resetStyling() {
            memberContainer.style.top = "0";
            memberContainer.style.paddingTop = "70px";
            memberContainer.style.maxHeight = "100vh";
            memberContainer.style.overflowY = "auto";
        }

        toggleButton.addEventListener('click', () => {
            if (!isToggled) {
                resetStyling();
                openMemberContainer();
            } else {
                closeMemberContainer();
            }
            isToggled = !isToggled;
        });

        groupSelector.addEventListener('click', async () => {
            const container = document.getElementById("group-container");
            const caretIcon = document.getElementById("caret-icon");
            const headerColor = document.getElementById("header-color");

            const isOpen = container.style.display === "block";

            // Toggle dropdown UI
            container.style.display = isOpen ? "none" : "block";
            caretIcon.classList.toggle("bi-caret-up-fill", !isOpen);
            caretIcon.classList.toggle("bi-caret-down-fill", isOpen);
            headerColor.classList.toggle("bg-white", !isOpen);
            headerColor.classList.toggle("bg-transparent", isOpen);
            // Reset to default label ONLY when opening
            if (selectedGroupId === null) {
                selectedGroupName.innerHTML = `Select Circle <i class="bi bi-caret-up-fill ms-2" id="caret-icon"></i>`;
            }

            // Don't fetch again if already open
            if (isOpen) return;

            try {
                const res = await fetch('../assets/php/getUserCircles.php');
                const data = await res.json();
                groupList.innerHTML = '';

                if (!data.length) {
                    groupList.innerHTML = '<div class="text-muted">You are not yet part of any circle. You may join or create one.</div>';
                    return;
                }

                data.forEach(circle => {
                    const btn = document.createElement('button');
                    btn.className = 'd-flex align-items-center my-2 p-0 border-0 bg-transparent';
                    btn.innerHTML = `
                <img src="../assets/images/group-photo.png" class="group-image">
                <div class="ms-2">${circle.circleName}</div>
            `;

                    btn.addEventListener('click', () => {
                        selectedGroupId = circle.circleId;
                        selectedCircleName = circle.circleName;

                        selectedGroupName.innerHTML = `${circle.circleName} <i class="bi bi-caret-down-fill ms-2" id="caret-icon"></i>`;

                        container.style.display = "none";
                        caretIcon.classList.remove("bi-caret-up-fill");
                        caretIcon.classList.add("bi-caret-down-fill");
                        headerColor.classList.remove("bg-white");
                        headerColor.classList.add("bg-transparent");

                        resetStyling();
                        isViewed = false;
                        isToggled = true;
                        openMemberContainer();
                        startMemberUpdates(circle.circleId, circle.circleName);

                    });

                    groupList.appendChild(btn);
                });

            } catch (error) {
                console.error("Fetch error:", error);
                groupList.innerHTML = '<div class="text-danger">Failed to fetch circles. Please try again later.</div>';
            }
        });


        function startMemberUpdates(circleId, circleName) {
            if (!isViewed) {
                resetStyling();
            }
            clearInterval(membersInterval);

            // Load initially
            loadMembers(circleId, circleName);

            membersInterval = setInterval(() => {
                loadMembers(circleId, circleName);
            }, 10000);
        }

        function startLocationUpdates(userId, userName, profilePicture) {
            clearInterval(locationInterval);

            getLocation(userId, userName, profilePicture);

            locationInterval = setInterval(() => {
                getLocation(userId, userName, profilePicture);
            }, 5000);
        }

        async function loadMembers(circleId, circleName) {
            try {
                const res = await fetch(`../assets/php/getCircleMembers.php?circleId=${circleId}`);
                const data = await res.json();

                memberContent.innerHTML = '';

                const {
                    members,
                    role
                } = data;

                console.log("Role:", role);
                if (!members || !members.length) {
                    memberContent.innerHTML = '<p class="text-muted">No members found in this circle.</p>';
                    return;
                }

                if (data.error) {
                    memberContent.innerHTML = `<p class="text-danger">Select A Circle First.</p>`;
                    return;
                }


                members.forEach(member => {
                    const {
                        profilePic,
                        userName,
                        status,
                        userId,
                        role
                    } = member;

                    const displayName = userId === sessionUserId ?
                        `${userName} <span class="text-muted">(You)</span>` :
                        userName;

                    let statusIcon = '';
                    switch (status) {
                        case "1":
                            statusIcon = "<i class='bi bi-truck'></i>";
                            break;
                        case "0":
                            statusIcon = "<i class='bi bi-slash-circle'></i>";
                            break;
                        case "2":
                            statusIcon = "<i class='bi bi-check-circle'></i>";
                            break;
                        default:
                            statusIcon = "<i class='bi bi-question-circle'></i>";
                    }

                    let statusActive = '';
                    switch (status) {
                        case "1":
                            statusActive = "Riding";
                            break;
                        case "0":
                            statusActive = "Offline";
                            break;
                        case "2":
                            statusActive = "Online";
                            break;
                        default:
                            statusActive = "Idle";
                    }

                    const memberBtn = document.createElement("button");
                    memberBtn.onclick = () => {
                        // goViewMembers();
                        resetWarning();
                        startLocationUpdates(member.userId, member.userName, member.profilePic);
                        showDriverContainer(
                            member.userId,
                            member.userName,
                            member.status,
                            member.profilePic,
                            statusIcon,
                            statusActive,
                            circleName
                        );
                    };

                    memberBtn.className = "d-flex align-items-center py-3 px-2 border-bottom border-dark w-100 bg-transparent border-0 text-start custom-profile-icon";
                    memberBtn.innerHTML = `
                        <img src="${profilePic}" alt="${userName}" onerror="this.onerror=null; this.src='../assets/images/profile-default.png';" class="rounded-circle me-3 profile-icon-image" style="width: 50px; height: 50px;">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${displayName}</div>
                            <div class="d-flex align-items-center">
                                ${statusIcon}
                                <span class="mx-1">${statusActive}</span>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <div class="fw-bold">${circleName}</div>
                            <div class="text-muted small">${role.charAt(0).toUpperCase() + role.slice(1)}</div>
                        </div>
                    `;

                    memberContent.appendChild(memberBtn);
                });
                console.log("Member Role:", role);

                if (role === 'admin' || role === 'owner') {
                    const addPersonDiv = document.createElement("a");
                    addPersonDiv.href = "./inviteMember.php?circleId=" + circleId;
                    addPersonDiv.className =
                        "d-flex align-items-center py-3 px-2 border-bottom border-dark w-100 text-decoration-none text-dark text-start";
                    addPersonDiv.innerHTML = `
                            <img src="../assets/images/group-photo.png" alt="Add a Person" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                            <div class="fw-bold">Add a Person</div>
                        `;
                    memberContent.appendChild(addPersonDiv);
                }
            } catch (error) {
                console.error("Load members error:", error);
                memberContent.innerHTML = '<p class="text-danger">Unable to load members.</p>';
            }
        }

        function showDriverContainer(userID, userName, status, profilePic, statusIcon, statusActive, circleName) {
            isViewed = true;
            clearInterval(membersInterval);
            memberContainer.style.top = "50vh";
            memberContainer.style.paddingTop = "5px";
            memberContainer.style.maxHeight = "55vh";
            memberContainer.style.overflowY = "scroll";
            memberContent.innerHTML = `
                                    <div class="d-flex align-items-center py-3 mx-2 border-bottom border-dark custom-profile-icon">
                                        <img src="${profilePic}" alt="${userName}" onerror="this.onerror=null; this.src='../assets/images/profile-default.png';" onclick="panMap(); goViewMember()" class="rounded-circle me-3 profile-icon-image" style="width: 50px; height: 50px;">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">${userName}</div>
                                            <div class="d-flex align-items-center">
                                                ${statusIcon}
                                                <span class="ms-1">${statusActive}</span>
                                            </div>
                                        </div>
                                        <div class="fw-bold">${circleName}</div>
                                    </div>
                                    
                                `;

            // Fetch latest driver info

            fetch(`../assets/php/getLatestDriver.php?userId=${userID}`)
                .then(response => response.json())
                .then(driver => {
                    let rideTimeText = "";
                    if (driver.dropOffTime === null) {
                        rideTimeText = "Currently Riding";
                    } else {
                        const date = new Date(driver.dropOffTime);
                        const formattedDate = date.toLocaleDateString('en-US', {
                            month: 'long',
                            day: '2-digit'
                        });
                        const formattedTime = date.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                        rideTimeText = `Last Ride: ${formattedDate} - ${formattedTime}`;
                    }
                    if (driver.driverId) {
                        let ridingDetails = "";
                        switch (status) {
                            case "1":
                                ridingDetails = "Riding with";
                                break;
                            case "0":
                                ridingDetails = "Last Rode With";
                                break;
                            case "2":
                                ridingDetails = "Last Rode With";
                                break;
                            default:
                                ridingDetails = "New to Application";
                        }

                        // Driver details
                        memberContent.innerHTML += `
                                    <div class="my-3 mx-2 fw-bold">${ridingDetails}</div>
                                    <div class="card rounded-4 glass shadow px-4 py-4 mb-5 start-50 translate-middle-x"
                                        style="background-color: #2ebcbc!important; top: 55%; width: 90%; max-width: 500px;">
                                        <div class="d-flex flex-row align-items-center justify-content-between profile-container" id="profile-details">
                                            <div class="me-3 custom-profile-icon">
                                                <img src="${driver.profilePic}" alt="Driver" onerror="this.onerror=null; this.src='../assets/images/profile-default.png';" onclick="panMap(); goView()" class="rounded-circle custom-profile-icon profile-icon-details" style="width: 50px!important; height: 50px!important;">
                                            </div>
                                            <div class="flex-grow-1 me-2">
                                                <div class="d-flex align-items-center">
                                                    <h5 class="mb-0 me-2">${driver.driverName}</h5>
                                                    <img src="../assets/images/verified.png" alt="Verified" style="width: 12px;">
                                                </div>
                                                <div class="align-items-center">
                                                    <small>Plate No:</small>
                                                    <b>${driver.plateNo}</b>
                                                </div>
                                                <div class="align-items-center">
                                                    <b id="ride-time">${rideTimeText}</b>
                                                </div>
                                                <div class="collapse mt-3" id="driver-details">
                                                    <div class="border-top border-dark pt-2">
                                                        <p class="mb-1"><b>Tricycle Model:</b> ${driver.model}</p>
                                                        <p class="mb-1"><b>Toda Registration:</b> ${driver.todaReg}</p>
                                                        <p class="mb-1"><b>Contact:</b> ${driver.contactNo}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="btn p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#driver-details" aria-expanded="false">
                                                <img src="../assets/images/drop-down.png" alt="Dropdown" width="13" class="drop-arrow text-center" id="arrow-icon">
                                            </button>
                                        </div>
                                    </div>
                `;
                    } else {
                        memberContent.innerHTML += `<div class="text-muted px-3"> No recent driver found.</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching latest driver:', error);
                    memberContent.innerHTML += `<div class="text-danger px-3">Failed to load driver info.</div>`;
                });
        }

        async function getLocation(userId, userName, profilePicture) {
            try {
                const res = await fetch(`../assets/php/getUserLocation.php?userId=${userId}`);
                const data = await res.json();

                if (data.error || !data.latitude || !data.longitude) {
                    console.error("Invalid location data:", data);
                    alert("No location found for this user.");
                    return;
                }

                const coords = [parseFloat(data.latitude), parseFloat(data.longitude)];
                showLocation(userId, userName, coords, profilePicture);
            } catch (error) {
                console.error("Failed to fetch location:", error);
                alert("Failed to load user location.");
            }
        }
    </script>
</body>

</html>