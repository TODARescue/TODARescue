window.fallbackCoords = [14.08849, 121.0995];
window.mapBounds = L.latLngBounds([13.7925, 120.9155], [14.2378, 121.252]);

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
  maxBoundsViscosity: 1.0,
  minZoom: 15,
  maxZoom: 19,
});

const geoJsonKey = "tanauanGeoJSON_v1";
const cachedData = localStorage.getItem(geoJsonKey);

if (cachedData) {
  const data = JSON.parse(cachedData);
  console.log("Loaded GeoJSON from localStorage");
  initMap(data);
} else {
  fetch("../assets/js/JSON/tanauan.geojson")
    .then((res) => res.json())
    .then((data) => {
      localStorage.setItem(geoJsonKey, JSON.stringify(data));
      console.log("Fetched GeoJSON from server and cached");
      initMap(data);
    })
    .catch((err) => {
      console.error("Failed to fetch geojson", err);
    });
}

function initMap(data) {
  window.tanauanGeoJSON = data;
  window.tanauanPolygon = L.geoJSON(data, {
    style: {
      color: "#2ebcbc",
      weight: 5,
      fillOpacity: 0.05,
    },
  }).addTo(map);

  map.setMaxBounds(mapBounds);
  map.fitBounds(mapBounds);

  map.locate({
    watch: true,
    enableHighAccuracy: true,
  });
}

// Base layers
const streetLayer = L.tileLayer(
  "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  {
    attribution: "&copy; OpenStreetMap contributors",
    maxZoom: 19,
  }
).addTo(map);

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
function goView() {
  map.setView(fallbackCoords, 17);
}

map.on("locationfound", function (e) {
  setInterval(() => {
    const lat = e.latlng.lat;
    const long = e.latlng.lng;
    storeLocationToPHP(long, lat);
  }, 5000);

  const pt = turf.point([e.latlng.lng, e.latlng.lat]);
  const poly = window.tanauanGeoJSON.features[0];

  const inBounds = turf.booleanPointInPolygon(pt, poly);
  window.poly = poly;

  // Show warning ONCE if out of bounds
  if (!inBounds && !outOfBoundsWarned && window.hasArrived === "0") {
    outOfBoundsWarned = true;
    const modalElement = document.getElementById("gpsWarningModal");
    const gpsModal = new bootstrap.Modal(modalElement, {
      backdrop: "static",
      keyboard: false,
    });
    gpsModal.show();
  }

  const targetCoords = inBounds ? e.latlng : fallbackCoords;
  if (window.hasArrived) {
    map.setView(targetCoords, 17);
  }
  // Set view on first location only
  if (!firstLocationFound) {
    map.setView(targetCoords, 17);
    firstLocationFound = true;
  }

  // Remove existing marker and accuracy indicators
  if (currentMarker) map.removeLayer(currentMarker);
  if (accuracyCircle) map.removeLayer(accuracyCircle);
  if (accuracyOutline) map.removeLayer(accuracyOutline);

  // Place the live marker
  currentMarker = L.marker(targetCoords, {
    icon: profileIcon,
  })
    .addTo(map)
    .bindPopup(popupContent)
    .on("click", function () {
      map.setView(currentMarker.getLatLng(), 19);
    });

  // Accuracy visual
  accuracyOutline = L.circle(targetCoords, {
    radius: e.accuracy + 2,
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

let hasStoredLocation = false;
function storeLocationToPHP(longitude, latitude) {
  const formData = new FormData();
  formData.append("action", "store_location");
  formData.append("userId", userId);
  formData.append("longitude", longitude);
  formData.append("latitude", latitude);

  fetch("../assets/php/storeLocation.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success && !hasStoredLocation) {
        console.log("Location stored in DB");
        hasStoredLocation = true;
      } else if (!data.success && !hasStoredLocation) {
        console.error("Failed to store location");
        hasStoredLocation = true;
      }
    })
    .catch((err) => {
      console.error("AJAX error:", err);
    });

  /* For debugging purposes
  fetch("../assets/php/storeLocation.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((text) => {
      console.log("RAW PHP Response:", text); 
      const data = JSON.parse(text);
      if (data.success) {
        console.log("Location stored in DB");
      } else {
        console.error("Failed to store location");
      }
    })
    .catch((err) => {
      console.error("AJAX error:", err);
    });
    */
}
