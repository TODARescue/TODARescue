const fallbackCoords = [14.08849, 121.0995];
const tanauanBounds = L.latLngBounds(
  [14.04146, 121.06599], //SW
  [14.10694, 121.15791] //NE
);
// Scroll member container
const memberContainer = document.getElementById("member-container");
const showButton = document.getElementById("toggle-button");
const headerColor = document.getElementById("header-color");
const memberContent = document.getElementById("member-content");
const mapContainer = document.getElementById("map-container");

const members = [
  {
    userID: 1,
    userName: "Alison Jackson",
    profilePic: "../assets/images/profile-default.png",
    coords: [14.087825, 121.098003],
    driverInfo: {
      name: "Pedro Santos",
      plateNo: "ABC-123",
      model: "Bajaj RE",
      todaReg: "TODA-001",
      contactNo: "09171234567",
      profilePic: "../assets/images/profile-default.png",
    },
    status: "Riding",
  },
  {
    userID: 2,
    userName: "Antok na",
    profilePic: "../assets/images/profile-default.png",
    coords: [14.083091, 121.093293],
    driverInfo: {
      name: "Carlos Agassi",
      plateNo: "XYZ-456",
      model: "Yamaha Tricity",
      todaReg: "TODA-002",
      contactNo: "09179876543",
      profilePic: "../assets/images/profile-default.png",
    },
    status: "Offline",
  },
  {
    userID: 3,
    userName: "Mak Mak",
    profilePic: "../assets/images/profile-default.png",
    coords: [14.5764, 121.0851],
    driverInfo: {
      name: "Jose Dela Cruz",
      plateNo: "LMN-789",
      model: "Honda TMX",
      todaReg: "TODA-003",
      contactNo: "09171239876",
      profilePic: "../assets/images/profile-default.png",
    },
    status: "Riding",
  },
];

let isOpen = false;
let isViewed = false; // true = driver view, false = list view

showButton.addEventListener("click", function () {
  if (!isOpen) {
    openMemberContainer();
  } else {
    closeMemberContainer();
  }
  isOpen = !isOpen;
});

function openMemberContainer() {
  memberContainer.style.top = "0px";
  if (!isViewed) {
    resetStyling();
    generateMemberButtons();
  }
}

function closeMemberContainer() {
  memberContainer.style.top = "100vh";
  mapContainer.classList.add("h-100");
  mapContainer.classList.add("w-100");
  mapContainer.classList.remove("h-50");
  isViewed = false;
}

function generateMemberButtons() {
  memberContent.innerHTML = "";

  members.forEach((member) => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className =
      "d-flex align-items-center py-3 border-bottom border-dark w-100 bg-transparent border-0 text-start";

    let statusIcon = "";
    switch (member.status) {
      case "Riding":
        statusIcon = "bi bi-truck";
        break;
      case "Offline":
        statusIcon = "bi bi-slash-circle";
        break;
      case "Available":
        statusIcon = "bi bi-check-circle";
        break;
      default:
        statusIcon = "bi bi-question-circle";
    }

    btn.onclick = () => {
      showLocation(member.userID, member.userName, member.coords);
      showDriverContainer(
        member.userID,
        member.userName,
        member.driverInfo,
        member.status,
        member.profilePic
      );
    };
    btn.innerHTML = `
            <img src="${member.profilePic}" alt="${member.userName}" class="rounded-circle me-3" style="width: 50px; height: 50px; border: 2px solid #2EBCBC;">
            <div>
                <div class="fw-bold">${member.userName}</div>
                <div class="d-flex align-items-center">
                    <i class="${statusIcon}"></i>
                    <span class="ms-1">${member.status}</span>
                </div>
            </div>
        `;
    memberContent.appendChild(btn);
  });

  const addPersonDiv = document.createElement("a");
  addPersonDiv.href = "./joinCircle.php";
  addPersonDiv.className =
    "d-flex align-items-center py-3 border-bottom border-dark w-100 text-decoration-none text-dark text-start";
  addPersonDiv.innerHTML = `
        <img src="../assets/images/group-photo.png" alt="Add a Person" class="rounded-circle ms-1 me-3" style="width: 50px; height: 50px;">
        <div class="fw-bold">Add a Person</div>
    `;
  memberContent.appendChild(addPersonDiv);
}

