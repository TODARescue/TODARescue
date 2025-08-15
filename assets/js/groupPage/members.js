window.fallbackCoords = [14.08849, 121.0995];
window.mapBounds = L.latLngBounds([13.7925, 120.9155], [14.2378, 121.252]);

let memberMarker = null;
let outOfBoundsMember = false;
let memberCoords = null;
let hasPanned = false;
let lastCoords = null;
let nameRevealed = false;

function showLocation(userID, userName, coords, profilePicture) {
  window.isViewingMember = true;
  // Remove existing member marker if any
  if (memberMarker) {
    map.removeLayer(memberMarker);
  }

  const pt = turf.point([coords[0], coords[1]]);
  // const inBounds = turf.booleanPointInPolygon(pt, window.poly);
  const inBounds = window.testBounds.contains(coords);

  if (!inBounds && !outOfBoundsMember) {
    outOfBoundsMember = true;
    // Show modal
    const gpsModal = new bootstrap.Modal(
      document.getElementById("gpsWarningModal")
    );
    gpsModal.show();
    memberCoords = inBounds ? coords : fallbackCoords;
    lastCoords = memberCoords;

    if (!hasPanned) {
      hasPanned = true;
      map.once("moveend", () => {
        const panY = window.innerHeight * 0.25;
        console.log("Panning map for member view: " + panY);
        map.panBy([0, panY], { animate: true });
      });

      goViewMember();
    }
  } else {
    memberCoords = inBounds ? coords : fallbackCoords;
    if (!hasPanned) {
      hasPanned = true;
      map.once("moveend", () => {
        const panY = window.innerHeight * 0.25;
        console.log("Panning map for member view: " + panY);
        map.panBy([0, panY], { animate: true });
      });

      goViewMember();
    }
  }

  const profileIcon = L.divIcon({
    className: "custom-profile-icon",
    html: `<img src="${profilePicture}" class="profile-icon-image">`,
    iconSize: [40, 40],
    iconAnchor: [20, 20],
    popupAnchor: [2, -20],
  });

  // Add new member marker
  memberMarker = L.marker(memberCoords, {
    icon: profileIcon,
  })
    .addTo(map)
    .bindPopup(
      `
                <div class="profile-popup" style="position: relative; text-align: center;">
                    <div class="name-overlay" style="position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%); color: white; font-weight: bold; font-size: 10px; text-shadow: 0 0 3px black;">
                        ${userName}
                    </div>
                    <img src="${profilePicture}" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white;" />
                    <div style="margin-top: 4px; font-size: 12px; color: #333;">Driver:</div>
                </div>
                `
    );
}

function goViewMember() {
  map.setView(memberCoords, 17, (animate = true));
}

function panMap() {
  map.once("moveend", () => {
    const panY = window.innerHeight * 0.25;
    console.log("Panning map for member view: " + panY);
    map.panBy([0, panY], { animate: true });
  });
}
function resetWarning() {
  outOfBoundsMember = false;
  memberCoords = null;
  hasPanned = false;
  nameRevealed = false;
}
