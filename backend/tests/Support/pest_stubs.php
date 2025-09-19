<?php

// Lightweight stubs so editors/static analyzers don't flag global Pest functions.
// They are defined only if Pest is not loaded. When running with Pest, the
// real implementations already exist and these will not be loaded.

if (!function_exists('uses')) {
    function uses(...$args) {
        return new class {
            public function in(...$dirs) { return $this; }
            public function group(...$groups) { return $this; }
        };
    }
}

if (!function_exists('it')) {
    function it(string $description, callable $closure = null) {
        // No-op stub for static analysis only.
        return null;
    }
}

if (!function_exists('test')) {
    function test(string $description, callable $closure = null) {
        // No-op stub for static analysis only.
        return null;
    }
}

if (!function_exists('expect')) {
    function expect($value = null) {
        return new class($value) {
            public function __construct(private $v) {}
            public function __call($name, $arguments) { return $this; }
            public function not() { return $this; }
        };
    }
}
