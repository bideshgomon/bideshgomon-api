<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelRoom;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = [
            // Dhaka Hotels
            [
                'name' => 'Radisson Blu Dhaka Water Garden',
                'description' => 'Located in the heart of Dhaka, Radisson Blu offers luxurious accommodations with stunning views of the water garden. Features world-class dining and modern amenities.',
                'city' => 'Dhaka',
                'area' => 'Airport Road',
                'address' => 'Airport Road, Dhaka 1229',
                'latitude' => 23.8103,
                'longitude' => 90.4125,
                'phone' => '+880-2-8836010',
                'email' => 'info.dhaka@radissonblu.com',
                'website' => 'https://www.radissonhotels.com',
                'star_rating' => 5,
                'property_type' => 'hotel',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'parking', 'spa', 'room_service', 'concierge', 'airport_shuttle'],
                'featured_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800',
                    'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800',
                ],
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in. After that, cancellation fee applies.',
                'house_rules' => 'Check-in: 2:00 PM | Check-out: 12:00 PM | No smoking | Pets not allowed',
                'is_featured' => true,
                'rooms' => [
                    [
                        'room_type' => 'Superior Room',
                        'description' => 'Spacious room with modern amenities and city view',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 30,
                        'size_sqm' => 28,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 12000,
                        'amenities' => ['tv', 'minibar', 'safe', 'coffee_maker', 'work_desk'],
                        'images' => ['https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800'],
                    ],
                    [
                        'room_type' => 'Deluxe Suite',
                        'description' => 'Luxury suite with separate living area and premium amenities',
                        'max_adults' => 3,
                        'max_children' => 2,
                        'total_rooms' => 15,
                        'size_sqm' => 45,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 18000,
                        'amenities' => ['tv', 'minibar', 'safe', 'coffee_maker', 'work_desk', 'balcony', 'living_area'],
                        'images' => ['https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800'],
                    ],
                ],
            ],
            [
                'name' => 'Pan Pacific Sonargaon',
                'description' => 'A historic landmark hotel offering world-class hospitality in the diplomatic zone of Dhaka.',
                'city' => 'Dhaka',
                'area' => 'Karwan Bazar',
                'address' => '107 Kazi Nazrul Islam Avenue, Dhaka 1215',
                'latitude' => 23.7504,
                'longitude' => 90.3957,
                'phone' => '+880-2-8159001',
                'email' => 'info.dhaka@panpacific.com',
                'star_rating' => 5,
                'property_type' => 'hotel',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'parking', 'spa', 'business_center', 'bar'],
                'featured_image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800',
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
                ],
                'is_featured' => true,
                'rooms' => [
                    [
                        'room_type' => 'Deluxe Room',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 40,
                        'size_sqm' => 30,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 15000,
                        'amenities' => ['tv', 'minibar', 'safe', 'work_desk'],
                    ],
                ],
            ],

            // Cox's Bazar Hotels
            [
                'name' => 'Long Beach Hotel Cox\'s Bazar',
                'description' => 'Beachfront luxury resort with direct access to the world\'s longest natural beach.',
                'city' => 'Cox\'s Bazar',
                'area' => 'Kolatoli Beach',
                'address' => 'Kolatoli Road, Cox\'s Bazar 4700',
                'latitude' => 21.4272,
                'longitude' => 92.0058,
                'phone' => '+880-341-64506',
                'email' => 'reservations@longbeachcoxsbazar.com',
                'star_rating' => 4,
                'property_type' => 'resort',
                'amenities' => ['wifi', 'pool', 'restaurant', 'parking', 'beach_access', 'spa', 'bar'],
                'featured_image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
                    'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800',
                ],
                'is_featured' => true,
                'rooms' => [
                    [
                        'room_type' => 'Sea View Room',
                        'description' => 'Stunning ocean views with private balcony',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 50,
                        'size_sqm' => 32,
                        'bed_type' => 'Queen',
                        'bed_count' => 2,
                        'price_per_night' => 8000,
                        'weekend_price' => 10000,
                        'amenities' => ['tv', 'balcony', 'sea_view', 'minibar'],
                        'discount_percentage' => 15,
                        'discount_valid_from' => now()->subDays(10),
                        'discount_valid_until' => now()->addDays(20),
                    ],
                    [
                        'room_type' => 'Beach Front Suite',
                        'description' => 'Luxury suite right on the beach',
                        'max_adults' => 4,
                        'max_children' => 2,
                        'total_rooms' => 20,
                        'size_sqm' => 55,
                        'bed_type' => 'King',
                        'bed_count' => 2,
                        'price_per_night' => 14000,
                        'weekend_price' => 17000,
                        'amenities' => ['tv', 'balcony', 'sea_view', 'minibar', 'living_area', 'jacuzzi'],
                    ],
                ],
            ],
            [
                'name' => 'Royal Tulip Sea Pearl Beach Resort',
                'description' => 'Premium beach resort with world-class facilities and breathtaking sea views.',
                'city' => 'Cox\'s Bazar',
                'area' => 'Inani Beach',
                'address' => 'Marine Drive, Inani, Cox\'s Bazar',
                'latitude' => 21.2936,
                'longitude' => 92.0676,
                'phone' => '+880-341-51234',
                'email' => 'info@seapearlbd.com',
                'star_rating' => 5,
                'property_type' => 'resort',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'parking', 'beach_access', 'spa', 'water_sports', 'kids_club'],
                'featured_image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
                ],
                'is_featured' => true,
                'rooms' => [
                    [
                        'room_type' => 'Premium Ocean View',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 60,
                        'size_sqm' => 35,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 12000,
                        'weekend_price' => 15000,
                        'amenities' => ['tv', 'balcony', 'sea_view', 'minibar', 'coffee_maker'],
                    ],
                ],
            ],

            // Sylhet Hotels
            [
                'name' => 'Hotel Grand Sultan Tea Resort',
                'description' => 'Nestled in tea gardens with panoramic views of Sylhet hills.',
                'city' => 'Sylhet',
                'area' => 'Chandghat',
                'address' => 'Chandghat, Sylhet 3100',
                'latitude' => 24.8949,
                'longitude' => 91.8687,
                'phone' => '+880-821-720000',
                'email' => 'info@grandsultan.com',
                'star_rating' => 4,
                'property_type' => 'resort',
                'amenities' => ['wifi', 'restaurant', 'parking', 'garden', 'outdoor_seating'],
                'featured_image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800',
                ],
                'rooms' => [
                    [
                        'room_type' => 'Hill View Room',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 35,
                        'size_sqm' => 28,
                        'bed_type' => 'Queen',
                        'bed_count' => 1,
                        'price_per_night' => 6000,
                        'amenities' => ['tv', 'balcony', 'mountain_view'],
                    ],
                ],
            ],

            // Chittagong Hotels
            [
                'name' => 'Peninsula Chittagong',
                'description' => 'Modern business hotel in the port city with excellent amenities.',
                'city' => 'Chittagong',
                'area' => 'Agrabad',
                'address' => 'O R Nizam Road, Agrabad, Chittagong',
                'latitude' => 22.3382,
                'longitude' => 91.8318,
                'phone' => '+880-31-2523031',
                'email' => 'reservations@peninsulachittagong.com',
                'star_rating' => 4,
                'property_type' => 'hotel',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'parking', 'business_center'],
                'featured_image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
                ],
                'rooms' => [
                    [
                        'room_type' => 'Executive Room',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 45,
                        'size_sqm' => 30,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 7000,
                        'amenities' => ['tv', 'work_desk', 'minibar'],
                    ],
                ],
            ],

            // International - Dubai
            [
                'name' => 'Burj Al Arab Jumeirah',
                'description' => 'The world\'s most luxurious hotel with unparalleled service and opulence.',
                'city' => 'Dubai',
                'area' => 'Jumeirah Beach',
                'address' => 'Jumeirah St, Umm Suqeim 3, Dubai, UAE',
                'country' => 'UAE',
                'latitude' => 25.1413,
                'longitude' => 55.1853,
                'phone' => '+971-4-3017777',
                'email' => 'info@burjalarab.com',
                'website' => 'https://www.jumeirah.com',
                'star_rating' => 5,
                'property_type' => 'hotel',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'parking', 'spa', 'beach_access', 'butler_service', 'helipad'],
                'featured_image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800',
                ],
                'is_featured' => true,
                'rooms' => [
                    [
                        'room_type' => 'Deluxe King Suite',
                        'description' => 'Two-floor suite with Arabian elegance',
                        'max_adults' => 3,
                        'max_children' => 2,
                        'total_rooms' => 28,
                        'size_sqm' => 170,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 150000,
                        'currency' => 'BDT',
                        'amenities' => ['tv', 'living_area', 'dining_area', 'sea_view', 'butler_service', 'jacuzzi'],
                    ],
                ],
            ],

            // International - Bangkok
            [
                'name' => 'Mandarin Oriental Bangkok',
                'description' => 'Historic riverside hotel combining Thai heritage with contemporary luxury.',
                'city' => 'Bangkok',
                'area' => 'Riverside',
                'address' => '48 Oriental Avenue, Bangkok 10500, Thailand',
                'country' => 'Thailand',
                'latitude' => 13.7243,
                'longitude' => 100.5126,
                'phone' => '+66-2-6599000',
                'email' => 'mobkk-reservations@mohg.com',
                'star_rating' => 5,
                'property_type' => 'hotel',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'spa', 'river_view', 'cooking_school'],
                'featured_image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800',
                ],
                'is_featured' => true,
                'rooms' => [
                    [
                        'room_type' => 'Deluxe Room River View',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 50,
                        'size_sqm' => 42,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 35000,
                        'amenities' => ['tv', 'river_view', 'minibar', 'balcony'],
                    ],
                ],
            ],

            // International - Singapore
            [
                'name' => 'Marina Bay Sands',
                'description' => 'Iconic luxury resort with rooftop infinity pool and stunning city views.',
                'city' => 'Singapore',
                'area' => 'Marina Bay',
                'address' => '10 Bayfront Ave, Singapore 018956',
                'country' => 'Singapore',
                'latitude' => 1.2834,
                'longitude' => 103.8607,
                'phone' => '+65-6688-8868',
                'email' => 'reservations@marinabaysands.com',
                'star_rating' => 5,
                'property_type' => 'resort',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'parking', 'spa', 'casino', 'shopping_mall', 'rooftop_bar'],
                'featured_image' => 'https://images.unsplash.com/photo-1595567882616-7f8ccb23a814?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1595567882616-7f8ccb23a814?w=800',
                ],
                'is_featured' => true,
                'rooms' => [
                    [
                        'room_type' => 'Premier Room City View',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 80,
                        'size_sqm' => 38,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 55000,
                        'amenities' => ['tv', 'city_view', 'minibar', 'work_desk'],
                    ],
                ],
            ],

            // International - Kuala Lumpur
            [
                'name' => 'Petronas Tower Hotel KL',
                'description' => 'Modern luxury hotel with views of the iconic Petronas Twin Towers.',
                'city' => 'Kuala Lumpur',
                'area' => 'KLCC',
                'address' => 'KLCC, Kuala Lumpur, Malaysia',
                'country' => 'Malaysia',
                'latitude' => 3.1579,
                'longitude' => 101.7116,
                'phone' => '+60-3-2382-8888',
                'email' => 'reservations@petronastower.com',
                'star_rating' => 5,
                'property_type' => 'hotel',
                'amenities' => ['wifi', 'pool', 'gym', 'restaurant', 'parking', 'business_center'],
                'featured_image' => 'https://images.unsplash.com/photo-1596701062351-8c2c14d1fdd0?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1596701062351-8c2c14d1fdd0?w=800',
                ],
                'rooms' => [
                    [
                        'room_type' => 'Executive Room',
                        'max_adults' => 2,
                        'max_children' => 1,
                        'total_rooms' => 60,
                        'size_sqm' => 32,
                        'bed_type' => 'King',
                        'bed_count' => 1,
                        'price_per_night' => 28000,
                        'amenities' => ['tv', 'city_view', 'work_desk', 'minibar'],
                    ],
                ],
            ],
        ];

        foreach ($hotels as $hotelData) {
            $rooms = $hotelData['rooms'];
            unset($hotelData['rooms']);

            // Create hotel
            $hotel = Hotel::create($hotelData);

            // Set lowest price from rooms
            $lowestPrice = collect($rooms)->min('price_per_night');
            $hotel->price_from = $lowestPrice;
            $hotel->save();

            // Create rooms for this hotel
            foreach ($rooms as $roomData) {
                $roomData['hotel_id'] = $hotel->id;
                HotelRoom::create($roomData);
            }
        }

        $this->command->info('✅ Created ' . Hotel::count() . ' hotels with ' . HotelRoom::count() . ' room types');
    }
}
