-- ==========================================
-- SMART TRAFFIC VIOLATION MONITORING SYSTEM
-- Database Schema (3-Table Clean Slate)
-- For "Introduction to DBMS" Mini Project
-- ==========================================

-- 1. Create Database if not exists
CREATE DATABASE IF NOT EXISTS traffic_violation_db;
USE traffic_violation_db;

-- 2. Drop existing tables if they exist
-- The tables are dropped in reverse order of dependencies.
DROP TABLE IF EXISTS VIOLATION;
DROP TABLE IF EXISTS VEHICLE;
DROP TABLE IF EXISTS OWNER;

-- 3. Create Tables

-- A. OWNER Table
-- Stores details of vehicle owners
CREATE TABLE OWNER (
    Owner_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    License_No VARCHAR(50) UNIQUE NOT NULL
);

-- B. VEHICLE Table
-- Stores vehicle details, referencing its Owner via Owner_ID
CREATE TABLE VEHICLE (
    Vehicle_No VARCHAR(20) PRIMARY KEY,
    Owner_ID INT NOT NULL,
    Model VARCHAR(100) NOT NULL,
    FOREIGN KEY (Owner_ID) REFERENCES OWNER(Owner_ID) ON DELETE CASCADE
);

-- C. VIOLATION Table
-- Stores traffic violations, referencing the violating vehicle via Vehicle_No
CREATE TABLE VIOLATION (
    Violation_ID INT AUTO_INCREMENT PRIMARY KEY,
    Vehicle_No VARCHAR(20) NOT NULL,
    Type VARCHAR(100) NOT NULL,
    Fine_Amount DECIMAL(10, 2) NOT NULL,
    Status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (Vehicle_No) REFERENCES VEHICLE(Vehicle_No) ON DELETE CASCADE
);
