#!/bin/bash

# script per sviluppo locale con eclipse, non usare in altri casi.

rsync -avuz --exclude 'anagrafica' --exclude '.gitignore' --exclude '.metadata' /home/buck/dev/arpasmr/web/dockerwebana/portaleINM/htdocs/ /var/www/html/sinergicoweb/
rsync -avuz --exclude=".*/" --exclude ".*" /home/buck/dev/arpasmr/web/dockerwebana/portaleINM/htdocs/anagrafica/ /var/www/html/sinergicoweb/anagrafica
