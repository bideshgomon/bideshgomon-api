/**
 * Profile Data Constants
 * Predefined data for dropdowns and selections across profile sections
 */

// Bangladesh divisions
export const BANGLADESH_DIVISIONS = [
    'Dhaka',
    'Chittagong',
    'Rajshahi',
    'Khulna',
    'Barisal',
    'Sylhet',
    'Rangpur',
    'Mymensingh',
];

// Bangladesh districts by division
export const BANGLADESH_DISTRICTS = {
    Dhaka: ['Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Kishoreganj', 'Madaripur', 'Manikganj', 'Munshiganj', 'Narayanganj', 'Narsingdi', 'Rajbari', 'Shariatpur', 'Tangail'],
    Chittagong: ['Chittagong', 'Bandarban', 'Brahmanbaria', 'Chandpur', 'Comilla', 'Cox\'s Bazar', 'Feni', 'Khagrachari', 'Lakshmipur', 'Noakhali', 'Rangamati'],
    Rajshahi: ['Rajshahi', 'Bogra', 'Joypurhat', 'Naogaon', 'Natore', 'Chapainawabganj', 'Pabna', 'Sirajganj'],
    Khulna: ['Khulna', 'Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah', 'Kushtia', 'Magura', 'Meherpur', 'Narail', 'Satkhira'],
    Barisal: ['Barisal', 'Barguna', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur'],
    Sylhet: ['Sylhet', 'Habiganj', 'Moulvibazar', 'Sunamganj'],
    Rangpur: ['Rangpur', 'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh', 'Thakurgaon'],
    Mymensingh: ['Mymensingh', 'Jamalpur', 'Netrokona', 'Sherpur'],
};

// Major cities in Bangladesh
export const BANGLADESH_CITIES = [
    'Dhaka',
    'Chittagong',
    'Sylhet',
    'Rajshahi',
    'Khulna',
    'Barisal',
    'Rangpur',
    'Mymensingh',
    'Comilla',
    'Gazipur',
    'Narayanganj',
    'Cox\'s Bazar',
    'Jessore',
    'Bogra',
    'Dinajpur',
];

// Countries list (comprehensive)
export const COUNTRIES = [
    // South Asia
    'Bangladesh', 'India', 'Pakistan', 'Sri Lanka', 'Nepal', 'Bhutan', 'Maldives', 'Afghanistan',
    
    // Middle East (Popular for Bangladeshi workers)
    'Saudi Arabia', 'United Arab Emirates', 'Qatar', 'Kuwait', 'Oman', 'Bahrain', 'Jordan', 'Lebanon', 'Iraq', 'Yemen',
    
    // Southeast Asia
    'Malaysia', 'Singapore', 'Thailand', 'Indonesia', 'Philippines', 'Vietnam', 'Myanmar', 'Cambodia', 'Brunei', 'Laos',
    
    // East Asia
    'Japan', 'South Korea', 'China', 'Hong Kong', 'Taiwan',
    
    // Europe (Popular study destinations)
    'United Kingdom', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Belgium', 'Switzerland', 'Austria', 'Sweden',
    'Norway', 'Denmark', 'Finland', 'Poland', 'Czech Republic', 'Greece', 'Portugal', 'Ireland', 'Hungary', 'Romania',
    
    // North America
    'United States', 'Canada', 'Mexico',
    
    // Oceania
    'Australia', 'New Zealand',
    
    // Others
    'Turkey', 'Russia', 'Brazil', 'Argentina', 'Chile', 'South Africa', 'Egypt', 'Morocco', 'Kenya', 'Nigeria',
];

// Nationalities (matching countries)
export const NATIONALITIES = [
    'Bangladeshi', 'Indian', 'Pakistani', 'Sri Lankan', 'Nepalese', 'Bhutanese', 'Maldivian', 'Afghan',
    'Saudi', 'Emirati', 'Qatari', 'Kuwaiti', 'Omani', 'Bahraini', 'Jordanian', 'Lebanese', 'Iraqi', 'Yemeni',
    'Malaysian', 'Singaporean', 'Thai', 'Indonesian', 'Filipino', 'Vietnamese', 'Burmese', 'Cambodian', 'Bruneian', 'Lao',
    'Japanese', 'Korean', 'Chinese', 'Hong Konger', 'Taiwanese',
    'British', 'German', 'French', 'Italian', 'Spanish', 'Dutch', 'Belgian', 'Swiss', 'Austrian', 'Swedish',
    'Norwegian', 'Danish', 'Finnish', 'Polish', 'Czech', 'Greek', 'Portuguese', 'Irish', 'Hungarian', 'Romanian',
    'American', 'Canadian', 'Mexican',
    'Australian', 'New Zealander',
    'Turkish', 'Russian', 'Brazilian', 'Argentine', 'Chilean', 'South African', 'Egyptian', 'Moroccan', 'Kenyan', 'Nigerian',
];

// Gender options
export const GENDER_OPTIONS = [
    'Male',
    'Female',
    'Other',
    'Prefer not to say',
];

// Marital status options
export const MARITAL_STATUS_OPTIONS = [
    'Single',
    'Married',
    'Divorced',
    'Widowed',
    'Separated',
];

// Blood groups
export const BLOOD_GROUPS = [
    'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-',
];

// Religion options
export const RELIGION_OPTIONS = [
    'Islam',
    'Hinduism',
    'Buddhism',
    'Christianity',
    'Other',
    'Prefer not to say',
];

// Education levels
export const EDUCATION_LEVELS = [
    'Primary School',
    'Secondary School (SSC)',
    'Higher Secondary (HSC)',
    'Bachelor\'s Degree',
    'Master\'s Degree',
    'PhD/Doctorate',
    'Diploma',
    'Certificate',
];

// Degree types
export const DEGREE_TYPES = [
    'Bachelor of Science (B.Sc.)',
    'Bachelor of Arts (B.A.)',
    'Bachelor of Business Administration (BBA)',
    'Bachelor of Computer Science (B.CS)',
    'Bachelor of Engineering (B.Eng)',
    'Master of Science (M.Sc.)',
    'Master of Arts (M.A.)',
    'Master of Business Administration (MBA)',
    'Master of Engineering (M.Eng)',
    'Doctor of Philosophy (Ph.D.)',
    'Diploma',
    'Certificate',
];

// Fields of study
export const FIELDS_OF_STUDY = [
    'Computer Science',
    'Business Administration',
    'Electrical Engineering',
    'Civil Engineering',
    'Mechanical Engineering',
    'Economics',
    'English Literature',
    'Mathematics',
    'Physics',
    'Chemistry',
    'Accounting',
    'Finance',
    'Marketing',
    'Management',
    'Software Engineering',
    'Data Science',
    'Information Technology',
    'Medicine',
    'Pharmacy',
    'Law',
    'Architecture',
    'Agricultural Science',
];

// Employment types
export const EMPLOYMENT_TYPES = [
    'full_time',
    'part_time',
    'contract',
    'freelance',
    'internship',
];

// Relationship types for family members
export const RELATIONSHIP_TYPES = [
    'Spouse',
    'Child',
    'Parent',
    'Sibling',
    'Grandparent',
    'Grandchild',
    'Uncle',
    'Aunt',
    'Cousin',
    'Other',
];

// Language proficiency levels
export const LANGUAGE_PROFICIENCY_LEVELS = [
    'Native',
    'Fluent',
    'Advanced',
    'Intermediate',
    'Basic',
    'Beginner',
];

// Common languages
export const COMMON_LANGUAGES = [
    'Bengali',
    'English',
    'Arabic',
    'Hindi',
    'Urdu',
    'Chinese (Mandarin)',
    'Spanish',
    'French',
    'German',
    'Japanese',
    'Korean',
    'Malay',
    'Thai',
];

// Language test types
export const LANGUAGE_TEST_TYPES = [
    'IELTS',
    'TOEFL',
    'PTE',
    'Duolingo English Test',
    'Cambridge English',
    'OET (Occupational English Test)',
    'TOEIC',
    'JLPT (Japanese)',
    'HSK (Chinese)',
    'DELF/DALF (French)',
    'TestDaF (German)',
    'DELE (Spanish)',
    'TOPIK (Korean)',
    'Other',
];

// Income sources
export const INCOME_SOURCES = [
    'Salary',
    'Business',
    'Investment',
    'Rental Income',
    'Pension',
    'Freelance',
    'Family Support',
    'Other',
];

// Asset types
export const ASSET_TYPES = [
    'Real Estate',
    'Vehicle',
    'Bank Account',
    'Investment',
    'Jewelry',
    'Business',
    'Other',
];

// Popular work destinations for Bangladeshis
export const WORK_DESTINATIONS = [
    'Saudi Arabia',
    'United Arab Emirates',
    'Qatar',
    'Kuwait',
    'Oman',
    'Bahrain',
    'Malaysia',
    'Singapore',
];

// Popular study destinations for Bangladeshis
export const STUDY_DESTINATIONS = [
    'United States',
    'United Kingdom',
    'Canada',
    'Australia',
    'Germany',
    'Malaysia',
    'Japan',
    'South Korea',
];
