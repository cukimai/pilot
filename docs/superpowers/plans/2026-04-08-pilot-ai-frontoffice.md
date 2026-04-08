# Pilot AI Front Office - Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build an AI-powered front office system for an installation company (CV, airco, solar) that handles chat, voice, and ticketing automatically.

**Architecture:** Laravel 12 monolith with FilamentPHP 3 admin panel, Livewire chat widget on a public onepager, Claude API for chat AI, Vapi.ai for voice AI, Google Calendar for appointment sync.

**Tech Stack:** Laravel 12, Livewire 3, TailwindCSS, FilamentPHP 3, PostgreSQL, Redis, Laravel Reverb, Laravel Horizon, Claude API, Vapi.ai, Google Calendar API

---

## File Structure

```
pilot/
├── app/
│   ├── Enums/
│   │   ├── Channel.php                    # chat, voice, email
│   │   ├── ConversationStatus.php         # active, closed
│   │   ├── MessageRole.php                # user, assistant, system
│   │   ├── TicketType.php                 # storing, afspraak, offerte, overig
│   │   ├── TicketPriority.php             # low, medium, high, urgent
│   │   ├── TicketStatus.php               # open, in_progress, scheduled, resolved, closed
│   │   ├── AppointmentStatus.php          # planned, confirmed, completed, cancelled
│   │   ├── KnowledgeCategory.php          # diensten, faq, bedrijfsinfo, werkgebied, prijzen
│   │   └── UserRole.php                   # admin, monteur
│   ├── Models/
│   │   ├── User.php                       # Modified: add role, phone, google_calendar_id
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   ├── Ticket.php
│   │   ├── Contact.php
│   │   ├── KnowledgeEntry.php
│   │   ├── Appointment.php
│   │   ├── NotificationLog.php
│   │   └── Setting.php                    # Key-value settings store
│   ├── Filament/
│   │   ├── Pages/
│   │   │   └── Settings.php               # Custom settings page
│   │   ├── Resources/
│   │   │   ├── TicketResource.php
│   │   │   ├── TicketResource/
│   │   │   │   └── Pages/
│   │   │   │       ├── ListTickets.php
│   │   │   │       └── ViewTicket.php
│   │   │   ├── ConversationResource.php
│   │   │   ├── ConversationResource/
│   │   │   │   └── Pages/
│   │   │   │       ├── ListConversations.php
│   │   │   │       └── ViewConversation.php
│   │   │   ├── ContactResource.php
│   │   │   ├── AppointmentResource.php
│   │   │   ├── KnowledgeEntryResource.php
│   │   │   └── UserResource.php           # Monteurs management
│   │   ├── RelationManagers/
│   │   │   ├── TicketsRelationManager.php
│   │   │   ├── ConversationsRelationManager.php
│   │   │   └── MessagesRelationManager.php
│   │   └── Widgets/
│   │       ├── StatsOverview.php
│   │       ├── LatestTickets.php
│   │       └── TodayAppointments.php
│   ├── Services/
│   │   ├── ChatAiService.php              # Claude API integration
│   │   ├── KnowledgeBaseService.php       # Builds prompt context from knowledge entries
│   │   └── GoogleCalendarService.php      # Google Calendar sync
│   ├── Jobs/
│   │   ├── ProcessChatMessage.php
│   │   ├── ProcessVoiceWebhook.php
│   │   ├── SyncAppointmentToGoogle.php
│   │   ├── SendTicketNotification.php
│   │   └── SendUrgentAlert.php
│   ├── Notifications/
│   │   ├── NewTicketNotification.php
│   │   └── UrgentTicketNotification.php
│   ├── Livewire/
│   │   ├── Pages/
│   │   │   └── Homepage.php               # Onepager full-page component
│   │   └── ChatWidget.php                 # Floating chat widget
│   └── Http/
│       └── Controllers/
│           └── Api/
│               └── VoiceWebhookController.php
├── database/
│   └── migrations/
│       ├── xxxx_modify_users_table.php
│       ├── xxxx_create_contacts_table.php
│       ├── xxxx_create_conversations_table.php
│       ├── xxxx_create_messages_table.php
│       ├── xxxx_create_tickets_table.php
│       ├── xxxx_create_knowledge_entries_table.php
│       ├── xxxx_create_appointments_table.php
│       ├── xxxx_create_notification_logs_table.php
│       └── xxxx_create_settings_table.php
├── resources/
│   └── views/
│       ├── livewire/
│       │   ├── pages/
│       │   │   └── homepage.blade.php
│       │   └── chat-widget.blade.php
│       ├── filament/
│       │   └── pages/
│       │       └── settings.blade.php
│       └── components/
│           └── layouts/
│               └── app.blade.php          # Public layout
├── routes/
│   ├── web.php                            # Homepage route
│   └── api.php                            # Voice webhook route
├── config/
│   └── services.php                       # Claude + Vapi API keys
└── tests/
    └── Feature/
        ├── Models/
        │   ├── ConversationTest.php
        │   ├── TicketTest.php
        │   ├── ContactTest.php
        │   └── KnowledgeEntryTest.php
        ├── Services/
        │   ├── ChatAiServiceTest.php
        │   └── KnowledgeBaseServiceTest.php
        ├── Jobs/
        │   ├── ProcessChatMessageTest.php
        │   ├── ProcessVoiceWebhookTest.php
        │   └── SendTicketNotificationTest.php
        └── Livewire/
            ├── HomepageTest.php
            └── ChatWidgetTest.php
```

---

## Task 1: Laravel Project Setup

**Files:**
- Create: Laravel project via `laravel new`
- Modify: `.env`, `config/database.php`, `config/services.php`, `composer.json`

- [ ] **Step 1: Create Laravel project**

```bash
cd /Users/tomsoplanit/PhpstormProjects
rm -rf pilot/.oma
laravel new pilot --database=pgsql --no-interaction
cd pilot
```

Select "Livewire" as starter kit if prompted. If `laravel new` doesn't support direct starter kit selection, install a blank project.

- [ ] **Step 2: Install core packages**

```bash
cd /Users/tomsoplanit/PhpstormProjects/pilot
composer require filament/filament:"^3.0" laravel/reverb laravel/horizon
```

- [ ] **Step 3: Install and publish Filament**

```bash
php artisan filament:install --panels
```

When asked for the panel ID, enter: `admin`

- [ ] **Step 4: Install Reverb and Horizon**

```bash
php artisan reverb:install
php artisan install:broadcasting
php artisan horizon:install
```

- [ ] **Step 5: Configure environment**

Update `.env`:

```env
APP_NAME=Pilot
APP_URL=http://pilot.test

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pilot
DB_USERNAME=postgres
DB_PASSWORD=

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=pilot
REVERB_APP_KEY=pilot-key
REVERB_APP_SECRET=pilot-secret

ANTHROPIC_API_KEY=
VAPI_API_KEY=
VAPI_WEBHOOK_SECRET=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS="noreply@pilot.test"
MAIL_FROM_NAME="Pilot AI"
```

- [ ] **Step 6: Add service config for Claude and Vapi**

Add to `config/services.php` in the return array:

```php
'anthropic' => [
    'api_key' => env('ANTHROPIC_API_KEY'),
    'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
],

'vapi' => [
    'api_key' => env('VAPI_API_KEY'),
    'webhook_secret' => env('VAPI_WEBHOOK_SECRET'),
],

'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
],
```

- [ ] **Step 7: Create PostgreSQL database**

```bash
createdb pilot
```

- [ ] **Step 8: Run initial migration and verify**

```bash
php artisan migrate
php artisan serve
```

Visit `http://localhost:8000` — should see Laravel welcome page.
Visit `http://localhost:8000/admin` — should see Filament login.

- [ ] **Step 9: Commit**

```bash
git add -A
git commit -m "feat: initial Laravel project setup with Filament, Reverb, Horizon"
```

---

## Task 2: Enums

**Files:**
- Create: `app/Enums/Channel.php`
- Create: `app/Enums/ConversationStatus.php`
- Create: `app/Enums/MessageRole.php`
- Create: `app/Enums/TicketType.php`
- Create: `app/Enums/TicketPriority.php`
- Create: `app/Enums/TicketStatus.php`
- Create: `app/Enums/AppointmentStatus.php`
- Create: `app/Enums/KnowledgeCategory.php`
- Create: `app/Enums/UserRole.php`

- [ ] **Step 1: Create all enum files**

`app/Enums/Channel.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum Channel: string
{
    case Chat = 'chat';
    case Voice = 'voice';
    case Email = 'email';

    public function label(): string
    {
        return match ($this) {
            self::Chat => 'Chat',
            self::Voice => 'Telefoon',
            self::Email => 'E-mail',
        };
    }
}
```

`app/Enums/ConversationStatus.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum ConversationStatus: string
{
    case Active = 'active';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actief',
            self::Closed => 'Gesloten',
        };
    }
}
```

`app/Enums/MessageRole.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum MessageRole: string
{
    case User = 'user';
    case Assistant = 'assistant';
    case System = 'system';
}
```

`app/Enums/TicketType.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketType: string
{
    case Storing = 'storing';
    case Afspraak = 'afspraak';
    case Offerte = 'offerte';
    case Overig = 'overig';

    public function label(): string
    {
        return match ($this) {
            self::Storing => 'Storing',
            self::Afspraak => 'Afspraak',
            self::Offerte => 'Offerte',
            self::Overig => 'Overig',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Storing => 'danger',
            self::Afspraak => 'info',
            self::Offerte => 'warning',
            self::Overig => 'gray',
        };
    }
}
```

