<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill; // Import the Skill model
use Illuminate\Support\Str; // Import the String helper

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ⚠️ Optional: Uncomment if you want to reset before seeding
        // Skill::truncate();

        $skills = [
            // --- Tech & Office Skills ---
            'PHP', 'Laravel', 'Vue.js', 'React', 'JavaScript', 'HTML', 'CSS', 'Tailwind CSS',
            'MySQL', 'PostgreSQL', 'Database Design', 'API Development', 'REST APIs',
            'Project Management', 'Agile Methodologies', 'Communication', 'Teamwork',
            'Problem Solving', 'Critical Thinking', 'Customer Service', 'Sales', 'Marketing',
            'Data Analysis', 'Microsoft Excel', 'Graphic Design', 'UI/UX Design',
            'Content Writing', 'SEO', 'Digital Marketing', 'Social Media Management',
            'Consulting', 'Visa Processing', 'Immigration Law', 'Student Counseling',

            // --- Skills for Bangladeshi Migrant Workers ---
            'Mason', 'Tile Fitter', 'Plumber', 'Electrician', 'Welder', 'Steel Fixer', 'Pipe Fitter',
            'Carpenter', 'Scaffolder', 'Painter', 'AC Technician', 'HVAC Technician',
            'General Laborer', 'Construction Helper', 'Civil Foreman', 'Surveyor',
            'Crane Operator', 'Forklift Operator', 'Heavy Vehicle Driver',
            'Sewing Machine Operator', 'Overlock Operator', 'Cutting Master', 'Quality Checker',
            'Embroidery Worker', 'Pattern Maker', 'Tailor', 'Iron Man', 'Packaging Worker',
            'Auto Mechanic', 'Diesel Mechanic', 'Auto Electrician', 'Motorbike Mechanic',
            'Hydraulic Technician', 'Mechanical Fitter', 'Machine Operator',
            'Chef', 'Cook', 'Assistant Cook', 'Waiter', 'Kitchen Helper', 'Barista',
            'Baker', 'Pastry Chef', 'Dishwasher', 'Hotel Housekeeping', 'Room Attendant',
            'Front Desk Officer', 'Restaurant Cashier', 'Receptionist',
            'Cleaner', 'Housekeeper', 'Janitor', 'Laundry Worker', 'Gardener',
            'Building Maintenance Worker', 'Waste Management Worker',
            'Light Vehicle Driver', 'Heavy Vehicle Driver', 'Truck Driver',
            'Delivery Driver', 'Bus Driver', 'Taxi Driver', 'Logistics Assistant',
            'Caregiver', 'Nanny', 'Babysitter', 'Domestic Worker', 'Elderly Care Assistant',
            'Home Nurse', 'Health Care Assistant',
            'Farm Worker', 'Livestock Caretaker', 'Dairy Worker', 'Poultry Farm Worker',
            'Fisherman', 'Gardener', 'Irrigation Technician', 'Agricultural Technician',
            'Ship Welder', 'Ship Painter', 'Deck Crew', 'Marine Mechanic',
            'Fishing Boat Worker', 'Dock Laborer',
            'Factory Worker', 'Production Operator', 'Machine Operator', 'Packing Worker',
            'Quality Inspector', 'Warehouse Worker', 'Assembly Line Worker',
            'Material Handler', 'Forklift Operator',
            'Security Guard', 'CCTV Operator', 'Firefighter', 'Safety Officer',
            'First Aid Responder', 'Rescue Team Member',
            'Basic English Communication', 'Arabic Language', 'Japanese Language (JLPT N4/N5)',
            'Korean Language (EPS TOPIK)', 'Malay Language', 'Driving Skills (International)',
            'Workplace Safety Awareness', 'Time Management', 'Adaptability',
            'Solar Panel Technician', 'Electrical Panel Assembler', 'Maintenance Technician',
            'Pipe Welding (TIG/MIG)', 'Plasterer', 'Roof Tiler', 'Shuttering Carpenter',
            'Data Entry Operator', 'Office Assistant', 'Admin Clerk', 'Call Center Agent',
            'Document Controller', 'HR Assistant', 'Travel Consultant',
            'Warehouse Assistant', 'Inventory Clerk', 'Freight Handler', 'Customs Assistant',
            'Hospital Cleaner', 'Pharmacy Assistant', 'Medical Support Staff',
            'Butcher', 'Fish Processor', 'Packing Assistant', 'Courier', 'Assembler',
            'Maintenance Electrician', 'Waterproofing Technician', 'Insulation Worker',
            'Rebar Worker', 'Tile Mason', 'Landscaper',

            // --- New Practical & Modern Skills ---
            'Barber', 'Beautician', 'Makeup Artist', 'Hairdresser', 'Salon Assistant',
            'Photographer', 'Video Editor', 'Cameraman', 'Content Creator',
            'Drone Operator', 'IT Support Technician', 'Computer Operator',
            'E-commerce Assistant', 'Online Sales Executive', 'Delivery Rider',
            'Mobile Phone Technician', 'Solar Installation Assistant',
            'Refrigeration Technician', 'Water Pump Technician', 'Cable Installer',
            'House Wiring Electrician', 'Panel Board Technician', 'CNC Machine Operator',
            'Lathe Machine Operator', 'Pipe Fabricator', 'Scaffold Supervisor',
            'Tiler', 'Plaster Mason', 'Road Construction Worker', 'Bridge Construction Laborer',
            'Oil & Gas Rig Worker', 'Refinery Operator', 'Petroleum Technician',
            'Shipyard Worker', 'Dock Operator', 'Marine Electrician',
            'Logistics Coordinator', 'Warehouse Supervisor', 'Procurement Assistant',
            'Cashier', 'Supermarket Attendant', 'Retail Salesperson', 'Storekeeper',
            'Food Packer', 'Cold Storage Worker', 'Meat Cutter',
            'Airport Loader', 'Baggage Handler', 'Ground Service Staff',
            'Aircraft Cleaner', 'Airport Security', 'Check-in Assistant',
            'Call Center Executive', 'Online Chat Support Agent',
            'Computer Typist', 'Transcriptionist', 'Office Boy',
            'Tea Boy', 'Messenger', 'Security Supervisor', 'Parking Attendant',
            'Lift Operator', 'Gatekeeper', 'Site Supervisor', 'Construction Estimator',
            'Civil Draftsman', 'Mechanical Draftsman', 'Electrical Draftsman',
            'Architectural Assistant', 'Quantity Surveyor Assistant',
            'AutoCAD Operator', 'GIS Technician', 'Building Painter',
            'Tile Grout Finisher', 'Epoxy Painter', 'Glass Installer',
            'Ceiling Installer', 'Drywall Worker', 'Plasterboard Installer',
            'Safety Assistant', 'Fire Watcher', 'Flagman', 'Traffic Marshall',
            'Road Maintenance Worker', 'Bridge Painter', 'Pipe Insulator',
            'Steam Pipe Fitter', 'Duct Fabricator', 'Duct Installer',
            'Scaffold Inspector', 'Rigger', 'Signalman',
            'Crane Rigger', 'Winch Operator', 'Boom Truck Operator',
            'Concrete Finisher', 'Concrete Pump Operator', 'Batching Plant Operator',
            'Stone Cutter', 'Marble Installer', 'Granite Polisher',
            'Wood Polisher', 'Cabinet Maker', 'Furniture Carpenter',
            'Spray Painter', 'Auto Body Painter', 'Panel Beater',
            'Tyre Technician', 'Battery Technician', 'AC Installer',
            'Refrigeration Helper', 'Machine Helper', 'Shop Helper',
            'Store Laborer', 'Packaging Helper', 'Kitchen Cleaner',
            'Industrial Cleaner', 'Window Cleaner', 'Airport Janitor',
            'Mall Cleaner', 'Hospital Attendant', 'Laundry Attendant',
            'Pressing Worker', 'Ironing Worker', 'Trolley Pusher',
            'Logistics Driver', 'Delivery Helper', 'Parcel Sorter',
            'Container Loader', 'Shipping Clerk', 'Port Laborer'
        ];

        // --- Insert or update each skill safely ---
        foreach ($skills as $skillName) {
            $slug = Str::slug($skillName);

            Skill::updateOrCreate(
                ['slug' => $slug],  // Check by slug (unique key)
                ['name' => $skillName] // Update or create name
            );
        }
    }
}
