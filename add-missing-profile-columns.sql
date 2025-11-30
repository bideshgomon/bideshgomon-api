-- Add missing columns to user_profiles table

-- Profile Details section
ALTER TABLE user_profiles 
ADD COLUMN present_address_line TEXT AFTER present_address,
ADD COLUMN present_division VARCHAR(100) AFTER present_address_line,
ADD COLUMN present_district VARCHAR(100) AFTER present_division,
ADD COLUMN permanent_address_line TEXT AFTER permanent_address,
ADD COLUMN permanent_division VARCHAR(100) AFTER permanent_address_line,
ADD COLUMN permanent_district VARCHAR(100) AFTER permanent_division,
ADD COLUMN nid VARCHAR(50) AFTER nationality,
ADD COLUMN dob DATE AFTER date_of_birth;

-- Social Media section
ALTER TABLE user_profiles
ADD COLUMN facebook_url VARCHAR(255) AFTER social_links,
ADD COLUMN linkedin_url VARCHAR(255) AFTER facebook_url,
ADD COLUMN twitter_url VARCHAR(255) AFTER linkedin_url,
ADD COLUMN instagram_url VARCHAR(255) AFTER twitter_url,
ADD COLUMN whatsapp_number VARCHAR(20) AFTER instagram_url,
ADD COLUMN telegram_username VARCHAR(100) AFTER whatsapp_number;

-- Medical Information section
ALTER TABLE user_profiles
ADD COLUMN blood_group VARCHAR(10) AFTER health_insurance_expiry_date,
ADD COLUMN medical_conditions TEXT AFTER blood_group,
ADD COLUMN allergies TEXT AFTER medical_conditions,
ADD COLUMN medications TEXT AFTER allergies,
ADD COLUMN vaccinations JSON AFTER medications,
ADD COLUMN health_insurance_provider VARCHAR(255) AFTER vaccinations,
ADD COLUMN health_insurance_number VARCHAR(100) AFTER health_insurance_provider;

-- Financial Information section  
ALTER TABLE user_profiles
ADD COLUMN bank_account_number VARCHAR(100) AFTER bank_name,
ADD COLUMN tax_identification_number VARCHAR(50) AFTER other_assets_bdt,
ADD COLUMN source_of_funds VARCHAR(255) AFTER tax_identification_number;

-- Emergency Contact (already has emergency_contact_name, phone)
ALTER TABLE user_profiles
ADD COLUMN emergency_contact_relationship VARCHAR(100) AFTER emergency_contact_relation;

-- Passport section (for backward compatibility with profile fields)
ALTER TABLE user_profiles
ADD COLUMN passport_number VARCHAR(50) AFTER name_as_per_passport,
ADD COLUMN passport_issue_date DATE AFTER passport_number,
ADD COLUMN passport_expiry_date DATE AFTER passport_issue_date,
ADD COLUMN passport_issue_place VARCHAR(255) AFTER passport_expiry_date;

-- Professional section
ALTER TABLE user_profiles
ADD COLUMN current_occupation VARCHAR(255) AFTER employer_name,
ADD COLUMN current_company VARCHAR(255) AFTER current_occupation,
ADD COLUMN years_of_experience INT AFTER current_company;

-- Travel preferences
ALTER TABLE user_profiles
ADD COLUMN preferred_destination_countries JSON AFTER bio,
ADD COLUMN travel_purpose VARCHAR(100) AFTER preferred_destination_countries;
