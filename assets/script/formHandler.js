document.addEventListener("DOMContentLoaded", () => {
  const checkInDate = document.getElementById("check_in_date");
  const checkOutDate = document.getElementById("check_out_date");

  // Copy selected features to the main form upon submission
  document
    .querySelector('form[action="./api/book_room.php"]')
    .addEventListener("submit", function (e) {
      const startDate = new Date(checkInDate.value);
      const endDate = new Date(checkOutDate.value);

      // Validate the date range
      if (
        isNaN(startDate.getTime()) || // Check if the check-in date is invalid
        isNaN(endDate.getTime()) || // Check if the check-out date is invalid
        startDate >= endDate // Ensure the check-in date is before the check-out date
      ) {
        e.preventDefault(); // Prevent form submission
        alert("Please enter a valid date range."); // Show error message
        return;
      }

      // Find all selected features
      const selectedFeatures = document.querySelectorAll(
        'input[name="features[]"]:checked'
      );

      // Add selected features as hidden inputs to the form
      selectedFeatures.forEach((feature) => {
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "features[]";
        hiddenInput.value = feature.value;
        this.appendChild(hiddenInput);
      });
    });
});
