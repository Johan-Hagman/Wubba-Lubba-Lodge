<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../api/database.php';

use benhall14\phpCalendar\Calendar;

// Hämta alla rum från databasen
$roomsStmt = $pdo->query("SELECT id, type FROM rooms");
$rooms = $roomsStmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rooms) {
    echo "Inga rum hittades i databasen.";
    exit;
}

// Array för att lagra hårdkodade bilder och beskrivningar
$roomDetails = [
    'Budget Room' => [
        'image' => '/assets/standard-room.jpg',
        'description' => 'A cozy and simple budget-friendly room for travelers looking for a comfortable stay.',
    ],
    'Standard Room' => [
        'image' => 'path/to/standard-room.jpg',
        'description' => 'A perfect blend of comfort and style for a relaxing stay.',
    ],
    'Luxury Room' => [
        'image' => 'path/to/luxury-room.jpg',
        'description' => 'An exquisite space offering ultimate luxury and elegance.',
    ],
];

// Loop för att generera en kalender för varje rum
foreach ($rooms as $room) {
    $roomId = $room['id'];
    $roomName = $room['type'];

    // Hämta bokningar för det aktuella rummet
    $stmt = $pdo->prepare("
        SELECT check_in_date, check_out_date, guest_name 
        FROM bookings 
        WHERE room_id = :room_id 
        AND (
            check_in_date <= '2025-01-31' 
            AND check_out_date >= '2025-01-01'
        )
    ");
    $stmt->execute(['room_id' => $roomId]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Skapa kalender för januari 2025
    $calendar = new Calendar(2025, 1);
    $calendar->useMondayStartingDate();
    $calendar->useFullDayNames();
    // $calendar->stylesheet();
    $calendarHTML = $calendar->draw(date('2025-01-01'));

    // Modifiera kalendern för att lägga till `booked-date`
    $bookedDays = []; // Array för att lagra bokade datum
    foreach ($bookings as $booking) {
        $start = new DateTime($booking['check_in_date']);
        $end = new DateTime($booking['check_out_date']);

        while ($start <= $end) {
            if ($start->format('n') == 1) { // Endast januari
                $day = (int)$start->format('j'); // Hämta dagens nummer (1-31)
                $bookedDays[$day] = htmlspecialchars($booking['guest_name']); // Lagra gästnamn
            }
            $start->modify('+1 day');
        }
    }

    // === Modifiera kalenderns HTML baserat på bokade datum ===
    $calendarHTML = preg_replace_callback(
        '/<div class="cal-day-box">(.*?)<\/div>/',
        function ($matches) use ($bookedDays) {
            $day = (int)trim($matches[1]);
            if (isset($bookedDays[$day])) {
                return '<div class="cal-day-box booked-date" title="Gäst: ' . $bookedDays[$day] . '">' . $day . '</div>';
            }
            return $matches[0]; // Returnera oförändrad om dagen inte är bokad
        },
        $calendarHTML
    );


    // Hämta bild och beskrivning baserat på rummets namn
    $image = $roomDetails[$roomName]['image'] ?? 'path/to/default-room.jpg';
    $description = $roomDetails[$roomName]['description'] ?? 'No description available.';

    // Visa kalender, bild och beskrivning
    echo "<div class='calendar-item'>";
    echo "<div class='calendar'>";
    echo "<h2>$roomName</h2>"; // Visa rummets namn
    echo $calendarHTML; // Visa kalender
    echo "</div>";
    echo "<div class='info'>";
    echo "<img src='$image' alt='$roomName' />";
    echo "<p>$description</p>";
    echo "</div>";
    echo "</div>";
}
