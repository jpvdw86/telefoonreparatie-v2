# Multi-Domain Symfony Application in Docker

This project is a PHP 8.3 Symfony 7.3 web application that renders four distinct websites from a single codebase. The specific variant is selected based on an environment variable (`APP_ENV`). It runs inside a Docker container using MySQL 8 and Redis for caching.

## 🌍 Supported Domains

This application supports the following domains:

1. [telefoonreparatiebus.nl](https://telefoonreparatiebus.nl)
2. [telefoonreparatiebus.be](https://telefoonreparatiebus.be)
3. [detelefoonreparatiewinkel.nl](https://detelefoonreparatiewinkel.nl)
4. [detelefoonreparatiewinkel.be](https://detelefoonreparatiewinkel.be)

## 🚀 Features

- 🧠 Single codebase, multiple frontends, one backend / admin panel
- 🌐 Environment-based website variant rendering
- 🎨 Variant-specific templates, assets and configuration
- ⚡ Redis-based caching layer
- 🐳 Fully containerized using Docker

## 🧱 Stack

- PHP 8.3 (Caddy & FPM)
- Symfony 7.3
- MySQL 8
- Redis
- Docker + Docker Compose

## 🔧 `dev.sh` – Development Workflow Script

This project includes a helper script [`dev.sh`](./dev.sh) to streamline development tasks using Docker. It provides both an interactive menu and direct command execution for managing the application lifecycle, code quality checks, and test environments.

### 📋 Features

- Start, stop, and restart the application in Docker
- Switch between development and test environments
- Set environment-specific `DATABASE_URL` via `database.localvar`
- Run Composer & Yarn install commands
- Execute migrations and schema validations
- Run PHP CS Fixer, PHPStan, PHP Mess Detector, Prettier, and Twig CS
- Reset and seed test database environment
- Run unit tests and generate test coverage reports
- Fully rebuild services or prune Docker system

### ▶️ Interactive Menu

Run without arguments to open an interactive menu:

```bash
./dev.sh
```


