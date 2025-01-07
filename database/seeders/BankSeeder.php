<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('banks')->insert([
            ['name' => 'ZENITH -BANK', 'logo' => 'bank-logos/zenith-bank-logo.png'],
            ['name' => 'GTB -BANK', 'logo' => 'bank-logos/GuarantyTrustBank.svg.png'],
            ['name' => 'ZOJAPAY', 'logo' => 'bank-logos/zojapay-logo.png'],
            ['name' => 'OPAY', 'logo' => 'bank-logos/opay-logo-png.png'],
            ['name' => 'PALMPAY', 'logo' => 'bank-logos/palmpay-logo.png'],

        ]);
    }
}
