document.addEventListener("DOMContentLoaded", () => {
  // Markera bokade datum i kalendern
  document.querySelectorAll(".cal-day-box.booked-date").forEach((date) => {
    date.style.backgroundColor = "red"; // Ändra bakgrundsfärgen till röd
    date.style.color = "white"; // Ändra textfärgen till vit
    date.style.fontWeight = "bold"; // Gör texten fet
  });
});
