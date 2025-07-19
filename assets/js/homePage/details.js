const arrowIcon = document.getElementById("arrow-icon");
const collapse = document.getElementById("driver-details");
const profileCard = document.getElementById("profile-card");
const profileContainer = document.getElementById("profile-details");
const arriveButton = document.getElementById("arrive-button");
const showButton = document.getElementById("toggle-btn");

function removeUrlParam() {
  const url = new URL(window.location.href);
  url.searchParams.delete("arrived");
  window.history.replaceState({}, document.title, url.pathname + url.search);
  console.log(window.hasArrived);
}

document.addEventListener("DOMContentLoaded", function () {
  // Show completion
  if (window.hasArrived) {
    const modalElement = document.getElementById("arrivalModal");
    const gpsModal = new bootstrap.Modal(modalElement, {
      backdrop: "static",
      keyboard: false,
    });
    gpsModal.show();
  }

  const originalTop = 75;
  const offset = 20;

  let collapseOpened = false;

  if (collapse) {
    collapse.addEventListener("show.bs.collapse", function () {
      if (!collapseOpened) {
        arriveButton.style.top = `${originalTop + offset}%`;
        arrowIcon.classList.add("rotate");
        profileContainer.classList.add("collapsed");
        collapseOpened = true;
      }
    });

    collapse.addEventListener("hide.bs.collapse", function () {
      if (collapseOpened) {
        arriveButton.style.top = `${originalTop}%`;
        arrowIcon.classList.remove("rotate");
        profileContainer.classList.remove("collapsed");
        collapseOpened = false;
      }
    });
  }

  let isDown = false;

  showButton.addEventListener("click", function () {
    if (isDown) {
      // Slide up (show)
      profileCard.style.transition = "top 0.3s ease-in-out";
      profileCard.style.top = "55%";

      if (!collapseOpened) {
        if (arriveButton) {
          arriveButton.style.transition = "top 0.3s ease-in-out";
          arriveButton.style.top = `${originalTop}%`;
        }
      } else {
        if (arriveButton) {
          arriveButton.style.top = `${originalTop + offset}%`;
        }
      }
    } else {
      // Slide down (hide everything)
      profileCard.style.transition = "top 0.3s ease-in-out";
      profileCard.style.top = "100vh";

      if (arriveButton) {
        arriveButton.style.transition = "top 0.3s ease-in-out";
        arriveButton.style.top = "120vh";
      }
    }
    isDown = !isDown;
  });
});
