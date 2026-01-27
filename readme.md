# Laravel Form

A powerful HTML and Form Builder package for the Laravel Framework. This package provides an elegant and fluent interface for creating forms and HTML elements in your Laravel applications.

## Features

- **Form Builder**: Create forms with model binding, CSRF protection, and method spoofing
- **HTML Builder**: Generate HTML elements, links, and assets with ease
- **Eloquent Integration**: Seamless integration with Eloquent models using the `FormAccessible` trait
- **Blade Directives**: Use form and HTML helpers directly in your Blade templates
- **Component System**: Create custom form components for reusable form elements
- **Helper Functions**: Convenient helper functions for common operations
- **Type Safety**: Full support for PHP 8.0+ features and type hints

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Basic Usage](#basic-usage)
  - [Form Builder](#form-builder)
  - [HTML Builder](#html-builder)
  - [Helper Functions](#helper-functions)
  - [Blade Directives](#blade-directives)
- [Eloquent Integration](#eloquent-integration)
  - [Using FormAccessible Trait](#using-formaccessible-trait)
  - [Model Binding](#model-binding)
  - [Form Value Resolution](#form-value-resolution)
- [Custom Components](#custom-components)
- [Advanced Features](#advanced-features)
  - [Security Features](#security-features)
  - [Request Consideration](#request-consideration)
  - [Old Input Handling](#old-input-handling)
  - [Method Spoofing](#method-spoofing)
  - [File Uploads](#file-uploads)
- [API Reference](#api-reference)
  - [Form Builder Methods](#form-builder-methods)
  - [HTML Builder Methods](#html-builder-methods)
- [Troubleshooting & FAQ](#troubleshooting--faq)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## Requirements

- PHP 8.0 or higher
- Laravel 9.0, 10.0, 11.0, or 12.0

## Installation

### 1. Install via Composer

```bash
composer require spaanproductions/laravel-form
```

### 2. Service Provider Registration

The package will automatically register itself with Laravel using auto-discovery. If you're using Laravel 5.4 or earlier, you'll need to manually register the service provider in your `config/app.php`:

```php
'providers' => [
    // ...
    SpaanProductions\LaravelForm\HtmlServiceProvider::class,
],

'aliases' => [
    // ...
    'Form' => SpaanProductions\LaravelForm\FormFacade::class,
    'Html' => SpaanProductions\LaravelForm\HtmlFacade::class,
],
```

### 3. Publish Configuration (Optional)

The package doesn't require a configuration file by default, but you can publish one if you need to customize settings:

```bash
php artisan vendor:publish --provider="SpaanProductions\LaravelForm\HtmlServiceProvider"
```

## Basic Usage

### Form Builder

#### Creating Forms

```php
// Basic form
{{ Form::open(['url' => 'users']) }}
    {{ Form::text('name') }}
    {{ Form::email('email') }}
    {{ Form::submit('Create User') }}
{{ Form::close() }}

// Form with model binding
{{ Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT']) }}
    {{ Form::text('name') }}
    {{ Form::email('email') }}
    {{ Form::submit('Update User') }}
{{ Form::close() }}

// Form with file upload
{{ Form::open(['route' => 'users.store', 'files' => true]) }}
    {{ Form::file('avatar') }}
    {{ Form::submit('Upload') }}
{{ Form::close() }}
```

#### Form Input Types

All form inputs automatically generate appropriate `id` attributes from field names for accessibility:

```php
// Text inputs
{{ Form::text('name', null, ['class' => 'form-control']) }}
// Generates: <input type="text" name="name" id="name" class="form-control">

{{ Form::email('email', null, ['placeholder' => 'Enter email']) }}
{{ Form::password('password', ['class' => 'form-control']) }}
{{ Form::number('age', null, ['min' => 18, 'max' => 100]) }}
{{ Form::tel('phone', null, ['pattern' => '[0-9]{10}']) }}
{{ Form::url('website', null, ['placeholder' => 'https://example.com']) }}

// Array notation is converted to valid IDs
{{ Form::text('user[name]') }}
// Generates id="user_name"

// You can override the auto-generated ID
{{ Form::text('name', null, ['id' => 'custom-id']) }}

// Date and time inputs (all accept DateTime/DateTimeImmutable objects)
{{ Form::date('birth_date') }}
{{ Form::time('meeting_time') }}
{{ Form::datetime('event_datetime') }}
{{ Form::datetimeLocal('local_datetime') }}
{{ Form::month('month') }}
{{ Form::week('week') }}

// Passing DateTime objects (automatically formatted)
{{ Form::date('event_date', new \DateTime('2024-12-25')) }}
{{ Form::date('event_date', now()) }}  // Laravel helper
{{ Form::datetime('created_at', $model->created_at) }}  // Carbon instance

// Other input types
{{ Form::search('query', null, ['placeholder' => 'Search...']) }}
{{ Form::range('volume', 50, ['min' => 0, 'max' => 100]) }}
{{ Form::color('theme_color', '#ff0000') }}
{{ Form::file('document', ['accept' => '.pdf,.doc']) }}

// Hidden inputs
{{ Form::hidden('user_id', $user->id) }}
{{ Form::token() }} {{-- CSRF token --}}
```

#### Datalist

HTML5 datalist elements provide autocomplete suggestions for input fields:

```php
{{-- Create the datalist --}}
{{ Form::datalist('browsers', ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera']) }}

{{-- Link an input to the datalist using the list attribute --}}
{{ Form::text('browser', null, ['list' => 'browsers', 'placeholder' => 'Choose a browser']) }}

{{-- Datalist with associative array --}}
{{ Form::datalist('countries', ['us' => 'United States', 'ca' => 'Canada', 'uk' => 'United Kingdom']) }}
{{ Form::text('country', null, ['list' => 'countries']) }}
```

#### Textarea

```php
{{ Form::textarea('description', null, ['rows' => 5, 'cols' => 50]) }}
```

#### Select Dropdowns

```php
// Basic select
{{ Form::select('country', ['us' => 'United States', 'ca' => 'Canada', 'uk' => 'United Kingdom']) }}

// Select with default value
{{ Form::select('country', $countries, 'us') }}

// Select with placeholder
{{ Form::select('country', ['' => 'Select Country'] + $countries, null, ['class' => 'form-control']) }}

// Select with option groups
{{ Form::select('category', [
    'Electronics' => ['laptop' => 'Laptop', 'phone' => 'Phone'],
    'Clothing' => ['shirt' => 'Shirt', 'pants' => 'Pants']
]) }}

// Advanced: Per-option attributes (e.g., disable specific options)
{{ Form::select('product', $products, null,
    ['class' => 'form-control'],
    ['product_1' => ['disabled' => true], 'product_3' => ['data-price' => '29.99']]
) }}

// Select range
{{ Form::selectRange('year', 2020, 2030, 2024) }}

// Select year
{{ Form::selectYear('birth_year', 1950, date('Y'), 1990) }}

// Select month (format parameter uses IntlDateFormatter patterns)
{{ Form::selectMonth('birth_month', null, ['class' => 'form-control'], 'MMMM') }}  // January, February, etc.
{{ Form::selectMonth('birth_month', null, [], 'MMM') }}  // Jan, Feb, etc.
{{ Form::selectMonth('birth_month', null, [], 'MM') }}  // 01, 02, etc.
```

#### Checkboxes and Radio Buttons

```php
// Checkbox
{{ Form::checkbox('terms', 1, false, ['id' => 'terms']) }}
{{ Form::label('terms', 'I agree to the terms') }}

// Radio buttons
{{ Form::radio('gender', 'male', false, ['id' => 'male']) }}
{{ Form::label('male', 'Male') }}

{{ Form::radio('gender', 'female', false, ['id' => 'female']) }}
{{ Form::label('female', 'Female') }}

// Multiple checkboxes
@foreach($interests as $interest)
    {{ Form::checkbox('interests[]', $interest->id, in_array($interest->id, $user->interests->pluck('id')->toArray())) }}
    {{ Form::label('interests', $interest->name) }}
@endforeach
```

#### Buttons

```php
{{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
{{ Form::button('Click Me', ['class' => 'btn btn-secondary']) }}
{{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
{{ Form::image('button.png', 'Submit', ['class' => 'btn-image']) }}
```

### HTML Builder

#### Links

```php
// Basic link
{{ Html::link('users', 'View Users') }}

// Link with attributes
{{ Html::link('users', 'View Users', ['class' => 'btn btn-primary']) }}

// Secure link
{{ Html::secureLink('admin/dashboard', 'Admin Dashboard') }}

// Link to asset
{{ Html::linkAsset('css/app.css', 'Stylesheet') }}

// Link to route
{{ Html::linkRoute('users.show', 'View Profile', ['user' => $user->id]) }}

// Link to action
{{ Html::linkAction('UserController@show', 'View Profile', ['user' => $user->id]) }}

// Email link (automatically obfuscated to prevent spam bots)
{{ Html::mailto('info@example.com', 'Contact Us') }}

// Secure asset link
{{ Html::linkSecureAsset('documents/report.pdf', 'Download Report') }}
```

#### Assets

```php
// Script tags
{{ Html::script('js/app.js') }}
{{ Html::script('js/app.js', ['defer' => true]) }}

// Style tags
{{ Html::style('css/app.css') }}
{{ Html::style('css/app.css', ['media' => 'print']) }}

// Images
{{ Html::image('images/logo.png', 'Company Logo') }}
{{ Html::image('images/logo.png', 'Company Logo', ['class' => 'logo']) }}

// Favicon
{{ Html::favicon('favicon.ico') }}
```

#### Lists

```php
// Ordered list
{{ Html::ol(['Item 1', 'Item 2', 'Item 3']) }}

// Unordered list
{{ Html::ul(['Apple', 'Banana', 'Orange']) }}

// Definition list
{{ Html::dl(['Name' => 'John Doe', 'Email' => 'john@example.com']) }}
```

#### Meta Tags

```php
{{ Html::meta('description', 'This is my website description') }}
{{ Html::meta('keywords', 'laravel, php, web development') }}
{{ Html::meta('viewport', 'width=device-width, initial-scale=1') }}
```

#### Generic HTML Tags

```php
{{ Html::tag('div', 'Content here', ['class' => 'container']) }}
{{ Html::tag('span', 'Inline text', ['style' => 'color: red;']) }}
```

#### Utility Methods

```php
// HTML entity encoding/decoding
{{ Html::entities('<script>alert("XSS")</script>') }}  // Converts to entities
{{ Html::decode('&lt;p&gt;Hello&lt;/p&gt;') }}  // Converts back to HTML

// Email obfuscation (anti-spam)
{{ Html::email('contact@example.com') }}  // Returns obfuscated email string
{{ Html::obfuscate('Sensitive text') }}  // Obfuscates any text

// Non-breaking spaces
{{ Html::nbsp(3) }}  // Generates &nbsp;&nbsp;&nbsp;
```

### Helper Functions

The package provides global helper functions for convenience. These are automatically loaded and available throughout your application.

#### Available Helpers

```php
// Get the FormBuilder instance
$form = form();
// Returns: SpaanProductions\LaravelForm\FormBuilder

// Use it programmatically
$emailInput = form()->email('contact', 'user@example.com', ['class' => 'form-control']);

// Link helpers (all return HtmlString objects)
link_to('users', 'View Users', ['class' => 'btn']);
link_to_asset('css/app.css', 'Stylesheet');
link_to_route('users.show', 'View Profile', ['user' => 1]);
link_to_action([UserController::class, 'show'], 'View Profile', ['user' => 1]);
```

#### Helper Function Signatures

```php
/**
 * Generate a HTML link
 * @param string $url
 * @param string $title
 * @param array $attributes
 * @param bool $secure
 * @param bool $escape
 * @return \Illuminate\Support\HtmlString
 */
function link_to($url, $title = null, $attributes = [], $secure = null, $escape = true)

/**
 * Generate a HTML link to an asset
 * @param string $url
 * @param string $title
 * @param array $attributes
 * @param bool $secure
 * @param bool $escape
 * @return \Illuminate\Support\HtmlString
 */
function link_to_asset($url, $title = null, $attributes = [], $secure = null, $escape = true)

/**
 * Generate a HTML link to a named route
 * @param string $name
 * @param string $title
 * @param array $parameters
 * @param array $attributes
 * @return \Illuminate\Support\HtmlString
 */
function link_to_route($name, $title = null, $parameters = [], $attributes = [])

/**
 * Generate a HTML link to a controller action
 * @param string $action
 * @param string $title
 * @param array $parameters
 * @param array $attributes
 * @return \Illuminate\Support\HtmlString
 */
function link_to_action($action, $title = null, $parameters = [], $attributes = [])

/**
 * Get the FormBuilder instance
 * @return \SpaanProductions\LaravelForm\FormBuilder
 */
function form()
```

#### Usage Examples

```php
// In controllers
public function create()
{
    // Build a form element programmatically
    $cancelLink = link_to_route('users.index', 'Cancel', [], ['class' => 'btn btn-secondary']);

    return view('users.create', compact('cancelLink'));
}

// In views
<div class="actions">
    {!! link_to_route('users.index', 'Back to List', [], ['class' => 'btn btn-link']) !!}
    {!! link_to_asset('docs/manual.pdf', 'Download Manual', ['target' => '_blank']) !!}
</div>

// Using the form helper
@php
    $formInstance = form();
    $isModelSet = $formInstance->getModel() !== null;
@endphp
```

**Note:** Helper functions return `HtmlString` objects which are safe to echo directly without additional escaping.

### Blade Directives

The package automatically generates 50+ Blade directives for all public methods on the Form and HTML builders. Directives follow a simple naming convention: `@{builder}_{method}` in snake_case.

**Available Directive Patterns:**

- Form directives: `@form_*` (e.g., `@form_text`, `@form_select`, `@form_checkbox`)
- HTML directives: `@html_*` (e.g., `@html_link`, `@html_script`, `@html_image`)

```php
{{-- Form directives --}}
@form_open(['route' => 'users.store'])
    @form_text('name', null, ['class' => 'form-control'])
    @form_email('email')
    @form_date('birth_date')
    @form_select('country', $countries)
    @form_textarea('bio', null, ['rows' => 5])
    @form_checkbox('terms', 1, false)
    @form_submit('Create User')
@form_close()

{{-- HTML directives --}}
@html_link('users', 'View Users')
@html_link_route('users.show', 'Profile', ['user' => 1])
@html_script('js/app.js')
@html_style('css/app.css')
@html_image('logo.png', 'Logo')
@html_mailto('contact@example.com', 'Email Us')

{{-- Date/time directives --}}
@form_datetime_local('meeting', null, ['class' => 'form-control'])
@form_month('month')
@form_week('week')

{{-- Advanced directives --}}
@form_select_range('year', 2020, 2030)
@form_select_month('birth_month')
@form_datalist('browsers', ['Chrome', 'Firefox', 'Safari'])
```

**Note:** All directives echo their output automatically, just like `{{ }}` blade syntax.

## Eloquent Integration

### Using FormAccessible Trait

Add the `FormAccessible` trait to your Eloquent models to enable form mutators. Form mutators allow you to transform model attributes specifically for display in forms, without affecting how they're stored or accessed elsewhere.

**Form Mutator Pattern:** `form{AttributeName}Attribute($value)`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SpaanProductions\LaravelForm\Eloquent\FormAccessible;

class User extends Model
{
    use FormAccessible;

    protected $fillable = ['name', 'email', 'birth_date', 'salary'];

    protected $casts = [
        'birth_date' => 'datetime',
        'salary' => 'decimal:2'
    ];

    /**
     * Form mutator for birth_date
     * Formats the date specifically for form display
     */
    public function formBirthDateAttribute($value)
    {
        return $value ? $value->format('Y-m-d') : null;
    }

    /**
     * Form mutator for computed field
     * Useful for read-only fields that combine multiple attributes
     */
    public function formFullNameAttribute($value)
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Form mutator for salary
     * Display without currency symbol in forms, but include it elsewhere
     */
    public function formSalaryAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    /**
     * Regular accessor (for general display)
     * Shows salary with currency symbol
     */
    public function getSalaryAttribute($value)
    {
        return '$' . number_format($value, 2);
    }
}
```

**Key Benefits:**

- **Separation of Concerns**: Different formatting for forms vs. display
- **Date Handling**: Automatically handles DateTime objects in form fields
- **Computed Fields**: Display derived values without storing them
- **No Side Effects**: Doesn't affect normal attribute access or database operations

### Model Binding

```php
// In your controller
public function edit(User $user)
{
    return view('users.edit', compact('user'));
}

// In your Blade template
{{ Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT']) }}
    {{ Form::text('name') }}
    {{ Form::email('email') }}
    {{ Form::date('birth_date') }}
    {{ Form::text('full_name') }} {{-- Uses form mutator --}}
    {{ Form::submit('Update User') }}
{{ Form::close() }}
```

### Form Value Resolution

The Form Builder uses a specific priority order when determining what value to display in form fields. Understanding this order is crucial for debugging form behavior:

**Priority Order (highest to lowest):**

1. **Old Input** - Values from the previous request (after validation failure)
2. **Request Data** - Current request values (only when `considerRequest()` is enabled)
3. **Explicit Value** - The value parameter you pass to the form method
4. **Model Attribute** - Value from the bound model (with form mutators applied)
5. **Null** - Default fallback

```php
// Example demonstrating the priority order:

// 1. After validation failure, old input takes precedence
{{ Form::model($user, ['route' => 'users.store']) }}
    {{-- If validation fails, this will show the previously submitted value --}}
    {{ Form::text('name') }}
{{ Form::close() }}

// 2. With considerRequest(), current request data is used
{{ Form::open(['route' => 'search']) }}
    {{ Form::considerRequest() }}
    {{-- Will populate from current request query parameters --}}
    {{ Form::text('query') }}
{{ Form::close() }}

// 3. Explicit values override model values
{{ Form::model($user, ['route' => 'users.update']) }}
    {{-- Shows 'Default Name' instead of $user->name --}}
    {{ Form::text('name', 'Default Name') }}
{{ Form::close() }}

// 4. Model values with form mutators
{{ Form::model($user, ['route' => 'users.update']) }}
    {{-- If User has formBirthDateAttribute(), that mutator is applied --}}
    {{ Form::date('birth_date') }}
{{ Form::close() }}
```

**Accessing Nested Model Relationships:**

The Form Builder supports dot notation for accessing nested relationships:

```php
{{ Form::model($user, ['route' => 'profile.update']) }}
    {{-- Access nested relationship attributes --}}
    {{ Form::text('profile.bio') }}
    {{ Form::text('address.city') }}
    {{ Form::text('address.country.name') }}

    {{-- Works with form mutators on nested models too --}}
    {{ Form::date('profile.birth_date') }}
{{ Form::close() }}
```

**Getting and Setting the Model:**

```php
// Set model after opening a form
$form = Form::open(['route' => 'users.store']);
Form::setModel($user);

// Get the current model
$currentModel = Form::getModel();
```

## Custom Components

Both Form and HTML builders support a powerful component system for creating reusable form elements. Components are registered once and can be used throughout your application like built-in methods.

### Creating Custom Form Components

**Step 1: Register the component** (typically in `AppServiceProvider`)

```php
use SpaanProductions\LaravelForm\FormFacade as Form;

public function boot()
{
    // Register a Bootstrap-styled text input component
    // Syntax: component(name, view, [parameters])
    Form::component('bsText', 'components.form.text', ['name', 'value', 'attributes']);

    // Register a complete form group with label and error handling
    Form::component('bsFormGroup', 'components.form.group', ['name', 'label', 'type', 'value', 'attributes']);
}
```

**Step 2: Create the component view**

```php
{{-- resources/views/components/form/text.blade.php --}}
<div class="form-group mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ ucfirst(str_replace('_', ' ', $name)) }}
    </label>
    <input type="text"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ $value }}"
           class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }} {{ $attributes['class'] ?? '' }}"
           {!! Html::attributes($attributes) !!}>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

**Step 3: Use the component**

```php
{{ Form::bsText('username', null, ['placeholder' => 'Enter username', 'required' => true]) }}
{{ Form::bsText('email', $user->email, ['class' => 'form-control-lg']) }}
```

### Advanced Form Component Example

```php
{{-- resources/views/components/form/group.blade.php --}}
<div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>

    @if($type === 'textarea')
        <textarea name="{{ $name }}"
                  id="{{ $name }}"
                  class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}"
                  {!! Html::attributes($attributes) !!}>{{ $value }}</textarea>
    @elseif($type === 'select')
        <select name="{{ $name }}"
                id="{{ $name }}"
                class="form-select {{ $errors->has($name) ? 'is-invalid' : '' }}"
                {!! Html::attributes($attributes) !!}>
            {!! $value !!}  {{-- Pass rendered options --}}
        </select>
    @else
        <input type="{{ $type }}"
               name="{{ $name }}"
               id="{{ $name }}"
               value="{{ $value }}"
               class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}"
               {!! Html::attributes($attributes) !!}>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if(isset($attributes['help']))
        <div class="form-text">{{ $attributes['help'] }}</div>
    @endif
</div>

{{-- Usage --}}
{{ Form::bsFormGroup('email', 'Email Address', 'email', null, ['help' => 'We will never share your email']) }}
```

### Creating Custom HTML Components

```php
// Register in AppServiceProvider
public function boot()
{
    Html::component('alert', 'components.alert', ['type', 'message', 'dismissible']);
    Html::component('card', 'components.card', ['title', 'content', 'footer']);
}
```

```php
{{-- resources/views/components/alert.blade.php --}}
<div class="alert alert-{{ $type ?? 'info' }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    {{ $message }}
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    @endif
</div>

{{-- Usage --}}
{{ Html::alert('success', 'User created successfully!', true) }}
{{ Html::alert('danger', 'An error occurred.', false) }}
```

### Component Parameter Mapping

Parameters are mapped to view variables by position:

```php
// Registration
Form::component('example', 'components.example', ['param1', 'param2', 'param3']);

// Usage - parameters are mapped in order
{{ Form::example('value1', 'value2', 'value3') }}

// In the view, you'll have access to:
// $param1 = 'value1'
// $param2 = 'value2'
// $param3 = 'value3'
```

**Default Values in Components:**

```php
{{-- Handle undefined parameters with defaults --}}
<div class="custom-input">
    <input type="{{ $type ?? 'text' }}"
           name="{{ $name }}"
           value="{{ $value ?? '' }}"
           placeholder="{{ $placeholder ?? '' }}">
</div>
```

### Real-World Example: Tailwind Form Components

```php
// AppServiceProvider
public function boot()
{
    Form::component('twInput', 'components.tailwind.input', ['name', 'label', 'value', 'type', 'attributes']);
}
```

```php
{{-- resources/views/components/tailwind/input.blade.php --}}
<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
        </label>
    @endif

    <input type="{{ $type ?? 'text' }}"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ $value }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ $errors->has($name) ? 'border-red-500' : '' }} {{ $attributes['class'] ?? '' }}"
           {!! Html::attributes(collect($attributes)->except('class')->toArray()) !!}>

    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Usage --}}
{{ Form::model($user, ['route' => 'users.update']) }}
    {{ Form::twInput('name', 'Full Name', null, 'text', ['required' => true]) }}
    {{ Form::twInput('email', 'Email Address', null, 'email', ['placeholder' => 'you@example.com']) }}
    <button type="submit" class="btn btn-primary">Save</button>
{{ Form::close() }}
```

## Advanced Features

### Security Features

The Laravel Form package includes several built-in security features to protect your application:

#### 1. CSRF Protection (Automatic)

All forms automatically include CSRF token protection when using POST, PUT, PATCH, or DELETE methods:

```php
{{ Form::open(['route' => 'users.store']) }}
    {{-- CSRF token is automatically added --}}
    {{ Form::text('name') }}
{{ Form::close() }}

// Manual CSRF token (if needed)
{{ Form::token() }}
```

#### 2. XSS Prevention via Entity Encoding

Form values and HTML output are automatically encoded to prevent XSS attacks:

```php
// User input containing <script> tags will be safely encoded
{{ Form::text('name', $userInput) }}  // Automatically escaped

// The $escape parameter controls encoding behavior
{{ Html::link('url', '<b>Bold</b>', [], null, false) }}  // Renders bold text
{{ Html::link('url', '<b>Bold</b>', [], null, true) }}   // Shows literal <b> tags (safer)
```

#### 3. Email Obfuscation (Anti-Spam)

Email addresses in mailto links are automatically obfuscated to prevent spam bot harvesting:

```php
// Automatically obfuscated using random entity encoding
{{ Html::mailto('contact@example.com') }}
// Output: <a href="&#109;&#x61;&#x69;&#108;&#116;&#x6f;&#58;&#99;...">

// Obfuscate email without creating a link
{{ Html::email('admin@example.com') }}
// Output: &#97;&#100;&#x6d;&#x69;&#110;&#64;...

// Obfuscate any text
{{ Html::obfuscate('Sensitive information') }}
```

#### 4. Method Spoofing (REST Support)

Forms handle HTTP method spoofing securely for PUT, PATCH, and DELETE requests:

```php
{{ Form::open(['route' => ['users.destroy', $user->id], 'method' => 'DELETE']) }}
    {{-- Generates: <form method="POST"> --}}
    {{-- Plus hidden field: <input type="hidden" name="_method" value="DELETE"> --}}
    {{ Form::submit('Delete User') }}
{{ Form::close() }}
```

#### 5. Secure HTTPS Links

Generate secure HTTPS links easily:

```php
// Force HTTPS on any link
{{ Html::secureLink('admin/dashboard', 'Admin Panel') }}

// Secure asset links
{{ Html::linkSecureAsset('documents/contract.pdf', 'Download Contract') }}
```

**Best Practices:**

- Always validate and sanitize user input server-side (these tools help with output, not input validation)
- Use the `$escape` parameter wisely - default to `true` unless you trust the content
- Never disable CSRF protection in production
- Use HTTPS in production environments
- Be cautious when using `false` for the `$escape` parameter

### Request Consideration

Enable request consideration to automatically fill form values from the current request:

```php
{{ Form::open(['route' => 'users.store']) }}
    {{ Form::considerRequest() }}
    {{ Form::text('name') }} {{-- Will be filled from request if available --}}
    {{ Form::email('email') }}
    {{ Form::submit('Create User') }}
{{ Form::close() }}
```

### Old Input Handling

The form builder automatically handles old input from failed validation:

```php
{{ Form::text('name', null, ['class' => 'form-control']) }}
{{ Form::email('email', null, ['class' => 'form-control']) }}

// If validation fails, the fields will be automatically filled with old input
```

### Method Spoofing

The form builder automatically handles method spoofing for PUT, PATCH, and DELETE requests:

```php
{{ Form::open(['route' => ['users.update', $user->id], 'method' => 'PUT']) }}
    {{ Form::text('name') }}
    {{ Form::submit('Update') }}
{{ Form::close() }}

// This will create a form with method="POST" and add a hidden _method field with value "PUT"
```

### File Uploads

```php
{{ Form::open(['route' => 'users.store', 'files' => true]) }}
    {{ Form::file('avatar', ['accept' => 'image/*']) }}
    {{ Form::file('documents[]', ['multiple' => true, 'accept' => '.pdf,.doc,.docx']) }}
    {{ Form::submit('Upload') }}
{{ Form::close() }}
```

## API Reference

### Form Builder Methods

#### Form Control Methods

| Method | Description |
|--------|-------------|
| `open($options)` | Open a new HTML form. Options: `url`, `route`, `action`, `method`, `files` |
| `model($model, $options)` | Create a model-based form with automatic value binding |
| `close()` | Close the current form and reset model binding |
| `token()` | Generate CSRF token hidden field |
| `getModel()` | Get the current model instance |
| `setModel($model)` | Set the model instance for value binding |

#### Input Elements

| Method | Description |
|--------|-------------|
| `label($name, $value, $options, $escape)` | Generate a label element |
| `input($type, $name, $value, $options)` | Generate a generic input element |
| `text($name, $value, $options)` | Generate a text input |
| `email($name, $value, $options)` | Generate an email input |
| `password($name, $options)` | Generate a password input (never fills value) |
| `url($name, $value, $options)` | Generate a URL input |
| `tel($name, $value, $options)` | Generate a telephone input |
| `number($name, $value, $options)` | Generate a number input |
| `search($name, $value, $options)` | Generate a search input |
| `range($name, $value, $options)` | Generate a range slider input |
| `hidden($name, $value, $options)` | Generate a hidden input |
| `file($name, $options)` | Generate a file upload input |
| `color($name, $value, $options)` | Generate a color picker input |

#### Date & Time Inputs

| Method | Description |
|--------|-------------|
| `date($name, $value, $options)` | Generate a date input (accepts DateTime objects) |
| `datetime($name, $value, $options)` | Generate a datetime input (accepts DateTime objects) |
| `datetimeLocal($name, $value, $options)` | Generate a datetime-local input (accepts DateTime objects) |
| `time($name, $value, $options)` | Generate a time input (accepts DateTime objects) |
| `month($name, $value, $options)` | Generate a month input (accepts DateTime objects) |
| `week($name, $value, $options)` | Generate a week input (accepts DateTime objects) |

#### Selection & Choice Elements

| Method | Description |
|--------|-------------|
| `select($name, $list, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes)` | Generate a select dropdown with advanced option control |
| `selectRange($name, $begin, $end, $selected, $options)` | Generate a select with numeric range |
| `selectYear($name, $begin, $end, $selected, $options)` | Alias for selectRange (for year selection) |
| `selectMonth($name, $selected, $options, $format)` | Generate a select with localized month names |
| `datalist($id, $list)` | Generate an HTML5 datalist element for autocomplete |
| `checkbox($name, $value, $checked, $options)` | Generate a checkbox input |
| `radio($name, $value, $checked, $options)` | Generate a radio button input |

#### Buttons

| Method | Description |
|--------|-------------|
| `submit($value, $options)` | Generate a submit button |
| `button($value, $options)` | Generate a button element |
| `reset($value, $options)` | Generate a reset button |
| `image($url, $name, $options)` | Generate an image submit button |

#### Textarea

| Method | Description |
|--------|-------------|
| `textarea($name, $value, $options)` | Generate a textarea. Supports `size` option (e.g., '50x10') |

#### Value Resolution

| Method | Description |
|--------|-------------|
| `getValueAttribute($name, $value)` | Get the value for a field (respects priority: old input → request → explicit → model) |
| `old($name)` | Retrieve old input from session |
| `considerRequest($consider)` | Enable/disable using current request for default values |

#### Session Management

| Method | Description |
|--------|-------------|
| `getSessionStore()` | Get the current session store instance |
| `setSessionStore($session)` | Set a custom session store instance |

### HTML Builder Methods

#### Link Generation

| Method | Description |
|--------|-------------|
| `link($url, $title, $attributes, $secure, $escape)` | Generate an HTML link |
| `secureLink($url, $title, $attributes, $escape)` | Generate a secure HTTPS link |
| `linkAsset($url, $title, $attributes, $secure, $escape)` | Generate a link to an asset file |
| `linkSecureAsset($url, $title, $attributes, $escape)` | Generate a secure HTTPS link to an asset |
| `linkRoute($name, $title, $parameters, $attributes, $secure, $escape)` | Generate a link to a named route |
| `linkAction($action, $title, $parameters, $attributes, $secure, $escape)` | Generate a link to a controller action |
| `mailto($email, $title, $attributes, $escape)` | Generate an obfuscated mailto link (anti-spam) |

#### Assets

| Method | Description |
|--------|-------------|
| `script($url, $attributes, $secure)` | Generate a script tag. Supports attributes like `defer`, `async` |
| `style($url, $attributes, $secure)` | Generate a stylesheet link. Supports `media` attribute |
| `image($url, $alt, $attributes, $secure)` | Generate an image tag |
| `favicon($url, $attributes, $secure)` | Generate a favicon link tag |

#### Lists

| Method | Description |
|--------|-------------|
| `ol($list, $attributes)` | Generate an ordered list (supports nested arrays) |
| `ul($list, $attributes)` | Generate an unordered list (supports nested arrays) |
| `dl($list, $attributes)` | Generate a definition list |

#### Meta & Generic Tags

| Method | Description |
|--------|-------------|
| `meta($name, $content, $attributes)` | Generate a meta tag |
| `tag($tag, $content, $attributes)` | Generate any HTML tag with content |

#### Utility Methods

| Method | Description |
|--------|-------------|
| `entities($value)` | Convert string to HTML entities (XSS protection) |
| `decode($value)` | Convert HTML entities back to characters |
| `email($email)` | Obfuscate an email address (returns string, not link) |
| `obfuscate($value)` | Obfuscate any string to prevent spam bot harvesting |
| `nbsp($num)` | Generate non-breaking space entities (&nbsp;) |
| `attributes($attributes)` | Build an HTML attribute string from an array |

## Troubleshooting & FAQ

### Common Issues

#### Q: Why isn't my form field populating with the model value?

**A:** Check the value resolution priority order:

1. Is there old input in the session? (After validation failure, old input takes precedence)
2. Is `considerRequest()` enabled and is there request data?
3. Did you pass an explicit value parameter?
4. Is the model actually set? Use `Form::getModel()` to verify
5. Is the attribute name correct and accessible on the model?

```php
// Debug value resolution
@if(Form::getModel())
    Model is set: {{ get_class(Form::getModel()) }}
@endif

// Check what value the form builder is using
{{ Form::getValueAttribute('field_name') }}
```

#### Q: How do I access nested model relationships in forms?

**A:** Use dot notation:

```php
{{ Form::model($user, ['route' => 'profile.update']) }}
    {{-- Access nested relationships --}}
    {{ Form::text('profile.bio') }}
    {{ Form::text('company.address.city') }}
{{ Form::close() }}
```

Make sure the relationships are loaded (use `$user->load('profile', 'company.address')`).

#### Q: My form mutators aren't working

**A:** Verify the following:

1. You've added the `FormAccessible` trait to your model
2. The method name follows the pattern: `form{AttributeName}Attribute`
3. The attribute name matches exactly (case-sensitive)
4. You're using `Form::model()` for model binding

```php
// Correct mutator pattern
public function formBirthDateAttribute($value)
{
    return $value ? $value->format('Y-m-d') : null;
}
```

#### Q: Can I disable CSRF protection for a specific form?

**A:** CSRF protection is automatic. If you need to disable it (not recommended), you can:

1. Remove the token manually (not ideal): Don't use `Form::open()`, write raw HTML
2. Exclude the route in `VerifyCsrfToken` middleware
3. Add the route to `$except` array in the CSRF middleware

**Better approach:** Keep CSRF enabled and handle it properly in your JavaScript/AJAX calls.

#### Q: Old input isn't showing after validation failure

**A:** This usually means the session isn't configured properly:

1. Verify session configuration in `config/session.php`
2. Check that the session middleware is active for your route
3. Ensure you're redirecting with `withInput()`: `return back()->withInput()`
4. Verify the FormBuilder has access to the session (dependency injection handles this automatically)

#### Q: How do I create custom form components?

**A:** Register them in a service provider:

```php
// In AppServiceProvider or custom provider
public function boot()
{
    Form::component('bsText', 'components.form.bootstrap-text', ['name', 'value', 'attributes']);
}
```

See the [Custom Components](#custom-components) section for full examples.

#### Q: Date inputs aren't formatting correctly

**A:** DateTime objects are automatically formatted for HTML5 date inputs. If using custom formats:

```php
// The form builder handles these automatically:
{{ Form::date('date', $model->date) }}  // Expects Y-m-d
{{ Form::datetime('datetime', $model->datetime) }}  // RFC3339 format
{{ Form::datetimeLocal('datetime', $model->datetime) }}  // Y-m-d\TH:i

// If you need custom formatting, use a form mutator:
public function formCustomDateAttribute($value)
{
    return $value ? $value->format('m/d/Y') : null;
}
```

#### Q: How do I add custom validation display?

**A:** Combine with Laravel's error handling:

```php
{{ Form::text('email', null, ['class' => 'form-control ' . ($errors->has('email') ? 'is-invalid' : '')]) }}

@error('email')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
```

#### Q: Can I use this with Vue/React components?

**A:** Yes, but you have two approaches:

1. **Server-side rendering**: Use Form Builder in Blade, then hydrate with JavaScript
2. **API approach**: Build forms in JavaScript, use Laravel for API endpoints only

For SPAs, consider using the Form Builder primarily for initial page loads or progressive enhancement.

## Testing

The package includes comprehensive test coverage. To run the tests:

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The Laravel Form package is open-sourced software licensed under the [MIT license](LICENSE.txt).

## Support

- **Issues**: [GitHub Issues](https://github.com/spaanproductions/laravel-form/issues)
- **Source**: [GitHub Repository](https://github.com/spaanproductions/laravel-form)
- **Website**: [Spaan Productions](https://spaanproductions.nl)
