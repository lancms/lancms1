#!/bin/bash

xgettext -Lphp -i ../*.php -i ../modules/*/*.php -i ../inc/*.php -i ../installer/*.php -klang -o messages.pot --from-code=UTF-8 --copyright-holder="LANCMS and it's creators" --package-name='LANCMS' --msgid-bugs-address='bugs.launchpad.net' --package-version='1.0-alpha'


msgfmt -cv -o nb_NO.utf8/LC_MESSAGES/messages.mo nb.po