`app/Enums/TicketPriority.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Laag',
            self::Medium => 'Normaal',
            self::High => 'Hoog',
            self::Urgent => 'Urgent',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Medium => 'info',
            self::High => 'warning',
            self::Urgent => 'danger',
        };
    }
}
```

`app/Enums/TicketStatus.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Scheduled = 'scheduled';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In behandeling',
            self::Scheduled => 'Ingepland',
            self::Resolved => 'Opgelost',
            self::Closed => 'Gesloten',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'info',
            self::InProgress => 'warning',
            self::Scheduled => 'primary',
            self::Resolved => 'success',
            self::Closed => 'gray',
        };
    }
}
```

`app/Enums/AppointmentStatus.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentStatus: string
{
    case Planned = 'planned';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Gepland',
            self::Confirmed => 'Bevestigd',
            self::Completed => 'Afgerond',
            self::Cancelled => 'Geannuleerd',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planned => 'info',
            self::Confirmed => 'success',
            self::Completed => 'gray',
            self::Cancelled => 'danger',
        };
    }
}
```

`app/Enums/KnowledgeCategory.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum KnowledgeCategory: string
{
    case Diensten = 'diensten';
    case Faq = 'faq';
    case Bedrijfsinfo = 'bedrijfsinfo';
    case Werkgebied = 'werkgebied';
    case Prijzen = 'prijzen';

    public function label(): string
    {
        return match ($this) {
            self::Diensten => 'Diensten',
            self::Faq => 'Veelgestelde vragen',
            self::Bedrijfsinfo => 'Bedrijfsinformatie',
            self::Werkgebied => 'Werkgebied',
            self::Prijzen => 'Prijsindicaties',
        };
    }
}
```

`app/Enums/UserRole.php`:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Monteur = 'monteur';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Beheerder',
            self::Monteur => 'Monteur',
        };
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Enums/
git commit -m "feat: add all enum types for conversations, tickets, appointments, knowledge"
```

---

## Task 3: Database Migrations

**Files:**
- Create: 9 migration files in `database/migrations/`
- Modify: `app/Models/User.php` (add UUID trait)

- [ ] **Step 1: Modify users migration to use UUID and add fields**

Create migration:

```bash
php artisan make:migration modify_users_table --table=users
```

Migration content:

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin');
            $table->string('phone')->nullable();
            $table->string('google_calendar_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'google_calendar_id']);
        });
    }
};
```

- [ ] **Step 2: Create contacts migration**

```bash
php artisan make:migration create_contacts_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
```

- [ ] **Step 3: Create conversations migration**

```bash
php artisan make:migration create_conversations_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel');
            $table->string('status')->default('active');
            $table->text('summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
```

- [ ] **Step 4: Create messages migration**

```bash
php artisan make:migration create_messages_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
```

- [ ] **Step 5: Create tickets migration**

```bash
php artisan make:migration create_tickets_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('contact_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('priority')->default('medium');
            $table->string('status')->default('open');
            $table->string('subject');
            $table->text('description');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
```

- [ ] **Step 6: Create knowledge_entries migration**

```bash
php artisan make:migration create_knowledge_entries_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category');
            $table->string('question');
            $table->text('answer');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_entries');
    }
};
```

- [ ] **Step 7: Create appointments migration**

```bash
php artisan make:migration create_appointments_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->string('google_event_id')->nullable();
            $table->string('status')->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
```

- [ ] **Step 8: Create notification_logs migration**

```bash
php artisan make:migration create_notification_logs_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('recipient');
            $table->string('subject');
            $table->text('body');
            $table->nullableUuidMorphs('related');
            $table->timestamp('sent_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
```

- [ ] **Step 9: Create settings migration**

```bash
php artisan make:migration create_settings_table
```

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
```

- [ ] **Step 10: Run migrations**

```bash
php artisan migrate
```

Expected: All migrations run successfully, no errors.

- [ ] **Step 11: Commit**

```bash
git add database/migrations/
git commit -m "feat: add all database migrations for contacts, conversations, messages, tickets, knowledge, appointments, settings"
```

---

## Task 4: Eloquent Models

**Files:**
- Modify: `app/Models/User.php`
- Create: `app/Models/Contact.php`
- Create: `app/Models/Conversation.php`
- Create: `app/Models/Message.php`
- Create: `app/Models/Ticket.php`
- Create: `app/Models/KnowledgeEntry.php`
- Create: `app/Models/Appointment.php`
- Create: `app/Models/NotificationLog.php`
- Create: `app/Models/Setting.php`

- [ ] **Step 1: Update User model**

Modify `app/Models/User.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}
```

- [ ] **Step 2: Create Contact model**

`app/Models/Contact.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'notes' => 'array',
        ];
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
```

- [ ] **Step 3: Create Conversation model**

`app/Models/Conversation.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'channel' => Channel::class,
            'status' => ConversationStatus::class,
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }
}
```

- [ ] **Step 4: Create Message model**

`app/Models/Message.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MessageRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'role' => MessageRole::class,
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
```

- [ ] **Step 5: Create Ticket model**

`app/Models/Ticket.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => TicketType::class,
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
```

- [ ] **Step 6: Create KnowledgeEntry model**

`app/Models/KnowledgeEntry.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KnowledgeCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class KnowledgeEntry extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'category' => KnowledgeCategory::class,
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, KnowledgeCategory $category)
    {
        return $query->where('category', $category);
    }
}
```

- [ ] **Step 7: Create Appointment model**

`app/Models/Appointment.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => AppointmentStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 8: Create NotificationLog model**

`app/Models/NotificationLog.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationLog extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
```

- [ ] **Step 9: Create Setting model**

`app/Models/Setting.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
```

- [ ] **Step 10: Verify models load correctly**

```bash
php artisan tinker --execute="new App\Models\Contact(); echo 'Models OK';"
```

Expected: `Models OK`

- [ ] **Step 11: Commit**

```bash
git add app/Models/ app/Enums/
git commit -m "feat: add all Eloquent models with relationships and enum casts"
```

---

## Task 5: Filament Resources - KnowledgeEntry & Contact

**Files:**
- Create: `app/Filament/Resources/KnowledgeEntryResource.php`
- Create: `app/Filament/Resources/KnowledgeEntryResource/Pages/ListKnowledgeEntries.php`
- Create: `app/Filament/Resources/KnowledgeEntryResource/Pages/CreateKnowledgeEntry.php`
- Create: `app/Filament/Resources/KnowledgeEntryResource/Pages/EditKnowledgeEntry.php`
- Create: `app/Filament/Resources/ContactResource.php` (+ pages)

- [ ] **Step 1: Create KnowledgeEntry resource**

```bash
php artisan make:filament-resource KnowledgeEntry --generate
```

Then replace the generated `KnowledgeEntryResource.php` with:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\KnowledgeCategory;
use App\Filament\Resources\KnowledgeEntryResource\Pages;
use App\Models\KnowledgeEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KnowledgeEntryResource extends Resource
{
    protected static ?string $model = KnowledgeEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Kennisbank';

    protected static ?string $modelLabel = 'Kennisbank item';

    protected static ?string $pluralModelLabel = 'Kennisbank';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('category')
                ->label('Categorie')
                ->options(KnowledgeCategory::class)
                ->required(),
            Forms\Components\TextInput::make('question')
                ->label('Vraag / Titel')
                ->required()
                ->maxLength(255),
            Forms\Components\RichEditor::make('answer')
                ->label('Antwoord / Inhoud')
                ->required()
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')
                ->label('Actief')
                ->default(true),
            Forms\Components\TextInput::make('sort_order')
                ->label('Sortering')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->label('Categorie')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question')
                    ->label('Vraag / Titel')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actief')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Volgorde')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categorie')
                    ->options(KnowledgeCategory::class),
            ])
            ->defaultSort('category')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeEntries::route('/'),
            'create' => Pages\CreateKnowledgeEntry::route('/create'),
            'edit' => Pages\EditKnowledgeEntry::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 2: Create Contact resource**

```bash
php artisan make:filament-resource Contact --generate
```

Replace the generated `ContactResource.php` with:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Contacten';

    protected static ?string $modelLabel = 'Contact';

    protected static ?string $pluralModelLabel = 'Contacten';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contactgegevens')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefoon')
                    ->tel()
                    ->maxLength(255),
            ])->columns(3),
            Forms\Components\Section::make('Adres')->schema([
                Forms\Components\TextInput::make('address')
                    ->label('Adres')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('Stad')
                    ->maxLength(255),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Postcode')
                    ->maxLength(10),
            ])->columns(3),
            Forms\Components\Textarea::make('notes')
                ->label('Notities')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefoon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Stad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tickets_count')
                    ->label('Tickets')
                    ->counts('tickets')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Verify resources appear in Filament**

```bash
php artisan serve
```

Visit `http://localhost:8000/admin` — "Kennisbank" and "Contacten" should appear in the sidebar.

- [ ] **Step 4: Commit**

```bash
git add app/Filament/
git commit -m "feat: add Filament resources for KnowledgeEntry and Contact"
```

---

## Task 6: Filament Resources - Ticket & Conversation

**Files:**
- Create: `app/Filament/Resources/TicketResource.php` (+ pages)
- Create: `app/Filament/Resources/ConversationResource.php` (+ pages)
- Create: `app/Filament/RelationManagers/MessagesRelationManager.php`
- Create: `app/Filament/RelationManagers/TicketsRelationManager.php`

- [ ] **Step 1: Create Ticket resource**

```bash
php artisan make:filament-resource Ticket --view
```

