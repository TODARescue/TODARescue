const arrowIcon = document.getElementById("arrow-icon");
const collapse = document.getElementById("driver-details");
const profileContainer = document.getElementById("profile-details");

collapse.addEventListener("show.bs.collapse", () => {
  arrowIcon.classList.add("rotate");
  profileContainer.classList.add("collapsed");
});

collapse.addEventListener("hide.bs.collapse", () => {
  arrowIcon.classList.remove("rotate");
  profileContainer.classList.remove("collapsed");
});
