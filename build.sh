#!/bin/bash

cd docker &&
docker compose down -v &&
docker compose -p qltv up -d --build
