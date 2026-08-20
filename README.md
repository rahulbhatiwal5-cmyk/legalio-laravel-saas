# Legalio — AI-Powered Legal Document Generation SaaS

Legalio is a Laravel-based SaaS platform for generating legal documents through an AI-powered questionnaire and contract-generation workflow.

Administrators can configure legal document types, jurisdictions, questions, clauses, and contract templates. Users provide the required information through dynamically generated questionnaires, and the platform uses OpenAI to generate and populate legal document content.

The platform also includes subscription management, one-time purchases, payment integrations, document credits, free-trial access, authentication, admin dashboards, and document-generation workflows.

---

## 🚀 Key Features

### 🤖 AI-Powered Document Generation

* Generate legal document questionnaires dynamically.
* Generate contract templates and document content using OpenAI.
* Build questionnaires based on document type and legal requirements.
* Process user answers and generate contract content.
* Support dynamic questions, options, conditions, and question flows.
* Generate document placeholders that users can complete before purchasing or subscribing.
* Maintain previous AI outputs and generation context where required.
* Support AI-generated clauses and standard clause analysis.

### 📋 Dynamic Questionnaire Workflow

Legalio uses a multi-step document generation workflow:

```text
Select Document
       ↓
Generate Questionnaire
       ↓
Answer Questions
       ↓
Process Conditional Questions
       ↓
Generate Contract
       ↓
Complete Contract Placeholders
       ↓
Purchase / Subscribe
       ↓
Download Document
```

The questionnaire system supports conditional logic, allowing questions to depend on previous answers.

Example:

```text
Question 1
    ↓
User selects an option
    ↓
Condition evaluated
    ↓
Relevant next question displayed
```

This allows different document types to have dynamic and flexible question flows.

---

## 📄 Document Generation

The platform supports a complete legal document generation workflow.

### Admin Side

Administrators can:

* Create and manage document types.
* Define document questions.
* Configure question options.
* Configure conditional question flows.
* Define standard clauses.
* Configure contract templates.
* Manage document-related settings.
* Configure pricing and subscription plans.
* Manage document-generation workflows.

### User Side

Users can:

* Select a document.
* Complete the generated questionnaire.
* Review generated contract content.
* Complete required placeholders.
* Purchase or subscribe to access the document.
* Download eligible documents.

---

## 🧠 OpenAI Integration

OpenAI is used as part of the document-generation workflow.

The application builds prompts using document information, questionnaire data, technical specifications, previous outputs, clauses, and other required context before requesting generated content.

High-level workflow:

```text
Document Configuration
        ↓
Questionnaire Data
        ↓
User Answers
        ↓
Prompt Construction
        ↓
OpenAI API
        ↓
Generated Content
        ↓
Contract Editor
```

The platform also maintains generation logs and relevant AI response information for the document workflow.

---

## 📥 Document Import / AI Bypass Workflow

Legalio includes an admin-side document import workflow that can be used to define documents without going through the normal AI generation process.

Administrators can import:

* Questions JSON
* Contract JSON

The import can be performed using:

1. JSON file upload
2. JSON pasted directly into the interface

Workflow:

```text
Upload Document
       ↓
Choose Import Method
       ↓
Upload JSON / Paste JSON
       ↓
Validate JSON
       ↓
Load Questions
       ↓
Load Contract
       ↓
Continue Editing
       ↓
Save Using Existing Document Workflow
```

This provides an alternative workflow for documents whose questionnaire and contract structure are already defined.

---

## 💳 Payment & Subscription System

Legalio supports both subscription-based and one-time payment workflows.

### Stripe

Stripe is integrated for:

* Subscription payments
* One-time payments
* Pricing plans
* Recurring billing
* Payment status handling
* Subscription lifecycle management
* Webhook processing

Relevant subscription events are processed through webhooks to keep application subscription state synchronized with Stripe.

### PayPal

PayPal is also integrated for subscription payments.

The application handles PayPal subscription lifecycle events and synchronizes subscription/payment status with the application.

---

## 🔄 Subscription Workflow

A simplified subscription flow:

