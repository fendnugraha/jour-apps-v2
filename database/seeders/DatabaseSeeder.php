<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Database\Seeders\AccountSeeder;
use Database\Seeders\ChartOfAccountSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            AccountSeeder::class,
            ChartOfAccountSeeder::class
        ]);

        Warehouse::create([
            'w_code' => 'HQT',
            'w_name' => 'HEADQUARTER',
            'address' => 'Bandung, Jawa Barat, ID, 40375',
            'chart_of_account_id' => 1
        ]);

        Contact::create([
            'name' => 'General',
            'type' => 'Customer',
            'Description' => 'General Customer',
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@jour.com',
            'password' => bcrypt('admin123'),
            'role' => 'Administrator',
            'warehouse_id' => 1
        ]);

        User::create([
            'name' => 'fend',
            'email' => 'fend@jour.com',
            'password' => bcrypt('user123'),
            'role' => 'Administrator',
            'warehouse_id' => 1
        ]);

        // Contact::factory()->count(50)->create();

        Setting::create([
            'app_name' => 'Jour Apps',
            'address' => 'Jl. Pahlawan No. 1',
            'telephone' => '08123456789',
            'email' => 'admin@jour.com',
            'logo' => 'logo.png',
            'favicon' => 'favicon.png',
            'description' => 'Jour Apps',
            'cash_account' => '10100-002',
        ]);
    }
}
