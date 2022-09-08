#!/bin/bash
# Open local server using default browser.
if command -v xdg-open &> /dev/null # If linux or wsl.
then
    xdg-open http://127.0.0.1:8000/icon-manager/index.php
else # Otherwise maybe it's on windows.
    start http://127.0.0.1:8000/icon-manager/index.php
fi
cp -n sample.conf ../manager.conf
php -S localhost:8000 -t ../.