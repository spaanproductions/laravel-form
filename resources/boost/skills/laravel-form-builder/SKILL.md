---
name: laravel-form-builder
description: Use this skill when working with Laravel forms, form inputs, model binding, CSRF protection, select dropdowns, checkboxes, radio buttons, file uploads, or any FormBuilder functionality from the spaanproductions/laravel-form package.
---

# Laravel Form Builder

A fluent interface for building HTML forms in Laravel applications with automatic CSRF protection, model binding, and smart value resolution. Part of the `spaanproductions/laravel-form` package.

**Namespace**: `SpaanProductions\LaravelForm`
**Facade**: `Form` or `\SpaanProductions\LaravelForm\Facades\Form`
**Helper**: `form()` returns the form builder instance

## When to Use

- Creating HTML forms with automatic CSRF token injection
- Building forms that bind to Eloquent models for editing
- Generating form inputs with automatic value population from old input, request data, or model attributes
- Handling validation errors with automatic old input restoration
- Creating select dropdowns with complex option structures
- Implementing RESTful forms with method spoofing (PUT, PATCH, DELETE)
- Building file upload forms
- Working with date/time inputs that need proper formatting

## Key Capabilities

- **Automatic CSRF Protection**: CSRF tokens injected automatically on all non-GET forms
- **Model Binding**: Bind Eloquent models to forms for automatic value population
- **Smart Value Resolution**: Values resolved in order: old input → request → model → default
- **Method Spoofing**: Automatic handling of PUT/PATCH/DELETE via hidden `_method` field
- **Form Mutators**: Define custom form-specific attribute transformations via `FormAccessible` trait
- **Nested Relationships**: Access nested model data using dot notation (e.g., `user.profile.name`)
- **Date/Time Formatting**: Automatic formatting of DateTime objects for HTML5 date/time inputs
- **Advanced Select Options**: Per-option attributes and nested option groups
- **Custom Components**: Register reusable form components as Blade views
- **Macros**: Extend FormBuilder with custom methods using Laravel's Macroable trait
- **Blade Directives**: Auto-generated Blade directives (e.g., `@form_text()`, `@form_select()`)

## Core Methods

### Form Management

**`open(array $options = [])`**
Opens a new form. Automatically adds CSRF token for non-GET requests and method spoofing for PUT/PATCH/DELETE. Options include `method`, `url`, `route`, `action`, `files` (for uploads).

**`model($model, array $options = [])`**
Opens a form bound to an Eloquent model. All subsequent inputs will automatically populate from the model's attributes using smart value resolution.

**`close()`**
Closes the current form and clears the model binding.

**`setModel($model)`**
Sets the model instance without opening a form.

**`getModel()`**
Returns the current model instance bound to the form.

### Text Inputs

**`text($name, $value = null, $options = [])`**
Creates a text input field.

**`email($name, $value = null, $options = [])`**
Creates an email input field with HTML5 email validation.

**`password($name, $options = [])`**
Creates a password input field. Values are never auto-filled for security.

**`url($name, $value = null, $options = [])`**
Creates a URL input field with HTML5 URL validation.

**`tel($name, $value = null, $options = [])`**
Creates a telephone input field.

**`number($name, $value = null, $options = [])`**
Creates a number input field with HTML5 number validation.

**`search($name, $value = null, $options = [])`**
Creates a search input field.

**`hidden($name, $value = null, $options = [])`**
Creates a hidden input field.

**`range($name, $value = null, $options = [])`**
Creates a range slider input.

**`color($name, $value = null, $options = [])`**
Creates a color picker input.

### Date and Time Inputs

**`date($name, $value = null, $options = [])`**
Creates a date input field. Automatically formats DateTime/DateTimeImmutable objects to `Y-m-d`.

**`datetime($name, $value = null, $options = [])`**
Creates a datetime input field. Formats DateTime objects to RFC3339 format.

**`datetimeLocal($name, $value = null, $options = [])`**
Creates a datetime-local input field. Formats DateTime objects to `Y-m-d\TH:i`.

**`time($name, $value = null, $options = [])`**
Creates a time input field. Formats DateTime objects to `H:i`.

**`month($name, $value = null, $options = [])`**
Creates a month input field. Formats DateTime objects to `Y-m`.

**`week($name, $value = null, $options = [])`**
Creates a week input field. Formats DateTime objects to `Y-\WW`.

### Text Areas and Labels

