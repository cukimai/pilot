# Pilot - AI Front Office voor Installatiebedrijven

## Overzicht

Een AI-gestuurd softwarepakket dat de volledige frontoffice van een installatiebedrijf overneemt. Het systeem vangt websitebezoekers op via chat, neemt telefoontjes aan via voice AI, en zet alles automatisch om naar tickets die slim worden afgehandeld.

**Doelgroep MVP:** Installatiebedrijven (CV-ketels, airco, zonnepanelen)
**Architectuur:** Laravel monoliet (single-tenant)
**Stack:** Laravel + Livewire + TailwindCSS + FilamentPHP

---

## Stack & Tooling

| Component | Technologie |
|-----------|------------|
| Backend | Laravel 12 |
| Frontend (publiek) | Livewire + TailwindCSS |
| Admin panel | FilamentPHP 3 |
| Database | PostgreSQL |
| Cache & Queues | Redis |
| WebSockets | Laravel Reverb |
| Queue management | Laravel Horizon |
| Chat AI | Claude API (Anthropic) - claude-sonnet-4-6 |
| Voice AI | Vapi.ai |
| Agenda | Google Calendar API |
| Mail | Laravel Mail (SMTP/Mailgun/Postmark) |
| Hosting | Laravel Forge of Ploi op DigitalOcean/Hetzner |

---

## Database Structuur

### conversations

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| contact_id | uuid, nullable | Kan anoniem starten |
| channel | enum: chat, voice, email | Kanaal van het gesprek |
| status | enum: active, closed | Status |
| summary | text, nullable | AI-gegenereerde samenvatting |
| created_at | timestamp | |
| updated_at | timestamp | |

### messages

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| conversation_id | uuid, FK | |
| role | enum: user, assistant, system | |
| content | text | Berichtinhoud |
| metadata | json, nullable | Extra data (tool calls, etc.) |
| created_at | timestamp | |

### tickets

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| conversation_id | uuid, nullable, FK | Gekoppeld gesprek |
| contact_id | uuid, FK | |
| type | enum: storing, afspraak, offerte, overig | |
| priority | enum: low, medium, high, urgent | |
| status | enum: open, in_progress, scheduled, resolved, closed | |
| subject | string | |
| description | text | |
| assigned_to | uuid, nullable, FK (users) | Toegewezen monteur |
| scheduled_at | timestamp, nullable | Geplande afspraak |
| created_at | timestamp | |
| updated_at | timestamp | |

### contacts

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| name | string | |
| email | string, nullable | |
| phone | string, nullable | |
| address | string, nullable | |
| city | string, nullable | |
| postal_code | string, nullable | |
| notes | json, nullable | Vrije notities |
| created_at | timestamp | |
| updated_at | timestamp | |

### knowledge_entries

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| category | enum: diensten, faq, bedrijfsinfo, werkgebied, prijzen | |
| question | string | De vraag / titel |
| answer | text | Het antwoord / inhoud |
| is_active | boolean, default: true | |
| sort_order | integer, default: 0 | |
| created_at | timestamp | |
| updated_at | timestamp | |

### users

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| name | string | |
| email | string | |
| password | string | |
| role | enum: admin, monteur | |
| phone | string, nullable | |
| google_calendar_id | string, nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### appointments

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| ticket_id | uuid, FK | |
| contact_id | uuid, FK | |
| user_id | uuid, FK (monteur) | |
| scheduled_at | timestamp | |
| duration_minutes | integer, default: 60 | |
| google_event_id | string, nullable | |
| status | enum: planned, confirmed, completed, cancelled | |
| notes | text, nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### notification_log

| Kolom | Type | Beschrijving |
|-------|------|-------------|
| id | uuid | Primary key |
| type | string | Notificatietype (email) |
| recipient | string | |
| subject | string | |
| body | text | |
| related_type | string, nullable | Polymorfe relatie |
| related_id | uuid, nullable | |
| sent_at | timestamp | |

---

## AI Chat Flow

### Architectuur

De chat widget is een Livewire component op de onepager. Berichten worden via een queue job verwerkt om timeouts te voorkomen. Real-time updates via Laravel Reverb.

### Flow

1. Bezoeker opent chat widget (floating button rechtsonder)
2. Livewire component maakt `conversation` aan (channel: chat)
3. Bezoeker typt bericht → `message` opgeslagen (role: user)
4. `ProcessChatMessage` job dispatched naar queue
5. Job bouwt prompt op:
   - System prompt: "Je bent de digitale assistent van [bedrijf]..."
   - Kennisbank entries (alle actieve `knowledge_entries`)
   - Conversatiehistorie (alle `messages` van deze conversation)
   - Tool definities (zie hieronder)
