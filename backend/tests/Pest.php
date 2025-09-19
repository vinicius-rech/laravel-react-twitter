<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to this function will be executed for every test
| in your test suite. Here you may perform per-test setup actions.
|
*/
// Provide a stub for static analysis when Pest is not loaded.
if (!function_exists('uses')) {
	function uses(...$args) {
		return new class {
			public function in(...$dirs) { return $this; }
		};
	}
}

uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');
