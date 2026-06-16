<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Business;
use App\Models\Cart;
use App\Models\Category;
use App\Models\District;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\RedeemPoint;
use App\Models\Regency;
use App\Models\SetCategory;
use App\Models\SetCategoryItem;
use App\Models\User;
use App\Models\Village;
use App\Services\JurnalApi;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use PhpOffice\PhpSpreadsheet\Calculation\Category as CalculationCategory;

class DatabaseSeeder extends Seeder
{

    protected $jurnalApi;

    public function __construct(JurnalApi $jurnalApi)
    {
        $this->jurnalApi = $jurnalApi;
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(SettingSeeder::class);

        // Menggunakan UserSeeder yang sudah ada untuk membuat Admin, Sales, dan Customers
        $this->call(UserSeeder::class);

        // Menggunakan BusinessSeeder yang sudah ada untuk membuat data bisnis bagi para customer
        $this->call(BusinessSeeder::class);

        $this->call(NewsletterSeeder::class);
        $this->call(CouponSeeder::class);
        $this->call(OutletSeeder::class);
        $this->call(SectionSeeder::class);
        $this->call(OutletImageSeeder::class);

        $this->call(ReservationSeeder::class);
        $this->call(OutletReviewSeeder::class);


        foreach (Outlet::where('is_active', true)->get() as $key => $item) {
            $name = strtolower(array_reverse(explode(' ', $item->name))[0]);
            User::factory()->create([
                'name' => "Admin $name",
                'email' => "$name@admin.com",
                'role' => 'outlet-admin',
                'outlet_id' => $item->id
            ]);
        }

        $this->call(CardSeeder::class);
        $this->call(TypeSeeder::class);
        $this->call(MemberSeeder::class);

        $this->call(SettingSeeder::class);
        $this->call(IndoRegionSeeder::class);

        foreach (range(1, 3) as $key => $item) {
            $regency = Regency::inRandomOrder()->first();
            $district = District::where('regency_id', $regency->id)->inRandomOrder()->first();
            $village = Village::where('district_id', $district->id)->inRandomOrder()->first();

            Address::factory()->recycle(User::all())->create([
                'regency_id' => $regency->id,
                'district_id' => $district->id,
                'village_id' => $village->id
            ]);
        }

        $this->call(RedeemPointSeeder::class);

        // $this->call(CategorySeeder::class);
        // $this->call(ProductSeeder::class);
        $this->call(PricelistSeeder::class);
        // $this->call(CustomerPricelistSeeder::class);

        // $this->call(TransactionSeeder::class);
    }
}