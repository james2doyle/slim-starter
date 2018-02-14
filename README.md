Slim Starter
============

> Based on the [Slim 3 Skeleton](https://github.com/slimphp/Slim-Skeleton).

Additional features:

* JWT support
* examples for a few controllers (home, resource, login, stream file)
* helper composer commands for testing, migrations, seeds, etc.
* simple queries using EasyDB and Latitude - basically build queries easily without hydration or model classes
* support for `.env` file
* support for migrations and seeds with fake data
* easily test with migrations and seeds
* simple authentication in tests with `actingAs` method

Additional packages:

* paragonie/easydb
* tuupola/slim-jwt-auth
* vlucas/phpdotenv
* latitude/latitude
* robmorgan/phinx
* fzaninotto/faker

### Sample Env

Copy the contents below into a `.env` file at the root of the project:

```
APP_ENV=local
APP_NAME=slim-app
APP_URL=slim.localhost
APP_DEBUG=true
JWT_SECRET=supersecretdefaultkeyyoushouldnotcommittogithub
# used for monolog critical hook - can be empty
SLACK_WEBHOOK=https://hooks.slack.com/services/SOME/SLACK/CRAZY-URL
DB_ADAPTER=mysql
DB_HOST=localhost
DB_NAME=testing_slim
DB_USER=root
DB_PASS=root
DB_PORT=3306
DB_CHARSET=utf8mb4
```