Replace `TicketResource.php` with:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $pluralModelLabel = 'Tickets';

    protected static ?int $navigationSort = 1;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Ticket Details')->schema([
                Infolists\Components\TextEntry::make('subject')
                    ->label('Onderwerp'),
                Infolists\Components\TextEntry::make('type')
                    ->label('Type')
                    ->badge(),
                Infolists\Components\TextEntry::make('priority')
                    ->label('Prioriteit')
                    ->badge(),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                Infolists\Components\TextEntry::make('description')
                    ->label('Beschrijving')
                    ->columnSpanFull(),
            ])->columns(4),
            Infolists\Components\Section::make('Gekoppeld')->schema([
                Infolists\Components\TextEntry::make('contact.name')
                    ->label('Contact'),
                Infolists\Components\TextEntry::make('assignedTo.name')
                    ->label('Toegewezen aan')
                    ->default('Niet toegewezen'),
                Infolists\Components\TextEntry::make('scheduled_at')
                    ->label('Ingepland')
                    ->dateTime('d-m-Y H:i')
                    ->default('Niet ingepland'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i'),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Onderwerp')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioriteit')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Monteur')
                    ->default('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(TicketType::class),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioriteit')
                    ->options(TicketPriority::class),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(TicketStatus::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('assign')
                    ->label('Toewijzen')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Monteur')
                            ->options(User::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(fn (Ticket $record, array $data) => $record->update($data)),
                Tables\Actions\Action::make('changeStatus')
                    ->label('Status')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(TicketStatus::class)
                            ->required(),
                    ])
                    ->action(fn (Ticket $record, array $data) => $record->update($data)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 2: Create Conversation resource**

```bash
php artisan make:filament-resource Conversation --view
```

Replace `ConversationResource.php` with:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Channel;
use App\Filament\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Gesprekken';

    protected static ?string $modelLabel = 'Gesprek';

    protected static ?string $pluralModelLabel = 'Gesprekken';

    protected static ?int $navigationSort = 2;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Gesprek Details')->schema([
                Infolists\Components\TextEntry::make('channel')
                    ->label('Kanaal')
                    ->badge(),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                Infolists\Components\TextEntry::make('contact.name')
                    ->label('Contact')
                    ->default('Anoniem'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Gestart')
                    ->dateTime('d-m-Y H:i'),
            ])->columns(4),
            Infolists\Components\Section::make('Samenvatting')->schema([
                Infolists\Components\TextEntry::make('summary')
                    ->label('')
                    ->default('Geen samenvatting beschikbaar'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel')
                    ->label('Kanaal')
                    ->badge(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->default('Anoniem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Berichten')
                    ->counts('messages'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Gestart')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->label('Kanaal')
                    ->options(Channel::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ConversationResource\RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'view' => Pages\ViewConversation::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 3: Create MessagesRelationManager**

Create `app/Filament/Resources/ConversationResource/RelationManagers/MessagesRelationManager.php`:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\ConversationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Berichten';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user' => 'info',
                        'assistant' => 'success',
                        'system' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('content')
                    ->label('Bericht')
                    ->wrap()
                    ->limit(200),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tijd')
                    ->dateTime('d-m-Y H:i:s'),
            ])
            ->defaultSort('created_at', 'asc')
            ->paginated(false);
    }
}
```

- [ ] **Step 4: Commit**

```bash
git add app/Filament/
git commit -m "feat: add Filament resources for Ticket and Conversation with MessagesRelationManager"
```

---

## Task 7: Filament Resources - Appointment & User (Monteurs)

**Files:**
- Create: `app/Filament/Resources/AppointmentResource.php` (+ pages)
- Create: `app/Filament/Resources/UserResource.php` (+ pages)

- [ ] **Step 1: Create Appointment resource**

```bash
php artisan make:filament-resource Appointment --generate
```

Replace `AppointmentResource.php` with:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Afspraken';

    protected static ?string $modelLabel = 'Afspraak';

    protected static ?string $pluralModelLabel = 'Afspraken';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('ticket_id')
                ->label('Ticket')
                ->options(Ticket::query()->pluck('subject', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('contact_id')
                ->label('Contact')
                ->options(Contact::query()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('user_id')
                ->label('Monteur')
                ->options(User::query()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\DateTimePicker::make('scheduled_at')
                ->label('Datum & Tijd')
                ->required(),
            Forms\Components\TextInput::make('duration_minutes')
                ->label('Duur (minuten)')
                ->numeric()
                ->default(60),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options(AppointmentStatus::class)
                ->default('planned'),
            Forms\Components\Textarea::make('notes')
                ->label('Notities')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Datum & Tijd')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Monteur'),
                Tables\Columns\TextColumn::make('ticket.subject')
                    ->label('Ticket')
                    ->limit(30),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duur')
                    ->suffix(' min'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\IconColumn::make('google_event_id')
                    ->label('Google')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn ($record) => $record->google_event_id !== null),
            ])
            ->defaultSort('scheduled_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(AppointmentStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Bevestigen')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Appointment $record) => $record->update(['status' => 'confirmed']))
                    ->visible(fn (Appointment $record) => $record->status === AppointmentStatus::Planned),
                Tables\Actions\Action::make('cancel')
                    ->label('Annuleren')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Appointment $record) => $record->update(['status' => 'cancelled']))
                    ->visible(fn (Appointment $record) => ! in_array($record->status, [AppointmentStatus::Completed, AppointmentStatus::Cancelled])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 2: Create User resource (Monteurs)**

```bash
php artisan make:filament-resource User --generate
```

Replace `UserResource.php` with:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Monteurs';

    protected static ?string $modelLabel = 'Monteur';

    protected static ?string $pluralModelLabel = 'Monteurs';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Naam')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('phone')
                ->label('Telefoon')
                ->tel()
                ->maxLength(255),
            Forms\Components\TextInput::make('password')
                ->label('Wachtwoord')
                ->password()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->maxLength(255),
            Forms\Components\Select::make('role')
                ->label('Rol')
                ->options(UserRole::class)
                ->default('monteur')
                ->required(),
            Forms\Components\TextInput::make('google_calendar_id')
                ->label('Google Calendar ID')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefoon'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge(),
                Tables\Columns\IconColumn::make('google_calendar_id')
                    ->label('Google Cal')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->google_calendar_id !== null),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/
git commit -m "feat: add Filament resources for Appointment and User (monteurs)"
```

---

## Task 8: Filament Dashboard Widgets & Settings Page

**Files:**
- Create: `app/Filament/Widgets/StatsOverview.php`
- Create: `app/Filament/Widgets/LatestTickets.php`
- Create: `app/Filament/Widgets/TodayAppointments.php`
- Create: `app/Filament/Pages/Settings.php`
- Create: `resources/views/filament/pages/settings.blade.php`

- [ ] **Step 1: Create StatsOverview widget**

`app/Filament/Widgets/StatsOverview.php`:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Open Tickets', Ticket::query()->where('status', 'open')->count())
                ->icon('heroicon-o-ticket')
                ->color('warning'),
            Stat::make('Afspraken Vandaag', Appointment::query()->whereDate('scheduled_at', today())->count())
                ->icon('heroicon-o-calendar-days')
                ->color('info'),
            Stat::make('Nieuwe Contacten', Contact::query()->whereDate('created_at', today())->count())
                ->icon('heroicon-o-users')
                ->color('success'),
        ];
    }
}
```

- [ ] **Step 2: Create LatestTickets widget**

`app/Filament/Widgets/LatestTickets.php`:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestTickets extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Laatste Tickets';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Ticket::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Onderwerp')
                    ->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioriteit')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i'),
            ])
            ->paginated(false);
    }
}
```

- [ ] **Step 3: Create TodayAppointments widget**

`app/Filament/Widgets/TodayAppointments.php`:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TodayAppointments extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Afspraken Vandaag';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->whereDate('scheduled_at', today())
                    ->orderBy('scheduled_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Tijd')
                    ->dateTime('H:i'),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Monteur'),
                Tables\Columns\TextColumn::make('ticket.subject')
                    ->label('Ticket')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Geen afspraken vandaag')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
```

- [ ] **Step 4: Create Settings page**

`app/Filament/Pages/Settings.php`:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Instellingen';

    protected static ?string $title = 'Instellingen';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'company_name' => Setting::get('company_name', ''),
            'company_address' => Setting::get('company_address', ''),
            'company_phone' => Setting::get('company_phone', ''),
            'company_email' => Setting::get('company_email', ''),
            'ai_greeting' => Setting::get('ai_greeting', 'Welkom! Hoe kan ik u helpen?'),
            'ai_tone' => Setting::get('ai_tone', 'Vriendelijk en professioneel'),
            'voice_transfer_number' => Setting::get('voice_transfer_number', ''),
            'notification_emails' => Setting::get('notification_emails', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bedrijfsgegevens')->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Bedrijfsnaam')
                        ->required(),
                    Forms\Components\TextInput::make('company_address')
                        ->label('Adres'),
                    Forms\Components\TextInput::make('company_phone')
                        ->label('Telefoonnummer')
                        ->tel(),
                    Forms\Components\TextInput::make('company_email')
                        ->label('E-mail')
                        ->email(),
                ])->columns(2),
                Forms\Components\Section::make('AI Instellingen')->schema([
                    Forms\Components\TextInput::make('ai_greeting')
                        ->label('Begroetingstekst')
                        ->helperText('De eerste tekst die de chatbot toont'),
                    Forms\Components\Textarea::make('ai_tone')
                        ->label('Tone of voice')
                        ->helperText('Beschrijf hoe de AI moet communiceren'),
                ]),
                Forms\Components\Section::make('Voice Instellingen')->schema([
                    Forms\Components\TextInput::make('voice_transfer_number')
                        ->label('Doorverbind-nummer bij urgentie')
                        ->tel()
                        ->helperText('Telefoonnummer waar urgente bellers naartoe worden doorverbonden'),
                ]),
                Forms\Components\Section::make('Notificaties')->schema([
                    Forms\Components\Textarea::make('notification_emails')
                        ->label('E-mailadressen voor notificaties')
                        ->helperText('Eén e-mailadres per regel'),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Instellingen opgeslagen')
            ->success()
            ->send();
    }
}
```

