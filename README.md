# Página web dockerizada en AWS EC2

Este proyecto consiste en una aplicación web PHP conectada a una base de datos MySQL, administrada mediante phpMyAdmin y desplegada en una instancia EC2 de AWS usando Docker.

## Tecnologías usadas

- PHP
- Apache
- MySQL
- phpMyAdmin
- Docker
- Docker Compose
- Ubuntu Server
- AWS EC2
- GitHub

## Objetivo

Dockerizar una aplicación web con conexión a base de datos y desplegarla en una instancia Ubuntu en AWS EC2.

## Estructura del proyecto

```text
pagina-web-docker-aws/
├── web/
│   ├── index.php
│   └── Dockerfile
├── db/
│   └── init.sql
├── docker-compose.yml
├── README.md
└── .gitignore