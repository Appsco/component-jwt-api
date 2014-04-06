SENDER
======

Sender implements the ```SenderInterface``` and is responsible for handling the response token and sending it back to
the caller. Default implementation provided by bundle is ```Sender``` class.

If response binding type is not specified in the context, the Sender sets it depending on if bearer is set and the
length of the response token.

| Bearer    |  Token Length |  Binding       |
| --------- | ------------- | -------------- |
| not set   | any           | CONTENT        |
| set       | >1200         | HTTP_POST      |
| set       | <=1200        | HTTP_REDIRECT  |

Sender returns ```Response``` which may also be ```RedirectResponse``` in case of HTTP_REDIRECT binding. In case of
CONTENT binding the response content is the response token. In case of HTTP_POST binding the response content is
html with a form with an input field with ```jwt``` name that automatically is submitted with javascript.

In case of HTTP_REDIRECT and HTTP_POST bindings the ```Sender``` must have an URL to send the response to. Value for
that url is first checked in context destination url. If context destination url is empty then it's value is taken
from the request jwt reply to field. If request jwt reply to field is also empty an exception is thrown.
