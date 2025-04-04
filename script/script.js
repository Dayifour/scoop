let menu = document.querySelector("#menu-icon");
let navbar = document.querySelector(".navbar");

menu.addEventListener("click", function () {
  navbar.classList.toggle("active");
});

window.onscroll = () => {
  navbar.classList.remove("active");
};
let homep = document.querySelector(".homep");
let homeimg = document.getElementById("homeimg");

if (window.innerWidth <= 917.6) {
  homeimg.src = "../img/OIP (2).jpg";
}
if (window.innerWidth <= 480) {
  homep.className = "";
}
