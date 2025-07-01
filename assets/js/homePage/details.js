const arrowIcon = document.getElementById("arrow-icon");
const collapse = document.getElementById("driver-details");
const profileContainer = document.getElementById("profile-details");
const arriveButton = document.getElementById("arrive-button");

document.addEventListener("DOMContentLoaded", function () {
  const originalTop = 75;
  const offset = 20;

  collapse.addEventListener("show.bs.collapse", function () {
    arriveButton.style.top = `${originalTop + offset}%`;
    arrowIcon.classList.add("rotate");
    profileContainer.classList.add("collapsed");
  });

  collapse.addEventListener("hide.bs.collapse", function () {
    arriveButton.style.top = `${originalTop}%`;
    arrowIcon.classList.remove("rotate");
    profileContainer.classList.remove("collapsed");
  });
});
