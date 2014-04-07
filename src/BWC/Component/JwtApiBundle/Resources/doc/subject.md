SUBJECT
=======

Subject is object specified by the subject JWT claim. It is obtained by ```SubjectProviderInterface``` and
loaded into context by ```SubjectProviderHandler```.

Implementations of ```SubjectProviderInterface``` in bundle are
1. ```BearerSubjectProvider``` - returns the bearer as subject
2. ```NullSubjectProvider``` - always return null

Normally you would implement your own bearer provider.

The subject provider can be specified with ```subject_provider``` configuration

``` yaml
# config.yml
bwc_component_jwt_api:
    subject_provider: id_of_the_subject_provider_service
```

Default subject provider is ```NullSubjectProvider```.


