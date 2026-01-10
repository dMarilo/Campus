# Dorms and Rooms Implementation

This document describes the implementation of the **Dorms** and **Rooms** modules in the Campus API.  
These modules are responsible for managing student dormitories and their internal room structure.

The implementation follows the same architectural principles used across the system:
- Controllers act as HTTP endpoints
- Models encapsulate domain logic
- All routes are protected via JWT authentication
- Database integrity is enforced through foreign keys

---

## Overview

A **Dorm** represents a physical student dormitory (building).
A **Room** represents an individual room inside a dorm.

Relationship:
- One dorm has many rooms
- Each room belongs to exactly one dorm

This structure allows future extensions such as:
- Assigning students to rooms
- Tracking occupancy
- Enforcing capacity limits
- Computing dorm-level statistics

---

## Database Schema

### Dorms Table

The `dorms` table stores high-level dormitory information.

Fields:
- `id` – Primary key
- `name` – Dorm name
- `address` – Physical address (nullable)
- `total_rooms` – Total number of rooms
- `total_beds` – Total number of beds
- `description` – Optional descriptive text
- `created_at`, `updated_at` – Timestamps

Each dorm is independent and does not rely on other tables.

---

### Rooms Table

The `rooms` table stores rooms belonging to dorms.

Fields:
- `id` – Primary key
- `dorm_id` – Foreign key referencing `dorms.id`
- `room_number` – Room identifier (unique per dorm)
- `capacity` – Number of beds in the room
- `occupied_beds` – Currently occupied beds
- `created_at`, `updated_at` – Timestamps

Constraints:
- A room cannot exist without a dorm
- `(dorm_id, room_number)` is unique

---

## Models

### Dorm Model

The `Dorm` model encapsulates all dorm-related domain logic.

Implemented responsibilities:
- Retrieve all dorms
- Retrieve a dorm by ID
- Search dorms by name
- Create a new dorm
- Update dorm details
- Delete a dorm
- Retrieve total bed capacity
- Retrieve total room count

The model exposes simple, readable domain methods that are consumed by the controller.

---

### Room Model

The `Room` model handles room-level operations.

Implemented responsibilities:
- Retrieve all rooms
- Retrieve rooms by dorm
- Retrieve a room by ID
- Search rooms
- Create a room
- Update a room
- Delete a room
- Expose capacity and occupancy data

All room operations respect dorm ownership via foreign keys.

---

## Controllers

### DormController

The `DormController` provides REST-style endpoints for dorm management.

Responsibilities:
- Listing all dorms
- Fetching a dorm by ID
- Searching dorms by name
- Creating a new dorm
- Updating an existing dorm
- Deleting a dorm
- Retrieving dorm capacity
- Retrieving dorm room count

The controller contains no business logic beyond validation and response formatting.

---

### RoomController

The `RoomController` manages dorm rooms.

Responsibilities:
- Listing all rooms
- Listing rooms for a specific dorm
- Fetching a room by ID
- Searching rooms
- Creating new rooms
- Updating room data
- Deleting rooms
- Retrieving room capacity

Room creation and updates automatically enforce dorm ownership and data integrity.