`resources/views/filament/pages/settings.blade.php`:

```blade
<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Opslaan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
```

- [ ] **Step 5: Commit**

```bash
git add app/Filament/ resources/views/filament/
git commit -m "feat: add dashboard widgets (stats, latest tickets, today appointments) and settings page"
```

---

## Task 9: KnowledgeBase Service & Chat AI Service

**Files:**
- Create: `app/Services/KnowledgeBaseService.php`
- Create: `app/Services/ChatAiService.php`

- [ ] **Step 1: Create KnowledgeBaseService**

`app/Services/KnowledgeBaseService.php`:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\KnowledgeCategory;
use App\Models\KnowledgeEntry;
use App\Models\Setting;

class KnowledgeBaseService
{
    public function buildSystemPrompt(): string
    {
        $companyName = Setting::get('company_name', 'ons bedrijf');
        $tone = Setting::get('ai_tone', 'Vriendelijk en professioneel');

        $prompt = "Je bent de digitale assistent van {$companyName}.\n";
        $prompt .= "Je helpt klanten met vragen over diensten, het melden van storingen, het plannen van afspraken en het aanvragen van offertes.\n\n";

        foreach (KnowledgeCategory::cases() as $category) {
            $entries = KnowledgeEntry::query()
                ->active()
                ->byCategory($category)
                ->orderBy('sort_order')
                ->get();

            if ($entries->isEmpty()) {
                continue;
            }

            $prompt .= "{$category->label()}:\n";

            foreach ($entries as $entry) {
                $prompt .= "- {$entry->question}: {$entry->answer}\n";
            }

            $prompt .= "\n";
        }

        $prompt .= "Instructies:\n";
        $prompt .= "- Communiceer in het Nederlands\n";
        $prompt .= "- Tone of voice: {$tone}\n";
        $prompt .= "- Als iemand een storing meldt: vraag contactgegevens en maak een ticket aan via de create_ticket tool\n";
        $prompt .= "- Als iemand een afspraak wil: verzamel informatie en gebruik de schedule_appointment tool\n";
        $prompt .= "- Als iemand een offerte wil: verzamel details en maak een offerte-ticket aan\n";
        $prompt .= "- Verzamel altijd contactgegevens via de collect_contact_info tool\n";
        $prompt .= "- Als je het antwoord niet weet: escaleer via de escalate_to_human tool\n";
        $prompt .= "- Wees beknopt maar behulpzaam\n";

        return $prompt;
    }
}
```

- [ ] **Step 2: Create ChatAiService**

`app/Services/ChatAiService.php`:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MessageRole;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Jobs\SendTicketNotification;
use App\Jobs\SendUrgentAlert;
use App\Jobs\SyncAppointmentToGoogle;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAiService
{
    private KnowledgeBaseService $knowledgeBase;

    public function __construct(KnowledgeBaseService $knowledgeBase)
    {
        $this->knowledgeBase = $knowledgeBase;
    }

    public function processMessage(Conversation $conversation): Message
    {
        $systemPrompt = $this->knowledgeBase->buildSystemPrompt();
        $messages = $this->buildMessageHistory($conversation);
        $tools = $this->getToolDefinitions();

        $response = $this->callClaudeApi($systemPrompt, $messages, $tools);

        return $this->handleResponse($conversation, $response);
    }

    private function buildMessageHistory(Conversation $conversation): array
    {
        return $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $message) => [
                'role' => $message->role->value,
                'content' => $message->content,
            ])
            ->toArray();
    }

    private function getToolDefinitions(): array
    {
        return [
            [
                'name' => 'create_ticket',
                'description' => 'Maak een nieuw ticket aan voor een storing, afspraak, offerte of overige vraag. Gebruik dit wanneer de klant een probleem meldt of een verzoek heeft dat actie vereist.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'type' => [
                            'type' => 'string',
                            'enum' => ['storing', 'afspraak', 'offerte', 'overig'],
                            'description' => 'Het type ticket',
                        ],
                        'priority' => [
                            'type' => 'string',
                            'enum' => ['low', 'medium', 'high', 'urgent'],
                            'description' => 'De prioriteit. Urgent voor: geen verwarming in de winter, gaslucht, grote lekkage',
                        ],
                        'subject' => [
                            'type' => 'string',
                            'description' => 'Korte samenvatting van het probleem/verzoek',
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'Gedetailleerde beschrijving',
                        ],
                    ],
                    'required' => ['type', 'priority', 'subject', 'description'],
                ],
            ],
            [
                'name' => 'schedule_appointment',
                'description' => 'Plan een afspraak in voor een monteurbezoek. Gebruik dit wanneer de klant een afspraak wil maken.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'date' => [
                            'type' => 'string',
                            'description' => 'Gewenste datum in YYYY-MM-DD formaat',
                        ],
                        'time' => [
                            'type' => 'string',
                            'description' => 'Gewenste tijd in HH:MM formaat',
                        ],
                        'duration' => [
                            'type' => 'integer',
                            'description' => 'Geschatte duur in minuten',
                            'default' => 60,
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'Wat moet er gedaan worden',
                        ],
                    ],
                    'required' => ['date', 'time', 'description'],
                ],
            ],
            [
                'name' => 'collect_contact_info',
                'description' => 'Sla contactgegevens op van de klant. Gebruik dit zodra je naam, telefoon of adres hebt ontvangen.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'Volledige naam'],
                        'phone' => ['type' => 'string', 'description' => 'Telefoonnummer'],
                        'email' => ['type' => 'string', 'description' => 'E-mailadres'],
                        'address' => ['type' => 'string', 'description' => 'Straatnaam en huisnummer'],
                        'city' => ['type' => 'string', 'description' => 'Stad'],
                        'postal_code' => ['type' => 'string', 'description' => 'Postcode'],
                    ],
                    'required' => ['name'],
                ],
            ],
            [
                'name' => 'escalate_to_human',
                'description' => 'Escaleer het gesprek naar een menselijke medewerker. Gebruik dit als je de vraag niet kunt beantwoorden of als de klant erom vraagt.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'reason' => [
                            'type' => 'string',
                            'description' => 'Reden voor escalatie',
                        ],
                    ],
                    'required' => ['reason'],
                ],
            ],
        ];
    }

    private function callClaudeApi(string $systemPrompt, array $messages, array $tools): array
    {
        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model'),
            'max_tokens' => 1024,
            'system' => $systemPrompt,
            'messages' => $messages,
            'tools' => $tools,
        ]);

        if ($response->failed()) {
            Log::error('Claude API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Claude API call failed: ' . $response->body());
        }

        return $response->json();
    }

    private function handleResponse(Conversation $conversation, array $response): Message
    {
        $textContent = '';
        $toolResults = [];

        foreach ($response['content'] ?? [] as $block) {
            if ($block['type'] === 'text') {
                $textContent .= $block['text'];
            }

            if ($block['type'] === 'tool_use') {
                $result = $this->executeTool($conversation, $block['name'], $block['input']);
                $toolResults[] = [
                    'tool' => $block['name'],
                    'input' => $block['input'],
                    'result' => $result,
                ];
            }
        }

        if (! empty($toolResults) && $response['stop_reason'] === 'tool_use') {
            $toolResultMessages = $this->buildToolResultMessages($response, $toolResults);
            $allMessages = $this->buildMessageHistory($conversation);
            $allMessages[] = ['role' => 'assistant', 'content' => $response['content']];
            $allMessages[] = ['role' => 'user', 'content' => $toolResultMessages];

            $followUp = $this->callClaudeApi(
                $this->knowledgeBase->buildSystemPrompt(),
                $allMessages,
                $this->getToolDefinitions(),
            );

            foreach ($followUp['content'] ?? [] as $block) {
                if ($block['type'] === 'text') {
                    $textContent = $block['text'];
                }
            }
        }

        return Message::create([
            'conversation_id' => $conversation->id,
            'role' => MessageRole::Assistant,
            'content' => $textContent,
            'metadata' => ! empty($toolResults) ? ['tool_calls' => $toolResults] : null,
        ]);
    }

    private function buildToolResultMessages(array $response, array $toolResults): array
    {
        $results = [];

        foreach ($response['content'] as $block) {
            if ($block['type'] !== 'tool_use') {
                continue;
            }

            $matchingResult = collect($toolResults)->firstWhere('tool', $block['name']);

            $results[] = [
                'type' => 'tool_result',
                'tool_use_id' => $block['id'],
                'content' => json_encode($matchingResult['result'] ?? ['status' => 'ok']),
            ];
        }

        return $results;
    }

    private function executeTool(Conversation $conversation, string $toolName, array $input): array
    {
        return match ($toolName) {
            'create_ticket' => $this->executeCreateTicket($conversation, $input),
            'schedule_appointment' => $this->executeScheduleAppointment($conversation, $input),
            'collect_contact_info' => $this->executeCollectContactInfo($conversation, $input),
            'escalate_to_human' => $this->executeEscalateToHuman($conversation, $input),
            default => ['status' => 'error', 'message' => "Unknown tool: {$toolName}"],
        };
    }

    private function executeCreateTicket(Conversation $conversation, array $input): array
    {
        $contact = $conversation->contact;

        if (! $contact) {
            return ['status' => 'error', 'message' => 'Geen contactgegevens beschikbaar. Vraag eerst om contactgegevens.'];
        }

        $ticket = Ticket::create([
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'type' => $input['type'],
            'priority' => $input['priority'],
            'status' => TicketStatus::Open,
            'subject' => $input['subject'],
            'description' => $input['description'],
        ]);

        SendTicketNotification::dispatch($ticket);

        if ($input['priority'] === 'urgent') {
            SendUrgentAlert::dispatch($ticket);
        }

        return ['status' => 'ok', 'ticket_id' => $ticket->id, 'message' => 'Ticket aangemaakt'];
    }

    private function executeScheduleAppointment(Conversation $conversation, array $input): array
    {
        $contact = $conversation->contact;

        if (! $contact) {
            return ['status' => 'error', 'message' => 'Geen contactgegevens beschikbaar.'];
        }

        $ticket = $conversation->ticket;

        if (! $ticket) {
            $ticket = Ticket::create([
                'conversation_id' => $conversation->id,
                'contact_id' => $contact->id,
                'type' => TicketType::Afspraak,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Scheduled,
                'subject' => 'Afspraak: ' . $input['description'],
                'description' => $input['description'],
            ]);
        }

        $scheduledAt = $input['date'] . ' ' . $input['time'];
        $monteur = User::query()->where('role', 'monteur')->first();

        $appointment = Appointment::create([
            'ticket_id' => $ticket->id,
            'contact_id' => $contact->id,
            'user_id' => $monteur?->id ?? $conversation->contact_id,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $input['duration'] ?? 60,
            'notes' => $input['description'],
        ]);

        if ($monteur) {
            SyncAppointmentToGoogle::dispatch($appointment);
        }

        return ['status' => 'ok', 'appointment_id' => $appointment->id, 'message' => 'Afspraak ingepland'];
    }

    private function executeCollectContactInfo(Conversation $conversation, array $input): array
    {
        $contact = $conversation->contact;

        if ($contact) {
            $contact->update(array_filter($input));
        } else {
            $contact = Contact::create(array_filter($input));
            $conversation->update(['contact_id' => $contact->id]);
        }

        return ['status' => 'ok', 'contact_id' => $contact->id, 'message' => 'Contactgegevens opgeslagen'];
    }

    private function executeEscalateToHuman(Conversation $conversation, array $input): array
    {
        $conversation->update(['summary' => 'ESCALATIE: ' . $input['reason']]);

        $notificationEmails = Setting::get('notification_emails', '');

        if ($notificationEmails) {
            SendUrgentAlert::dispatch(null, 'Escalatie: ' . $input['reason'], $notificationEmails);
        }

        return ['status' => 'ok', 'message' => 'Gesprek geëscaleerd naar medewerker'];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Services/
git commit -m "feat: add KnowledgeBaseService and ChatAiService with Claude API tool use integration"
```

