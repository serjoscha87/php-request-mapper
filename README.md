# PHP request-mapper / auto-router / request-to-file-resolver

> README STILL ON THE WAY TO COMPLETENESS <

## Purpose

Ever though that it can not be that hard to reflect smart-url requets to files on your server's file-system? Well. Trying it you will quickly face edge cases that will convince you of the opposite.

This requets-mapper does the heavy lifting of reflecting request to files on the local filesystem of the server while serving you a configurable abstraction layer.

Basically this lib renders the need of a php-router for manual route-bindings obsolete.

I primarily wrote this for using it within a framework for more or less static websites (not apps) I wrote some time ago. But actually this lib may be used beyond this purpose I guess.

## Installation

``composer require serjoscha87/php-request-mapper``

## Simple Example

**filesystem:**
```
index.php
pages
- test.php
- home.php
- 404.php
- foobar (dir)
  - foobar.php
  - abc.php
  - detail.php
```

(of course the level of dir-nesting is not limited by the request-mapper)

**index.php:**

```
use serjoscha87\phpRequestMapper\CurrentRequest;
use serjoscha87\phpRequestMapper\CurrentPage;

// if you need the mapper instance for the current request
// $rm = CurrentRequest::inst()->mapper(); 

// get all information on the page that is currently mapped by the concrete request
$currentPage = CurrentPage::get();

var_dump($currentPage->getName());
var_dump($currentPage->is404());
var_dump($currentPage->getFilePath());
// ...

// send the page out for rendering
if(CurrentRequest::needsRedirect())
  // ... do the header redirect here (you can utilize CurrentRequest::getRedirectUri() for that)

if(!CurrentRequest::inst()->mapper()->isReal404())
    require_once $currentPage->getFilePath(); // this will automatically send out the content of the 404 page if the page requested and mapped does not exist - otherwise it will deliver the content of the files the mapper mapped
```

### Examples for better understanding what the request mapper is good for

considering the filesys structure given above (see "Simple Example"):

  - Request to: ``/test`` -> will deliver the content of ``pages/test.php``
  - Request to: ``/quxx`` -> will deliver the content of ``pages/404.php`` (because there is no quxx.php in the pages dir)
  - Request to: ``/`` -> will deliver the content of ``pages/home.php``
  - Request to: ``/home`` -> the requestmapper will tell you that a redirect to ``/`` is required (through ``CurrentRequest::needsRedirect()`` - you can use ``CurrentRequest::getRedirectUri()`` to get the redirect target)
  - Request to: ``/foobar/foobar`` -> the requestmapper will tell you that a redirect to ``/foobar`` is required (see above)
  - Request to: ``/foobar`` -> will deliver the content of ``pages/foobar/foobar.php``
  - Request to: ``/foobar/detail/something`` -> will deliver the content of ``pages/foobar/detail.php`` and will pass everything after ``/detail`` to the php file
  - Request to: ``/foobar/abc`` -> like seen before ... nothing special. Will deliver the content ``pages/foobar/abc``

### self instancing:

```
$rm = new RequestMapper('/my-emulation-url');
```

... up to come

## configuration

up to come...

but generally pages base-dir, file-extensions, route-prefixes and base-paths are configurable

## RequestMapper::isReal404()

...
important thing...
Imagine you let the request mapper answer all 404 requests with a cool html error page. 
Now imagine you are having a gallery in your websites which has some wrong path references and causes 20 of the images to run into a 404.
Without making your logic check if the request is a 'real404' every of those asset requests will return a pretty 404 page markup. 
You just don't want this if you are using a framework like me - because this means your framework will not only be booted for handling the actual website request but also for every single 404 asset request which causes massive server load for noting.
This is why the request mapper has this method. 
Make sure to use it before sending out the page html:

```
if(!CurrentRequest::inst()->mapper()->isReal404()) {
     // your logic for outputting the page content here
}
```
...

## misc

This lib also works perfectly fine with great template engines / implementations like [BladeOne](https://github.com/EFTEC/BladeOne)
