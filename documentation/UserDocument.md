# User Creation and Profile Initialization

This document describes how user creation works in the system and how student and professor profiles are initialized during that process.

The system is designed with a strict separation of concerns. Authentication and authorization are handled exclusively through the `users` table, while academic and professional data is stored in dedicated profile tables (`students` and `professors`). This avoids data duplication, keeps authentication logic centralized, and allows profile data to evolve independently.

---

## Users and Authentication

Every user in the system is represented by a record in the `users` table. A user is identified by an email address and authenticated using a hashed password. Each user also has a `type` that defines their role in the system and a `status` that determines whether the account is active.

The supported user types are `admin`, `student`, and `professor`. There is no public registration flow. All users are created by administrators through an authenticated API endpoint.

---

## Student and Professor Profiles

Students and professors have additional domain-specific data that does not belong in the `users` table. This data is stored in separate profile tables.

When a user is created with type `student`, a corresponding record is created in the `students` table.  
When a user is created with type `professor`, a corresponding record is created in the `professors` table.  
When a user is created with type `admin`, no additional profile record is created.

These profile records are created as profile shells. At creation time, only basic identity data is stored. All other academic or employment-related fields are nullable and are expected to be filled later through update endpoints.

---

## Data Passed During User Creation

User creation requires only the data needed to initialize the account and its profile shell.

The following fields are required:
- `email`
- `password`
- `type`
- `first_name`
- `last_name`

The email is stored in the `users` table and duplicated into the related profile table (`students` or `professors`). This duplication is intentional and allows profile queries without always joining the users table.

All other profile fields such as student indexes, codes, academic titles, departments, employment types, and similar attributes remain nullable in the database schema and are not required during user creation.

---

## Atomic Creation Process

User creation is performed inside a database transaction.

First, a record is created in the `users` table.  
Then, depending on the user type, a corresponding profile record is created in the appropriate profile table.

If any step of this process fails, the entire transaction is rolled back. This ensures that the system never ends up with partially created users or orphaned profile records.