---

## Task 10: Queue Jobs & Notifications

**Files:**
- Create: `app/Jobs/ProcessChatMessage.php`
- Create: `app/Jobs/ProcessVoiceWebhook.php`
- Create: `app/Jobs/SyncAppointmentToGoogle.php`
- Create: `app/Jobs/SendTicketNotification.php`
- Create: `app/Jobs/SendUrgentAlert.php`
- Create: `app/Notifications/NewTicketNotification.php`
- Create: `app/Notifications/UrgentTicketNotification.php`
- Create: `app/Events/ChatMessageReceived.php`

- [ ] **Step 1: Create ChatMessageReceived event**

`app/Events/ChatMessageReceived.php`:

```php
<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageReceived implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Message $message,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'role' => $this->message->role->value,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
```

- [ ] **Step 2: Create ProcessChatMessage job**

`app/Jobs/ProcessChatMessage.php`:

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\ChatMessageReceived;
use App\Models\Conversation;
use App\Services\ChatAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessChatMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $timeout = 45;

    public function __construct(
        public Conversation $conversation,
    ) {
    }

    public function handle(ChatAiService $chatAiService): void
    {
        $message = $chatAiService->processMessage($this->conversation);

        ChatMessageReceived::dispatch($message);
    }
}
```

- [ ] **Step 3: Create ProcessVoiceWebhook job**

`app/Jobs/ProcessVoiceWebhook.php`:

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Enums\MessageRole;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVoiceWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $payload,
    ) {
    }

    public function handle(): void
    {
        $contact = null;
        $callerName = $this->payload['caller_name'] ?? null;
        $callerPhone = $this->payload['caller_phone'] ?? null;

        if ($callerName || $callerPhone) {
            $contact = Contact::firstOrCreate(
                ['phone' => $callerPhone],
                ['name' => $callerName ?? 'Onbekend', 'phone' => $callerPhone],
            );
        }

        $conversation = Conversation::create([
            'contact_id' => $contact?->id,
            'channel' => Channel::Voice,
            'status' => ConversationStatus::Closed,
            'summary' => $this->payload['summary'] ?? null,
        ]);

        if (! empty($this->payload['transcript'])) {
            foreach ($this->payload['transcript'] as $entry) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => $entry['role'] === 'assistant' ? MessageRole::Assistant : MessageRole::User,
                    'content' => $entry['content'],
                ]);
            }
        }

        if (! $contact) {
            return;
        }

        $priority = $this->payload['urgency'] === 'urgent'
            ? TicketPriority::Urgent
            : TicketPriority::Medium;

        $ticket = Ticket::create([
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'type' => $this->mapTicketType($this->payload['type'] ?? 'overig'),
            'priority' => $priority,
            'status' => TicketStatus::Open,
            'subject' => $this->payload['summary'] ?? 'Telefonische melding',
            'description' => $this->payload['summary'] ?? 'Binnenkomend telefoongesprek',
        ]);

        SendTicketNotification::dispatch($ticket);

        if ($priority === TicketPriority::Urgent) {
            SendUrgentAlert::dispatch($ticket);
        }
    }

    private function mapTicketType(string $type): TicketType
    {
        return match ($type) {
            'storing' => TicketType::Storing,
            'afspraak' => TicketType::Afspraak,
            'offerte' => TicketType::Offerte,
            default => TicketType::Overig,
        };
    }
}
```

- [ ] **Step 4: Create SendTicketNotification job**

`app/Jobs/SendTicketNotification.php`:

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Ticket $ticket,
    ) {
    }

    public function handle(): void
    {
        $emails = $this->getNotificationEmails();

        if (empty($emails)) {
            return;
        }

        $subject = "Nieuw ticket: {$this->ticket->subject}";
        $body = "Er is een nieuw ticket aangemaakt.\n\n"
            . "Type: {$this->ticket->type->label()}\n"
            . "Prioriteit: {$this->ticket->priority->label()}\n"
            . "Onderwerp: {$this->ticket->subject}\n"
            . "Beschrijving: {$this->ticket->description}\n"
            . "Contact: {$this->ticket->contact?->name}\n";

        foreach ($emails as $email) {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            NotificationLog::create([
                'type' => 'email',
                'recipient' => $email,
                'subject' => $subject,
                'body' => $body,
                'related_type' => Ticket::class,
                'related_id' => $this->ticket->id,
            ]);
        }
    }

    private function getNotificationEmails(): array
    {
        $raw = Setting::get('notification_emails', '');

        return array_filter(
            array_map('trim', explode("\n", $raw)),
        );
    }
}
```

- [ ] **Step 5: Create SendUrgentAlert job**

`app/Jobs/SendUrgentAlert.php`:

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendUrgentAlert implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ?Ticket $ticket = null,
        public ?string $reason = null,
        public ?string $overrideEmails = null,
    ) {
    }

    public function handle(): void
    {
        $emails = $this->overrideEmails
            ? array_filter(array_map('trim', explode("\n", $this->overrideEmails)))
            : $this->getNotificationEmails();

        if (empty($emails)) {
            return;
        }

        $subject = '[URGENT] ' . ($this->ticket?->subject ?? $this->reason ?? 'Urgente melding');
        $body = "URGENTE MELDING\n\n";

        if ($this->ticket) {
            $body .= "Ticket: {$this->ticket->subject}\n"
                . "Type: {$this->ticket->type->label()}\n"
                . "Beschrijving: {$this->ticket->description}\n"
                . "Contact: {$this->ticket->contact?->name}\n"
                . "Telefoon: {$this->ticket->contact?->phone}\n";
        }

        if ($this->reason) {
            $body .= "Reden: {$this->reason}\n";
        }

        foreach ($emails as $email) {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            NotificationLog::create([
                'type' => 'email',
                'recipient' => $email,
                'subject' => $subject,
                'body' => $body,
                'related_type' => $this->ticket ? Ticket::class : null,
                'related_id' => $this->ticket?->id,
            ]);
        }
    }

    private function getNotificationEmails(): array
    {
        $raw = Setting::get('notification_emails', '');

        return array_filter(
            array_map('trim', explode("\n", $raw)),
        );
    }
}
```

- [ ] **Step 6: Create SyncAppointmentToGoogle job**

