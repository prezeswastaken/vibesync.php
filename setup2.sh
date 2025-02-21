#!/bin/bash

./vendor/bin/sail artisan key:generate

./vendor/bin/sail artisan jwt:secret --force

./vendor/bin/sail artisan migrate --force

./vendor/bin/sail artisan db:seed --class=GenreSeeder
./vendor/bin/sail artisan db:seed --class=TagSeeder
./vendor/bin/sail artisan db:seed --class=CurrencySeeder
