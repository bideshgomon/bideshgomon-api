-- Essential Bangladesh Data

-- Insert Bangladesh and popular destination countries
INSERT INTO countries (name, code, iso_code, phone_code, currency_code, flag_emoji, capital, continent, is_popular, is_active, display_order) VALUES
('Bangladesh', 'BGD', 'BD', '+880', 'BDT', '🇧🇩', 'Dhaka', 'Asia', TRUE, TRUE, 1),
('United States', 'USA', 'US', '+1', 'USD', '🇺🇸', 'Washington D.C.', 'North America', TRUE, TRUE, 2),
('United Kingdom', 'GBR', 'GB', '+44', 'GBP', '🇬🇧', 'London', 'Europe', TRUE, TRUE, 3),
('Canada', 'CAN', 'CA', '+1', 'CAD', '🇨🇦', 'Ottawa', 'North America', TRUE, TRUE, 4),
('Australia', 'AUS', 'AU', '+61', 'AUD', '🇦🇺', 'Canberra', 'Oceania', TRUE, TRUE, 5),
('Germany', 'DEU', 'DE', '+49', 'EUR', '🇩🇪', 'Berlin', 'Europe', TRUE, TRUE, 6),
('United Arab Emirates', 'ARE', 'AE', '+971', 'AED', '🇦🇪', 'Abu Dhabi', 'Asia', TRUE, TRUE, 7),
('Saudi Arabia', 'SAU', 'SA', '+966', 'SAR', '🇸🇦', 'Riyadh', 'Asia', TRUE, TRUE, 8),
('Malaysia', 'MYS', 'MY', '+60', 'MYR', '🇲🇾', 'Kuala Lumpur', 'Asia', TRUE, TRUE, 9),
('Singapore', 'SGP', 'SG', '+65', 'SGD', '🇸🇬', 'Singapore', 'Asia', TRUE, TRUE, 10);

-- Insert currencies
INSERT INTO currencies (code, name, symbol, exchange_rate, is_active) VALUES
('BDT', 'Bangladeshi Taka', '৳', 1.000000, TRUE),
('USD', 'US Dollar', '$', 0.0091, TRUE),
('GBP', 'British Pound', '£', 0.0072, TRUE),
('CAD', 'Canadian Dollar', 'C$', 0.0124, TRUE),
('AUD', 'Australian Dollar', 'A$', 0.0140, TRUE),
('EUR', 'Euro', '€', 0.0084, TRUE),
('AED', 'UAE Dirham', 'د.إ', 0.0334, TRUE),
('SAR', 'Saudi Riyal', 'ر.س', 0.0341, TRUE),
('MYR', 'Malaysian Ringgit', 'RM', 0.0406, TRUE),
('SGD', 'Singapore Dollar', 'S$', 0.0122, TRUE);

-- Insert major Bangladesh cities
INSERT INTO cities (country_id, name, code, is_popular, is_active) VALUES
(1, 'Dhaka', 'DHA', TRUE, TRUE),
(1, 'Chittagong', 'CGP', TRUE, TRUE),
(1, 'Sylhet', 'ZYL', TRUE, TRUE),
(1, 'Rajshahi', 'RJH', TRUE, TRUE),
(1, 'Khulna', 'KHL', TRUE, TRUE),
(1, 'Barisal', 'BZL', TRUE, TRUE),
(1, 'Rangpur', 'RGP', TRUE, TRUE),
(1, 'Mymensingh', 'MYM', TRUE, TRUE);

-- Insert major airports
INSERT INTO airports (country_id, city_id, name, iata_code, icao_code, is_international, is_active) VALUES
(1, 1, 'Hazrat Shahjalal International Airport', 'DAC', 'VGHS', TRUE, TRUE),
(1, 2, 'Shah Amanat International Airport', 'CGP', 'VGEG', TRUE, TRUE),
(1, 3, 'Osmani International Airport', 'ZYL', 'VGSY', TRUE, TRUE);

-- Insert languages
INSERT INTO languages (name, code, native_name, direction, is_active, display_order) VALUES
('English', 'en', 'English', 'ltr', TRUE, 1),
('Bengali', 'bn', 'বাংলা', 'ltr', TRUE, 2),
('Arabic', 'ar', 'العربية', 'rtl', TRUE, 3),
('French', 'fr', 'Français', 'ltr', TRUE, 4),
('German', 'de', 'Deutsch', 'ltr', TRUE, 5),
('Spanish', 'es', 'Español', 'ltr', TRUE, 6),
('Chinese', 'zh', '中文', 'ltr', TRUE, 7),
('Japanese', 'ja', '日本語', 'ltr', TRUE, 8);

-- Insert language tests
INSERT INTO language_tests (language_id, name, code, description, max_score, passing_score, validity_months, is_active) VALUES
(1, 'IELTS', 'IELTS', 'International English Language Testing System', 9.00, 6.00, 24, TRUE),
(1, 'TOEFL', 'TOEFL', 'Test of English as a Foreign Language', 120.00, 80.00, 24, TRUE),
(1, 'PTE Academic', 'PTE', 'Pearson Test of English Academic', 90.00, 50.00, 24, TRUE),
(1, 'Cambridge English', 'CAE', 'Cambridge Assessment English', 230.00, 160.00, 36, TRUE),
(2, 'Bangla Proficiency Test', 'BPT', 'Bengali Language Proficiency Test', 100.00, 60.00, 24, TRUE),
(3, 'Arabic Proficiency Test', 'APT', 'Arabic Language Proficiency Test', 100.00, 60.00, 24, TRUE),
(4, 'DELF', 'DELF', 'Diplôme d\'Études en Langue Française', 100.00, 50.00, 36, TRUE),
(5, 'TestDaF', 'TestDaF', 'Test Deutsch als Fremdsprache', 20.00, 16.00, 24, TRUE);

