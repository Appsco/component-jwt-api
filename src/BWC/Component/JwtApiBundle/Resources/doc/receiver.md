RECEIVER
========

Receiver implements the ```ReceiverInterface``` and is responsible for creation of the context and its population with
* request
* request token
* request binding type

Default receiver is composite receiver in current implementation encapsulating only one - the ```JwtReceiver```


Composite receiver
------------------

Composite receiver iterates over it's encapsulated child receivers and returns first non null value any of them returns.


Jwt Receiver
------------

The ```JwtReceiver``` is looking for request token in the ```jwt``` parameter, first in POST and then in GET.


Customizing receiver
--------------------

Customization of the receiver is possible by changing the value of the ```bwc_component_jwt_api.receiver.class```
parameter to some class that implements ```ReceiverInterface```.

Eventually, it's also possible to call ```addReceiver()``` method from some third party bundle on default composite
receiver ```bwc_component_jwt_api.receiver```.

