BEARER
======

Bearer is the carrier of the request. It should be set to a non null value only in case user carrying the request
has an authenticated session. That may only happen if requester is making the request by sending the user agent
to the server. In case request is done in the "background", for example with curl w/out some valid server's session
cookie, bearer is unknown and set to null to the context.

The class implementing the ```BearerProviderInterface``` is responsible for providing the bearer to the bearer
provider handler that sets obtained value to the context.

Implementations of the ```BearerProviderInterface``` are
1. ```NullBearerProvider``` - always returns null
2. ```UserSecurityContextBearerProvider``` - returns user from the security context token if present

The bearer provider can be specified with ```bearer_provider``` configuration

``` yaml
# config.yml
bwc_component_jwt_api:
    bearer_provider: id_of_the_bearer_provider_service
```

Default bearer provider is ```UserSecurityContextBearerProvider```.
