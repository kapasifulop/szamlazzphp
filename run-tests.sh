#!/bin/bash

# Ha még nincs telepítve a composer függőségek
if [ ! -d "vendor" ]; then
    echo "Composer függőségek telepítése..."
    composer install
fi

# Tesztek futtatása
./vendor/bin/phpunit 