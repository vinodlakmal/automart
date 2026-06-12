<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@ikman.test')->firstOrFail();
        $categories = Category::all()->keyBy('slug');
        $districts = District::with('cities')->get()->keyBy('slug');

        $ads = [
            // Real Estate
            [
                'category' => 'real-estate-houses-for-sale',
                'district' => 'colombo', 'city' => 'maharagama',
                'title' => '3 Bedroom House for Sale in Maharagama',
                'description' => 'Well-maintained 3-bedroom house with garden, 2 bathrooms, and car park in a quiet residential area. Close to schools and shopping centres. Title deed clear. Motivated seller.',
                'price' => 18500000, 'condition' => null, 'is_negotiable' => true, 'is_featured' => true,
                'contact_name' => 'Sunil Perera', 'contact_phone' => '0771234567',
            ],
            [
                'category' => 'real-estate-apartments',
                'district' => 'colombo', 'city' => 'colombo-1',
                'title' => 'Modern Studio Apartment for Rent – Colombo 7',
                'description' => 'Fully furnished studio apartment on the 8th floor with city views. Air conditioned, 24-hour security, gym and pool access. Utilities included. Minimum 6-month lease.',
                'price' => 75000, 'condition' => null, 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Kamani Fernando', 'contact_phone' => '0112345678',
            ],
            [
                'category' => 'real-estate-land',
                'district' => 'kandy', 'city' => 'peradeniya',
                'title' => 'Bare Land for Sale – 20 Perches, Peradeniya',
                'description' => '20-perch land in a prime location near Peradeniya University. Electricity and water available. Good road access. Ideal for house construction. Deeds clear.',
                'price' => 4200000, 'condition' => null, 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Bandara', 'contact_phone' => '0714567890',
            ],

            // Vehicles
            [
                'category' => 'vehicles-cars',
                'district' => 'colombo', 'city' => 'dehiwala',
                'title' => 'Toyota Aqua 2016 – Excellent Condition, Low Mileage',
                'description' => 'Toyota Aqua S grade 2016, pearl white, 62,000 km. Single owner, full service history. Reverse camera, push start, alloys. No accidents. Fitness valid till 2027.',
                'price' => 4950000, 'condition' => 'used', 'is_negotiable' => true, 'is_featured' => true,
                'contact_name' => 'Rohan Jayasinghe', 'contact_phone' => '0777654321',
            ],
            [
                'category' => 'vehicles-motorbikes',
                'district' => 'gampaha', 'city' => 'negombo',
                'title' => 'Honda CB Hornet 160R 2020 – Like New',
                'description' => 'Honda Hornet 160R 2020, red/black, 18,500 km. All documents up to date. No modifications. Selling due to upgrade. Genuine reason for sale.',
                'price' => 850000, 'condition' => 'used', 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Pradeep Silva', 'contact_phone' => '0761112233',
            ],
            [
                'category' => 'vehicles-three-wheelers',
                'district' => 'kandy', 'city' => 'kandy',
                'title' => 'Bajaj RE Three Wheeler 2019 – Good Running Condition',
                'description' => '2019 Bajaj RE three-wheeler, meter fitted, passenger version. Revenue licence and fitness up to date. Engine recently overhauled. Ready to work.',
                'price' => 1100000, 'condition' => 'used', 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Nihal', 'contact_phone' => '0812223344',
            ],
            [
                'category' => 'vehicles-cars',
                'district' => 'gampaha', 'city' => 'ja-ela',
                'title' => 'Suzuki Alto 2019 Automatic – First Owner',
                'description' => '2019 Suzuki Alto 800cc automatic, silver, 38,000 km. Lady-driven, first owner. Full options – power windows, central lock, ABS. Fitness and revenue valid. Negotiable.',
                'price' => 3200000, 'condition' => 'used', 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Dilrukshi', 'contact_phone' => '0318889900',
            ],

            // Electronics
            [
                'category' => 'electronics-mobile-phones',
                'district' => 'colombo', 'city' => 'moratuwa',
                'title' => 'iPhone 14 Pro 128GB – Space Black, Mint Condition',
                'description' => 'Apple iPhone 14 Pro 128GB space black. Purchased locally, in warranty until Dec 2024. Comes with original box, cable, and Apple silicone case. No scratches, battery health 97%.',
                'price' => 195000, 'condition' => 'used', 'is_negotiable' => false, 'is_featured' => true,
                'contact_name' => 'Tharaka Mendis', 'contact_phone' => '0752233445',
            ],
            [
                'category' => 'electronics-tvs-audio',
                'district' => 'gampaha', 'city' => 'wattala',
                'title' => 'Samsung 55" QLED 4K Smart TV – 2022 Model',
                'description' => 'Samsung 55-inch QLED 4K Smart TV, model QN55Q60B. Used for 1.5 years, perfect working condition. No dead pixels. Original remote and stand included. Moving abroad – urgent sale.',
                'price' => 155000, 'condition' => 'used', 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Achini Perera', 'contact_phone' => '0114455667',
            ],
            [
                'category' => 'electronics-computers-tablets',
                'district' => 'colombo', 'city' => 'kotte',
                'title' => 'Dell Inspiron 15 Core i7 12th Gen – 16GB RAM, 512GB SSD',
                'description' => 'Dell Inspiron 15 3511, Intel Core i7-1255U, 16GB DDR4, 512GB NVMe SSD. Windows 11 Pro. Purchased 8 months ago. Original charger included. Selling due to company laptop upgrade.',
                'price' => 145000, 'condition' => 'used', 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Kasun Rathnayake', 'contact_phone' => '0723344556',
            ],
            [
                'category' => 'electronics-home-appliances',
                'district' => 'galle', 'city' => 'galle',
                'title' => 'Singer Double Door Refrigerator 290L – Excellent Condition',
                'description' => 'Singer double-door refrigerator 290 litre, white. 3 years old, works perfectly. No rust or damage. Perfect for family use. Reason for sale: upgrading to larger model.',
                'price' => 52000, 'condition' => 'used', 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Gayani', 'contact_phone' => '0913344556',
            ],

            // Education
            [
                'category' => 'education-tuition-classes',
                'district' => 'colombo', 'city' => 'maharagama',
                'title' => 'A/L Combined Maths & Physics Tuition – Colombo South',
                'description' => 'Individual and small group tuition for A/L Combined Maths and Physics. Experienced graduate teacher with 10 years track record. Past papers covered. Home visits also available.',
                'price' => 3500, 'condition' => null, 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Mr. Wickramasinghe', 'contact_phone' => '0718877665',
            ],
            [
                'category' => 'education-tuition-classes',
                'district' => 'kandy', 'city' => 'kandy',
                'title' => 'English Language Classes – All Levels, Kandy',
                'description' => 'Spoken and written English for O/L, A/L, IELTS, and general communication. Small group sessions (max 5 students). Online sessions also available. Flexible timings.',
                'price' => 2500, 'condition' => null, 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Ms. Fonseka', 'contact_phone' => '0816655443',
            ],

            // Shopping
            [
                'category' => 'shopping-clothing-fashion',
                'district' => 'colombo', 'city' => 'colombo-1',
                'title' => 'Ladies Saree Collection – Designer Prints, Brand New',
                'description' => 'Beautiful designer saree collection imported from India. Various colours and patterns. Georgette, chiffon, and silk options. Suitable for weddings and events. Priced per piece.',
                'price' => 4500, 'condition' => 'new', 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Dilhani Boutique', 'contact_phone' => '0772244556',
            ],
            [
                'category' => 'shopping-footwear',
                'district' => 'gampaha', 'city' => 'kelaniya',
                'title' => 'Nike Air Max 270 – Size 43, Brand New in Box',
                'description' => 'Nike Air Max 270 sneakers, size UK9 / EU43. Bought from abroad, wrong size. Still in original box with tags. Never worn. Triple black colourway.',
                'price' => 13500, 'condition' => 'new', 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Ashan', 'contact_phone' => '0753366779',
            ],

            // Jobs
            [
                'category' => 'jobs-it-software',
                'district' => 'colombo', 'city' => 'colombo-1',
                'title' => 'Software Engineer (React + Node.js) – Colombo, Competitive Salary',
                'description' => 'Fast-growing tech startup hiring mid-level software engineers. Requirements: 2+ years React/Node, REST APIs, Git. Nice to have: AWS, TypeScript. Hybrid work model, health insurance, transport allowance.',
                'price' => null, 'condition' => null, 'is_negotiable' => false, 'is_featured' => true,
                'contact_name' => 'HR Team', 'contact_phone' => '0112233445',
            ],
            [
                'category' => 'jobs-sales-marketing',
                'district' => 'gampaha', 'city' => 'negombo',
                'title' => 'Sales Executive – FMCG Company, Gampaha Region',
                'description' => 'Reputed FMCG company looking for energetic sales executives to cover Gampaha district. Own motorbike required. Attractive salary + commission + fuel allowance. A/L qualification minimum.',
                'price' => null, 'condition' => null, 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'HR Manager', 'contact_phone' => '0335566778',
            ],

            // Services
            [
                'category' => 'services-repair-maintenance',
                'district' => 'colombo', 'city' => 'homagama',
                'title' => 'AC Repair & Service – All Brands, Same-Day Service',
                'description' => 'Professional air conditioner repair, cleaning, gas refilling, and installation. All brands serviced. Fully equipped workshop. Free inspection for Colombo district. Guaranteed workmanship.',
                'price' => 2500, 'condition' => null, 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Cool Tech Services', 'contact_phone' => '0763355447',
            ],
            [
                'category' => 'services-home-garden-services',
                'district' => 'colombo', 'city' => 'moratuwa',
                'title' => 'Professional House Painting Service – Interior & Exterior',
                'description' => 'Experienced painting team for interior and exterior house painting. All materials supplied. Clean workmanship guaranteed. Free site visit and quotation. Licensed contractor.',
                'price' => 45000, 'condition' => null, 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Priyantha Painters', 'contact_phone' => '0718899001',
            ],
            [
                'category' => 'services-event-services',
                'district' => 'kandy', 'city' => 'kandy',
                'title' => 'Wedding Photography & Videography – Kandy Based',
                'description' => 'Professional wedding photography and videography packages. Drone footage available. Cinematic edits. Pre-shoot and engagement sessions. Portfolio available on request. Booking for 2025/2026.',
                'price' => 85000, 'condition' => null, 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Lens & Light Studio', 'contact_phone' => '0817722334',
            ],

            // More Electronics
            [
                'category' => 'electronics-mobile-phones',
                'district' => 'galle', 'city' => 'hikkaduwa',
                'title' => 'Samsung Galaxy S23 Ultra 256GB – Phantom Black',
                'description' => 'Samsung Galaxy S23 Ultra 256GB phantom black. 6 months old, excellent condition. S Pen included. Screen protector and case applied from day one. Selling to switch to iPhone.',
                'price' => 215000, 'condition' => 'used', 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Isuru', 'contact_phone' => '0913366778',
            ],

            // More Vehicles
            [
                'category' => 'vehicles-cars',
                'district' => 'galle', 'city' => 'galle',
                'title' => 'Honda Vezel 2017 RS – Pearl White, Fully Loaded',
                'description' => 'Honda Vezel RS hybrid 2017, pearl white, 78,000 km. Reverse camera, lane assist, Honda sensing, LED headlights. Two owners from new. All original. Fitness 2027.',
                'price' => 7250000, 'condition' => 'used', 'is_negotiable' => true, 'is_featured' => false,
                'contact_name' => 'Chaminda', 'contact_phone' => '0714433221',
            ],
            [
                'category' => 'vehicles-auto-parts-accessories',
                'district' => 'colombo', 'city' => 'dehiwala',
                'title' => 'Pioneer 2DIN Android Car Stereo – 10 inch, DSP',
                'description' => 'Pioneer AVIC-F980BT equivalent 10-inch 2DIN Android head unit with DSP. GPS navigation, Apple CarPlay, Android Auto, Bluetooth, 4x50W. Removed from Toyota Aqua. Selling with wiring harness.',
                'price' => 28000, 'condition' => 'used', 'is_negotiable' => false, 'is_featured' => false,
                'contact_name' => 'Lahiru', 'contact_phone' => '0765544332',
            ],
        ];

        foreach ($ads as $data) {
            $category = $categories->get($data['category']);
            $district  = $districts->get($data['district']);
            $city      = $district?->cities->firstWhere('slug', $data['city']);

            if (! $category || ! $district || ! $city) {
                $this->command->warn("Skipping ad '{$data['title']}' — missing category/district/city.");
                continue;
            }

            Ad::firstOrCreate(
                ['title' => $data['title']],
                [
                    'user_id'       => $user->id,
                    'category_id'   => $category->id,
                    'district_id'   => $district->id,
                    'city_id'       => $city->id,
                    'description'   => $data['description'],
                    'condition'     => $data['condition'],
                    'price'         => $data['price'],
                    'is_negotiable' => $data['is_negotiable'],
                    'is_featured'   => $data['is_featured'],
                    'status'        => 'active',
                    'contact_name'  => $data['contact_name'],
                    'contact_phone' => $data['contact_phone'],
                    'views'         => rand(15, 600),
                    'expires_at'    => now()->addDays(30),
                ]
            );
        }
    }
}
