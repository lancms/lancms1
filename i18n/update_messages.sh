#!/bin/bash

xgettext -Lphp -i ../*.php -i ../modules/*/*.php -i ../inc/*.php -i ../installer/*.php -klang -o messages.pot --from-code=UTF-8


msgfmt -cv -o nb_NO.utf8/LC_MESSAGES/messages.mo nb.po
