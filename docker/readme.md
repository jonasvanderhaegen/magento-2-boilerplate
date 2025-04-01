# Docker environment

This manual will guide you through setting up the docker environment.
This docker template is sourced from https://github.com/markshust/docker-magento

## Prerequisites Installation

Install docker for desktop from their official [website](https://www.docker.com/products/docker-desktop).

## Setup docker container

### 1: create symlink

In terminal go to this folder, create a symlink for a src directory to the root folder. Type & enter `./symlink.sh` that will return you the command to copy and paste to symlink correctly. Copy it, paste in terminal, press enter and you'll see a folder called src with an small corner arrow in this directory.

MAC/LINUX

```
ln -s '/Users/<user>/<path>/magento2-boilerplate/' '/Users/<user>/<path>/magento2-boilerplate/docker/src'
```

### 2: Init docker

Make sure that docker for desktop is installed and running. Anything else that is running a webserver on your local machine like homestead, mamp, etc please turn this off first. In terminal go to this folder and run:

```
docker compose -f docker-compose.yml up -d
```

Now the docker containers will be initialized for the first time, but the docker does not contain our files yet.
Run the command below to copy all the files in the container.

```
bin/copytocontainer --all
```

This might take a few minutes. When this is finished, import an existing database by using

```
bin/mysql < path/to/the/database.sql
```

Add the records below to your host file

```
127.0.0.1 ::1 m2.test
```

Restart the containers

```
bin/restart
```

Setup a new SSL cert for local domain

```
bin/setup-ssl m2.test
```

## Day to Day operations

Start container

```
bin/start
```

Stop container

```
bin/stop
```

Run composer

```
bin/composer
```

Run Magento

```
bin/magento
```
