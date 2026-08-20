function toggleSelect() {
    document.querySelector(".custom-select").classList.toggle("active");
  }
  
  function selectOption(element) {
    document.getElementById("selected-option").innerText = element.innerText;
    document.querySelector(".custom-select").classList.remove("active");
  }
  
  document.addEventListener("DOMContentLoaded", function () {
    const menuToggler = document.querySelector(".menu-toggler");
    const dashboardLft = document.querySelector(".dashboard_lft");

    if (menuToggler && dashboardLft) {
        menuToggler.addEventListener("click", function () {
            menuToggler.classList.toggle("active"); // Toggle class on button
            dashboardLft.classList.toggle("menu-open"); // Toggle class on dashboard_lft
        });
    }
});




  