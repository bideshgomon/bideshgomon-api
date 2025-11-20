<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Technical Skills
            ['name' => 'PHP', 'category' => 'Technical', 'description' => 'Server-side scripting language'],
            ['name' => 'JavaScript', 'category' => 'Technical', 'description' => 'Client-side programming language'],
            ['name' => 'Python', 'category' => 'Technical', 'description' => 'General-purpose programming language'],
            ['name' => 'Java', 'category' => 'Technical', 'description' => 'Object-oriented programming language'],
            ['name' => 'C++', 'category' => 'Technical', 'description' => 'High-performance programming language'],
            ['name' => 'React', 'category' => 'Technical', 'description' => 'JavaScript library for building UIs'],
            ['name' => 'Vue.js', 'category' => 'Technical', 'description' => 'Progressive JavaScript framework'],
            ['name' => 'Laravel', 'category' => 'Technical', 'description' => 'PHP web application framework'],
            ['name' => 'Node.js', 'category' => 'Technical', 'description' => 'JavaScript runtime environment'],
            ['name' => 'SQL', 'category' => 'Technical', 'description' => 'Database query language'],
            ['name' => 'MySQL', 'category' => 'Technical', 'description' => 'Relational database management system'],
            ['name' => 'MongoDB', 'category' => 'Technical', 'description' => 'NoSQL document database'],
            ['name' => 'Git', 'category' => 'Technical', 'description' => 'Version control system'],
            ['name' => 'Docker', 'category' => 'Technical', 'description' => 'Containerization platform'],
            ['name' => 'AWS', 'category' => 'Technical', 'description' => 'Amazon Web Services cloud platform'],
            ['name' => 'Azure', 'category' => 'Technical', 'description' => 'Microsoft cloud computing platform'],
            ['name' => 'Linux', 'category' => 'Technical', 'description' => 'Unix-like operating system'],
            ['name' => 'REST API', 'category' => 'Technical', 'description' => 'API design architecture'],
            ['name' => 'GraphQL', 'category' => 'Technical', 'description' => 'Query language for APIs'],
            ['name' => 'HTML/CSS', 'category' => 'Technical', 'description' => 'Web markup and styling'],
            ['name' => 'TypeScript', 'category' => 'Technical', 'description' => 'Typed superset of JavaScript'],
            ['name' => 'Tailwind CSS', 'category' => 'Technical', 'description' => 'Utility-first CSS framework'],
            
            // Design & Creative
            ['name' => 'Adobe Photoshop', 'category' => 'Design', 'description' => 'Image editing software'],
            ['name' => 'Adobe Illustrator', 'category' => 'Design', 'description' => 'Vector graphics editor'],
            ['name' => 'Figma', 'category' => 'Design', 'description' => 'UI/UX design tool'],
            ['name' => 'Sketch', 'category' => 'Design', 'description' => 'Digital design platform'],
            ['name' => 'UI/UX Design', 'category' => 'Design', 'description' => 'User interface and experience design'],
            ['name' => 'Graphic Design', 'category' => 'Design', 'description' => 'Visual communication design'],
            ['name' => 'Video Editing', 'category' => 'Design', 'description' => 'Post-production video work'],
            
            // Business & Management
            ['name' => 'Project Management', 'category' => 'Business', 'description' => 'Planning and organizing projects'],
            ['name' => 'Business Analysis', 'category' => 'Business', 'description' => 'Analyzing business needs'],
            ['name' => 'Financial Analysis', 'category' => 'Business', 'description' => 'Analyzing financial data'],
            ['name' => 'Strategic Planning', 'category' => 'Business', 'description' => 'Long-term business planning'],
            ['name' => 'Marketing', 'category' => 'Business', 'description' => 'Promoting products/services'],
            ['name' => 'Sales', 'category' => 'Business', 'description' => 'Selling products/services'],
            ['name' => 'Business Customer Service', 'category' => 'Business', 'description' => 'Supporting business customers'],
            ['name' => 'Accounting', 'category' => 'Business', 'description' => 'Financial record keeping'],
            ['name' => 'HR Management', 'category' => 'Business', 'description' => 'Human resources management'],
            
            // Soft Skills
            ['name' => 'Communication', 'category' => 'Soft Skills', 'description' => 'Effective information exchange'],
            ['name' => 'Leadership', 'category' => 'Soft Skills', 'description' => 'Guiding and motivating teams'],
            ['name' => 'Teamwork', 'category' => 'Soft Skills', 'description' => 'Collaborative work ability'],
            ['name' => 'Problem Solving', 'category' => 'Soft Skills', 'description' => 'Finding solutions to challenges'],
            ['name' => 'Critical Thinking', 'category' => 'Soft Skills', 'description' => 'Analytical reasoning'],
            ['name' => 'Time Management', 'category' => 'Soft Skills', 'description' => 'Efficient use of time'],
            ['name' => 'Adaptability', 'category' => 'Soft Skills', 'description' => 'Adjusting to changes'],
            ['name' => 'Creativity', 'category' => 'Soft Skills', 'description' => 'Innovative thinking'],
            ['name' => 'Attention to Detail', 'category' => 'Soft Skills', 'description' => 'Careful and thorough work'],
            
            // Trade & Labor Skills
            ['name' => 'Electrical Work', 'category' => 'Trade', 'description' => 'Electrical installation and repair'],
            ['name' => 'Plumbing', 'category' => 'Trade', 'description' => 'Pipe installation and repair'],
            ['name' => 'Carpentry', 'category' => 'Trade', 'description' => 'Woodworking and construction'],
            ['name' => 'Welding', 'category' => 'Trade', 'description' => 'Metal joining techniques'],
            ['name' => 'Automotive Repair', 'category' => 'Trade', 'description' => 'Vehicle maintenance and repair'],
            ['name' => 'Construction', 'category' => 'Trade', 'description' => 'Building and construction work'],
            ['name' => 'HVAC', 'category' => 'Trade', 'description' => 'Heating, ventilation, and air conditioning'],
            ['name' => 'Painting', 'category' => 'Trade', 'description' => 'Surface coating application'],
            ['name' => 'Machining', 'category' => 'Trade', 'description' => 'Operating machine tools'],
            
            // Healthcare
            ['name' => 'Nursing', 'category' => 'Healthcare', 'description' => 'Patient care services'],
            ['name' => 'First Aid', 'category' => 'Healthcare', 'description' => 'Emergency medical assistance'],
            ['name' => 'Patient Care', 'category' => 'Healthcare', 'description' => 'Healthcare support'],
            ['name' => 'Medical Terminology', 'category' => 'Healthcare', 'description' => 'Healthcare language'],
            
            // Hospitality & Service
            ['name' => 'Cooking', 'category' => 'Hospitality', 'description' => 'Food preparation'],
            ['name' => 'Bartending', 'category' => 'Hospitality', 'description' => 'Beverage preparation and service'],
            ['name' => 'Customer Service', 'category' => 'Hospitality', 'description' => 'Guest services'],
            ['name' => 'Hotel Management', 'category' => 'Hospitality', 'description' => 'Hospitality operations'],
            ['name' => 'Food Service', 'category' => 'Hospitality', 'description' => 'Restaurant service'],
            
            // Other Professional Skills
            ['name' => 'Data Entry', 'category' => 'Administrative', 'description' => 'Computer data input'],
            ['name' => 'Microsoft Office', 'category' => 'Administrative', 'description' => 'Office productivity suite'],
            ['name' => 'Microsoft Excel', 'category' => 'Administrative', 'description' => 'Spreadsheet software'],
            ['name' => 'Data Analysis', 'category' => 'Analytical', 'description' => 'Interpreting data patterns'],
            ['name' => 'Report Writing', 'category' => 'Administrative', 'description' => 'Professional documentation'],
            ['name' => 'Presentation Skills', 'category' => 'Communication', 'description' => 'Public speaking'],
            ['name' => 'Teaching', 'category' => 'Education', 'description' => 'Instructional expertise'],
            
            // MIDDLE EAST IMMIGRANT SKILLS (High Demand in Gulf Countries)
            // Construction & Building
            ['name' => 'Masonry', 'category' => 'Construction', 'description' => 'Bricklaying and stonework for building projects'],
            ['name' => 'Steel Fixing', 'category' => 'Construction', 'description' => 'Reinforcement bar installation for concrete structures'],
            ['name' => 'Scaffolding', 'category' => 'Construction', 'description' => 'Temporary structure erection for construction sites'],
            ['name' => 'Tile & Marble Fitting', 'category' => 'Construction', 'description' => 'Floor and wall finishing work'],
            ['name' => 'Gypsum Work', 'category' => 'Construction', 'description' => 'False ceiling and partition installation'],
            ['name' => 'Building Maintenance', 'category' => 'Construction', 'description' => 'General building upkeep and repairs'],
            
            // Mechanical & Technical
            ['name' => 'AC Technician', 'category' => 'Technical Trade', 'description' => 'Air conditioning installation and repair'],
            ['name' => 'Refrigeration', 'category' => 'Technical Trade', 'description' => 'Cooling system maintenance'],
            ['name' => 'MEP Work', 'category' => 'Technical Trade', 'description' => 'Mechanical, Electrical, Plumbing systems'],
            ['name' => 'Generator Operator', 'category' => 'Technical Trade', 'description' => 'Power generation equipment operation'],
            ['name' => 'Forklift Operation', 'category' => 'Technical Trade', 'description' => 'Material handling equipment operation'],
            ['name' => 'Crane Operation', 'category' => 'Technical Trade', 'description' => 'Heavy lifting equipment operation'],
            ['name' => 'Excavator Operation', 'category' => 'Technical Trade', 'description' => 'Earth moving equipment operation'],
            
            // Hospitality & Service (Gulf specific)
            ['name' => 'Hotel Housekeeping', 'category' => 'Hospitality', 'description' => 'Room cleaning and maintenance in hotels'],
            ['name' => 'Room Service', 'category' => 'Hospitality', 'description' => 'In-room food and beverage service'],
            ['name' => 'Front Desk Operations', 'category' => 'Hospitality', 'description' => 'Hotel reception and guest services'],
            ['name' => 'Restaurant Server', 'category' => 'Hospitality', 'description' => 'Food and beverage service in restaurants'],
            ['name' => 'Kitchen Helper', 'category' => 'Hospitality', 'description' => 'Food preparation assistance'],
            ['name' => 'Barista', 'category' => 'Hospitality', 'description' => 'Coffee preparation specialist'],
            ['name' => 'Laundry Worker', 'category' => 'Hospitality', 'description' => 'Commercial laundry operations'],
            
            // Domestic Work (Very common for Gulf employment)
            ['name' => 'Domestic Helper', 'category' => 'Domestic Services', 'description' => 'Household cleaning and maintenance'],
            ['name' => 'Nanny/Childcare', 'category' => 'Domestic Services', 'description' => 'Professional childcare services'],
            ['name' => 'Elderly Care', 'category' => 'Domestic Services', 'description' => 'Senior citizen care and assistance'],
            ['name' => 'Cook (Home)', 'category' => 'Domestic Services', 'description' => 'Private household cooking'],
            ['name' => 'Driver (Personal)', 'category' => 'Domestic Services', 'description' => 'Private vehicle driving services'],
            
            // Retail & Sales (Gulf Markets)
            ['name' => 'Retail Cashier', 'category' => 'Retail', 'description' => 'Point of sale operations'],
            ['name' => 'Storekeeper', 'category' => 'Retail', 'description' => 'Inventory management'],
            ['name' => 'Salesperson', 'category' => 'Retail', 'description' => 'Product sales and customer service'],
            ['name' => 'Merchandiser', 'category' => 'Retail', 'description' => 'Product display and arrangement'],
            
            // Security & Safety
            ['name' => 'Security Guard', 'category' => 'Security', 'description' => 'Property and personnel protection'],
            ['name' => 'Safety Officer', 'category' => 'Security', 'description' => 'Workplace safety management'],
            ['name' => 'Fire Safety', 'category' => 'Security', 'description' => 'Fire prevention and response'],
            
            // Oil & Gas (Gulf Specific)
            ['name' => 'Oil Rig Worker', 'category' => 'Oil & Gas', 'description' => 'Petroleum extraction operations'],
            ['name' => 'Pipeline Technician', 'category' => 'Oil & Gas', 'description' => 'Pipeline installation and maintenance'],
            ['name' => 'Petrochemical Operations', 'category' => 'Oil & Gas', 'description' => 'Chemical processing in oil industry'],
            
            // Beauty & Wellness (Gulf Market)
            ['name' => 'Beautician', 'category' => 'Beauty & Wellness', 'description' => 'Beauty treatments and services'],
            ['name' => 'Hairdresser', 'category' => 'Beauty & Wellness', 'description' => 'Hair styling and cutting'],
            ['name' => 'Spa Therapist', 'category' => 'Beauty & Wellness', 'description' => 'Massage and spa treatments'],
            ['name' => 'Nail Technician', 'category' => 'Beauty & Wellness', 'description' => 'Manicure and pedicure services'],
            
            // Tailoring & Garments
            ['name' => 'Tailor', 'category' => 'Garments', 'description' => 'Clothing alteration and creation'],
            ['name' => 'Garment Worker', 'category' => 'Garments', 'description' => 'Factory garment production'],
            ['name' => 'Embroidery', 'category' => 'Garments', 'description' => 'Decorative needlework'],
            
            // Agriculture (Farm Work in Gulf)
            ['name' => 'Farm Worker', 'category' => 'Agriculture', 'description' => 'Agricultural labor and crop management'],
            ['name' => 'Gardening', 'category' => 'Agriculture', 'description' => 'Landscape maintenance and plant care'],
            ['name' => 'Livestock Handling', 'category' => 'Agriculture', 'description' => 'Animal husbandry and care'],
            
            // Logistics & Warehouse
            ['name' => 'Warehouse Worker', 'category' => 'Logistics', 'description' => 'Storage and inventory management'],
            ['name' => 'Packing', 'category' => 'Logistics', 'description' => 'Product packaging and preparation'],
            ['name' => 'Loading/Unloading', 'category' => 'Logistics', 'description' => 'Cargo handling operations'],
            ['name' => 'Delivery Driver', 'category' => 'Logistics', 'description' => 'Goods transportation and delivery'],
            
            // Cleaning & Sanitation
            ['name' => 'Cleaner', 'category' => 'Cleaning Services', 'description' => 'Commercial and residential cleaning'],
            ['name' => 'Janitorial Services', 'category' => 'Cleaning Services', 'description' => 'Building maintenance and cleaning'],
            ['name' => 'Window Cleaning', 'category' => 'Cleaning Services', 'description' => 'High-rise and commercial window cleaning'],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(
                ['slug' => Str::slug($skill['name'])],
                [
                    'name' => $skill['name'],
                    'category' => $skill['category'],
                    'description' => $skill['description'],
                ]
            );
        }
    }
}
