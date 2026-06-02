# Smart Traffic Violation Monitoring System 🚦
An **"Introduction to DBMS"** College Mini Project.

This is a clean, modern, and beginner-friendly Database Management System (DBMS) web application designed using a **PHP** backend, **MySQL** database, and a highly polished **HTML, CSS, and JavaScript** frontend. 

It provides an intuitive dashboard alongside comprehensive **CRUD (Create, Read, Update, Delete)** modules for all core traffic administration entities.

---

## 📂 Project Structure

```text
smart-traffic-system/
├── config/
│   └── db_connect.php       # Database connection file using PHP MySQLi
├── css/
│   └── style.css            # Premium, modern responsive dashboard stylesheet
├── db/
│   └── database.sql         # SQL script containing schema and rich sample data
├── includes/
│   ├── header.php           # Shared dynamic navigation sidebar and global header
│   └── footer.php           # Shared UI footer and interactive script blocks
├── index.php                # Homepage Control Center Dashboard with analytics counters
├── owners.php               # Owner CRUD operations (List grid and Register form)
├── owner_edit.php           # Edit existing Owner parameters
├── vehicles.php             # Vehicle CRUD operations (List grid and dropdown link form)
├── vehicle_edit.php         # Edit Vehicle details
├── officers.php             # Officer CRUD operations (List grid and Register form)
├── officer_edit.php         # Edit Officer records
├── violations.php           # Violation CRUD operations (List grid and record form)
├── violation_edit.php       # Edit Violation details and status (Paid/Pending)
├── challans.php             # Challan CRUD operations (List grid and ticket generator)
└── challan_edit.php         # Edit Challan ticket parameters
```

---

## 📊 Database Schema Relationships
The relational database design maps real-world entities together with strict integrity constraints (`ON DELETE CASCADE`):
1. **OWNER** represents registered vehicle drivers. Primary key `Owner_ID`.
2. **VEHICLE** maps physical automobiles to owners. Primary key `Vehicle_No`. Foreign key `Owner_ID` references `OWNER(Owner_ID)`.
3. **OFFICER** stores traffic patrol personnel. Primary key `Officer_ID`.
4. **VIOLATION** registers traffic offenses. Primary key `Violation_ID`. Foreign key `Vehicle_No` references `VEHICLE(Vehicle_No)`.
5. **CHALLAN** generates payment tickets. Primary key `Challan_ID`. Foreign key `Violation_ID` references `VIOLATION(Violation_ID)`. Foreign key `Officer_ID` references `OFFICER(Officer_ID)`.

---

## ⚡ Setup & Run Instructions

Follow these step-by-step instructions to run this project on your local system:

### Prerequisite: Install a PHP/MySQL Local Server Environment
We recommend installing **XAMPP** (or WampServer, MAMP, Laragon) which bundles Apache, PHP, and MySQL together.
* Download XAMPP: [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)

---

### Step 1: Place Project Files in the Webroot
1. Copy the entire `smart-traffic-system` project folder.
2. Paste it into your XAMPP web server directory:
   * **Windows**: `C:\xampp\htdocs\`
   * **macOS**: `/Applications/XAMPP/htdocs/`
   * **Linux**: `/opt/lampp/htdocs/`

So your files reside at `C:\xampp\htdocs\smart-traffic-system\index.php`.

---

### Step 2: Start Apache and MySQL Servers
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to the **Apache** module (Web Server).
3. Click **Start** next to the **MySQL** module (Database Server).
   * Ensure both indicators turn green.

---

### Step 3: Create the Database & Import SQL Schema
1. Open your browser and navigate to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click on the **SQL** tab in the top navigation bar.
3. Open `db/database.sql` from your project folder, copy its entire contents, and paste it into the phpMyAdmin SQL text area.
4. Click **Go** in the bottom right corner.
   * This automatically creates the `traffic_violation_db` database and all 5 tables with complete sample records!

---

### Step 4: Run the Application!
Open your web browser and navigate to the project URL:
👉 **[http://localhost/smart-traffic-system/](http://localhost/smart-traffic-system/)**

You will be greeted by the beautiful dark-slate Control Center Dashboard displaying active metrics and recent activities immediately!

---

## 🚀 Key Features for DBMS Viva/Assessments
This project has been intentionally customized to show examiners advanced DBMS mastery:
* **Referential Integrity Validation**: Try deleting an Owner. In the dashboard or listing, notice how all their registered vehicles, violations, and challan tickets are safely deleted automatically. This demonstrates working Cascading Foreign Keys.
* **Complex JOIN Queries**: The dashboard (`index.php`), Violations page (`violations.php`), and Challans page (`challans.php`) combine multiple tables using `JOIN` operators to display synchronized driver name, vehicle plate, and officer name data.
* **Form Inputs Validation**: HTML drop-down selectors and modern forms prevent inputting invalid values or orphaned foreign keys, protecting database consistency.
* **SQL Injection Prevention**: Safe procedural variable escaping is written using `mysqli_real_escape_string` inside PHP handlers.
* **Polished Micro-Interactions**: Features a gorgeous dark theme, custom responsive tables, sliding sidebar highlight animations, and self-fading notification blocks.