`app/Jobs/SyncAppointmentToGoogle.php`:

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAppointmentToGoogle implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Appointment $appointment,
    ) {
    }

    public function handle(GoogleCalendarService $calendarService): void
    {
        $user = $this->appointment->user;

        if (! $user?->google_calendar_id) {
            Log::info('Skipping Google Calendar sync: no calendar ID for user', [
                'appointment_id' => $this->appointment->id,
            ]);

            return;
        }

        $eventId = $calendarService->createEvent($this->appointment);

        $this->appointment->update(['google_event_id' => $eventId]);
    }
}
```

- [ ] **Step 7: Commit**

```bash
git add app/Jobs/ app/Events/ app/Notifications/
git commit -m "feat: add queue jobs for chat processing, voice webhooks, notifications, and Google Calendar sync"
```

---

## Task 11: Google Calendar Service

**Files:**
- Create: `app/Services/GoogleCalendarService.php`

- [ ] **Step 1: Install Google API client**

```bash
composer require google/apiclient:"^2.0"
```

- [ ] **Step 2: Create GoogleCalendarService**

`app/Services/GoogleCalendarService.php`:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Setting;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private ?GoogleClient $client = null;

    public function getClient(): GoogleClient
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');

        $token = Setting::get('google_calendar_token');

        if ($token) {
            $tokenData = json_decode($token, true);
            $this->client->setAccessToken($tokenData);

            if ($this->client->isAccessTokenExpired() && $this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                Setting::set('google_calendar_token', json_encode($this->client->getAccessToken()));
            }
        }

        return $this->client;
    }

    public function createEvent(Appointment $appointment): ?string
    {
        $client = $this->getClient();

        if (! $client->getAccessToken()) {
            Log::warning('Google Calendar not authenticated');

            return null;
        }

        $calendarService = new Calendar($client);
        $calendarId = $appointment->user->google_calendar_id ?? 'primary';

        $contact = $appointment->contact;
        $ticket = $appointment->ticket;

        $event = new Event([
            'summary' => $ticket?->subject ?? 'Afspraak',
            'description' => implode("\n", array_filter([
                "Contact: {$contact?->name}",
                $contact?->phone ? "Tel: {$contact->phone}" : null,
                $contact?->address ? "Adres: {$contact->address}, {$contact->city} {$contact->postal_code}" : null,
                $appointment->notes ? "\nNotities: {$appointment->notes}" : null,
            ])),
            'start' => new EventDateTime([
                'dateTime' => $appointment->scheduled_at->toRfc3339String(),
                'timeZone' => 'Europe/Amsterdam',
            ]),
            'end' => new EventDateTime([
                'dateTime' => $appointment->scheduled_at->addMinutes($appointment->duration_minutes)->toRfc3339String(),
                'timeZone' => 'Europe/Amsterdam',
            ]),
        ]);

        if ($contact?->address) {
            $event->setLocation("{$contact->address}, {$contact->postal_code} {$contact->city}");
        }

        $createdEvent = $calendarService->events->insert($calendarId, $event);

        return $createdEvent->getId();
    }

    public function getAuthUrl(): string
    {
        return $this->getClient()->createAuthUrl();
    }

    public function handleCallback(string $code): void
    {
        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);
        Setting::set('google_calendar_token', json_encode($token));
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Services/GoogleCalendarService.php
git commit -m "feat: add Google Calendar service for appointment sync"
```

---

## Task 12: Voice Webhook Controller

**Files:**
- Create: `app/Http/Controllers/Api/VoiceWebhookController.php`
- Modify: `routes/api.php`

- [ ] **Step 1: Create VoiceWebhookController**

`app/Http/Controllers/Api/VoiceWebhookController.php`:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVoiceWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoiceWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'caller_name' => 'nullable|string',
            'caller_phone' => 'nullable|string',
            'summary' => 'nullable|string',
            'type' => 'nullable|string|in:storing,afspraak,offerte,overig',
            'urgency' => 'nullable|string|in:low,medium,high,urgent',
            'transcript' => 'nullable|array',
            'transcript.*.role' => 'required_with:transcript|string|in:user,assistant',
            'transcript.*.content' => 'required_with:transcript|string',
        ]);

        ProcessVoiceWebhook::dispatch($payload);

        return response()->json(['status' => 'ok']);
    }
}
```

- [ ] **Step 2: Add route**

Add to `routes/api.php`:

```php
use App\Http\Controllers\Api\VoiceWebhookController;

Route::post('/voice/webhook', VoiceWebhookController::class);
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Api/ routes/api.php
git commit -m "feat: add Vapi voice webhook endpoint"
```

---

## Task 13: Chat Widget Livewire Component

**Files:**
- Create: `app/Livewire/ChatWidget.php`
- Create: `resources/views/livewire/chat-widget.blade.php`

- [ ] **Step 1: Create ChatWidget component**

`app/Livewire/ChatWidget.php`:

```php
<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Enums\MessageRole;
use App\Jobs\ProcessChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatWidget extends Component
{
    public bool $isOpen = false;

    public ?string $conversationId = null;

    public string $input = '';

    public array $messages = [];

    public bool $isTyping = false;

    public function mount(): void
    {
        $greeting = Setting::get('ai_greeting', 'Welkom! Hoe kan ik u helpen?');
        $this->messages = [
            ['role' => 'assistant', 'content' => $greeting],
        ];
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function sendMessage(): void
    {
        $content = trim($this->input);

        if ($content === '') {
            return;
        }

        $this->input = '';

        if (! $this->conversationId) {
            $conversation = Conversation::create([
                'channel' => Channel::Chat,
                'status' => ConversationStatus::Active,
            ]);
            $this->conversationId = $conversation->id;
        }

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'role' => MessageRole::User,
            'content' => $content,
        ]);

        $this->messages[] = [
            'role' => 'user',
            'content' => $content,
        ];

        $this->isTyping = true;

        ProcessChatMessage::dispatch(
            Conversation::find($this->conversationId),
        );
    }

    #[On('echo:conversation.{conversationId},ChatMessageReceived')]
    public function onMessageReceived(array $data): void
    {
        $this->messages[] = [
            'role' => $data['role'],
            'content' => $data['content'],
        ];

        $this->isTyping = false;
    }

    public function getListeners(): array
    {
        if (! $this->conversationId) {
            return [];
        }

        return [
            "echo:conversation.{$this->conversationId},ChatMessageReceived" => 'onMessageReceived',
        ];
    }

    public function render()
    {
        return view('livewire.chat-widget');
    }
}
```

- [ ] **Step 2: Create chat widget Blade template**

`resources/views/livewire/chat-widget.blade.php`:

```blade
<div class="fixed bottom-6 right-6 z-50" x-data="{ scrollToBottom() { $nextTick(() => { const el = document.getElementById('chat-messages'); if (el) el.scrollTop = el.scrollHeight; }) } }">
    {{-- Chat Button --}}
    @unless($isOpen)
        <button
            wire:click="toggle"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-black text-white shadow-lg transition-transform hover:scale-105"
        >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
            </svg>
        </button>
    @endunless

    {{-- Chat Window --}}
    @if($isOpen)
        <div class="flex h-[500px] w-[380px] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Klantenservice</h3>
                    <p class="text-xs text-gray-500">Wij helpen u graag</p>
                </div>
                <button wire:click="toggle" class="rounded-full p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Messages --}}
            <div
                id="chat-messages"
                class="flex-1 space-y-3 overflow-y-auto px-5 py-4"
                x-init="scrollToBottom()"
                wire:poll.2s
            >
                @foreach($messages as $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed {{ $message['role'] === 'user' ? 'bg-black text-white' : 'bg-gray-100 text-gray-800' }}">
                            {!! nl2br(e($message['content'])) !!}
                        </div>
                    </div>
                @endforeach

                @if($isTyping)
                    <div class="flex justify-start">
                        <div class="rounded-2xl bg-gray-100 px-4 py-3">
                            <div class="flex space-x-1.5">
                                <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 0ms"></div>
                                <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 150ms"></div>
                                <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 300ms"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Input --}}
            <div class="border-t border-gray-100 px-4 py-3">
                <form wire:submit="sendMessage" class="flex items-center gap-2">
                    <input
                        wire:model="input"
                        type="text"
                        placeholder="Typ uw bericht..."
                        class="flex-1 rounded-full border border-gray-200 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none transition-colors focus:border-gray-400 focus:ring-0"
                        autocomplete="off"
                    />
                    <button
                        type="submit"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-black text-white transition-transform hover:scale-105 disabled:opacity-50"
                        @if($isTyping) disabled @endif
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-50 px-4 py-2 text-center">
                <span class="text-[10px] tracking-wide text-gray-300">Powered by Pilot AI</span>
            </div>
        </div>
    @endif
</div>
```

- [ ] **Step 3: Commit**

```bash
git add app/Livewire/ChatWidget.php resources/views/livewire/chat-widget.blade.php
git commit -m "feat: add Livewire chat widget with real-time messaging and Apple-inspired design"
```

---

## Task 14: Homepage (Onepager) Livewire Component

**Files:**
- Create: `app/Livewire/Pages/Homepage.php`
- Create: `resources/views/livewire/pages/homepage.blade.php`
- Create: `resources/views/components/layouts/app.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create app layout**

`resources/views/components/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased">
    {{ $slot }}
    @livewireScripts
</body>
</html>
```

- [ ] **Step 2: Create Homepage component**

`app/Livewire/Pages/Homepage.php`:

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\KnowledgeCategory;
use App\Models\KnowledgeEntry;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Homepage extends Component
{
    public string $companyName;

    public string $companyPhone;

    public string $companyEmail;

    public string $companyAddress;

    public array $services = [];

    public array $aboutInfo = [];

    public array $workArea = [];

    public function mount(): void
    {
        $this->companyName = Setting::get('company_name', 'Installatiebedrijf');
        $this->companyPhone = Setting::get('company_phone', '');
        $this->companyEmail = Setting::get('company_email', '');
        $this->companyAddress = Setting::get('company_address', '');

        $this->services = KnowledgeEntry::query()
            ->active()
            ->byCategory(KnowledgeCategory::Diensten)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $this->aboutInfo = KnowledgeEntry::query()
            ->active()
            ->byCategory(KnowledgeCategory::Bedrijfsinfo)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $this->workArea = KnowledgeEntry::query()
            ->active()
            ->byCategory(KnowledgeCategory::Werkgebied)
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    #[Title('Home')]
    public function render()
    {
        return view('livewire.pages.homepage');
    }
}
```

- [ ] **Step 3: Create Homepage Blade template**

`resources/views/livewire/pages/homepage.blade.php`:

```blade
<div>
    {{-- Navigation --}}
    <nav class="fixed top-0 z-40 w-full border-b border-gray-100/50 bg-white/80 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
            <span class="text-lg font-semibold tracking-tight">{{ $companyName }}</span>
            <div class="hidden items-center gap-8 md:flex">
                <a href="#diensten" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Diensten</a>
                <a href="#over-ons" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Over ons</a>
                <a href="#contact" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Contact</a>
            </div>
            @if($companyPhone)
                <a href="tel:{{ $companyPhone }}" class="rounded-full bg-black px-5 py-2 text-sm font-medium text-white transition-transform hover:scale-105">
                    Bel ons
                </a>
            @endif
        </div>
    </nav>

    {{-- Hero --}}
    <section class="flex min-h-[85vh] items-center pt-16">
        <div class="mx-auto max-w-6xl px-6">
            <div class="max-w-2xl">
                <h1 class="text-5xl font-bold leading-tight tracking-tight md:text-7xl">
                    Uw installatie<br>
                    <span class="text-gray-400">in vertrouwde<br>handen.</span>
                </h1>
                <p class="mt-6 text-lg leading-relaxed text-gray-500">
                    Van CV-ketel onderhoud tot airconditioning en zonnepanelen.
                    Wij staan voor u klaar — 24/7 bereikbaar via onze AI-assistent.
                </p>
                <div class="mt-10 flex items-center gap-4">
                    <button
                        onclick="document.querySelector('[wire\\:click=toggle]')?.click()"
                        class="rounded-full bg-black px-8 py-3.5 text-sm font-medium text-white transition-transform hover:scale-105"
                    >
                        Stel uw vraag
                    </button>
                    @if($companyPhone)
                        <a href="tel:{{ $companyPhone }}" class="rounded-full border border-gray-200 px-8 py-3.5 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400">
                            {{ $companyPhone }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Diensten --}}
    @if(count($services) > 0)
        <section id="diensten" class="border-t border-gray-100 py-24">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-16">
                    <p class="text-sm font-medium uppercase tracking-widest text-gray-400">Wat wij doen</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight">Onze diensten</h2>
                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($services as $service)
                        <div class="rounded-2xl border border-gray-100 p-8 transition-shadow hover:shadow-lg">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                                <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold">{{ $service['question'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-500">{!! strip_tags($service['answer']) !!}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Over ons --}}
    @if(count($aboutInfo) > 0)
        <section id="over-ons" class="border-t border-gray-100 bg-gray-50/50 py-24">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-16">
                    <p class="text-sm font-medium uppercase tracking-widest text-gray-400">Wie wij zijn</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight">Over ons</h2>
                </div>
                <div class="max-w-3xl space-y-6">
                    @foreach($aboutInfo as $info)
                        <div>
                            <h3 class="text-lg font-semibold">{{ $info['question'] }}</h3>
                            <p class="mt-2 leading-relaxed text-gray-600">{!! strip_tags($info['answer']) !!}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contact --}}
    <section id="contact" class="border-t border-gray-100 py-24">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-16">
                <p class="text-sm font-medium uppercase tracking-widest text-gray-400">Neem contact op</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight">Contact</h2>
            </div>
            <div class="grid gap-12 md:grid-cols-3">
                @if($companyPhone)
                    <div>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold">Telefoon</h3>
                        <a href="tel:{{ $companyPhone }}" class="mt-1 block text-gray-500 transition-colors hover:text-gray-900">{{ $companyPhone }}</a>
                        <p class="mt-1 text-sm text-gray-400">AI-assistent 24/7 bereikbaar</p>
                    </div>
                @endif
                @if($companyEmail)
                    <div>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <h3 class="font-semibold">E-mail</h3>
                        <a href="mailto:{{ $companyEmail }}" class="mt-1 block text-gray-500 transition-colors hover:text-gray-900">{{ $companyEmail }}</a>
                    </div>
                @endif
                @if($companyAddress)
                    <div>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold">Adres</h3>
                        <p class="mt-1 text-gray-500">{{ $companyAddress }}</p>
                    </div>
                @endif
            </div>

            @if(count($workArea) > 0)
                <div class="mt-16 rounded-2xl border border-gray-100 p-8">
                    <h3 class="font-semibold">Werkgebied</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($workArea as $area)
                            <span class="rounded-full bg-gray-50 px-4 py-1.5 text-sm text-gray-600">{{ $area['question'] }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 py-8">
        <div class="mx-auto max-w-6xl px-6 text-center">
            <p class="text-sm text-gray-400">&copy; {{ date('Y') }} {{ $companyName }}. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    {{-- Chat Widget --}}
    <livewire:chat-widget />
</div>
```

- [ ] **Step 4: Update routes**

Replace content of `routes/web.php`:

```php
<?php

use App\Livewire\Pages\Homepage;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class);
```

- [ ] **Step 5: Verify homepage loads**

```bash
php artisan serve
```

Visit `http://localhost:8000` — should see the onepager with hero, empty sections (no knowledge entries yet), and the chat widget button.

