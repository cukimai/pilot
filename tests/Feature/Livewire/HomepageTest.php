<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Models\KnowledgeEntry;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        Setting::set('company_name', 'Test Installatie');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Test Installatie');
    }

    public function test_homepage_shows_services(): void
    {
        Setting::set('company_name', 'Test');

        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'CV-ketel reparatie',
            'answer' => 'Wij repareren alle merken',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertSee('CV-ketel reparatie');
    }
}
