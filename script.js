document.getElementById("showMessage").addEventListener("click", function () {
  const message = document.getElementById("message");
  message.classList.toggle("hidden");
  this.textContent = message.classList.contains("hidden")
    ? "Fariinta Fur ❤️"
    : "Fariinta Qari";
});
