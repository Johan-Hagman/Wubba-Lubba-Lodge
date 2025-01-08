document.addEventListener("DOMContentLoaded", () => {
  const checkInDate = document.getElementById("check_in_date");
  const checkOutDate = document.getElementById("check_out_date");

  // Kopiera valda features till huvudformuläret vid submit
  document
    .querySelector('form[action="./api/book_room.php"]')
    .addEventListener("submit", function (e) {
      const startDate = new Date(checkInDate.value);
      const endDate = new Date(checkOutDate.value);

      if (
        isNaN(startDate.getTime()) ||
        isNaN(endDate.getTime()) ||
        startDate >= endDate
      ) {
        e.preventDefault();
        alert("Please enter a valid date range.");
        return;
      }

      const selectedFeatures = document.querySelectorAll(
        'input[name="features[]"]:checked'
      );
      selectedFeatures.forEach((feature) => {
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "features[]";
        hiddenInput.value = feature.value;
        this.appendChild(hiddenInput);
      });
    });
});