- [ ] **Step 6: Commit**

```bash
git add app/Livewire/ resources/views/ routes/web.php
git commit -m "feat: add homepage onepager and chat widget with Apple-inspired design"
```

---

## Task 15: Database Seeder for Demo Data

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Create seeder**

Replace `database/seeders/DatabaseSeeder.php`:

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\KnowledgeEntry;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pilot.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Monteur
        User::create([
            'name' => 'Jan Monteur',
            'email' => 'monteur@pilot.test',
            'password' => bcrypt('password'),
            'role' => 'monteur',
            'phone' => '06-12345678',
        ]);

        // Settings
        Setting::set('company_name', 'Warmte & Koeling BV');
        Setting::set('company_address', 'Industrieweg 42, 3500 AA Utrecht');
        Setting::set('company_phone', '030-1234567');
        Setting::set('company_email', 'info@warmteenkoeling.nl');
        Setting::set('ai_greeting', 'Welkom bij Warmte & Koeling BV! Hoe kan ik u helpen?');
        Setting::set('ai_tone', 'Vriendelijk, professioneel en behulpzaam. Gebruik u in plaats van je.');
        Setting::set('voice_transfer_number', '06-12345678');
        Setting::set('notification_emails', 'admin@pilot.test');

        // Kennisbank: Diensten
        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'CV-ketel onderhoud & reparatie',
            'answer' => 'Wij verzorgen jaarlijks onderhoud, storingen en reparaties aan alle merken CV-ketels. Inclusief storing buiten kantooruren.',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'Airconditioning',
            'answer' => 'Installatie, onderhoud en reparatie van airconditioningsystemen voor woning en bedrijf. Alle bekende merken.',
            'sort_order' => 2,
        ]);

        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'Zonnepanelen',
            'answer' => 'Advies, installatie en onderhoud van zonnepanelen. Wij verzorgen het complete traject van offerte tot oplevering.',
            'sort_order' => 3,
        ]);

        KnowledgeEntry::create([
            'category' => 'diensten',
            'question' => 'Vloerverwarming',
            'answer' => 'Aanleg en onderhoud van vloerverwarmingssystemen bij nieuwbouw en renovatie.',
            'sort_order' => 4,
        ]);

        // Kennisbank: FAQ
        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Wat kost een CV-ketel onderhoudsbeurt?',
            'answer' => 'Een standaard onderhoudsbeurt kost vanaf 89 euro inclusief BTW. De exacte prijs hangt af van het type ketel.',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Hoe snel kan een monteur langskomen bij een storing?',
            'answer' => 'Bij urgente storingen (geen verwarming, gaslucht) streven wij naar een bezoek binnen 4 uur. Voor niet-urgente storingen meestal binnen 1-2 werkdagen.',
            'sort_order' => 2,
        ]);

        KnowledgeEntry::create([
            'category' => 'faq',
            'question' => 'Zijn jullie ook in het weekend bereikbaar?',
            'answer' => 'Ja, onze AI-assistent is 24/7 bereikbaar. Voor urgente storingen hebben wij een weekenddienst. Reguliere afspraken plannen wij op werkdagen.',
            'sort_order' => 3,
        ]);

        // Kennisbank: Bedrijfsinfo
        KnowledgeEntry::create([
            'category' => 'bedrijfsinfo',
            'question' => 'Over Warmte & Koeling BV',
            'answer' => 'Warmte & Koeling BV is een installatietechnisch bedrijf gespecialiseerd in verwarming, koeling en duurzame energie. Met meer dan 15 jaar ervaring staan wij bekend om vakmanschap en betrouwbaarheid.',
            'sort_order' => 1,
        ]);

        // Kennisbank: Werkgebied
        KnowledgeEntry::create([
            'category' => 'werkgebied',
            'question' => 'Utrecht',
            'answer' => 'Gemeente Utrecht en omstreken',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'werkgebied',
            'question' => 'Amersfoort',
            'answer' => 'Gemeente Amersfoort en omstreken',
            'sort_order' => 2,
        ]);

        KnowledgeEntry::create([
            'category' => 'werkgebied',
            'question' => 'Hilversum',
            'answer' => 'Gemeente Hilversum en omstreken',
            'sort_order' => 3,
        ]);

        // Kennisbank: Prijzen
        KnowledgeEntry::create([
            'category' => 'prijzen',
            'question' => 'Voorrijkosten',
            'answer' => '35 euro voorrijkosten binnen ons werkgebied.',
            'sort_order' => 1,
        ]);

        KnowledgeEntry::create([
            'category' => 'prijzen',
            'question' => 'Uurtarief',
            'answer' => 'Ons uurtarief is 65 euro exclusief BTW voor reguliere werkzaamheden.',
            'sort_order' => 2,
        ]);
    }
}
```

- [ ] **Step 2: Run seeder**

```bash
php artisan db:seed
```

- [ ] **Step 3: Verify seeded data in Filament**

Visit `http://localhost:8000/admin`, login with `admin@pilot.test` / `password`.
Check Kennisbank — should have 12 entries.
Check Monteurs — should have 2 users.
Check Settings — should be pre-filled.

