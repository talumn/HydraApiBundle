[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lafourchette/HydraApiBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lafourchette/HydraApiBundle/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/lafourchette/HydraApiBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lafourchette/HydraApiBundle/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/lafourchette/HydraApiBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lafourchette/HydraApiBundle/build-status/master) [![Build Status](https://travis-ci.org/lafourchette/HydraApiBundle.svg?branch=fix%2Fphpcs)](https://travis-ci.org/lafourchette/HydraApiBundle)

# HydraApiBundle

The easiest way to use an Hydra-API with Symfony.

## Require 

* [Symfony 2.0.10.*](https://github.com/symfony/symfony/tree/2.0)

## Installation 

Add ```"lafourchette/hydra-api-bundle"```to your composer file.

```json
"require-dev": {
    "lafourchette/hydra-api-bundle": "dev-master"
}
```

And add ```HydraApiBundle```to your ```AppKernel.php``` file.
```php
if ($this->getEnvironment() != 'prod') {
    $bundles[] = new LaFourchette\HydraApiBundle\HydraApiBundle();
}
```
