PHP=app.application.php

ps:
	docker -D compose ps

ls:
	docker -D compose ls

up:
	docker -D compose up -d --build

down:
	docker -D compose stop
	docker -D compose down -v --rmi local

php:
	docker -D exec -it $(PHP) /bin/bash

about:
	docker -D exec -it $(PHP) php bin/console about -vvv

cc:
	docker -D exec -it $(PHP) php bin/console cache:clear -vvv