# Indianautosblog.com website

## Running project:
1. Install Docker and Docker Compose
2. Go to project folder and run docker
    ```bash
    cd /path/to/project
    docker-compose up -d --build
    ```
3. Run composer install
    ```bash
    sudo docker-compose exec all_php composer install
    ```
4. Host local website
    - Docker for Windows: 
        + Add `127.0.0.1	india-game.local` to `hosts` file.
        + Add `127.0.0.1	m.india-game.local` to `hosts` file.
    - Docker toolbox: 
        + Add `192.168.99.100	india-game.local` to `hosts` file.
        + Add `192.168.99.100	m.india-game.local` to `hosts` file.
    
5. Copy docker-compose.yml.dist to docker-compose.yml in root of project

6. Mount code folder for docker:
    - Docker for Windows: Replace from `/projects/india-project/india-game/desktop/` to `./` in docker-compose.yml file (if need)
    - Docker Toolbox: Mount driver in Docker Toolbox
        + Step 1: Add shared folder in Virtual Box, ex: PhpProjects -> D:\PhpProject (Root of all project)
        + Step 2: Open Docker Quickstart Terminal and Run command: `docker-machine ssh`
        + Step 3: Run command: `sudo vi /var/lib/boot2docker/bootlocal.sh`
        + Step 4: Paste content below and save
            - `sudo mkdir /projects`
            - `sudo mount -t vboxsf PhpProjects /projects`
        + Step 5: Quit machine by command: `exit`
        + Step 6: Restart machine with command: `docker-machine restart`
 
 7. Go to browser and type:
    - Desktop: `http://india-game.local/`
    - Mobile: `http://m.india-game.local/`

## Run code quality checker tools

### PHP CodeSniffer 

Must run PHP CodeSniffer before creating pull request and fix all violations
```bash
docker-compose exec all_php vendor/bin/php-cs-fixer --standard=phpcs.xml --extensions=php .
```

Some violations can auto fix with `phpcbf`
```bash
docker-compose exec all_php vendor/bin/phpcbf --standard=phpcs.xml --extensions=php .
```

### PHP Mess Detector 

Must run PHP Mess Detector before creating pull request and fix all violations
```bash
docker-compose exec app vendor/bin/phpmd . text ruleset.xml --suffixes php --exclude .idea,app,bin,web,docker,vendor,tests,var/cache/,var/logs/,var/sessions/,var/SymfonyRequirements.php
```