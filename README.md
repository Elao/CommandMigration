# ElaoCommandMigration

PHP library to run commands, for example on deployment.

## Why

Sometimes, on deployment or when switching to a branch, we need to run some migration commands,
for example reindex a database, calculate aggregation, remove whatever uploaded files...

Do not do it manually anymore by connecting to your server in ssh!
This library allow you to declare in your feature git branch what command(s) need to be run once deployed.

## Install

    $ composer require elao/elao-command-migration

Add a `elao_command_migration.yaml` file (in a not public directory of course!):

```yaml
elao_command_migration:
    storage:
        type: dbal
        dsn: mysql://db_user@db_host/my_database_name
        table_name: 'command_migrations'
    migrations: []
```

## Usage

Declare what command(s) need to be run in the `migrations` entry in your `elao_command_migration.yaml` file:

```yaml
elao_command_migration:
    # ...
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

Run `php bin/elaoCommandMigration path/to/elao_command_migration.yaml` to test it.

## Integration

Add `php bin/elaoCommandMigration path/to/elao_command_migration.yaml`
to your deployment process.

### Capifony

Set in deploy.rb:

```rb
    after :deploy, 'app_tasks:elao_command_migration'

    namespace :app_tasks do
      task :elao_command_migration do
        capifony_pretty_print "--> Run command migrations"
        invoke_command "php bin/elaoCommandMigration path/to/elao_command_migration.yaml", :via => run_method
        capifony_puts_ok
      end
    end
```

### Ansible

With [Manala/ansible-role-deploy](https://github.com/manala/ansible-role-deploy), add in `ansible/group_vars/deploy.yml`:

```yaml
    manala_deploy_tasks:
      - command: php bin/elaoCommandMigration path/to/elao_command_migration.yaml
```

or

```yaml
    manala_deploy_post_tasks:
      - command: php bin/elaoCommandMigration path/to/elao_command_migration.yaml
```

## How it works

ElaoCommandMigration is very inspired by [Doctrine Migrations](https://github.com/doctrine/migrations) but
for running commands.

The `elao:command-migration:run` command :

- Fetch all migrations already ran from `command_migrations` database table
- Get only migrations not already ran from `elao_command_migration.migrations`
- Store migration identifier in `command_migrations` database table.

## Clean old migrations

When the commands have been deployed and ran on production environment, you can (manually) delete the entries in
`elao_command_migration.migrations`.

## Who is using it?

- [Vimeet](https://vimeet.events/)
