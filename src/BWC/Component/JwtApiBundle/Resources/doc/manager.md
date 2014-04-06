MANAGER
=======

Manager is main class responsible for managing the jwt api request and serving the response

It uses ```ReceiverInterface``` implementation to receive request and create context.
Itself is ```CompositeContextHandler``` which encapsulates steps of context handling starting with request token
decoding and ending with creation of response token
It uses ```SenderInterface``` implementation to send the response token

It's structure is following:

* Manager - composite handler
    * receive - receives request and creates context
    * handle context - handles context
        * decoder handler - rank **100** - creates request jwt from request token
        * key provider handler - rank **200** - populates keys into context
        * validator handler - rank **300** - validates request jwt
        * bearer provider handler - rank **400** - populates bearer into context
        * subject provider handler - rank **500** - populates subject into context
        * method handler - composite - rank **1000** - handlers request jwt and creates response jwt
            * prototype
                * composite handler
                    * pre handlers - if method required pre handler decorators
                    * direction method filter handler - always exists to filter handling down to concrete method/direction
                        * concrete method handler
                    * post handlers - if method required post handler decorators
        * unhandled handler - rank **2000** - creates error response jwt if none set
        * encoder handler - rank **3000** - encodes response jwt into response token
    * send - sends response token

Methods
-------

All method classes must implement ```ContextHandlerInterface``` interface.

Methods are concrete handlers that should provide the functionality and eventually a response jwt. They are built from
services tagged with ```bwc_component_jwt_api.method``` tag. In that tag they must define the method name by
```method``` attribute and direction of the jwt by ```direction``` attribute. Direction must be valid value from
one of the ```Directions``` class constants: ```req``` or ```resp```.

Methods can require additional decorator handlers to be executed either before and after them. Required decorators
that are to be executed before method are specified by the ```bwc_component_jwt_api.pre``` tag, while decorators to
be executed after method are specified by the ```bwc_component_jwt_api.post``` tag. In both case tag must have
```decorator``` attribute which specifies the name of the decorator. Optionally, tag may have attribute ```priority```
 by which you can specify the order of execution of the pre decorators. If omitted it's defaults to 999999.

All method names should be namespaced with ```-``` (minus) at least with vendor name to avoid collisions.


Decorators
----------

Decorator services are context handlers, and as such must also implement ```ContextHandlerInterface``` interface.
A decorator is defined by tagging a service with the ```bwc_component_jwt_api.decorator``` tag. They all must also
specify it's name by ```decorator``` attribute. Methods are requiring decorators by their name from the
```decorator``` attribute.

There can be only one service that implements one decorator. In other words, come concrete decorator name can
appear only on one decorator service. Otherwise ```InvalidConfigurationException``` is thrown.

Decorator names should be namespaced with ```.``` (dot) at least with vendor name to avoid collisions.


Example of method and decorator declaration
-------------------------------------------

``` yaml
# services.yml
services:
    acme_jwt_api.method.buy_global:
        class: Acme\JwtApiBundle\Method\BuyGlobal
        tags:
            - { name: 'bwc_component_jwt_api.method', direction: 'req', method: 'acme-buy' }
            - { name: 'bwc_component_jwt_api.pre', decorator: 'acme.exchange_rate' }
            - { name: 'bwc_component_jwt_api.post', decorator: 'acme.vat' }

    acme_jwt_api.method.buy_local:
        class: Acme\JwtApiBundle\Method\BuyLocal
        tags:
            - { name: 'bwc_component_jwt_api.method', direction: 'req', method: 'acme-buy-local' }
            - { name: 'bwc_component_jwt_api.post', decorator: 'acme.vat' }

    acme_jwt_api.decorator.load_exchange_rate:
        class: Acme\JwtApiBundle\Decorator\LoadExchangeRage
        tags:
            - { name: 'bwc_component_jwt_api.decorator', decorator: 'acme.exchange_rate' }

    acme_jwt_api.decorator.calculate_vat:
        class: Acme\JwtApiBundle\Decorator\CalculateVat
        tags:
            - { name: 'bwc_component_jwt_api.decorator', decorator: 'acme.vat' }

```


Customization of context handling
---------------------------------

All context handlers are built during warm-up by bundle's extension from services tagged with
```bwc_component_jwt_api.handler``` tag. Order of execution is defined by tags attribute ```priority```.

Default handlers provided by the bundle are listed above in the manager structure. Custom handlers can be added by
third party bundles by tagging their services that implement ```ContextHandlerInterface``` and appropriately
setting their ```priority``` attribute so they fit on desired position in the execution order.

Default handlers can not be removed, but it's behavior can be changed by changing value of their class parameter.
Their functionality can be removed by setting their call either to ```bwc_component_jwt_api.handler.null.class```
or ```BWC\Component\JwtApi\Handler\Structural\NullContextHandler``` which is a null handler that does nothing.


