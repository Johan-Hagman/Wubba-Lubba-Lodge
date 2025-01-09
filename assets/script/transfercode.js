document
  .getElementById("transferForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevent the page from reloading on form submission

    const formData = new FormData(this); // Retrieve form data
    const messageDiv = document.getElementById("message"); // Get the element to display messages

    try {
      const response = await fetch("./api/transfercode.php", {
        method: "POST", // Send data via POST method
        body: formData, // Include form data in the request body
      });

      const result = await response.json(); // Parse the response as JSON

      if (response.ok) {
        // Display the transfer code if the request was successful
        messageDiv.innerHTML = `<p>Transfercode created: <strong>${result.transferCode}</strong></p>`;
      } else {
        // Display an error message if the server returned an error
        messageDiv.innerHTML = `<p>Something went wrong: ${
          result.error || "Unknown error"
        }</p>`;
        messageDiv.style.color = "red"; // Set the text color to red for errors
      }
    } catch (error) {
      // Handle network or server errors
      messageDiv.innerHTML = `<p>Error: ${error.message}</p>`;
      messageDiv.style.color = "red";
    }
  });
