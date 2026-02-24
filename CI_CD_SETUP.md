# CI/CD Setup

This repo now includes:
- `/.github/workflows/ci.yml`: lint checks for PHP API files.
- `/.github/workflows/cd.yml`: deploys to your DigitalOcean droplet after merges to `main`.

## 1) GitHub repository secrets

In GitHub: `Settings -> Secrets and variables -> Actions -> New repository secret`

Add:
- `DO_HOST`: droplet IP or domain (example: `schneidinecop4331.com`)
- `DO_USER`: SSH user (example: `root`)
- `DO_SSH_KEY`: private SSH key content used by GitHub Actions
- `DO_PORT`: SSH port (usually `22`)
- `DEPLOY_PATH`: repo path on server (example: `/var/www/html/api`)

## 2) Server one-time prep

Run on droplet:

```bash
cd /var/www/html/api
git remote -v
git status -sb
```

Confirm:
- Remote points to `https://github.com/SimonOcampo/COP4331smallproject.git`
- `main` is clean and tracks `origin/main`
- Production config exists at `api/config.php` (not tracked in git)

## 3) Branch protection (recommended)

In GitHub: `Settings -> Branches -> Add rule` for `main`

Enable:
- Require a pull request before merging
- Require status checks to pass
- Select check: `PHP Lint`

## 4) Deployment behavior

On every push to `main`, CD runs:

```bash
cd "$DEPLOY_PATH"
git fetch origin
git checkout main
git reset --hard origin/main
git clean -fd -e api/config.php
```

This keeps deployed code identical to GitHub `main` while preserving untracked `api/config.php`.
