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

```php
// Text inputs
{{ Form::text('name', null, ['class' => 'form-control']) }}
{{ Form::email('email', null, ['placeholder' => 'Enter email']) }}
{{ Form::password('password', ['class' => 'form-control']) }}
{{ Form::number('age', null, ['min' => 18, 'max' => 100]) }}
{{ Form::tel('phone', null, ['pattern' => '[0-9]{10}']) }}
{{ Form::url('website', null, ['placeholder' => 'https://example.com']) }}

// Date and time inputs
{{ Form::date('birth_date') }}
{{ Form::time('meeting_time') }}
{{ Form::datetime('event_datetime') }}
{{ Form::datetimeLocal('local_datetime') }}
{{ Form::month('month') }}
{{ Form::week('week') }}

// Other input types
{{ Form::search('query', null, ['placeholder' => 'Search...']) }}
{{ Form::range('volume', 50, ['min' => 0, 'max' => 100]) }}
{{ Form::color('theme_color', '#ff0000') }}
{{ Form::file('document', ['accept' => '.pdf,.doc']) }}

// Hidden inputs
{{ Form::hidden('user_id', $user->id) }}
{{ Form::token() }} {{-- CSRF token --}}
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

// Select range
{{ Form::selectRange('year', 2020, 2030, 2024) }}

// Select year
{{ Form::selectYear('birth_year', 1950, date('Y'), 1990) }}

// Select month
{{ Form::selectMonth('birth_month', null, ['class' => 'form-control'], 'MMMM') }}
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

// Email link
{{ Html::mailto('info@example.com', 'Contact Us') }}
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

### Helper Functions

The package provides several helper functions for convenience:

```php
// Form helper
$form = form();

// Link helpers
link_to('users', 'View Users');
link_to_asset('css/app.css', 'Stylesheet');
link_to_route('users.show', 'View Profile', ['user' => 1]);
link_to_action('UserController@show', 'View Profile', ['user' => 1]);
```

### Blade Directives

You can use form and HTML methods directly as Blade directives:

```php
{{-- Form directives --}}
@form_open(['route' => 'users.store'])
    @form_text('name', null, ['class' => 'form-control'])
    @form_email('email')
    @form_submit('Create User')
@form_close()

{{-- HTML directives --}}
@html_link('users', 'View Users')
@html_script('js/app.js')
@html_style('css/app.css')
@html_image('logo.png', 'Logo')
```

## Eloquent Integration

### Using FormAccessible Trait

Add the `FormAccessible` trait to your Eloquent models to enable form mutators:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SpaanProductions\LaravelForm\Eloquent\FormAccessible;

class User extends Model
{
    use FormAccessible;

    protected $fillable = ['name', 'email', 'birth_date'];

    /**
     * Form mutator for birth_date
     */
    public function formBirthDateAttribute($value)
    {
        return $value ? $value->format('Y-m-d') : null;
    }

    /**
     * Form mutator for full_name (computed field)
     */
    public function formFullNameAttribute($value)
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
```

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

## Custom Components

### Creating Custom Form Components

```php
// Register a component in your service provider
Form::component('bsText', 'components.form.text', ['name', 'value', 'attributes']);

// Create the component view: resources/views/components/form/text.blade.php
<div class="form-group">
    <label for="{{ $name }}">{{ ucfirst($name) }}</label>
    <input type="text" 
           name="{{ $name }}" 
           value="{{ $value }}" 
           class="form-control {{ $attributes['class'] ?? '' }}"
           {{ $attributes['required'] ?? '' }}>
</div>

// Use the component
{{ Form::bsText('username', null, ['class' => 'form-control-lg', 'required' => 'required']) }}
```

### Creating Custom HTML Components

```php
// Register a component
Html::component('alert', 'components.alert', ['type', 'message']);

// Create the component view: resources/views/components/alert.blade.php
<div class="alert alert-{{ $type }}">
    {{ $message }}
</div>

// Use the component
{{ Html::alert('success', 'Operation completed successfully!') }}
```

## Advanced Features

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

| Method | Description |
|--------|-------------|
| `open($options)` | Open a new HTML form |
| `model($model, $options)` | Create a model-based form |
| `close()` | Close the current form |
| `token()` | Generate CSRF token field |
| `label($name, $value, $options)` | Generate a label element |
| `input($type, $name, $value, $options)` | Generate an input element |
| `text($name, $value, $options)` | Generate a text input |
| `email($name, $value, $options)` | Generate an email input |
| `password($name, $options)` | Generate a password input |
| `hidden($name, $value, $options)` | Generate a hidden input |
| `textarea($name, $value, $options)` | Generate a textarea |
| `select($name, $list, $selected, $options)` | Generate a select dropdown |
| `checkbox($name, $value, $checked, $options)` | Generate a checkbox |
| `radio($name, $value, $checked, $options)` | Generate a radio button |
| `submit($value, $options)` | Generate a submit button |
| `button($value, $options)` | Generate a button |
| `file($name, $options)` | Generate a file input |
| `image($url, $name, $options)` | Generate an image submit button |
| `reset($value, $options)` | Generate a reset button |

### HTML Builder Methods

| Method | Description |
|--------|-------------|
| `link($url, $title, $attributes)` | Generate an HTML link |
| `secureLink($url, $title, $attributes)` | Generate a secure HTML link |
| `linkAsset($url, $title, $attributes)` | Generate a link to an asset |
| `linkRoute($name, $title, $parameters, $attributes)` | Generate a link to a named route |
| `linkAction($action, $title, $parameters, $attributes)` | Generate a link to a controller action |
| `mailto($email, $title, $attributes)` | Generate a mailto link |
| `script($url, $attributes)` | Generate a script tag |
| `style($url, $attributes)` | Generate a style tag |
| `image($url, $alt, $attributes)` | Generate an image tag |
| `favicon($url, $attributes)` | Generate a favicon link |
| `meta($name, $content, $attributes)` | Generate a meta tag |
| `tag($tag, $content, $attributes)` | Generate a generic HTML tag |
| `ol($list, $attributes)` | Generate an ordered list |
| `ul($list, $attributes)` | Generate an unordered list |
| `dl($list, $attributes)` | Generate a definition list |

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
