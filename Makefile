#  This Makefile is a tool for building, up, down, running, and cleaning the project data

# APP_ENV is a current environment name
ifndef APP_ENV
	include .env
	ifneq ("$(wildcard .env.local)","")
		include .env.local
	endif
endif

ifndef APP_ENV
$(error APP_ENV is not set)
endif

### PROCESS #1.1 - GET ENVIRONMENT: domain, service, node, get actual ENVs, check required vars & files
# Use 'docker compose' as engine instead a legacy plugin 'docker-compose'.
# Actually it is the same thing, but the first one is a new way to use docker-compose.
# [docs](https://docs.docker.com/compose/migrate/)
COMPOSE=docker compose
ifndef COMPOSE
$(error COMPOSE is not set)
endif

DOCKER=docker
ifndef DOCKER
$(error DOCKER is not set)
endif



# The LOCAL microservice database is used only for current purposes.
# You may find its value in the docker-compose.yml file in the database service section.
# Often it is a container name unique name or other unique sensitive param.
DATABASE=
#ifndef DATABASE
#$(error DATABASE is not set)
#endif



# The GLOBAL database is used for all microservices purposes.
MAIN_DATABASE=
#ifndef MAIN_DATABASE
#$(error MAIN_DATABASE is not set)
#endif



# Absolute path to apps working directory inside the (PHP) container.
# You may find its value in the docker-compose.yml file in the php service section if you need to change it or override it.
# In fact it is a  path to the project (PHP) root directory inside the apps container.
# Define or override it if you need to change it. (e.g. /var/www/html/sub.domain.zone/ or /application)
WORKING_DIR=/var/www/html
ifndef WORKING_DIR
$(error WORKING_DIR is not set)
endif



# The COMPOSE_PROJECT_NAME is a docker-compose project name.
ifndef COMPOSE_PROJECT_NAME
$(error COMPOSE_PROJECT_NAME is not set)
endif



# Container names
PHP=$(COMPOSE_PROJECT_NAME).php
APACHE=$(COMPOSE_PROJECT_NAME).php.apache
DATABASE=$(COMPOSE_PROJECT_NAME).database.postgres
APP=$(PHP)

#PHP=$(PREFIX).$(APPLICATION).php
#APACHE=$(PREFIX).$(APPLICATION).apache
#REDIS=$(PREFIX).$(APPLICATION).redis
#DATABASE=$(PREFIX).$(APPLICATION).database
#ZOOKEEPER=$(PREFIX).application.zookeeper
#KAFKA=$(PREFIX).application.kafka
#MYSQL=$(PREFIX).application.mysql
#ELASTICSEARCH=$(PREFIX).application.elasticsearch
#KIBANA=$(PREFIX).application.kibana
#LOGSTASH=$(PREFIX).application.logstash
#FILEBEAT=$(PREFIX).application.filebeat
#NGINX=$(PREFIX).application.nginx
#NGINX_PROXY=$(PREFIX).application.nginx-proxy

#MYSQL_PASSWORD=
#MYSQL_ROOT_PASSWORD=
#MYSQL_DATABASE=
#MYSQL_USER=root
#MYSQL_PORT=3306
#MYSQL_HOST=localhost



#---------------------------------------------------------------------------------------------------------------------

logs:
	$(COMPOSE) logs -f --tail all
up:
	$(COMPOSE) up -d
ps:
	$(COMPOSE) ps
restart:
	$(COMPOSE) restart
stop:
	$(COMPOSE) stop
start:
	$(COMPOSE) start
down:
	$(COMPOSE) down
top:
	$(COMPOSE) top
kill:
	$(COMPOSE) kill
ls:
	$(COMPOSE) ls -a


#---------------------------------------------------------------------------------------------------------------------

build: fresh composer migrate about


composer:
	$(DOCKER) exec -it $(PHP) bash -c 'cd $(WORKING_DIR) && composer install --ignore-platform-reqs --no-interaction --optimize-autoloader -vvv'
	$(DOCKER) exec -it $(PHP) bash -c 'cd $(WORKING_DIR) && composer clear-cache --no-interaction -vvv'


composer-update:
	$(DOCKER) exec -it $(PHP) bash -c 'cd $(WORKING_DIR) && composer update --ignore-platform-reqs --no-interaction -vvv'
	$(DOCKER) exec -it $(PHP) bash -c 'cd $(WORKING_DIR) && composer clear-cache --no-interaction -vvv'

hclean:
	$(DOCKER) exec -it -u root $(PHP) rm -rf $(WORKING_DIR)/vendor
	$(DOCKER) exec -it -u root $(PHP) rm -rf $(WORKING_DIR)/docker/*/data/

clean:
	$(COMPOSE) down -v --rmi local --remove-orphans



fresh:
	$(COMPOSE) build --no-cache
	$(COMPOSE) up -d --force-recreate


prune:
	$(DOCKER) system prune -af --volumes



#---------------------------------------------------------------------------------------------------------------------

ll:
	$(DOCKER) ps \
		--all \
		--filter "name=$(PREFIX)" \
		--format \
			"table {{.Names}}\t{{.Image}}\t{{.Status}}\t{{.Ports}}\t{{.Size}}"

htop:
	$(DOCKER) stats \
		--all \
		--no-trunc \
		--format \
			"table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.BlockIO}}\t{{.MemPerc}}\t{{.PIDs}}"

php:
	$(DOCKER) exec -it $(PHP) /bin/bash

php-fpm:
	$(COMPOSE) exec 'php-fpm' /bin/bash

httpd:
	$(COMPOSE) exec -it 'httpd' /bin/sh

#---------------------------------------------------------------------------------------------------------------------



app:
	$(DOCKER) exec -it -u root $(PHP) /bin/bash

migrate:
	$(DOCKER) exec -it $(PHP) /bin/bash -c "php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing -v"

migrate-list:
	$(DsOCKER) exec -it $(PHP) /bin/bash -c "php bin/console doctrine:migrations:list -v"

migrate-status:
	$(DOCKER) exec -it $(PHP) /bin/bash -c "php bin/console doctrine:migrations:status -v"

cc:
	$(DOCKER) exec -it $(PHP) /bin/bash -c "php bin/console cache:clear -v"

about:
	$(DOCKER) exec -it $(PHP) /bin/bash -c "php bin/console about -v"

#---------------------------------------------------------------------------------------------------------------------

cron-logs:
	$(DOCKER) exec -it $(PHP) /bin/bash -c "tail -f /var/log/cron.log"

#---------------------------------------------------------------------------------------------------------------------



symfony-app-new-stable:
	$(DOCKER) exec -it $(PHP) bash -c \
		'symfony new "application-$(APP_NAME)" --dir="/var/www/html/application-$(APP_NAME)" --version="stable" --php="8.3" --no-git --docker --debug'

symfony-app-new-app-stable-raw:
	symfony new 'application-$(APP_NAME)' --dir='./application-$(APP_NAME)' --php='8.3' --version='stable' \
		--no-git \
		--docker \
		--debug

docker-show-entrypoint:
	$(DOCKER) container ls \
		--all \
		--format 'table {{ .Names }}\t{{ .Status }}\t{{ .Command }}' \
		--no-trunc
