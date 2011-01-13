#!/bin/bash

xgettext -Lphp -i ../index.php -i ../modules/*/*.php -klang -o messages.pot
