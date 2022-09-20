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

## Defining an action
Import:
```bash
use Amot\Conversate\Facades\Conversate;
```

Add:
```bash
Conversate::addAction('get-users',UsersService::class,'getUsers',true);
```
 Parameter 1 -> Action Name required, 
 Parameter 2 -> Class Path required, 
 Parameter 3 -> Method Name required, 
 Parameter 4 -> Requires Auth, default is false.
 
## Sample UserService Implementation
Import:
```bash
use Amot\Conversate\ActionResult;
use Amot\Conversate\Request;
```

Method:
```bash
public function getUsers(Request $request): ActionResult
{
    return $request->complete(User::all());
}
```
Request Data:
```bash
public function getUsers(Request $request): ActionResult
{
    $data = $request->parameter;
}
```

Auth User ID:
```bash
public function getUsers(Request $request): ActionResult
{
    $user_id = $request->user_id;
}
```
Please note user_id will only be set if action requires authentication.

## The client
Once all is set and running, the client can talk to the server. Link to client https://github.com/nyelnizy/conversate-client.git

## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.