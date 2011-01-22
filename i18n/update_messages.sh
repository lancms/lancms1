#!/bin/bash

xgettext -Lphp -i ../*.php -i ../modules/*/*.php -i ../inc/*.php -i ../installer/*.php -klang -o messages.pot


msgfmt -cv -o nb_NO.utf8/LC_MESSAGES/messages.mo nb.po
