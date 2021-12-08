Behat HTTP Mock Context
=================================

|  Version  | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
|  `main`   | [![CI][main Build Status Image]][main Build Status] | [![Coverage Status][main Code Coverage Image]][main Code Coverage] |
| `develop` | [![CI][develop Build Status Image]][develop Build Status] | [![Coverage Status][develop Code Coverage Image]][develop Code Coverage] |

Installation
============

Step 1: Install Context
----------------------------------
Open a command console, enter your project directory and execute:

```console
$ composer require --dev macpaw/behat-http-mock-context
```

Step 2: Update Container config to load Context
----------------------------------
In the `config/services_test.yaml` file of your project:

```yaml
    BehatHttpMockContext\:
        resource: '../vendor/macpaw/behat-http-mock-context/src/*'
        arguments:
            - '@test.service_container'
            
    BehatHttpMockContext\Collection\ExtendedMockHttpClientCollection:
        arguments:
            - !tagged_iterator mock.http_client
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

Step 4: Configure Behat
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
[develop Build Status]: https://github.com/macpaw/BehatHttpMockContext/actions?query=workflow%3ACI+branch%3Adevelop
[develop Build Status Image]: https://github.com/macpaw/BehatHttpMockContext/workflows/CI/badge.svg?branch=develop
[main Code Coverage]: https://codecov.io/gh/macpaw/BehatHttpMockContext/branch/main
[main Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/BehatHttpMockContext/main?logo=codecov
[develop Code Coverage]: https://codecov.io/gh/macpaw/BehatHttpMockContext/branch/develop
[develop Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/BehatHttpMockContext/develop?logo=codecov
