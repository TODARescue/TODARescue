const fallbackCoords = [14.08849, 121.0995];
const tanauanBounds = L.latLngBounds(
  [14.04146, 121.06599], //SW
  [14.10694, 121.15791] //NE
);

const policeStations = [
  {
    name: "Tanauan City Police Station - Talaga",
    coords: [14.101025, 121.098411],
    contact: "0939 322 7848",
  },
  {
    name: "Tanauan City Police Station - Sambat",
    coords: [14.085434, 121.13674],
    contact: "0977 685 6947 ",
  },
];

const fireStations = [
  {
    name: "Tanauan City Fire Station",
    coords: [14.081641613756855, 121.15299028141601],
    contact: "0922 344 8887",
  },
];

const hospitals = [
  {
    name: "Tanauan Medical Center",
    coords: [14.084456, 121.149504],
    contact: "(043) 778 1119",
  },
  {
    name: "Mercado Medical Center",
    coords: [14.079599, 121.151047],
    contact: "0917 466 2273",
  },
  {
    name: "Laurel Memorial District Hospital",
    coords: [14.088915, 121.122377],
    contact: "(043) 784 0958",
  },
];

const map = L.map("map", {
  maxBounds: tanauanBounds,
  maxBoundsViscosity: 1.0,
  minZoom: 15,
  maxZoom: 19,
});

// Base layers
const streetLayer = L.tileLayer(
  "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  {
    attribution: "&copy; OpenStreetMap contributors",
    maxZoom: 19,
  }
).addTo(map);

// Start locating
map.locate({
  watch: true,
  enableHighAccuracy: true,
});

hospitals.forEach(function (hospital) {
  L.marker(hospital.coords, {
    icon: L.icon({
      iconUrl: "../assets/images/hospital.png",
      iconSize: [25, 25],
      iconAnchor: [12, 25],
      popupAnchor: [0, 0],
    }),
  }).addTo(map).bindPopup(`
    <div style="font-family: "Inter", sans-serif; font-size: 14px; line-height: 1.4; text-align: left;">
      <strong style="font-size: 15px; color: #000;">${hospital.name}</strong><br>
      <span style="color: #333;">📞 <strong>${hospital.contact}</strong></span>
    </div>
  `);
});
fireStations.forEach(function (station) {
  L.marker(station.coords, {
    icon: L.icon({
      iconUrl: "../assets/images/fire-station.png",
      iconSize: [25, 25],
      iconAnchor: [12, 25],
      popupAnchor: [0, 0],
    }),
  }).addTo(map).bindPopup(`
    <div style="font-family: "Inter", sans-serif; font-size: 14px; line-height: 1.4; text-align: left;">
      <strong style="font-size: 15px; color: #000;">${station.name}</strong><br>
      <span style="color: #333;">📞 <strong>${station.contact}</strong></span>
    </div>
  `);
});
policeStations.forEach(function (station) {
  L.marker(station.coords, {
    icon: L.icon({
      iconUrl: "../assets/images/police-station.png",
      iconSize: [25, 25],
      iconAnchor: [12, 25],
      popupAnchor: [0, 0],
    }),
  }).addTo(map).bindPopup(`
    <div style="font-family: "Inter", sans-serif; font-size: 14px; line-height: 1.4; text-align: left;">
      <strong style="font-size: 15px; color: #000;">${station.name}</strong><br>
      <span style="color: #333;">📞 <strong>${station.contact}</strong></span>
    </div>
  `);
});
// Custom marker icon
const profileIcon = L.icon({
  iconUrl: "../assets/images/profile-default.png",
  iconSize: [30, 30],
  iconAnchor: [15, 30],
  popupAnchor: [0, -20],
  className: "rounded-icon",
});

// HTML popup content
const popupContent = `
    <div class="profile-popup" style="position: relative; text-align: center;">
        <div class="name-overlay" style="position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%); color: white; font-weight: bold; font-size: 12px; text-shadow: 0 0 3px black;">
            You 
        </div>
        <img src="../assets/images/profile-default.png" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white;" />
    </div>
`;

//Flags
let currentMarker = null;
let accuracyCircle = null;
let accuracyOutline = null;
let firstLocationFound = false;
let outOfBoundsWarned = false;

function fallbackLocation() {
  map.setView(fallbackCoords, 17);

  if (currentMarker) map.removeLayer(currentMarker);
  if (accuracyCircle) map.removeLayer(accuracyCircle);
  if (accuracyOutline) map.removeLayer(accuracyOutline);

  currentMarker = L.marker(fallbackCoords, {
    icon: profileIcon,
  })
    .addTo(map)
    .bindPopup(popupContent)
    .on("click", function () {
      map.setView(currentMarker.getLatLng(), 19);
    });
}

map.on("locationfound", function (e) {
  let targetCoords;

  if (tanauanBounds.contains(e.latlng)) {
    targetCoords = e.latlng;
    outOfBoundsWarned = false;
  } else {
    if (!outOfBoundsWarned) {
      outOfBoundsWarned = true;

      const modalElement = document.getElementById("gpsWarningModal");
      const gpsModal = new bootstrap.Modal(modalElement);
      gpsModal.show();
    }
    targetCoords = fallbackCoords;
  }

  if (!firstLocationFound) {
    map.setView(targetCoords, 17);
    firstLocationFound = true;
  }

  if (currentMarker) map.removeLayer(currentMarker);
  if (accuracyCircle) map.removeLayer(accuracyCircle);
  if (accuracyOutline) map.removeLayer(accuracyOutline);

  currentMarker = L.marker(targetCoords, {
    icon: profileIcon,
  })
    .addTo(map)
    .bindPopup(popupContent)
    .on("click", function () {
      map.setView(currentMarker.getLatLng(), 19);
    });

  accuracyOutline = L.circle(targetCoords, {
    radius: e.accuracy + 20,
    color: "#2ebcbc",
    weight: 6,
    opacity: 0.1,
    fillOpacity: 0,
  }).addTo(map);

  accuracyCircle = L.circle(targetCoords, {
    radius: e.accuracy,
    color: "#2ebcbc",
    weight: 2,
    fillColor: "#2ebcbc",
    fillOpacity: 0.15,
  }).addTo(map);
});

map.on("locationerror", fallbackLocation);
