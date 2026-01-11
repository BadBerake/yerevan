# 🏰 Yerevango Project Overview

**Yerevango** is a comprehensive city guide and booking platform designed for Yerevan, Armenia. It allows users to discover cafes, restaurants, and events, make reservations, buying tickets, and explore the city's vibrant life.

---

## 🔥 Key Features

### 1. Places & Venues (Cafes/Restaurants)
- **Rich Directory**: Browse a curated list of top cafes and restaurants.
- **Detailed Profiles**: Each place features:
  - 📸 High-quality Photo Gallery
  - 📍 Precise Location & Address
  - 📞 Direct Contact Buttons (Call, WhatsApp, Instagram, Website)
  - ⏰ Verified Working Hours
  - ✨ Amenities list (Wi-Fi, Outdoor seating, etc.)
- **Table Reservation**: Integrated booking system allowing users to select date, time, and party size.
- **User Reviews**: Rating and comment system for community feedback.

### 2. Tours & Itineraries 🗺️
- **Curated Trips**: Step-by-step guides for exploring Armenia (e.g., Garni & Geghard, Sevan Lake).
- **Rich Content**: Detailed descriptions, stops, duration, and difficulty levels.
- **Booking Integration**: Easy conversion to booking via phone or website.

### 3. Automated Data Engine (Scrapers) 🤖
Built-in tools to keep data fresh and synchronized with **2GIS**:
- **Selenium Scraper**: Advanced browser automation that crawls 2GIS to extract place details without API limits.
- **Image Intelligence**: Automatically checks and downloads high-quality images for each venue.
- **Smart Importer**: Python scripts (`import_cafes_v2.py`) that verify data, generate SEO-friendly slugs, and populate the PostgreSQL database.

### 3. Service & Event Management
- **Events Calendar**: Discover local events and happenings.
- **Ticketing System**: Complete flow for purchasing and managing event tickets.
- **Services**: Sections for other city services (IT, Relocation, etc.).

### 4. User Ecosystem
- **Secure Auth**: Registration and Login with encrypted credentials.
- **User Dashboard**:
  - `My Reservations`: Track booking status.
  - `My Tickets`: Access purchased event tickets.
  - `Support`: Direct line to admin support.

### 5. Support System
- **Help Desk**: Integrated ticketing system for user inquiries.
- **Status Tracking**: Users can track ticket status (Open, Answered, Closed).

### 6. Admin Panel
- **Content Management**: Approve, edit, or remove places and events.
- **User Management**: Oversee user base and permissions.
- **Analytics**: View core metrics (Total users, Active bookings).

---

## 🛠️ Technology Stack

- **Core**: Native PHP 8.x (High performance, no heavy framework overhead)
- **Database**: PostgreSQL
- **Frontend**: Modern HTML5 / CSS3 / Vanilla JS
- **Automation**: Python 3.9 + Selenium + BeautifulSoup4 + Requests
- **Server**: Compatible with Apache/Nginx (Local development via PHP built-in server)

---

## 📂 Project Structure

```
Yerevango/
├── public/                 # Web root
├── src/                    # Core logic & Classes
│   ├── Database.php        # DB Connection wrapper
│   ├── Auth.php            # Authentication logic
│   └── ...
├── templates/              # Frontend Views (PHP files)
│   ├── detail.php          # Place details page
│   ├── home.php            # Landing page
│   └── ...
├── database/               # SQL Schemas
├── 2gis_selenium_scraper.py# Data collection bot
├── import_cafes_v2.py      # Data importer
└── serve.sh                # Local dev server script
```

---

## 🚀 Usage

### Running Locally
```bash
./serve.sh
# Visit http://localhost:8000
```

### Collecting Data
```bash
# 1. Scrape 2GIS for Cafes
python3 2gis_selenium_scraper.py

# 2. Import to Database & Download Images
python3 import_cafes_v2.py
```
