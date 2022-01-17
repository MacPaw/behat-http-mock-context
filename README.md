Behat HTTP Mock Context
=================================

|  Version  | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
|  `main`   | [![CI][main Build Status Image]][main Build Status] | [![Coverage Status][main Code Coverage Image]][main Code Coverage] |

Installation
============

Step 1: Download the Bundle
----------------------------------
Open a command console, enter your project directory and execute:

###  Applications that use Symfony Flex [in progress]
```console
$ composer require --dev macpaw/behat-http-mock-context
```

### Applications that don't use Symfony Flex

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require --dev macpaw/behat-http-mock-context
```  

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.


Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            BehatHttpMockContext\BehatHttpMockContextBundle::class => ['test' => true],
        );

        // ...
    }

    // ...
}
```

Step 2: Mock http client
=============
Example you have http client in `config/services.yaml`
```yaml
    oauth_http_client:
        class: Symfony\Contracts\HttpClient\HttpClientInterface
        factory: ['Symfony\Component\HttpClient\HttpClient', createForBaseUri]
        arguments:
            - '%env(OAUTH_URL)%'
```

Now you need mock this client in `config/services_test.yaml`

```yaml
    oauth_http_client:
        class: ExtendedMockHttpClient\ExtendedMockHttpClient
        arguments:
            - '%env(OAUTH_URL)%'
        tags: ['mock.http_client']
...
```

Now we ready add build mock collection 
```yaml
    BehatHttpMockContext\Collection\ExtendedMockHttpClientCollection:
        arguments:
            - !tagged_iterator mock.http_client
...
```

Step 3: Configure Behat
=============
Go to `behat.yml`

```yaml
...
  contexts:
    - BehatHttpMockContext\Context\MockContext
...
```

Step 4: How to use:
=============
```
    Given I mock "oauth_http_client" HTTP client next response status code should be 200 with body:
        """
        {
            "success": true,
            "response": {
                "user_id": 234
            }
        }
        """
```

[main Build Status]: https://github.com/macpaw/BehatHttpMockContext/actions?query=workflow%3ACI+branch%3Amain
[main Build Status Image]: https://github.com/macpaw/BehatHttpMockContext/workflows/CI/badge.svg?branch=main
[main Code Coverage]: https://codecov.io/gh/macpaw/BehatHttpMockContext/branch/main
[main Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/BehatHttpMockContext/main?logo=codecov
