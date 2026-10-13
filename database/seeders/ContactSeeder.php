<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Tag;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagIds = Tag::pluck('id');

        Contact::factory()->count(20)->create()
        ->each(function($contact) use ($tagIds){
            $contact->tags()->attach(
                $tagIds->random(rand(1,3))
            );
        });
    }
}
