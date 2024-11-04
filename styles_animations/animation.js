// School list animation
document.querySelector(".collapsible").addEventListener("click", function () {
  var content = this.nextElementSibling;
  if (content.style.maxHeight) {
    content.style.maxHeight = null;
  }
  else {
    content.style.maxHeight = content.scrollHeight + "px";
  }
});

// Show settings
function showSettings() {
  var x = document.querySelector("#settings");
  if (x.style.display === "flex") {
    x.style.display = "none";
  }
  else {
    x.style.display = "flex";
  }
}
