# ConditionalExecution

[![Build Status](https://travis-ci.org/AndyDune/ConditionalExecution.svg?branch=master)](https://travis-ci.org/AndyDune/ConditionalExecution)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/andydune/conditional-execution.svg?style=flat-square)](https://packagist.org/packages/andydune/conditional-execution)
[![Total Downloads](https://img.shields.io/packagist/dt/andydune/conditional-execution.svg?style=flat-square)](https://packagist.org/packages/andydune/conditional-execution)


It allows for conditional execution of code fragments more beautiful and testable. 
Line code with no indentation prevents appearance of errors and improves readability.

Installation
------------

Installation using composer:

```
composer require andydune/conditional-execution 
```
Or if composer didn't install globally:
```
php composer.phar require andydune/conditional-execution
```
Or edit your `composer.json`:
```
"require" : {
     "andydune/conditional-execution": "^1"
}

```
And execute command:
```
php composer.phar update
```

See a problem
------------

Here condition for execution. I meet something like this it in many CMS ofter:
```php
if ((empty($arParams["PAGER_PARAMS_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PAGER_PARAMS_NAME"]))
    && $arParams["SECTION_ID"] > 0 && $arParams["SECTION_ID"]."" != $arParams["~SECTION_ID"]
)
{
	// some php code
	// fuction call or method
}
```

It's difficult to read, search error or edit.

This is better way library offers:
```php
use AndyDune\ConditionalExecution\ConditionHolder;

$instanceOr = new ConditionHolder();
$instanceOr->bindOr()
->add(empty($arParams["PAGER_PARAMS_NAME"]))
->add(!preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PAGER_PARAMS_NAME"]));

$instanceTotal =  = new ConditionHolder(); // default bind AND
$instanceTotal->executeIfTrue(function(){
	// some php code
	// fuction call or method
});
$instanceTotal->add($instanceOr)
->add($arParams["SECTION_ID"] > 0)
->add($arParams["SECTION_ID"]."" != $arParams["~SECTION_ID"]);

$result = $instanceTotal->doIt(); // yes, do it!
```
