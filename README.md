# Images Manager

A small PHP + MySQL web app where users register, log in, and manage their own
images (upload, rename, delete). Packaged with Docker so anyone can run it with
one command.

## Tech stack
- PHP 8.3 (Apache), `mysqli`
- MySQL 8.0
- Docker & Docker Compose
- GitHub Actions (CI)

## Features
- Sign up / sign in (passwords hashed with bcrypt)
- Upload images (JPEG, PNG, GIF, WebP, max 5 MB, validated by file content)
- Rename and delete your own images only
- Prepared statements, output escaping, and CSRF tokens throughout

## Project structure
```
images-manager/
├── src/                 # PHP source code
│   └── uploads/         # user uploads (git-ignored, Docker volume)
├── db/init.sql          # database schema (auto-loaded by MySQL container)
├── docs/
│   ├── screenshots/     # assignment screenshots
│   └── notes.md         # technical note
├── Dockerfile
├── docker-compose.yml
├── Makefile
├── .env.example
├── .dockerignore
└── .gitignore
```

## Run it (Docker)

Requirements: Docker Desktop (or Docker Engine + Compose plugin).

```bash
git clone https://github.com/fatmatabash054/images-manager.git
cd images-manager
cp .env.example .env        # optional - defaults work for a demo
docker compose up -d --build
```

Open **http://localhost:8080** and create an account.

Or with the Makefile: `make up`

## Stop and clean up

```bash
docker compose down        # stop containers, keep data
docker compose down -v     # stop and delete database + uploads
```

Makefile shortcuts: `make down`, `make clean`, `make logs`.

## Configuration

All settings are environment variables (copy `.env.example` to `.env`):

| Variable | Default | Meaning |
|---|---|---|
| `WEB_PORT` | `8080` | Port exposed on your machine |
| `DB_NAME` | `image_manager` | Database name |
| `DB_USER` | `imguser` | App database user |
| `DB_PASSWORD` | `imgpass` | App database password |
| `DB_ROOT_PASSWORD` | `rootpass` | MySQL root password |

> Change the passwords before using this anywhere other than your own machine.

## How to test it
1. Go to http://localhost:8080/register.php and create an account.
2. Upload a JPG/PNG - it appears in the grid.
3. Click **Edit** to rename it, **Delete** to remove it.
4. Check container health: `docker compose ps` (both services should be `healthy`).

## CI
`.github/workflows/docker.yml` builds the image, starts the stack, waits for the
healthcheck, and requests the login page on every push / pull request.

## Attribution
This is my own project, built as coursework (OS Lab - Assignment #2).
