# Books Module

This document describes the Books module of the Campus API.  
The module is responsible for managing bibliographic data, physical book copies, and exposing basic CRUD operations for books. It is designed to be simple, extensible, and consistent with the overall domain-driven approach used across the system.

---

## Conceptual Overview

In the Campus system, a **Book** represents a bibliographic entity: a title, author, publication data, and general description. A book itself is not borrowable directly. Instead, borrowing is performed on **Book Copies**, which represent physical instances of a book.

This separation allows the system to:
- Track multiple physical copies of the same book
- Keep a borrowing history
- Safely manage availability and inventory

The Books module is therefore concerned only with **book metadata and inventory counts**, while borrowing logic is handled elsewhere.

---

## Data Model

### Books Table

The `books` table stores bibliographic and inventory-related data.  
Each row represents a single book title.

Key characteristics of the model:
- A book has a title and author (required)
- Publication-related fields are optional
- Inventory is tracked using `total_copies` and `available_copies`
- ISBN is intentionally not used as an identifier in this system

The table includes timestamps for auditing and future extensions.

---

## Book Model Responsibilities

The `Book` model encapsulates all domain logic related to books.  
Controllers do not contain business logic; instead, they delegate all meaningful operations to the model.

The Book model is responsible for:
- Retrieving all books
- Retrieving a single book by ID
- Searching books by title
- Creating new books
- Updating existing books
- Deleting books

This ensures that all rules related to books are defined in one place and can be reused consistently.



