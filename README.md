# ElaoCommandMigrationBundle

Symfony Bundle to run commands, for example on deployment.

## Why

Sometimes, on deployment or when switching to a branch, we need to run some migration commands,
for example reindex a database, calculate aggregation, remove whatever uploaded files...

Do not do it manually anymore by connecting to your server in ssh!
This bundle allow you to declare in your feature git branch what command(s) need to be run once deployed.

## Install

    $ composer require elao/elao-command-migration-bundle

Add a `config/packages/elao_command_migration.yaml` (or added by the recipe):

```yaml
    elao_command_migration:
        adapter:
            type: dbal
            options:
                driver:    pdo_mysql
                host:      "%database_host%"
                port:      "%database_port%"
                dbname:    "%database_name%"
                user:      "%database_user%"
                password:  "%database_password%"
                tablename: "command_migration"
```

Add `php bin/console elao:command-migration:run` to your deployment process (see below for integration with Capifony or Ansible).

## Usage

Declare what command(s) need to be run in `config/packages/elao_command_migration.yaml`:

```yaml
    elao_command_migration:
        migrations:
            whateverUniqueIdentifier:
                - php bin/console app:posts:reindex
            20180510173033:
                - php bin/console app:posts:reindex
                - php bin/console doctrine:migrations:migrate
            20180622110900:
                - php bin/console app:posts:reindex
                - node hello-world.js
                - php bin/console doctrine:schema:update --force
                - rm -rf public/uploads/lolcats
                - php bin/console app:recalculate:turnover
```

Entries in `migrations` could have whatever identifier, but we recommend to use a date + time format: YYYYMMDDHHMMSS

## How it works

The `elao:command-migration:run` command :

- Fetch all migrations already ran from `command_migration` database table
- Get only migrations not already ran from `elao_command_migration.migrations`
- Deduplicate commands, for example, only one `php bin/console app:posts:reindex` will be ran for this two migrations:

```yaml
    elao_command_migration:
        migrations:
            20180510173033:
                - php bin/console app:posts:reindex
            20180622110900:
                - php bin/console app:posts:reindex
```

- Store migration identifier in `command_migration` database table.

## Clean old migrations

When the commands have been deployed and ran on production environment, you can (manually) delete the entries in
`elao_command_migration.migrations`.

## Integration

### Capifony

Set in deploy.rb:

```rb
    after :deploy, 'app_tasks:elao_command_migration'
    
    namespace :app_tasks do
      task :elao_command_migration do
        capifony_pretty_print "--> Run command migrations"
        invoke_command "php bin/console elao:command-migration:run", :via => run_method
        capifony_puts_ok
      end
    end
```

### Ansible

With [Manala/ansible-role-deploy](https://github.com/manala/ansible-role-deploy), add in `ansible/group_vars/deploy.yml`:

```yaml
    manala_deploy_tasks:
      - command: bin/console elao:command-migration:run
```