**`textarea($name, $value = null, $options = [])`**
Creates a textarea element. Supports `size` option (e.g., `'size' => '50x10'`) or individual `rows` and `cols`.

**`label($name, $value = null, $options = [], $escape_html = true)`**
Creates a label element. Automatically generates label text from field name if value is null.

### Selection Elements

**`select($name, $list = [], $selected = null, $selectAttributes = [], $optionsAttributes = [], $optgroupsAttributes = [])`**
Creates a select dropdown. Supports placeholder option, per-option attributes, nested option groups, and automatic selected state detection. Use `optionsAttributes` to pass attributes for specific options by value key.

**`selectRange($name, $begin, $end, $selected = null, $options = [])`**
Creates a select dropdown with a numeric range (e.g., years 1900-2024).

**`selectYear($name, $begin, $end, $selected = null, $options = [])`**
Alias for `selectRange()`, semantically clearer for year selection.

**`selectMonth($name, $selected = null, $options = [], $format = 'MMMM')`**
Creates a select dropdown with all 12 months. Uses Carbon for formatting month names.

**`datalist($id, $list = [])`**
Creates a datalist element for autocomplete suggestions on text inputs.

### Checkboxes and Radio Buttons

**`checkbox($name, $value = 1, $checked = null, $options = [])`**
Creates a checkbox input. Supports array values for multiple checkbox groups. Smart checked state detection from old input, request, or model.

**`radio($name, $value = null, $checked = null, $options = [])`**
Creates a radio button input. Smart checked state detection based on value comparison.

### Buttons

**`submit($value = null, $options = [])`**
Creates a submit button.

**`button($value = null, $options = [])`**
Creates a button element with `type="button"` by default.

**`reset($value, $attributes = [])`**
Creates a reset button.

**`image($url, $name = null, $attributes = [])`**
Creates an image button using the specified asset URL.

### Files

**`file($name, $options = [])`**
Creates a file upload input. Remember to set `'files' => true` in the `open()` options to set proper form encoding.

### Security

**`token()`**
Generates a hidden CSRF token field. Automatically called by `open()` for non-GET forms.

### Value Resolution

**`getValueAttribute($name, $value = null)`**
Gets the value that should be assigned to a field. Resolves in order: old input → request (if enabled) → explicit value → model attribute. Handles nested relationships with dot notation.

**`old($name)`**
Gets a value from the session's old input (from validation failures).

**`considerRequest($consider = true)`**
Enables or disables automatic value population from current request data.

### Custom Components

**`component($name, $view, $signature = [])`** (static method)
Registers a custom form component. The component is a Blade view that receives parameters mapped via the signature array.

## Usage Patterns

### Basic Form with CSRF Protection

```php
{!! Form::open(['url' => 'user/profile']) !!}
    {!! Form::label('email', 'Email Address') !!}
    {!! Form::text('email', null, ['class' => 'form-control']) !!}

    {!! Form::submit('Update Profile') !!}
{!! Form::close() !!}
```

### Model-Bound Form for Editing

```php
{!! Form::model($user, ['url' => route('user.update', $user->id), 'method' => 'PUT']) !!}
    {!! Form::text('name') !!}
    {!! Form::email('email') !!}
    {!! Form::textarea('bio') !!}

    {!! Form::submit('Save Changes') !!}
{!! Form::close() !!}
```

All inputs automatically populate with `$user->name`, `$user->email`, etc. Old input takes precedence if validation fails.

### File Upload Form

```php
{!! Form::open(['url' => 'photo', 'files' => true]) !!}
    {!! Form::file('photo', ['accept' => 'image/*']) !!}
    {!! Form::submit('Upload') !!}
{!! Form::close() !!}
```

### RESTful Form with Method Spoofing

```php
{!! Form::model($post, ['url' => route('posts.destroy', $post->id), 'method' => 'DELETE']) !!}
    {!! Form::submit('Delete Post', ['onclick' => 'return confirm("Are you sure?")']) !!}
{!! Form::close() !!}
```

Automatically adds hidden `_method` field with value `DELETE`.

### Route and Action-Based Forms

```php
// Using named route
{!! Form::open(['url' => route('user.store')]) !!}

// Using route with parameters
{!! Form::open(['url' => route('user.update', [$user->id])]) !!}

// Using controller action
{!! Form::open(['action' => 'UserController@store']) !!}

// Using controller action with parameters
{!! Form::open(['action' => ['UserController@update', $user->id]]) !!}
```

