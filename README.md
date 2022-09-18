# conversate

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/hardcorp/hydra-client.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/hardcorp/hydra-client.svg?style=flat-square)](https://packagist.org/packages/hardcorp/hydra-client)

## Description

A Laravel Library that enables RPC style communication over websockets.


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


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security

If you discover any security-related issues, please email yhiamdan@gmail.com instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.