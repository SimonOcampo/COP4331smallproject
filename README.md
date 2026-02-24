# COP4331 Small Project

PHP + MySQL contact manager backend with GitHub Actions CI/CD.

## What is in this repo

- `api/`: PHP API endpoints for auth and contact CRUD
- `database/`: SQL schema and seed scripts
- `.github/workflows/ci.yml`: CI lint workflow
- `.github/workflows/cd.yml`: CD deploy workflow for DigitalOcean
- `CI_CD_SETUP.md`: deployment setup notes

## API Endpoints

All endpoints expect JSON request bodies and return JSON.

- `api/Register.php`
- `api/Login.php`
- `api/AddContact.php`
- `api/SearchContacts.php`
- `api/UpdateContact.php`
- `api/DeleteContact.php`

## Local Setup

1. Install prerequisites:
- PHP 8.x
- MySQL

2. Create database:
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

3. Configure DB connection in:
- `api/config.php`

4. Run locally from repo root:
```bash
php -S localhost:8000
```

5. Call endpoints, for example:
- `http://localhost:8000/api/Login.php`

## CI/CD in this repo

### CI (`.github/workflows/ci.yml`)

Runs on:
- Pull requests into `main`
- Pushes to `main`, `api-kamilla`, `api-schneidine`, `frontend-simon`

What it does:
- Checks out code
- Sets up PHP 8.2
- Runs `php -l` lint against `api/*.php`

### CD (`.github/workflows/cd.yml`)

Runs on:
- Push to `main` only

What it does:
- SSHes into the DigitalOcean droplet
- Changes directory to `DEPLOY_PATH`
- Force-syncs droplet repo to `origin/main`:
```bash
git fetch origin
git checkout main
git reset --hard origin/main
git clean -fd -e api/config.php
```

This preserves untracked `api/config.php` on the server.

## Important: Local vs GitHub vs Droplet

There are 3 separate copies of the project:

1. Local copy (your laptop / VS Code)
- You still run `git pull` here.

2. GitHub copy (`origin`)
- Updated when you `git push`.

3. Droplet copy (production server)
- Updated by CD on pushes to `main` (if workflow succeeds).

So CD does not replace local `git pull`; it replaces manual deploy commands on the server.

## Deployment Secrets (GitHub Actions)

Required repository secrets:
- `DO_HOST`
- `DO_USER`
- `DO_SSH_KEY`
- `DEPLOY_PATH`

See `CI_CD_SETUP.md` for setup details.

## Security Notes

- Do not commit real credentials in `api/config.php`.
- Prefer environment-specific config and GitHub secrets for deployment credentials.