6. Claude API call met tool use
7. Tool calls worden uitgevoerd (ticket aanmaken, afspraak plannen, etc.)
8. Response opgeslagen als `message` (role: assistant)
9. Livewire event broadcast via Reverb → chat UI update

### AI Tools (Claude Tool Use)

De AI kan deze functies aanroepen tijdens een gesprek:

**create_ticket(type, priority, subject, description)**
- Maakt een nieuw ticket aan
- Triggert `SendTicketNotification` job

**schedule_appointment(date, time, duration, description)**
- Maakt afspraak aan gekoppeld aan ticket
- Triggert `SyncAppointmentToGoogle` job

**collect_contact_info(name, phone, email, address, city, postal_code)**
- Maakt of updatet contact record
- Koppelt aan huidige conversation

**escalate_to_human(reason)**
- Markeert conversation als "needs_human_attention"
- Stuurt urgente e-mail notificatie

### System Prompt Structuur

```
Je bent de digitale assistent van {bedrijfsnaam}.
Je helpt klanten met vragen over diensten, het melden van storingen,
het plannen van afspraken en het aanvragen van offertes.

Bedrijfsinformatie:
{kennisbank entries - categorie: bedrijfsinfo}

Diensten:
{kennisbank entries - categorie: diensten}

Veelgestelde vragen:
{kennisbank entries - categorie: faq}

Werkgebied:
{kennisbank entries - categorie: werkgebied}

Prijsindicaties:
{kennisbank entries - categorie: prijzen}

Instructies:
- Wees vriendelijk, professioneel en behulpzaam
- Beantwoord vragen op basis van bovenstaande informatie
- Als iemand een storing meldt: vraag contactgegevens en maak een ticket
- Als iemand een afspraak wil: check beschikbaarheid en plan in
- Als iemand een offerte wil: verzamel info en maak een offerte-ticket
- Als je het antwoord niet weet: escaleer naar een medewerker
- Communiceer in het Nederlands
```

---

## AI Voice Flow

### Architectuur

Vapi.ai beheert het telefoonnummer en de voice AI. Bij elk gesprek stuurt Vapi webhooks naar onze Laravel app voor verwerking.

### Flow

1. Inkomend telefoontje → Vapi.ai neemt op
2. AI begroeting: "Goedemorgen, u spreekt met de digitale assistent van [bedrijf]. Waarmee kan ik u helpen?"
3. Beller beschrijft probleem
4. AI bepaalt type + urgentie

**Niet-urgent (offerte, vraag, afspraak):**
- Vraagt contactinfo op (naam, telefoon, adres)
- Vapi stuurt webhook → Laravel `ProcessVoiceWebhook` job
- Conversation + messages (transcript) aangemaakt
- Ticket aangemaakt
- "Uw melding is genoteerd, we nemen zo snel mogelijk contact met u op"
- E-mail notificatie naar admin

**Urgent (geen verwarming in winter, gaslucht, lekkage):**
- Ticket aangemaakt (priority: urgent)
- "Ik verbind u door met een monteur"
- Vapi transfer call naar configureerbaar monteur-nummer
- E-mail notificatie naar admin

### Vapi Configuratie

- Vapi host het telefoonnummer
- Server URL (webhook): `POST /api/voice/webhook`
- Kennisbank als system prompt aan Vapi
- Transfer-nummers configureerbaar in Filament instellingen
- Transcript wordt opgeslagen als messages

### Niet in v1

- Geen volledige conversatie-AI aan de telefoon
- Geen real-time agenda check tijdens het gesprek
- Geen automatisch terugbellen

---

## Filament Dashboard

### Panel Structuur

```
Filament Admin Panel (/admin)
│
├── Dashboard
│    ├── StatsOverview widget: open tickets, afspraken vandaag, nieuwe contacten
│    ├── LatestTickets widget: 5 nieuwste tickets
│    └── TodayAppointments widget: afspraken van vandaag
│
├── Tickets (Resource)
│    ├── List: tabel met subject, type (badge), priority (badge), status, contact, created_at
│    ├── Filters: type, priority, status, assigned_to
│    ├── View: volledige ticket detail + conversatie-historie (RelationManager)
│    ├── Actions: status wijzigen, toewijzen aan monteur, afspraak plannen
│    └── Bulk actions: sluiten, toewijzen
│
├── Gesprekken (Resource)
│    ├── List: channel, contact, status, message count, created_at
│    ├── Filter: channel (chat|voice)
│    └── View: volledige transcript, gekoppeld ticket
│
├── Contacten (Resource, full CRUD)
│    ├── List + zoeken op naam/telefoon/email
│    ├── Detail: alle tickets + gesprekken (RelationManagers)
│    └── Notities veld
│
├── Afspraken (Resource)
│    ├── List: datum, contact, monteur, status
│    ├── Calendar widget (optioneel)
│    ├── Google Calendar sync status indicator
│    └── Actions: bevestigen, annuleren, verplaatsen
│
├── Kennisbank (Resource, full CRUD)
│    ├── Grouped by categorie
│    ├── Per entry: vraag + antwoord (rich text editor)
│    ├── Toggle: actief/inactief
│    └── Drag & drop sortering
│
├── Monteurs (Resource, full CRUD)
│    ├── Naam, email, telefoon
│    └── Google Calendar ID
│
└── Instellingen (Custom page)
     ├── Bedrijfsgegevens: naam, adres, telefoon, email
     ├── AI: tone of voice, begroetingstekst
     ├── Voice: doorverbind-nummers bij urgentie
     ├── Notificaties: email-adressen voor ticket alerts
     └── Google Calendar: OAuth koppeling
```

