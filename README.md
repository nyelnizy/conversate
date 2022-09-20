# conversate
## Description

A Laravel Library that enables RPC style communication over web sockets.

## Install
Add the following to composer.json file
```bash
    "repositories": [{
            "type": "vcs",
            "url": "git@github.com:nyelnizy/conversate.git"
        }
    ],
```
Add the dependency to require block
```bash
  "amot/conversate": "dev-master"
```
Run
```bash
  composer update
```

## Publishing Files

Run:

```bash
php artisan vendor:publish --tag=conversate
```
An actions.php file is published to the routes folder where you can find an example of how to define an action.
A conversate.php config file is published to the config folder.

## Starting Server
Run:
```bash
php artisan conversate:start
```
Run on specific port:
```bash
php artisan conversate:start --port=7001
```

Run in secure/ssl mode:
```bash
php artisan conversate:start --secure
```
Please note that the path to your ssl key and cert must be provided in the conversate config file.
## Security

If you discover any security-related issues, please email yhiamdan@gmail.com instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.