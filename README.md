# Flu data entry

Puts flu phenotypes into a database for later use in training ML models

## Setup

    $ cp config.dist.php config.php && vi .htaccess
    $ cp setup.htaccess .htaccess && vi .htaccess

things you ALMOST CERTAINLY should re-set are `$BASE_URL` and `RewriteBase`, and the database settings in `config.php`