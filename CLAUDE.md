# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a Laravel package that provides HTML and Form Builder functionality. It's a continuation/fork of the Laravel Collective HTML package, maintained by Spaan Productions. The package provides fluent interfaces for creating forms and HTML elements in Laravel applications.

**Package name**: `spaanproductions/laravel-form`
**Namespace**: `SpaanProductions\LaravelForm`

## Requirements

- PHP 8.0+
- Laravel 9.x, 10.x, 11.x, or 12.x

## Development Commands

### Running Tests

```bash
vendor/bin/phpunit
```

Tests are located in `tests/Unit/` and use PHPUnit with Orchestra Testbench. The test bootstrap (`phpunit.php`) sets up an in-memory SQLite database for testing Eloquent integration.

### Running Single Tests

```bash
vendor/bin/phpunit --filter TestClassName
vendor/bin/phpunit --filter testMethodName
```

### Installing Dependencies

```bash
composer install
```

### Testing Against Specific Laravel Versions

```bash
composer require "laravel/framework:^11.0" --no-update
composer update
```

## Architecture

### Core Components

1. **HtmlBuilder** (`src/HtmlBuilder.php`)
   - Generates HTML elements (links, assets, lists, meta tags, etc.)
   - Uses `Macroable` trait for custom extensions
   - Uses `Componentable` trait for custom component system

2. **FormBuilder** (`src/FormBuilder.php`)
   - Generates form elements with CSRF protection, model binding, and method spoofing
   - Handles old input from validation failures automatically
   - Integrates with Eloquent models through the `FormAccessible` trait
   - Uses both `Macroable` and `Componentable` traits

3. **HtmlServiceProvider** (`src/HtmlServiceProvider.php`)
   - Registers `html` and `form` services as singletons
   - Auto-generates Blade directives (e.g., `@form_text()`, `@html_link()`)
   - Implements `DeferrableProvider` for performance

4. **Componentable Trait** (`src/Componentable.php`)
   - Allows registration of custom reusable components for both Form and Html builders
   - Components are Blade views that receive parameters via signature mapping
   - Uses `__call()` magic method to intercept component calls

5. **FormAccessible Trait** (`src/Eloquent/FormAccessible.php`)
   - Add to Eloquent models to enable form-specific mutators
   - Mutators follow pattern: `formAttributeNameAttribute($value)`
   - Supports nested model relationships
   - Handles date attributes automatically

### Service Registration Flow

1. Service provider registers `html` builder (depends on: `url`, `view`)
2. Service provider registers `form` builder (depends on: `html`, `url`, `view`, CSRF token, `request`, `session.store`)
3. Blade directives are auto-registered by introspecting public methods on both builders
4. Facades (`Form` and `Html`) provide static access to builders

### Model Binding Architecture

When `Form::model($model)` is called:
1. Form builder stores the model internally
2. For each input field, it attempts to get values in this order:
   - Old input from previous request (validation failures)
   - Value from model's `getFormValue()` method (if `FormAccessible` trait is used)
   - Direct attribute access on the model
3. Form mutators (`formXxxAttribute`) transform values before display
4. Supports dot notation for nested relationships (e.g., `user.profile.name`)

### Custom Component System

Both Form and Html builders support registering custom components:

```php
Form::component('componentName', 'view.path', ['param1', 'param2']);
```

The component is then callable as: `Form::componentName($param1, $param2)`

The Componentable trait handles:
- Component registration storage
- Parameter mapping to view variables
- Rendering via view factory
- Dynamic method interception

### Blade Directive Generation

The service provider automatically creates Blade directives by:
1. Getting all public methods from `HtmlBuilder` and `FormBuilder`
2. Filtering to only supported methods (defined in `$directives` array)
3. Creating snake_case directive names: `@form_text()`, `@html_link()`, etc.
4. Directives echo the result of the corresponding facade method

### Helper Functions

Global helper functions are auto-loaded from `helper/helpers.php`:
- `link_to()`, `link_to_asset()`, `link_to_route()`, `link_to_action()`
- `form()` - returns the form builder instance

## Testing Architecture

Tests use Orchestra Testbench to simulate a full Laravel application. The `phpunit.php` bootstrap:
- Sets up Illuminate Database Capsule with in-memory SQLite
- Creates a `models` table for testing Eloquent integration
- Loads composer autoloader and sets timezone

Test structure:
- `tests/TestCase.php` - Base test case
- `tests/Unit/FormBuilderTest.php` - Form builder functionality (~38KB, comprehensive)
- `tests/Unit/HtmlBuilderTest.php` - HTML builder functionality
- `tests/Unit/FormAccessibleTest.php` - Eloquent trait functionality

## Code Style

- Follow PSR-12 coding standard
- All patches must include tests
- Document behavior changes in README.md
- One pull request per feature
- Commit messages should be meaningful

## Important Patterns

### Trait Method Resolution

Both builders use multiple traits that define `__call()`:
```php
use Macroable, Componentable {
    Macroable::__call as macroCall;
    Componentable::__call as componentCall;
}
```

The actual `__call()` implementation (in each builder) checks in order:
1. Static components via `hasComponent()`
2. Macros via `hasMacro()`
3. Throws `BadMethodCallException` if neither

### CSRF and Method Spoofing

The FormBuilder automatically:
- Injects CSRF token in `open()` method
- Adds `_method` hidden field for PUT/PATCH/DELETE requests
- Handles file upload form encoding (`files => true` option)

### Request Consideration

The FormBuilder can be set to consider the current request for default values via `considerRequest()`. This makes form fields auto-fill from request data.

## File Locations

- **Source**: `src/`
- **Tests**: `tests/Unit/`
- **Helpers**: `helper/helpers.php`
- **Config**: No published config by default (package is self-contained)

## CI/CD

GitHub Actions workflow (`.github/workflows/main.yml`) tests against:
- PHP: 8.0, 8.1, 8.2, 8.3
- Laravel: 9.x, 10.x, 11.x, 12.x
- OS: Ubuntu, Windows
- Stability: prefer-lowest, prefer-stable

Appropriate exclusions are defined for incompatible PHP/Laravel combinations.