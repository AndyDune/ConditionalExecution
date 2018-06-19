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
Or if composer was not installed globally:
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

Methods
------------

### add($condition)

It adds condition to a queue. Condition is not check immediately. It can be callable.

### bindAnd()

Change bind of conditions to AND logic. AND is used as default. 

### bindOr()

Change bind of conditions to OR logic. 

### check()

It executes check of all collected conditions.  

### doIt()

It checks of all collected conditions and execute appropriate function and triggers.  


Benefit
------------

## Simple add or delete conditions

You don't need to count brackets. Add, remove conditions is simple.
```php
use AndyDune\ConditionalExecution\ConditionHolder;

$instance = new ConditionHolder();
$instance->add($val1 > $val2);
$instance->add($someObject->isGood());

$instance->check(); // true

$instance->add('');
$instance->check(); // false

$instance->bindOr();
$instance->check(); // true
``` 

## Closure as condition and params

You can use closure as condition. Function can receive params witch was inserted with `doIt` or `check` methods.

```php
$instance = new ConditionHolder();
$instance->add(function ($value) {
    if ($value > 2) {
        return true;
    }
    return false;
});
$instance->executeIfTrue(function () {
    return 'Y';
});

$instance->executeIfFalse(function () {
    return 'N';
});


$instance->doIt(3); // returns 'Y'
$instance->chack(3); // returns true
$instance->doIt(1); // returns 'N'
$instance->chack(1); // returns false
```

## Check classes for checks of any complexity

There is mechanic for checks of any complexity. It is describes as instance of `AndyDune\ConditionalExecution\Check\CheckAbstract`.

You may create own custom classes and use.

### ArrayValueWithKeyNotEmpty

It checks array value with key is not empty.

```php
use AndyDune\ConditionalExecution\Check\ArrayValueWithKeyNotEmpty;
use AndyDune\ConditionalExecution\ConditionHolder;

// Source array 
$array = [
    'one' => 1
];

$condition = new ConditionHolder();
$condition->add(new ArrayValueWithKeyNotEmpty('one'));
$condition->check($array); // result is true
$condition->setNegative();
$condition->check($array);  // result is false
```

### ArrayHasNotEmptyValueOrKeyNotExist

Given array key must not exist or keep value *== true* 

```php
use AndyDune\ConditionalExecution\Check\ArrayHasNotEmptyValueOrKeyNotExist;
use AndyDune\ConditionalExecution\ConditionHolder;

$array = [
    'one' => 1,
    'two' => '',
    'three' => 0
];

$condition = new ConditionHolder();
$condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('one'))->check($array); // true

$condition = new ConditionHolder();
$condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('two'))->check($array); // false

$condition = new ConditionHolder();
$condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('three'))->check($array); // false

$condition = new ConditionHolder();
$condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('four'))->check($array); // true
```


Execute functions in list for get first result 
------------

```php
use AndyDune\ConditionalExecution\GetFirstSuccessResult;
$instance = new GetFirstSuccessResult();
$instance->add(function () {
    return '';
});
$instance->add(function () {
    return 'two';
});

$instance->get(); // resturns 'two'
```

With params:

```php
$instance = new GetFirstSuccessResult();
$instance->add(function ($string, $length = 5) {
    if (strlen($string) < $length) {
        return $string . '<';
    }
    return false;
});
$instance->add(function ($string, $length = 5) {
    if (strlen($string) > $length) {
        return $string . '>';
    }
    return false;
});

$instance->get('two', 4); // returns 'two<'
$instance->get('two', 2); // returns 'two>'
$instance->get('onetwo', 4); // returns onetwo>
$instance->get('tw', 2); returns false
```
