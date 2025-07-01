document
  .getElementById("group-selector")
  .addEventListener("click", function () {
    const container = document.getElementById("group-container");
    const caretIcon = document.getElementById("caret-icon");
    const headerColor = document.getElementById("header-color");
    const memberContainer = document.getElementById("member-container");

    if (container.style.display === "none" || container.style.display === "") {
      container.style.display = "block";
      headerColor.classList.remove("bg-transparent");
      headerColor.classList.add("bg-white");
      caretIcon.classList.add("bi-caret-up-fill");
    } else {
      container.style.display = "none";
      headerColor.classList.add("bg-transparent");
      headerColor.classList.add("bg-white");
      caretIcon.classList.remove("bi-caret-up-fill");
      caretIcon.classList.add("bi-caret-down-fill");
    }
  });
