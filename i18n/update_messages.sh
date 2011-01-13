#!/bin/bash

xgettext -Lphp -i ../*.php -i ../modules/*/*.php -i ../inc/*.php -i ../installer/*.php -klang -o messages.pot
