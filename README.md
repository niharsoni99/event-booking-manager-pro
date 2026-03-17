# Event Booking Manager Pro

A production-grade WordPress plugin for managing events with a Custom Post Type, rich meta fields, and a public REST API.

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.1 or higher |
| WordPress | 6.0 or higher |
| Composer | Any recent version |

---

## Installation

1. Copy the plugin folder to `wp-content/plugins/event-booking-manager-pro/`
2. Inside the plugin folder, run:
   ```bash
   composer install
   ```
3. In WP Admin, go to **Plugins** and activate **Event Booking Manager Pro**
4. Navigate to **Events** in the left admin menu to confirm the CPT is active

---

## Architecture Overview

The plugin uses a fully OOP architecture with PHP namespaces under `TPots\EventBooking`, PSR-4 autoloading via Composer, and a central `Loader` class that is the single location for all `add_action()` and `add_filter()` calls. The `Plugin` class bootstraps all components (EventPostType, EventMetaBox, EventsEndpoint, AdminAssets) and wires them into the Loader. No procedural code, no scattered hooks.

---

## Custom Post Type

- **Slug:** `event`
- **URL structure:** `/events/event-name/`
- **Supports:** title, editor, featured image
- **Admin icon:** Calendar (dashicons-calendar-alt)

---

## Meta Fields

| Field | Meta Key | Type |
|---|---|---|
| Event Date | `_event_date` | string (Y-m-d) |
| Event Time | `_event_time` | string (HH:MM) |
| Location | `_event_location` | string (multiline) |
| Available Seats | `_available_seats` | integer (>= 0) |
| Booking Status | `_booking_status` | enum: open / closed / cancelled |

---

## REST API

### Endpoint

```
GET /wp-json/tpots/v1/events
```

**Authentication:** None required (public endpoint)

### Query Parameters

| Parameter | Type | Default | Description |
|---|---|---|---|
| `date` | string (YYYY-MM-DD) | today | Return events on or after this date |
| `limit` | integer (1–100) | 10 | Maximum events to return |

### Example Requests

```bash
# All upcoming events
curl https://example.com/wp-json/tpots/v1/events

# Events from a specific date
curl https://example.com/wp-json/tpots/v1/events?date=2025-06-01

# Limit to 5 results
curl https://example.com/wp-json/tpots/v1/events?limit=5

# Combined
curl https://example.com/wp-json/tpots/v1/events?date=2025-06-01&limit=5
```

### Example Response

```json
[
  {
    "id": 42,
    "title": "Annual Tech Conference",
    "date": "2025-07-15",
    "time": "09:00",
    "location": "Convention Center, Hall A\nCity, State 12345",
    "available_seats": 250,
    "booking_status": "open",
    "permalink": "https://example.com/events/annual-tech-conference/"
  }
]
```

---

## Folder Structure

```
event-booking-manager-pro/
├── event-booking-manager-pro.php
├── composer.json
├── uninstall.php
├── README.md
├── .gitignore
├── assets/
│   ├── css/admin.css
│   └── js/admin.js
└── src/
    ├── Core/
    │   ├── Plugin.php
    │   ├── Loader.php
    │   └── Activator.php
    ├── PostTypes/
    │   └── EventPostType.php
    ├── MetaBox/
    │   └── EventMetaBox.php
    ├── RestAPI/
    │   └── EventsEndpoint.php
    └── Admin/
        └── AdminAssets.php
```

---

## License

GPL-2.0+
