<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../api/database.php';

use benhall14\phpCalendar\Calendar;

// Fetch all rooms from the database
$calendarRoomsStmt = $pdo->query("SELECT id, type FROM rooms");
$calendarRooms = $calendarRoomsStmt->fetchAll(PDO::FETCH_ASSOC);

if (!$calendarRooms) {
    echo "No rooms found in the database.";
    exit;
}

// Array to store hardcoded images and descriptions for rooms
$roomDetails = [
    'budget' => [
        'image' => './assets/images/budget-room.jpg',
        'title' => 'Rick’s Rusty Garage',
        'description' => 'Welcome to <b>Rick’s Rusty Garage</b>, the ultimate budget-friendly crash pad where scrap parts
         and ‘borrowed’ tech create a rugged, interdimensional vibe. It’s the perfect no-frills spot for adventurers
          who don’t mind a little grease with their comfort!',
    ],
    'standard' => [
        'image' => './assets/images/standard-room.jpg',
        'title' => 'Rick’s Cozy Retreat',
        'description' => 'Tucked away in the heart of the multiverse,
         <b>Rick’s Cozy Retreat</b> is a warm escape from interdimensional chaos,
          featuring plush furnishings and soft lighting. It’s the perfect spot to unwind in style,
           where coziness meets sophistication!',
    ],
    'luxury' => [
        'image' => './assets/images/luxury-room.webp',
        'title' => 'Citadel Sky Suite',
        'description' => 'Ascend to the <b>Citadel Sky Suite</b>, where the multiverse meets ultimate luxury.
         Gaze upon endless dimensions, enjoy futuristic comforts,
         and feel like the Rickest Rick—without the burden of saving Mortys.',
    ],
];

// Loop through each room to generate a calendar
foreach ($calendarRooms as $room) {
    $roomId = $room['id'];
    $roomName = $room['type'];

    // Fetch bookings for the current room
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

    // Create a calendar for January 2025
    $calendar = new Calendar(2025, 1);
    $calendar->useMondayStartingDate(); // Use Monday as the first day of the week
    $calendar->useFullDayNames(); // Display full names of the days
    $calendarHTML = $calendar->draw(date('2025-01-01'));

    // Modify the calendar to add booked dates
    $bookedDays = []; // Array to store booked dates
    foreach ($bookings as $booking) {
        $start = new DateTime($booking['check_in_date']);
        $end = new DateTime($booking['check_out_date']);

        while ($start <= $end) {
            if ($start->format('n') == 1) { // Only for January
                $day = (int)$start->format('j'); // Get the day of the month (1-31)
                $bookedDays[$day] = htmlspecialchars($booking['guest_name']); // Store guest name
            }
            $start->modify('+1 day'); // Move to the next day
        }
    }

    // Modify the calendar HTML based on booked dates
    $calendarHTML = preg_replace_callback(
        '/<div class="cal-day-box">(.*?)<\/div>/',
        function ($matches) use ($bookedDays) {
            $day = (int)trim($matches[1]);
            if (isset($bookedDays[$day])) {
                return '<div class="cal-day-box booked-date" title="Guest: ' . $bookedDays[$day] . '">' . $day . '</div>';
            }
            return $matches[0]; // Return unchanged if the day is not booked
        },
        $calendarHTML
    );

    // Fetch image and description based on the room type
    $image = $roomDetails[$roomName]['image'] ?? 'path/to/default-room.jpg';
    $description = $roomDetails[$roomName]['description'] ?? 'No description available.';
    $title = $roomDetails[$roomName]['title'] ?? 'Room';

    // Display the calendar, image, and description
    echo "<div class='calendar-item'>";
    echo "<div class='calendar'>";
    echo "<h2>$roomName</h2>"; // Display the room name
    echo $calendarHTML; // Display the calendar
    echo "</div>";
    echo "<div class='info'>";
    echo "<img src='$image' alt='$roomName' />"; // Display the room image
    echo "<div class='description'>";
    echo "<h2>$title</h2><p>$description</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}
