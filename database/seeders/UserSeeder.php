<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::create([
        //     'name' => 'Admin',
        //     'email' => "admin@gmail.com",
        //     'password' => Hash::make("123456") 
        // ]);
        User::create([
            'name' => 'Editor',
            'email' => "editorr@gmail.com",
            'password' => Hash::make("123456") 
        ]);

        $user = User::where('email', 'editorr@gmail.com')->firstOrFail();
        if ($user) {
            # code...
            $user->assignRole('editor');
        }
    }
}
