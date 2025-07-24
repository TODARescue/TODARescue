
   <div class="container-fluid position-fixed top-0 start-0 end-0 bg-white shadow rounded-bottom-5" style="z-index: 1030;">
    <div class="row">
        <div class="col d-flex align-items-center p-3 rounded-bottom-4">
            <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Back" class="img-fluid m-2" style="height: 40px;" onclick="history.back();" />
            <h3 id="page-title" class="fw-bold m-0 ps-2"></h3>
        </div>
    </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const pageTitle = document.getElementById("page-title");

    const pathParts = window.location.pathname.split("/");

    const folder = pathParts[pathParts.length - 2]; 
    const filename = pathParts[pathParts.length - 1]; 

    const pageKey = `${folder}/${filename}`; 

    const titles = {
      "passenger/settings.php": "Settings",
      "passenger/emergencyContact.php": "Emergency Contact",
      "passenger/profile.php": "Profile",
      "passenger/accountEdit.php": "Account",
      "passenger/accountView.php": "Account",
      "passenger/changeAdminStatusPassenger.php": "Change Admin Status",
      "passenger/circle.php": "Circle Management",
      "passenger/circleDetails.php": "Circle Management",
      "passenger/editCircleName.php": "Circle Management",
      "passenger/createCircle.php": "Create Circle",
      "passenger/groupPage.php": "Account",
      "passenger/inviteMember.php": "Invite Code",
      "passenger/joinCircle.php": "Join Circle",
      "passenger/removeCircleMember.php": "Remove Circle Member",
      "passenger/rideHistory.php": "Ride History",
      "passenger/scanQr.php": "TodaRescue",
      "passenger/verificationScreen.php": "TodaRescue",

      "driver/changeAdminStatus.php": "Change Admin Status",
      "driver/profileInformation.php": "Profile Information",
      "driver/settings.php": "Settings",
      "driver/emergencyContact.php": "Emergency Contact",
      "driver/profile.php": "Profile",
      "driver/accountEdit.php": "Account",
      "driver/accountView.php": "Account",
      "driver/circle.php": "Circle Management",
      "driver/circleDetails.php": "Circle Management",
      "driver/editCircleName.php": "Circle Management",
      "driver/createCircle.php": "Create Circle",
      "driver/groupPage.php": "Account",
      "driver/inviteMember.php": "Invite Code",
      "driver/joinCircle.php": "Join Circle",
      "driver/removeCircleMember.php": "Remove Circle Member",
      "driver/rideHistory.php": "Ride History",
      "driver/scanQr.php": "TodaRescue",
      "driver/verificationScreen.php": "TodaRescue"
    };

    pageTitle.textContent = titles[pageKey] || "Page";
  });
</script>
