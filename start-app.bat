@echo off
cd /d %~dp0
if not exist docker-compose.yml (
  echo Docker Compose file not found.
  exit /b 1
)

echo Starting Laravel with Docker...
docker compose up --build
