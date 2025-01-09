document.addEventListener("DOMContentLoaded", () => {
  // Highlight booked dates in the calendar
  document.querySelectorAll(".cal-day-box.booked-date").forEach((date) => {
    date.style.backgroundColor = "red"; // Change the background color to red
    date.style.color = "white"; // Change the text color to white
    date.style.fontWeight = "bold"; // Make the text bold
  });
});
