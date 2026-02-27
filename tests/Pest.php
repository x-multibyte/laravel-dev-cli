<?php

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeFile', function () {
    expect(file_exists($this->value))->toBeTrue("Failed asserting that '{$this->value}' is a file.");

    return $this;
});

expect()->extend('toBeDirectory', function () {
    expect(is_dir($this->value))->toBeTrue("Failed asserting that '{$this->value}' is a directory.");

    return $this;
});

expect()->extend('toBeJson', function () {
    expect(json_validate($this->value))->toBeTrue("Failed asserting that value is valid JSON.");

    return $this;
});