- [ ] **Step 4: Verify homepage shows content**

Visit `http://localhost:8000` — should show "Warmte & Koeling BV" with diensten, over ons, contact, werkgebied.

- [ ] **Step 5: Commit**

```bash
git add database/seeders/
git commit -m "feat: add database seeder with demo data for installatiebedrijf"
```

---

## Task 16: Tailwind & Vite Configuration

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`
- Modify: `vite.config.js`

- [ ] **Step 1: Ensure TailwindCSS content paths include Livewire and Filament**

Check and update `tailwind.config.js` to include:

```js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './app/Livewire/**/*.php',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
```

- [ ] **Step 2: Ensure app.css has Tailwind directives**

`resources/css/app.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

- [ ] **Step 3: Ensure app.js imports Echo for Reverb**

Check `resources/js/app.js` includes Echo setup (should be auto-configured by `reverb:install`). If not:

```js
import './bootstrap';
```

And `resources/js/bootstrap.js` should have the Echo/Reverb config from `reverb:install`.

- [ ] **Step 4: Build assets and verify**

```bash
npm install && npm run build
```

- [ ] **Step 5: Commit**

```bash
git add tailwind.config.js resources/css/ resources/js/ vite.config.js package.json
git commit -m "feat: configure TailwindCSS, Vite, and Reverb broadcasting"
```

---

## Task 17: Feature Tests

**Files:**
- Create: `tests/Feature/Models/ConversationTest.php`
- Create: `tests/Feature/Models/TicketTest.php`
- Create: `tests/Feature/Services/KnowledgeBaseServiceTest.php`
- Create: `tests/Feature/Jobs/ProcessChatMessageTest.php`
- Create: `tests/Feature/Livewire/ChatWidgetTest.php`
- Create: `tests/Feature/Livewire/HomepageTest.php`

- [ ] **Step 1: Configure test database**

Add to `phpunit.xml` in `<php>` section:

```xml
<env name="DB_CONNECTION" value="pgsql"/>
<env name="DB_DATABASE" value="pilot_test"/>
<env name="QUEUE_CONNECTION" value="sync"/>
```

Create test database:

```bash
createdb pilot_test
```

- [ ] **Step 2: Create ConversationTest**

`tests/Feature/Models/ConversationTest.php`:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use App\Enums\MessageRole;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversation_can_be_created_with_chat_channel(): void
    {
        $conversation = Conversation::create([
            'channel' => Channel::Chat,
            'status' => ConversationStatus::Active,
        ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'channel' => 'chat',
            'status' => 'active',
        ]);
    }

    public function test_conversation_has_messages(): void
    {
        $conversation = Conversation::create([
            'channel' => Channel::Chat,
            'status' => ConversationStatus::Active,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => MessageRole::User,
            'content' => 'Hallo',
        ]);

        $this->assertCount(1, $conversation->messages);
    }

    public function test_conversation_belongs_to_contact(): void
    {
        $contact = Contact::create(['name' => 'Test Contact']);

        $conversation = Conversation::create([
            'contact_id' => $contact->id,
            'channel' => Channel::Chat,
            'status' => ConversationStatus::Active,
        ]);

        $this->assertEquals($contact->id, $conversation->contact->id);
    }
}
```

- [ ] **Step 3: Create TicketTest**

`tests/Feature/Models/TicketTest.php`:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Contact;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_created(): void
    {
        $contact = Contact::create(['name' => 'Jan de Vries']);

        $ticket = Ticket::create([
            'contact_id' => $contact->id,
            'type' => TicketType::Storing,
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Open,
            'subject' => 'CV-ketel storing',
            'description' => 'Ketel maakt raar geluid',
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'type' => 'storing',
            'priority' => 'high',
            'status' => 'open',
        ]);
    }

    public function test_ticket_belongs_to_contact(): void
    {
        $contact = Contact::create(['name' => 'Test']);

        $ticket = Ticket::create([
            'contact_id' => $contact->id,
            'type' => TicketType::Overig,
            'priority' => TicketPriority::Low,
            'status' => TicketStatus::Open,
            'subject' => 'Test',
            'description' => 'Test',
        ]);

        $this->assertEquals('Test', $ticket->contact->name);
    }
}
```

- [ ] **Step 4: Create KnowledgeBaseServiceTest**

`tests/Feature/Services/KnowledgeBaseServiceTest.php`:

```php
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
```

- [ ] **Step 5: Create HomepageTest**

`tests/Feature/Livewire/HomepageTest.php`:

```php
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
```

- [ ] **Step 6: Create ChatWidgetTest**

`tests/Feature/Livewire/ChatWidgetTest.php`:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\ChatWidget;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_widget_renders(): void
    {
        Setting::set('ai_greeting', 'Welkom!');

        Livewire::test(ChatWidget::class)
            ->assertSee('Welkom!');
    }

    public function test_chat_widget_toggles_open(): void
    {
        Livewire::test(ChatWidget::class)
            ->assertSet('isOpen', false)
            ->call('toggle')
            ->assertSet('isOpen', true);
    }

    public function test_chat_widget_creates_conversation_on_first_message(): void
    {
        Livewire::test(ChatWidget::class)
            ->set('input', 'Hallo')
            ->call('sendMessage')
            ->assertSet('input', '')
            ->assertNotNull(fn ($component) => $component->conversationId);
    }

    public function test_chat_widget_ignores_empty_messages(): void
    {
        Livewire::test(ChatWidget::class)
            ->set('input', '   ')
            ->call('sendMessage')
            ->assertNull(fn ($component) => $component->conversationId);
    }
}
```

- [ ] **Step 7: Run tests**

```bash
php artisan test
```

Expected: All tests pass.

- [ ] **Step 8: Commit**

```bash
git add tests/
git commit -m "feat: add feature tests for models, services, and Livewire components"
```

---

## Task 18: Voice Webhook Test

**Files:**
- Create: `tests/Feature/Api/VoiceWebhookTest.php`

- [ ] **Step 1: Create VoiceWebhookTest**

`tests/Feature/Api/VoiceWebhookTest.php`:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Conversation;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoiceWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_accepts_valid_payload(): void
    {
        $response = $this->postJson('/api/voice/webhook', [
            'caller_name' => 'Jan de Vries',
            'caller_phone' => '06-12345678',
            'summary' => 'CV-ketel storing',
            'type' => 'storing',
            'urgency' => 'high',
            'transcript' => [
                ['role' => 'assistant', 'content' => 'Goedemorgen, waarmee kan ik u helpen?'],
                ['role' => 'user', 'content' => 'Mijn CV-ketel doet het niet meer'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_webhook_creates_conversation_and_ticket(): void
    {
        $this->postJson('/api/voice/webhook', [
            'caller_name' => 'Test Persoon',
            'caller_phone' => '06-99999999',
            'summary' => 'Airco kapot',
            'type' => 'storing',
            'urgency' => 'medium',
            'transcript' => [
                ['role' => 'user', 'content' => 'Mijn airco doet het niet'],
            ],
        ]);

        $this->assertDatabaseHas('conversations', ['channel' => 'voice']);
        $this->assertDatabaseHas('contacts', ['phone' => '06-99999999']);
        $this->assertDatabaseHas('tickets', ['subject' => 'Airco kapot']);
    }

    public function test_webhook_rejects_invalid_type(): void
    {
        $response = $this->postJson('/api/voice/webhook', [
            'type' => 'invalid_type',
        ]);

        $response->assertStatus(422);
    }
}
```

- [ ] **Step 2: Run tests**

```bash
php artisan test tests/Feature/Api/VoiceWebhookTest.php
```

Expected: All 3 tests pass.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Api/
git commit -m "feat: add voice webhook endpoint tests"
```

---

## Task 19: Final Integration Check

- [ ] **Step 1: Fresh migrate and seed**

```bash
php artisan migrate:fresh --seed
```

Expected: No errors.

- [ ] **Step 2: Run full test suite**

```bash
php artisan test
```

Expected: All tests pass.

- [ ] **Step 3: Start all services and verify manually**

Terminal 1:
```bash
php artisan serve
```

Terminal 2:
```bash
php artisan reverb:start
```

Terminal 3:
```bash
php artisan horizon
```

Terminal 4:
```bash
npm run dev
```

**Verify:**
1. `http://localhost:8000` — Onepager loads with company info, diensten, contact
2. Chat widget button visible, opens chat panel, shows greeting
3. `http://localhost:8000/admin` — Login with `admin@pilot.test` / `password`
4. Dashboard shows stats widgets
5. Kennisbank has 12 entries
6. Monteurs shows 2 users
7. Instellingen page loads with pre-filled data

- [ ] **Step 4: Final commit**

```bash
git add -A
git commit -m "feat: complete Pilot AI Front Office MVP - ready for testing"
```
