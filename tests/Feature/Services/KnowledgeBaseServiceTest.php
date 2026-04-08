<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\KnowledgeEntry;
use App\Models\Setting;
use App\Services\KnowledgeBaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_builds_system_prompt_with_company_name(): void
    {
        Setting::set('company_name', 'Test BV');

        $service = new KnowledgeBaseService();
        $prompt = $service->buildSystemPrompt();

        $this->assertStringContainsString('Test BV', $prompt);
    }

    public function test_includes_active_knowledge_entries(): void
    {
        Setting::set('company_name', 'Test');

        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Openingstijden?',
            'answer' => 'Ma-Vr 8-17',
            'is_active' => true,
        ]);

        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Inactieve vraag',
            'answer' => 'Niet tonen',
            'is_active' => false,
        ]);

        $service = new KnowledgeBaseService();
        $prompt = $service->buildSystemPrompt();

        $this->assertStringContainsString('Openingstijden?', $prompt);
        $this->assertStringNotContainsString('Inactieve vraag', $prompt);
    }
}
