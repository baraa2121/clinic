<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
    Admin::create([
        'name' => 'AdminBaraa',
        'email' => 'baraaaa@example.com',
        'password' => Hash::make('baraaa222111'),
    ]);
    //baraa20004#$
    //User->Bareey212@example.com
    //password->Barry211#$
    }
}
