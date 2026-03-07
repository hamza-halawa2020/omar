<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
    use App\Models\User;

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function testIndex(): void
    // {
    //     $category = Category::get();
    //     $response = $this->get(route('categories.index'));
    //     $response->assertStatus(200);
    // }


public function testIndex(): void
{
    $user = User::firstOrFail();

    $response = $this->actingAs($user)
        ->get(route('categories.index'));

    $response->assertStatus(200);
}
}
