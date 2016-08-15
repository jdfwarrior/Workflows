# v0.4

* MAJOR re-work of the get, set, read & write functions. The changes break any previous usage of them, mostly by changing the parameters so that the file you want to read is the first parameter.
* Added in some checks to see ifg you are using Alfred 2 or 3, it will take the first one it finds

# v0.3

* Added a simple curl client
* Marked `request` as deprecated

# v0.2

* A lot of variable name changes to make for easy reading
* Added some more unit tests for Workflow
* Tidied `get` function

# v0.1

* Made into composer package
* Added some base unit tests
* Made code PSR2 compliant