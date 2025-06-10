const fallbackCoords = [14.08849, 121.0995]; // Default coordinates
const map = L.map("map").setView(fallbackCoords, 16);

// Base layers
const streetLayer = L.tileLayer(
  "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  {
    attribution: "&copy; OpenStreetMap contributors",
    maxZoom: 19,
  }
).addTo(map);

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

// Variables to store current marker and circles
let currentMarker = null;
let accuracyCircle = null;
let accuracyOutline = null;

// Start locating
map.locate({
  setView: true,
  maxZoom: 19,
  watch: false,
  enableHighAccuracy: true,
});

// Location found
map.on("locationfound", function (e) {
  map.setView(e.latlng, 19);

  // Remove previous marker and circles if they exist
  if (currentMarker) {
    map.removeLayer(currentMarker);
  }
  if (accuracyCircle) {
    map.removeLayer(accuracyCircle);
  }
  if (accuracyOutline) {
    map.removeLayer(accuracyOutline);
  }

  // Add new marker
  currentMarker = L.marker(e.latlng, {
    icon: profileIcon,
  })
    .addTo(map)
    .bindPopup(popupContent)
    .on("click", function () {
      map.setView(currentMarker.getLatLng(), 19);
    });

  // Add new accuracy circles
  accuracyOutline = L.circle(e.latlng, {
    radius: e.accuracy + 10,
    color: "#2ebcbc",
    weight: 6,
    opacity: 0.1,
    fillOpacity: 0,
  }).addTo(map);

  accuracyCircle = L.circle(e.latlng, {
    radius: e.accuracy,
    color: "#2ebcbc",
    weight: 2,
    fillColor: "#2ebcbc",
    fillOpacity: 0.15,
  }).addTo(map);
});

// Location error fallback
map.on("locationerror", function () {
  alert("Could not detect location. Showing default location.");

  map.setView(fallbackCoords, 19);

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
});
