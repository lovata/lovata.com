inventory = local
version = develop

# Run/build/stop project
local-init:
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/init/init-docker.yml -i ansible/$(inventory)-hosts.yml
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/init/init-nginx.yml -i ansible/$(inventory)-hosts.yml
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/init/init-app.yml -i ansible/$(inventory)-hosts.yml
local-up:
	docker-compose up -d
	make docker-status
local-stop:
	docker-compose stop
local-bash:
	docker-compose exec --user=sail backend_app bash

# Project commands
app-migrate:
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/project/migrate.yml -i ansible/$(inventory)-hosts.yml
app-composer-install:
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/project/composer-install.yml -i ansible/$(inventory)-hosts.yml
app-create-database:
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/project/create-database.yml -i ansible/$(inventory)-hosts.yml
app-apply-dump:
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/project/apply-dump.yml -i ansible/$(inventory)-hosts.yml
	make app-migrate
app-npm-build:
	ansible-playbook --vault-id .vault-password-$(inventory) ansible/playbooks/project/npm-build.yml -i ansible/$(inventory)-hosts.yml

# Staging commands
staging-release:
	ansible-playbook --vault-id .vault-password-staging ansible/playbooks/staging/release.yml -i ansible/staging-hosts.yml --extra-vars "GIT_VERSION=$(version)"
staging-init:
	ansible-playbook --vault-id .vault-password-staging ansible/playbooks/init/init-docker.yml -i ansible/staging-hosts.yml
	ansible-playbook --vault-id .vault-password-staging ansible/playbooks/staging/init-nginx.yml -i ansible/staging-hosts.yml
	ansible-playbook --vault-id .vault-password-staging ansible/playbooks/init/init-app.yml -i ansible/staging-hosts.yml


# Docker commands
docker-status:
	docker ps

# Git flow commands
git-push-release:
	git checkout develop
	git push origin develop
	git checkout master
	git push origin master
	git push --tags
	git checkout develop
