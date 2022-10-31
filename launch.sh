#!/bin/bash
# Open local server using default browser
if [[ -n "$IS_WSL" || -n "$WSL_DISTRO_NAME" ]] # If wsl
then
	cmd.exe /c start http://127.0.0.1:800/icon-manager/index.php
elif command -v xdg-open &> /dev/null # If linux
then
    xdg-open http://127.0.0.1:800/icon-manager/index.php
else # If windows msys
    start http://127.0.0.1:800/icon-manager/index.php
fi
cp -n sample.conf ../manager.conf
php -S localhost:800 -t ../.