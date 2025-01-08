document.addEventListener("DOMContentLoaded", () => {
  // Elementreferenser
  const roomSelect = document.getElementById("room_id");
  const featuresCheckboxes = document.querySelectorAll(
    'input[type="checkbox"][data-price]'
  );
  const totalCostElement = document.getElementById("total-cost");
  const checkInDate = document.getElementById("check_in_date");
  const checkOutDate = document.getElementById("check_out_date");

  // Hämta rabattprocent från dold input
  const discountRateElement = document.getElementById("discount-rate");
  const discountRate = discountRateElement
    ? parseFloat(discountRateElement.value) / 100
    : 0;

  // Funktion för att beräkna total kostnad
  const calculateTotalCost = () => {
    let total = 0;
    let discount = 0;

    // Rumspris
    const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
    const roomPrice = parseFloat(selectedRoom.getAttribute("data-price")) || 0;

    // Beräkna antal dagar
    const startDate = new Date(checkInDate.value);
    const endDate = new Date(checkOutDate.value);

    if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
      const days = (endDate - startDate) / (1000 * 60 * 60 * 24); // Konvertera ms till dagar
      if (days > 0) {
        total += roomPrice * days;

        // Applicera rabatt om antal nätter är minst 3
        if (days >= 3 && discountRate > 0) {
          discount = total * discountRate;
        }
      } else {
        console.warn(
          "Invalid date range: Check-Out Date must be after Check-In Date."
        );
      }
    }

    // Features-kostnad
    featuresCheckboxes.forEach((checkbox) => {
      if (checkbox.checked) {
        total += parseFloat(checkbox.getAttribute("data-price")) || 0;
      }
    });

    // Dra av rabatten
    total -= discount;

    // Visa rabatten i konsolen (debug)
    console.log(`Discount Applied: ${discount.toFixed(2)}$`);

    // Uppdatera totalen på skärmen
    totalCostElement.textContent =
      isNaN(total) || total <= 0 ? "0" : total.toFixed(2);
  };

  // Event Listeners för att uppdatera total kostnad
  roomSelect.addEventListener("change", calculateTotalCost);
  checkInDate.addEventListener("change", calculateTotalCost);
  checkOutDate.addEventListener("change", calculateTotalCost);
  featuresCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", calculateTotalCost);
  });

  // Initiera beräkning vid sidladdning
  calculateTotalCost();
});
