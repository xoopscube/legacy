// Side Nav Menu
const menuNav = document.querySelector(".menu");
const btn = document.getElementsByClassName("mobile")[0];
const li = menuNav.getElementsByTagName("LI");

// Close Side Nav on click
for (const item of li) {
  item.addEventListener("click", function (event) {
    menuNav.classList.remove("open");
  });
}

// Open and switch color
function openNav(e) {
  var open = menuNav.classList.contains("open");
  if (open) {
    menuNav.classList.remove("open");
    btn.style.background = "#101010";
  } else {
    menuNav.classList.add("open");
    btn.style.background = "#00000055";
  }
}

// Callback render code block
Prism.highlightAll();