# 🚦 Smart Traffic Violation Monitoring System

A modern desktop-based **Database Management System (DBMS)** project developed using **Python**, **CustomTkinter**, and **MySQL**. The system enables traffic authorities to efficiently manage vehicle owners, vehicles, and traffic violations through an interactive graphical user interface.

---

## 📌 Project Overview

The Smart Traffic Violation Monitoring System is designed to digitize and simplify traffic violation record management. It provides a centralized platform for maintaining owner information, vehicle details, and violation records while ensuring data integrity through a MySQL database.

---

## 🎯 SDG Goal

### SDG 11 – Sustainable Cities and Communities

This project contributes to:

* 🚗 Safer roads and transportation
* 🚦 Improved traffic management
* 🏙️ Smart city initiatives
* 📊 Efficient law enforcement operations

---

## 🛠️ Technology Stack

| Layer                 | Technology            |
| --------------------- | --------------------- |
| Frontend              | Python CustomTkinter  |
| GUI Components        | Tkinter (TTK Widgets) |
| Backend               | Python                |
| Database              | MySQL                 |
| Database Connectivity | MySQL Connector       |
| IDE                   | VS Code               |

---

## ✨ Features

### 👤 Owner Management

* Add vehicle owner details
* Search owners instantly
* View owner records

### 🚗 Vehicle Management

* Register vehicles
* Link vehicles to owners
* Search vehicle records

### 🚨 Violation Management

* Record traffic violations
* Track fine amounts
* Monitor payment status
* Search violation history

### 🔍 Smart Search

* Real-time search functionality
* Fast data retrieval

### 💾 Database Management

* Automatic database initialization
* Secure MySQL connectivity
* Structured relational database

### 🎨 Modern User Interface

* Dark-themed dashboard
* Responsive design
* User-friendly navigation
* Professional CustomTkinter UI

---

## 📂 Project Structure

```text
smart-traffic-system/
│
├── main.py
├── db.py
│
├── db/
│   └── database.sql
│
└── README.md
```

---

## 🗄️ Database Schema

### OWNER

Stores vehicle owner information.

| Field      | Type        |
| ---------- | ----------- |
| Owner_ID   | Primary Key |
| Name       | VARCHAR     |
| License_No | VARCHAR     |

### VEHICLE

Stores vehicle details.

| Field      | Type        |
| ---------- | ----------- |
| Vehicle_No | Primary Key |
| Owner_ID   | Foreign Key |
| Model      | VARCHAR     |

### VIOLATION

Stores traffic violation records.

| Field        | Type        |
| ------------ | ----------- |
| Violation_ID | Primary Key |
| Vehicle_No   | Foreign Key |
| Type         | VARCHAR     |
| Fine_Amount  | DECIMAL     |
| Status       | VARCHAR     |

---

## ⚙️ Installation

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/smart-traffic-system.git
cd smart-traffic-system
```

### Step 2: Install Dependencies

```bash
pip install customtkinter
pip install mysql-connector-python
```

Or

```bash
pip install -r requirements.txt
```

### Step 3: Start MySQL Server

Ensure MySQL Server is running.

Default configuration used:

```python
host='localhost'
user='root'
password='tiger'
```

Update `db.py` if your credentials are different.

### Step 4: Create Database

Run the SQL script:

```sql
database.sql
```

This will create:

```sql
traffic_violation_db
```

along with all required tables.

### Step 5: Run Application

```bash
python main.py
```

---

## 🏗️ System Architecture

```text
CustomTkinter GUI
        │
        ▼
Python Application
        │
        ▼
 MySQL Connector
        │
        ▼
  MySQL Database
```

---

## 📸 Application Modules

### Owners Dashboard

* View all owners
* Search by name or license number

### Vehicles Dashboard

* View all registered vehicles
* Search vehicle information

### Violations Dashboard

* View traffic violations
* Track fine payment status

### Add Entry Module

* Register owner
* Register vehicle
* Log traffic violation
* Update existing records

---

## 🔐 Database Features

* Relational Database Design
* Primary Keys
* Foreign Keys
* Data Integrity
* Transaction Management
* SQL Query Optimization

---

## 🚀 Future Enhancements

* Officer Management Module
* Challan Generation
* PDF Report Export
* User Authentication
* Camera-based Violation Detection
* Cloud Database Integration
* SMS & Email Notifications

---

## 👨‍💻 Developed By
**Computer Science Engineering (CSE) Mini Project**
Smart Traffic Violation Monitoring System using Python, CustomTkinter, and MySQL.

---

## 📜 License
This project is developed for educational and academic purposes.