### Select with Option Attributes

```php
$users = [
    1 => 'John Doe',
    2 => 'Jane Smith',
    3 => 'Bob Johnson',
];

$optionsAttributes = [
    2 => ['disabled' => true],
    3 => ['data-role' => 'admin'],
];

{!! Form::select('user_id', $users, null, ['class' => 'form-control'], $optionsAttributes) !!}
```

### Select with Nested Option Groups

```php
$countries = [
    'Europe' => [
        'uk' => 'United Kingdom',
        'fr' => 'France',
    ],
    'Asia' => [
        'jp' => 'Japan',
        'cn' => 'China',
    ],
];

{!! Form::select('country', $countries) !!}
```

### Date Input with Model Binding

```php
// Assuming $event->start_date is a Carbon or DateTime instance
{!! Form::model($event, ['url' => route('events.update', $event->id)]) !!}
    {!! Form::date('start_date') !!}  <!-- Automatically formatted to Y-m-d -->
    {!! Form::time('start_time') !!}  <!-- Automatically formatted to H:i -->
{!! Form::close() !!}
```

### Checkbox Arrays for Multiple Selection

```php
{!! Form::checkbox('permissions[]', 'edit', in_array('edit', $user->permissions)) !!}
{!! Form::checkbox('permissions[]', 'delete', in_array('delete', $user->permissions)) !!}
{!! Form::checkbox('permissions[]', 'publish', in_array('publish', $user->permissions)) !!}
```

### Custom Components

```php
// Register a custom component (typically in a service provider)
Form::component('customInput', 'components.form.custom-input', ['name', 'value', 'attributes']);

// Use the component
{!! Form::customInput('username', 'defaultValue', ['class' => 'custom-class']) !!}
```

The component view receives `$name`, `$value`, and `$attributes` variables.

### Using Form Mutators with Models

```php
// In your Eloquent model
use SpaanProductions\LaravelForm\Eloquent\FormAccessible;

class User extends Model
{
    use FormAccessible;

    // This mutator transforms the value when displaying in forms
    public function formBioAttribute($value)
    {
        return strip_tags($value); // Remove HTML for editing
    }
}

// In your form
{!! Form::model($user, ['url' => route('user.update', $user->id)]) !!}
    {!! Form::textarea('bio') !!}  <!-- Bio will have HTML stripped -->
{!! Form::close() !!}
```

### Nested Model Relationships

```php
{!! Form::model($user, ['url' => route('user.update', $user->id)]) !!}
    {!! Form::text('profile.phone') !!}        <!-- Accesses $user->profile->phone -->
    {!! Form::text('company.address.city') !!} <!-- Accesses $user->company->address->city -->
{!! Form::close() !!}
```

## Important Notes

- **Value Resolution Priority**: Old input (from validation errors) → Request data (if `considerRequest()` enabled) → Explicit value parameter → Model attribute → null
- **Automatic CSRF Protection**: All non-GET forms automatically include CSRF token. No need to manually add `@csrf`.
- **Method Spoofing**: PUT, PATCH, and DELETE methods are automatically spoofed using a hidden `_method` field since HTML forms only support GET and POST.
- **Password Security**: Password fields never auto-fill values, even when bound to a model.
- **File Input Security**: File inputs never auto-fill values for security reasons.
- **Date Formatting**: DateTime and DateTimeImmutable objects are automatically formatted to match HTML5 input requirements.
- **Form Mutators Pattern**: Methods named `formXxxAttribute($value)` in models with `FormAccessible` trait transform values before display. Useful for stripping HTML, formatting dates, or converting data structures.
- **Nested Relationships**: Use dot notation (`profile.bio`, `company.name`) to access nested model relationships. Works with both reading and validation old input.
- **Boolean Attributes**: Set boolean attributes like `['required' => true]` to render proper HTML5 boolean attributes.
- **Array Syntax**: Field names with array syntax (e.g., `permissions[]` or `user[name]`) are automatically handled for old input and model binding.
- **HtmlString Return**: All methods return `Illuminate\Support\HtmlString` instances to prevent double-escaping in Blade templates.
- **Helper Function**: Use `form()` helper to access the FormBuilder instance for method chaining or conditional logic.
- **Blade Directives**: All public methods are available as Blade directives with `@form_` prefix (e.g., `@form_text('name')` equivalent to `{!! Form::text('name') !!}`).