```text
Pricing Page
     ↓
Select Plan
     ↓
Choose Payment Provider
     ↓
Stripe / PayPal
     ↓
Payment
     ↓
Subscription Created
     ↓
Webhook
     ↓
Update Subscription
     ↓
Update User Plan / Credits
     ↓
Document Access
```

Subscription records maintain information such as:

* Plan
* Price
* Currency
* Billing interval
* Number of months
* Payment provider identifiers
* Subscription status
* Trial information
* Allowed users
* Access settings

---

## 🎁 Free Trial

Legalio includes a free-trial workflow.

During the trial:

* Users can access eligible documents.
* Users can view the generated contract.
* Document downloads are restricted during trial access.
* Attempting to download a restricted document can redirect the user toward available paid plans.
* After purchasing a paid plan, the trial access can be replaced by the paid subscription.

The trial functionality is implemented as an application-level workflow rather than requiring a separate Stripe free-trial product.

---

## 💰 Credits & Document Access

The platform supports document credits and credit transactions.

Relevant application concepts include:

```text
User
  ↓
User Plan
  ↓
User Credits
  ↓
Credit Transactions
  ↓
Document Access
```

Credits and subscription information are updated as part of successful payment/subscription workflows.

---

## 👤 Authentication & User Management

Legalio includes secure authentication and user-related workflows.

Features include:

* User authentication
* User account management
* Subscription-aware access
* Document access control
* Guest document workflows
* Session-based handling for guest users
* User-specific document management

---

## 🛠️ Admin Dashboard

The admin dashboard provides management functionality for the platform.

Administrators can manage:

* Users
* Documents
* Document questions
* Contract templates
* Standard clauses
* AI prompts
* Subscription plans
* Payment configuration
* Site settings
* Product/document information
* Notifications
* Metadata
* Other application configuration

---

## 🔗 Third-Party Integrations

Legalio integrates with external services including:

| Service               | Purpose                                          |
| --------------------- | ------------------------------------------------ |
| OpenAI                | AI-powered questionnaire and contract generation |
| Stripe                | Payments and subscriptions                       |
| PayPal                | Subscription payments                            |
| Google Cloud services | Supporting cloud functionality                   |

API credentials are configured through environment variables and are intentionally excluded from version control.

---

## 🏗️ Technology Stack

### Backend

* PHP
* Laravel
* MVC Architecture
* RESTful APIs
* Laravel Eloquent ORM
* Laravel Middleware
* Form Requests
* Authentication
* Events / Listeners
* Jobs
* Services

### Frontend

* Blade
* HTML5
* CSS
* JavaScript
* jQuery
* AJAX
* Bootstrap
* Vite

### Database

* MySQL
* Eloquent ORM
* Database Migrations
* Seeders

### APIs & Services

* OpenAI API
* Stripe API
* PayPal API
* Google Cloud services

### Development Tools

* Git
* GitHub
* Composer
* NPM
* Vite
* Postman
* Linux

---

## 📁 High-Level Project Structure

```text
legalio/
│
├── app/
│   ├── Console/
│   ├── Events/
│   ├── Exceptions/
│   ├── Helpers/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Jobs/
│   ├── Listeners/
│   ├── Livewire/
│   ├── Mail/
│   ├── Models/
│   ├── Notifications/
│   ├── Providers/
│   ├── Services/
│   └── View/
│
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── storage/
├── tests/
├── artisan
├── composer.json
├── package.json
└── vite.config.js
```

---

## 🔐 Environment Configuration

Sensitive credentials should never be committed to the repository.

Create a local `.env` file:

```env
APP_NAME=Legalio
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=legalio
DB_USERNAME=root
DB_PASSWORD=

OPENAI_API_KEY=

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
PAYPAL_MODE=sandbox
```

Use `.env.example` as the template for required environment variables.

> Never commit `.env`, payment credentials, private keys, cloud service-account credentials, or other secrets to GitHub.

---

## ⚙️ Local Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/legalio-laravel-saas.git
cd legalio-laravel-saas
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Create environment file

```bash
cp .env.example .env
```

