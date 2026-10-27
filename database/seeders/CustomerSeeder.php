<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@example.com',
                'phone' => '09171234567',
                'address' => '12 Rizal Street, Davao City',
            ],
            [
                'name' => 'John Reyes',
                'email' => 'john.reyes@example.com',
                'phone' => '09181234567',
                'address' => '45 Quezon Avenue, Davao City',
            ],
            [
                'name' => 'Ana Cruz',
                'email' => 'ana.cruz@example.com',
                'phone' => '09191234567',
                'address' => '8 Bonifacio Road, Davao City',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(['email' => $customer['email']], $customer);
        }
    }
}
