# Wubba Lubba Lodge - A Rick and Morty Hotel Booking Website

Welcome to **Wubba Lubba Lodge**, a Rick and Morty-inspired hotel booking platform created as part of the Yrgopelago school project. This vibrant and creative website provides a unique experience with its multiverse theme. Users can explore room types, book their stays, and enjoy fun Rick and Morty references throughout the site.

## Project Details

- **Project Name**: Wubba Lubba Lodge
- **Theme**: Rick and Morty
- **Purpose**: School project under the Yrgopelago initiative. More details can be found at [yrgopelago.se](https://yrgopelago.se).
- **Languages Used**: 
  - PHP
  - JavaScript
  - HTML
  - CSS
  - SQLite
- **External Libraries/Tools**:
  - [PHP-Calendar](https://packagist.org/packages/benhall14/php-calendar): For the calendar feature.
  - [Dotenv](https://github.com/vlucas/phpdotenv): For environment variable management.
- **License**: MIT License

## Features

- **Multiverse-themed Booking System**: Explore and book from three distinct room types:
  - **Budget**: Rick’s Rusty Garage, an interdimensional no-frills room.
  - **Standard**: Rick’s Cozy Retreat, where chaos meets coziness.
  - **Luxury**: The Reverie Throne, a chair-driven dream experience.
- **Dynamic Pricing**: Discounts for stays of three nights or more, excluding additional features.
- **Additional Features**: Add-ons like minibars, saunas, and more can be selected during booking.
- **Admin Panel**: Accessible via a secure login system to manage bookings and settings.

## Installation and Setup

1. Clone the repository to your local environment.
   ```bash
   git clone <repository-url>
   cd wubba-lubba-lodge

2. Install dependencies using Composer.
    composer install

3. Set up the .env file for environment variables.
    API_KEY=your-api-key-here
DATABASE_PATH=/path/to/your/database.sqlite

4. Ensure the php-calendar and dotenv libraries are correctly included in the project.

## SQL Queries for Recreating the Database

-- Table for features that can be added to bookings
CREATE TABLE features (
    id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
    name TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);

-- Table for room bookings
CREATE TABLE bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
    room_id INTEGER NOT NULL REFERENCES rooms(id),
    guest_name TEXT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    transfer_code TEXT NOT NULL
);

-- Table for available rooms
CREATE TABLE rooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    hotel_id INTEGER NOT NULL,
    type TEXT NOT NULL,
    price INTEGER NOT NULL
);

-- Table for logging API requests and responses
CREATE TABLE api_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    endpoint VARCHAR(255) NOT NULL,
    request_data TEXT NOT NULL,
    response_data TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for mapping features to specific bookings
CREATE TABLE booking_features (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_id INTEGER NOT NULL,
    feature_id INTEGER NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (feature_id) REFERENCES features(id)
);

-- Table for application settings
CREATE TABLE settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    value VARCHAR(255) NOT NULL
);

-- Table for hotel information
CREATE TABLE hotel_info (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    name TEXT NOT NULL,                   
    stars INTEGER NOT NULL,               
    description TEXT                      
);



## Usage
Homepage: Explore the hotel with its Rick and Morty-inspired design.
Room Booking: Use the booking form to choose a room, select dates, and add features.
Admin Panel: Secure access for managing bookings and settings.
Custom Themes: Dynamic elements such as discounts and colorful designs enhance the user experience.

## Credits
Inspiration: Rick and Morty
Design: School project under the Yrgopelago initiative.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Feedback

Functions.php:180-187 - might want to change the $stmt variables for query for easier understanding.

booking_form.php:16-18 - maybe assign htmlspecialchars($room['type']) to a $roomType variable for example just to make it easier to reuse.

header.php:18 - try not to mix php and html code.

style.css:36-73 - inline styling is not recommended consider using classes instead.

transfercode.js:19-29 - .innerHTML can be risky in terms of security and performance might want to use .textContent instead.

database.php:10 - might want to hide your database in a .env file so that no hackers can get information about your guests for example.

Book_room.php:162-164 - using bindParam instead of an array in the execute function might give a more type accurate INSERT. (Optional)