Configure the database and required API credentials in `.env`.

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Run database migrations

```bash
php artisan migrate
```

If seed data is required:

```bash
php artisan db:seed
```

### 7. Build frontend assets

For development:

```bash
npm run dev
```

For production build:

```bash
npm run build
```

### 8. Start Laravel development server

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

## 🧪 Testing

Run the Laravel test suite with:

```bash
php artisan test
```

or:

```bash
./vendor/bin/phpunit
```

---

## 🔄 Webhook Architecture

Payment providers communicate with the application through webhooks.

High-level architecture:

```text
Payment Provider
      ↓
Webhook Endpoint
      ↓
Webhook Controller
      ↓
Validate Event
      ↓
Process Payment / Subscription
      ↓
Update Database
      ↓
Update User Plan
      ↓
Update Credits
      ↓
Create Subscription / Payment Logs
```

This allows the application to maintain payment and subscription state based on provider events.

---

## 📊 Core Application Concepts

Some of the important domain concepts in the application include:

```text
User
 │
 ├── Documents
 │
 ├── Subscription
 │      └── Plan
 │
 ├── Credits
 │      └── Credit Transactions
 │
 └── Payment / Subscription Logs

Document
 │
 ├── Questions
 │      ├── Options
 │      └── Conditions
 │
 └── Contract
```

This structure allows document generation, subscription access, credit management, and payment workflows to work together.

---

## 🎯 Main Business Workflow

The overall platform can be represented as:

```text
                    ┌─────────────────┐
                    │     Admin       │
                    └────────┬────────┘
                             │
              Configure Documents / Questions
                             │
                             ▼
                    ┌─────────────────┐
                    │ Legal Document  │
                    │   Definition    │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │  Questionnaire  │
                    └────────┬────────┘
                             │
                       User Answers
                             │
                             ▼
                    ┌─────────────────┐
                    │  OpenAI Prompt  │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Generated       │
                    │ Contract        │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Payment /       │
                    │ Subscription    │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Document Access │
                    └─────────────────┘
```

---

## 🔧 Architecture & Code Quality

The project follows Laravel's MVC approach and uses Laravel features such as:

* Controllers
* Eloquent Models
* Form Requests
* Middleware
* Services
* Jobs
* Events and Listeners
* Database Migrations
* API Resources
* Blade Views
* Authentication and Authorization

The codebase is structured to keep presentation, business logic, validation, database operations, and external API integrations separated where appropriate.

---

## 📌 Important Security Notes

For security reasons, this repository should never contain:

* `.env` files
* Stripe secret keys
* PayPal credentials
* OpenAI API keys
* Google Cloud private keys
* Service-account JSON files
* Database passwords
* Production credentials
* Webhook signing secrets

Use environment variables and secret-management solutions instead.

---

## 🚧 Portfolio Disclaimer

This repository represents a portfolio-oriented implementation of an AI-powered legal document generation SaaS concept based on professional Laravel development experience.

It is intended to demonstrate:

* Laravel backend development
* SaaS architecture
* AI API integration
* Payment integration
* Subscription management
* Database design
* REST API development
* Admin dashboard development
* Dynamic questionnaire workflows
* Document generation workflows

No production credentials, private keys, customer data, or other confidential information should be included in this repository.

---

## 👨‍💻 Developer

**Rahul Prajapat**

PHP Laravel Developer

### Core Skills

```text
PHP
Laravel
MySQL
REST APIs
JavaScript
Blade
jQuery
AJAX
Bootstrap
Git
GitHub
Stripe
PayPal
OpenAI API
Linux
```

---

## ⭐ Project Highlights

* AI-powered legal document generation
* Dynamic questionnaire engine
* Conditional question logic
* Contract generation workflow
* OpenAI API integration
* Stripe subscription integration
* PayPal subscription integration
* One-time payment workflow
* Free-trial access
* Credit management
* Admin document generator
* JSON document import
* Authentication and authorization
* RESTful APIs
* MySQL database
* Laravel MVC architecture
* Admin dashboard
* Payment webhook processing
* Subscription lifecycle management