function resetStyling() {
  memberContainer.style.top = "0";
  memberContainer.style.paddingTop = "70px";
  memberContainer.style.maxHeight = "100vh";
  memberContainer.style.overflowY = "scroll";
}

function showDriverContainer(userID, userName, driverInfo, status, profilePic) {
  isViewed = true;
  mapContainer.classList.add("h-50");
  mapContainer.classList.add("w-100");
  mapContainer.classList.remove("h-100");
  memberContainer.style.top = "50vh";
  memberContainer.style.paddingTop = "5px";
  memberContainer.style.maxHeight = "55vh";
  memberContainer.style.overflowY = "scroll";
  memberContent.innerHTML = `
                                    <div class="d-flex align-items-center py-3 mx-2 border-bottom border-dark">
                                        <img src="${profilePic}" alt="${userName}" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">${userName}</div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-truck"></i>
                                                <span class="ms-1">${status}</span>
                                            </div>
                                        </div>
                                        <div class="fw-bold">Group 1</div>
                                    </div>
                                    <div class="my-3 mx-2 fw-bold">Riding With</div>
                                    <div class="card rounded-4 shadow px-4 py-4 mb-5 start-50 translate-middle-x"
                                        style="background-color: #2EBCBC; top: 55%; width: 90%; max-width: 500px;">
                                        <div class="d-flex flex-row align-items-center justify-content-between profile-container" id="profile-details">
                                            <div class="me-3 profile-pic">
                                                <img src="${driverInfo.profilePic}" alt="Driver" class="rounded-circle" width="50" height="50">
                                            </div>
                                            <div class="flex-grow-1 me-2">
                                                <div class="d-flex align-items-center">
                                                    <h5 class="mb-0 me-2">${driverInfo.name}</h5>
                                                    <img src="../assets/images/verified.png" alt="Verified" style="width: 12px;">
                                                </div>
                                                <div class="align-items-center">
                                                    <small>Plate No:</small>
                                                    <b>${driverInfo.plateNo}</b>
                                                </div>
                                                <div class="collapse mt-3" id="driver-details">
                                                    <div class="border-top border-dark pt-2">
                                                        <p class="mb-1"><b>Tricycle Model:</b> ${driverInfo.model}</p>
                                                        <p class="mb-1"><b>Toda Registration:</b> ${driverInfo.todaReg}</p>
                                                        <p class="mb-1"><b>Contact:</b> ${driverInfo.contactNo}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="btn p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#driver-details" aria-expanded="false">
                                                <img src="../assets/images/drop-down.png" alt="Dropdown" width="13" class="drop-arrow text-center" id="arrow-icon">
                                            </button>
                                        </div>
                                    </div>
                                `;
}
// Track the dynamic member marker separately
let memberMarker = null;

function showLocation(userID, userName, coords) {
  // Remove existing member marker if any
  if (memberMarker) {
    map.removeLayer(memberMarker);
  }

  const latLng = L.latLng(coords);
  if (!tanauanBounds.contains(latLng)) {
    // Show modal
    const gpsModal = new bootstrap.Modal(
      document.getElementById("gpsWarningModal")
    );
    gpsModal.show();

    map.setView(fallbackCoords, 15, { animate: true });
    return;
  }

  const profileIcon = L.icon({
    iconUrl: "../assets/images/profile-default.png",
    iconSize: [30, 30],
    iconAnchor: [15, 30],
    popupAnchor: [0, -20],
    className: "rounded-icon",
  });

  // Add new member marker
  memberMarker = L.marker(coords, {
    icon: profileIcon,
  })
    .addTo(map)
    .bindPopup(
      `
                <div class="profile-popup" style="position: relative; text-align: center;">
                    <div class="name-overlay" style="position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%); color: white; font-weight: bold; font-size: 12px; text-shadow: 0 0 3px black;">
                        ${userName}
                    </div>
                    <img src="../assets/images/profile-default.png" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white;" />
                    <div style="margin-top: 4px; font-size: 12px; color: #333;">Driver:</div>
                </div>
                `
    )
    .openPopup();

  map.setView(coords, 17, { animate: true });
}
