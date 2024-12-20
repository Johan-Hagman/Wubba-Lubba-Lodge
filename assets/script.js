document.addEventListener('DOMContentLoaded', () => {
    // Markera bokade datum i kalendern
    document.querySelectorAll('.cal-day-box.booked-date').forEach(date => {
      date.style.backgroundColor = 'red'; // Ändra bakgrundsfärgen till röd
      date.style.color = 'white';        // Ändra textfärgen till vit
      date.style.fontWeight = 'bold';    // Gör texten fet
    });
  
    // Elementreferenser
    const roomSelect = document.getElementById('room_id');
    const featuresCheckboxes = document.querySelectorAll('input[type="checkbox"][data-price]');
    const totalCostElement = document.getElementById('total-cost');
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
  
    // Debug-loggar för att säkerställa att element hittas korrekt
    console.log(roomSelect, featuresCheckboxes, totalCostElement, checkInDate, checkOutDate);
  
    // Funktion för att beräkna total kostnad
    const calculateTotalCost = () => {
      let total = 0;
  
      // Rumspris
      const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
      const roomPrice = parseFloat(selectedRoom.getAttribute('data-price')) || 0;
  
      // Beräkna antal dagar
      const startDate = new Date(checkInDate.value);
      const endDate = new Date(checkOutDate.value);
  
      if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
        const days = (endDate - startDate) / (1000 * 60 * 60 * 24);
        if (days > 0) {
          total += roomPrice * days;
        } else {
          console.warn('Invalid date range: Check-Out Date must be after Check-In Date.');
        }
      }
  
      // Features-kostnad
      featuresCheckboxes.forEach((checkbox) => {
        if (checkbox.checked) {
          total += parseFloat(checkbox.getAttribute('data-price')) || 0;
        }
      });
  
      // Uppdatera totalen på skärmen
      totalCostElement.textContent = isNaN(total) || total <= 0 ? 'Invalid date range or inputs' : total.toFixed(2);
    };
  
    // Event Listeners för att uppdatera total kostnad
    roomSelect.addEventListener('change', calculateTotalCost);
    checkInDate.addEventListener('change', calculateTotalCost);
    checkOutDate.addEventListener('change', calculateTotalCost);
    featuresCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener('change', calculateTotalCost);
    });
  
    // Kopiera valda features till huvudformuläret vid submit
    document.querySelector('form[action="/api/book_room.php"]').addEventListener('submit', function (e) {
      // Validera datumen innan formuläret skickas
      const startDate = new Date(checkInDate.value);
      const endDate = new Date(checkOutDate.value);
  
      if (isNaN(startDate.getTime()) || isNaN(endDate.getTime()) || startDate >= endDate) {
        e.preventDefault();
        alert('Please enter a valid date range.');
        return;
      }
  
      // Lägg till valda features i formuläret
      const selectedFeatures = document.querySelectorAll('input[name="features[]"]:checked');
  
      selectedFeatures.forEach(feature => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'features[]';
        hiddenInput.value = feature.value;
        this.appendChild(hiddenInput);
      });
    });
  });
  