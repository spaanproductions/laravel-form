<?php

namespace SpaanProductions\LaravelForm\Tests;

use SpaanProductions\LaravelForm\HtmlServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
		return [
			HtmlServiceProvider::class,
		];
	}

	protected function getPackageAliases($app)
	{
		return [
			"Form" => "SpaanProductions\LaravelForm\FormFacade",
			"Html" => "SpaanProductions\LaravelForm\HtmlFacade"
		];
	}
}
