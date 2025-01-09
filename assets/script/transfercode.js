document
  .getElementById("transferForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault(); // Förhindra att sidan laddas om

    const formData = new FormData(this); // Hämta formulärdata
    const messageDiv = document.getElementById("message"); // Hämta meddelandeelementet

    try {
      const response = await fetch("/../api/transfercode.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (response.ok) {
        // Visa transferkoden
        messageDiv.innerHTML = `<p>Transfercode created: <strong>${result.transferCode}</strong></p>`;
      } else {
        // Visa felmeddelande
        messageDiv.innerHTML = `<p>Något gick fel: ${
          result.error || "Okänt fel"
        }</p>`;
        messageDiv.style.color = "red"; // Sätt färg för fel
      }
    } catch (error) {
      // Hantera nätverks- eller serverfel
      messageDiv.innerHTML = `<p>Fel: ${error.message}</p>`;
      messageDiv.style.color = "red";
    }
  });
