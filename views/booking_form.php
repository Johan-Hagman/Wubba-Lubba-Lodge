<div id="scroll-to-booking">
</div>
<article class="booking">
    <div class="form-container">
        <!-- Formulär för att boka ett rum och välja features -->
        <form action="./api/book_room.php" method="POST">

            <div class="form-header">
                <h2>Book room</h2>
            </div>

            <!-- Room selection -->
            <select id="room_id" name="room_id" required>
                <?php
                foreach ($rooms as $room): ?>
                    <option value="<?php echo $room['id']; ?>"
                        data-price="<?php echo isset($room['price']) ? $room['price'] : 0; ?>">
                        <?php echo htmlspecialchars($room['type']) . " ({$room['price']}$/night)"; ?>
                    </option>
                <?php endforeach; ?>

            </select><br><br>

            <!-- Check-in and Check-out dates -->
            <label for="check_in_date">Check-In Date:</label>
            <input type="date" id="check_in_date" name="check_in_date" min="2025-01-01" max="2025-01-31" required><br><br>

            <label for="check_out_date">Check-Out Date:</label>
            <input type="date" id="check_out_date" name="check_out_date" min="2025-01-01" max="2025-01-31" required><br><br>

            <!-- Guest Name -->
            <label for="guest_name">Guest Name:</label>
            <input type="text" id="guest_name" name="guest_name" required><br><br>

            <!-- Transfer Code -->
            <label for="transfer_code">Transfer Code:</label>
            <input type="text" id="transfer_code" name="transfer_code" required><br><br>

            <!-- Features selection -->
            <h2>Select Features</h2>
            <div class="features-container">
                <?php foreach ($features as $feature): ?>
                    <div>
                        <input type="checkbox" id="feature-<?php echo $feature['id']; ?>"
                            name="features[]"
                            value="<?php echo $feature['id']; ?>"
                            data-price="<?php echo isset($feature['price']) ? $feature['price'] : 0; ?>">
                        <label for="feature-<?php echo $feature['id']; ?>">
                            <?php echo htmlspecialchars($feature['name']) . " (Price: {$feature['price']}$)"; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Dold input för rabatt -->
            <input type="hidden" id="discount-rate" value="<?php echo htmlspecialchars($currentDiscount); ?>">

            <!-- Total Cost -->
            <p><strong>Total Cost:</strong> <span id="total-cost">0</span> $</p>
            <button type="submit">Book Now</button>
        </form>
    </div>

    <form id="transferForm">
        <div class="form-header">
            <h2>Grab Your Cash</h2>
        </div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="apiKey">API-Key:</label>
        <input type="text" id="apiKey" name="apiKey" required>

        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" step="0.01" required>

        <button type="submit">Create transfercode</button>

        <!-- Lägg till ett tomt meddelandeelement här -->
        <div id="message" style="margin-top: 10px;"></div>
    </form>

    <div id="message"></div>



    <div class="squanch-container">
        <div class="speech-bubble">
            <p>
                Hey squanchy travelers! Book 3+ nights at Wubba Lubba Lodge and enjoy
                <span class="dynamic-discount"><b><?php echo htmlspecialchars($currentDiscount); ?></span>%</b> off your room rate (features excluded). The more nights you squanch, the more you save. Now that’s interdimensional squanchiness!
            </p>
        </div>
        <img src="./assets/images/squanch-removebg-preview.png" alt="Squanch" class="squanch-image">
    </div>
</article>