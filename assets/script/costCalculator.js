document.addEventListener("DOMContentLoaded", () => {
  // Element references
  const roomSelect = document.getElementById("room_id");
  const featuresCheckboxes = document.querySelectorAll(
    'input[type="checkbox"][data-price]'
  );
  const totalCostElement = document.getElementById("total-cost");
  const checkInDate = document.getElementById("check_in_date");
  const checkOutDate = document.getElementById("check_out_date");

  // Get discount rate from hidden input
  const discountRateElement = document.getElementById("discount-rate");
  const discountRate = discountRateElement
    ? parseFloat(discountRateElement.value) / 100
    : 0;

  // Function to calculate the total cost
  const calculateTotalCost = () => {
    let total = 0;
    let discount = 0;

    // Room price
    const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
    const roomPrice = parseFloat(selectedRoom.getAttribute("data-price")) || 0;

    // Calculate number of days
    const startDate = new Date(checkInDate.value);
    const endDate = new Date(checkOutDate.value);

    if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
      const days = (endDate - startDate) / (1000 * 60 * 60 * 24); // Convert ms to days
      if (days > 0) {
        total += roomPrice * days;

        // Apply discount if the number of nights is at least 3
        if (days >= 3 && discountRate > 0) {
          discount = total * discountRate;
        }
      } else {
        console.warn(
          "Invalid date range: Check-Out Date must be after Check-In Date."
        );
      }
    }

    // Features cost
    featuresCheckboxes.forEach((checkbox) => {
      if (checkbox.checked) {
        total += parseFloat(checkbox.getAttribute("data-price")) || 0;
      }
    });

    // Subtract the discount
    total -= discount;

    // Log discount for debugging
    console.log(`Discount Applied: ${discount.toFixed(2)}$`);

    // Update total cost on the screen
    totalCostElement.textContent =
      isNaN(total) || total <= 0 ? "0" : total.toFixed(2);
  };

  // Event listeners to update total cost
  roomSelect.addEventListener("change", calculateTotalCost);
  checkInDate.addEventListener("change", calculateTotalCost);
  checkOutDate.addEventListener("change", calculateTotalCost);
  featuresCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", calculateTotalCost);
  });

  // Initialize calculation on page load
  calculateTotalCost();
});
