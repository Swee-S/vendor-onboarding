# Vendor Onboarding System

A Laravel-based vendor onboarding application where users create vendor records and submit them for admin approval. Sensitive data is masked unless the viewer is the creator or an admin.

---

## Tech Stack

| Layer    | Choice                        |
|----------|-------------------------------|
| Backend  | Laravel (latest stable)       |
| Frontend | Blade Templates               |
| Database | MySQL                         |
| Auth     | Email + Password (Laravel UI) |

---

## Setup Instructions

```bash
# 1. Clone the repository
git clone <your-repo-url>
cd vendor-onboarding

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Update .env with your database credentials
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 6. Run migrations and seed
php artisan migrate --seed

# 7. Start the server
php artisan serve
```

Visit: http://127.0.0.1:8000

---

## Test Accounts

| Email               | Password | Role  |
|---------------------|----------|-------|
| admin@example.com   | password | Admin |
| user1@example.com   | password | User  |
| user2@example.com   | password | User  |
| user3@example.com   | password | User  |

> Three user accounts are provided so the evaluator can verify that users cannot see each other's sensitive data.

---

## Reviewer Verification Flow

### Step 1 — Create an application
- Login as `user1@example.com` / `password`
- Click **New Application** and fill all fields
- Application saves as **Draft**

### Step 2 — Test sensitive data masking
- Login as `user2@example.com` / `password`
- Open the vendor list
- Mobile, PAN, GST, Bank Account should all be **masked**
- Login as `user1@example.com` — same fields should show **unmasked**
- Login as `admin@example.com` — all fields show **unmasked**

### Step 3 — Submit and admin review
- Login as `user1@example.com` → click **Submit**
- Login as `admin@example.com`
- Choose **Approve**, **Reject**, or **Send Back** with a reason

### Step 4 — Test sent back flow
- Admin clicks **Send Back** with a reason
- Login as `user1@example.com` — status shows sent_back with reason
- User can **Edit** and **Resubmit**

### Step 5 — Test final state lock
- Once **Approved** or **Rejected** — edit and resubmit buttons disappear
- Backend also blocks any attempt to edit or transition — returns 403

### Step 6 — Test duplicate validation
- Try creating a second application with the same Mobile or PAN
- Should show: "An active application with this mobile number already exists"

---

## Key Implementation Details

| Feature | Implementation |
|---------|---------------|
| Bank account encryption | Laravel `Crypt::encryptString()` — AES-256-CBC |
| Status transitions | `StatusTransitionService` enforced at backend |
| Authorization | `assertAdmin()` and `assertCreator()` in controller — not just UI |
| Edit restriction | Backend blocks edit unless status is draft or sent_back |
| Sensitive data masking | Model helper methods: `maskedPan()`, `maskedMobile()`, `maskedGst()`, `maskedAccountNumber()` |
| Uniqueness | One active application per mobile, PAN, GST (excludes rejected records) |

---

## Assumptions

- GST number is optional — all other fields are mandatory
- Bank account numbers are encrypted at rest — never stored as plain text
- "Active application" for uniqueness means any status except `rejected`
- Only the original creator can edit or re-submit their own application
- `Approved` and `Rejected` are final states — no further transitions allowed
- No delete functionality — not required per spec

---

## What Is Not Implemented (per spec)

- OTP / SMS verification
- Forgot password
- Email notifications
- PAN / GST government API verification
- File uploads
- Queues or background jobs
- Docker (optional bonus — not implemented)