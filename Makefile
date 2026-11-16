.PHONY: build up down logs restart clean

build:      ## build the web image
	docker compose build

up:         ## build (if needed) and start everything in the background
	docker compose up -d --build

down:       ## stop and remove containers (data is kept)
	docker compose down

logs:       ## follow logs
	docker compose logs -f

restart: down up

clean:      ## stop and DELETE containers + database + uploads volumes
	docker compose down -v