-- Insert service categories
INSERT INTO service_categories (name, slug, description, icon, display_order, is_active) VALUES
('Visa Processing', 'visa-processing', 'Complete visa application and processing services', 'passport', 1, TRUE),
('Document Attestation', 'document-attestation', 'Embassy and MOFA document attestation services', 'certificate', 2, TRUE),
('Translation Services', 'translation', 'Professional document translation services', 'language', 3, TRUE),
('Flight Booking', 'flight-booking', 'International flight booking assistance', 'plane', 4, TRUE),
('Hotel Booking', 'hotel-booking', 'Hotel reservation services worldwide', 'hotel', 5, TRUE),
('Travel Insurance', 'travel-insurance', 'Comprehensive travel insurance packages', 'shield', 6, TRUE),
('Job Assistance', 'job-assistance', 'Overseas job search and placement', 'briefcase', 7, TRUE),
('Student Visa', 'student-visa', 'Study abroad visa and admission services', 'graduation-cap', 8, TRUE);

-- Insert agency types
INSERT INTO agency_types (name, slug, description, is_active) VALUES
('Travel Agency', 'travel-agency', 'General travel and tour agencies', TRUE),
('Visa Consultancy', 'visa-consultancy', 'Specialized visa processing consultancy', TRUE),
('Recruiting Agency', 'recruiting-agency', 'Overseas recruitment and manpower agencies', TRUE),
('Education Consultancy', 'education-consultancy', 'Study abroad and education consultancy', TRUE),
('Attestation Service', 'attestation-service', 'Document attestation service providers', TRUE),
('Translation Service', 'translation-service', 'Document translation service providers', TRUE);

-- Insert degree levels
INSERT INTO degrees (name, level, display_order, is_active) VALUES
('SSC', 'Secondary', 1, TRUE),
('HSC', 'Higher Secondary', 2, TRUE),
('Diploma', 'Diploma', 3, TRUE),
('Bachelor', 'Undergraduate', 4, TRUE),
('Honours', 'Undergraduate', 5, TRUE),
('Masters', 'Postgraduate', 6, TRUE),
('MBA', 'Postgraduate', 7, TRUE),
('PhD', 'Doctoral', 8, TRUE);

-- Insert institution types
INSERT INTO institution_types (name, description, is_active) VALUES
('University', 'Public and private universities', TRUE),
('College', 'Degree and honors colleges', TRUE),
('School', 'Primary and secondary schools', TRUE),
('Technical Institute', 'Technical and vocational institutes', TRUE),
('Madrasa', 'Islamic educational institutions', TRUE),
('Training Center', 'Professional training centers', TRUE);

-- Insert document types
INSERT INTO document_types (name, code, category, description, is_required, is_active) VALUES
('Passport', 'PASSPORT', 'Identity', 'Valid passport', TRUE, TRUE),
('National ID Card', 'NID', 'Identity', 'Bangladesh National ID Card', TRUE, TRUE),
('Birth Certificate', 'BIRTH_CERT', 'Identity', 'Official birth certificate', FALSE, TRUE),
('Educational Certificate', 'EDU_CERT', 'Education', 'Degree/diploma certificates', FALSE, TRUE),
('Experience Certificate', 'EXP_CERT', 'Employment', 'Work experience letters', FALSE, TRUE),
('Bank Statement', 'BANK_STMT', 'Financial', 'Bank account statements', FALSE, TRUE),
('Photo', 'PHOTO', 'Identity', 'Passport size photographs', TRUE, TRUE),
('Police Clearance', 'POLICE_CLEAR', 'Background', 'Police clearance certificate', FALSE, TRUE),
('Medical Certificate', 'MEDICAL', 'Health', 'Medical fitness certificate', FALSE, TRUE),
('Marriage Certificate', 'MARRIAGE', 'Family', 'Marriage registration certificate', FALSE, TRUE);

-- Insert essential system settings
INSERT INTO settings (`key`, value, type, group_name, description, is_public) VALUES
('site_name', 'BideshGomon', 'string', 'general', 'Website name', TRUE),
('site_tagline', 'Your Trusted Migration Partner', 'string', 'general', 'Website tagline', TRUE),
('default_currency', 'BDT', 'string', 'general', 'Default currency code', TRUE),
('default_language', 'bn', 'string', 'general', 'Default language code', TRUE),
('referral_reward_amount', '500', 'number', 'referral', 'Referral reward amount in BDT', FALSE),
('min_withdrawal_amount', '1000', 'number', 'wallet', 'Minimum withdrawal amount', FALSE),
('support_email', 'support@bideshgomon.com', 'string', 'contact', 'Support email address', TRUE),
('support_phone', '+880-1234-567890', 'string', 'contact', 'Support phone number', TRUE);
