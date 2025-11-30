-- =====================================================
-- CRITICAL SCHEMA FIXES - Scope Column Issues
-- Generated from deep-schema-scan.php
-- Error Type: SQLSTATE[42S22] - Column not found
-- =====================================================

-- Fix 1: Agency model scope columns
ALTER TABLE agencies 
ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER is_active,
ADD COLUMN is_premium TINYINT(1) DEFAULT 0 AFTER is_featured,
ADD COLUMN average_rating DECIMAL(3,2) DEFAULT 0.00 AFTER is_premium;

-- Fix 2: AgencyType model scope column
ALTER TABLE agency_types 
ADD COLUMN display_order INT DEFAULT 0 AFTER is_active;

-- Fix 3: Appointment model scope columns  
ALTER TABLE appointments 
ADD COLUMN appointment_date DATE NULL AFTER preferred_time,
ADD COLUMN appointment_time TIME NULL AFTER appointment_date;

-- Fix 4: City model scope column
ALTER TABLE cities 
ADD COLUMN is_capital TINYINT(1) DEFAULT 0 AFTER country_id;

-- Fix 5: InstitutionType model scope column
ALTER TABLE institution_types 
ADD COLUMN category VARCHAR(100) NULL AFTER name;

-- Fix 6: ServiceApplication model scope column
ALTER TABLE service_applications 
ADD COLUMN priority VARCHAR(50) DEFAULT 'normal' AFTER user_id;

-- Fix 7: ServiceCategory model scope column
ALTER TABLE service_categories 
ADD COLUMN sort_order INT DEFAULT 0 AFTER is_active;
