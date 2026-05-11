const showRegister = document.getElementById("showRegister");
const showLogin = document.getElementById("showLogin");
if (showRegister && showLogin) {
  showRegister.addEventListener("click", (e) => {
    e.preventDefault();
    toggleSections();
  });
  showLogin.addEventListener("click", (e) => {
    e.preventDefault();
    toggleSections();
  });
}

function toggleSections() {
  const visibleSection = document.querySelector(".form-section.visible");
  const hiddenSection = document.querySelector(".form-section:not(.visible)");
  if (visibleSection) {
    visibleSection.classList.remove("visible");
  }
  if (hiddenSection) {
    hiddenSection.classList.add("visible");
  }
}