### Filament Specifiek

- Resources met ViewAction (niet EditAction) voor tickets en gesprekken
- RelationManagers voor contact → tickets/gesprekken
- Custom Filament widgets op dashboard
- Filament Actions voor ticket-workflow (status wijzigen, toewijzen)
- Settings package of custom model voor instellingen

---

## Publieke Onepager + Chat Widget

### Design Principes

- **Apple-achtig:** strak wit, minimalistisch, veel witruimte
- **Typografie:** clean sans-serif (Inter of SF Pro via Tailwind)
- **Kleuren:** wit + lichtgrijs + 1 accentkleur (configureerbaar)
- **Schaduwen:** subtiel, geen harde randen
- **Mobile-first:** responsive met TailwindCSS

### Pagina Structuur

**Hero sectie**
- Bedrijfsnaam + tagline
- Telefoonnummer (click-to-call op mobile)
- CTA button: "Stel uw vraag" → opent chat widget

**Diensten sectie**
- Dynamisch uit kennisbank (categorie: diensten)
- Clean grid/cards layout
- Iconen per diensttype

**Over ons sectie**
- Kort blokje bedrijfsinfo uit kennisbank

**Contact sectie**
- Adres, telefoon, email
- Werkgebied
- Openingstijden + "AI assistent 24/7 beschikbaar"

### Chat Widget

- Floating button rechtsonder (vast gepositioneerd)
- Slide-up panel bij openen
- Berichtgeschiedenis binnen sessie (session-based, geen login)
- Typing indicator (animated dots) terwijl AI nadenkt
- Berichten met timestamps
- "Powered by [merk]" footer
- Livewire component met eigen state
- Real-time updates via Reverb

### Technisch

- Livewire full-page component voor de onepager
- Content dynamisch uit `knowledge_entries` tabel
- Chat widget als apart nested Livewire component
- Server-side rendered (SEO-friendly)
- Geen externe JS dependencies voor de chat

---

## Integraties

### Claude API (Anthropic)

- Model: claude-sonnet-4-6 (snel, kostenefficient)
- Tool use voor gestructureerde acties
- Kennisbank als context in system prompt
- HTTP client of Anthropic PHP SDK

### Vapi.ai

- Telefoonnummer hosting
- Voice AI met system prompt uit kennisbank
- Webhooks naar `POST /api/voice/webhook`
- Call transfer functionaliteit bij urgentie
- Configuratie van transfer-nummers via Filament

### Google Calendar API

- OAuth2 koppeling per monteur
- Afspraken sync: Laravel → Google Calendar
- Beschikbaarheid check (free/busy API)
- `google/apiclient` package

### Mail

- Laravel Notifications systeem
- Mailable templates voor ticket notificaties
- Configureerbare ontvangers via instellingen

---

## Queue Jobs

| Job | Trigger | Actie |
|-----|---------|-------|
| ProcessChatMessage | Nieuw chatbericht | Stuurt naar Claude API, slaat response op, broadcast via Reverb |
| ProcessVoiceWebhook | Vapi webhook | Maakt conversation + ticket aan, slaat transcript op |
| SyncAppointmentToGoogle | Afspraak aangemaakt/gewijzigd | Pusht naar Google Calendar |
| SendTicketNotification | Nieuw ticket | Stuurt e-mail naar admin |
| SendUrgentAlert | Urgent ticket | Stuurt directe e-mail notificatie |

---

## Niet in Scope (v1)

- Multi-tenancy
- WhatsApp integratie
- HR module (sollicitaties/onboarding)
- Sales opvolging
- Facturatie
- Document upload / RAG met embeddings
- Volledige voice conversatie-AI
- Automatisch terugbellen
- Real-time agenda check in voice gesprekken
- Monteur beschikbaarheidsplanning (uitgebreid)
