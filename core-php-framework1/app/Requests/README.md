# app/Requests

Reserved for dedicated Form Request classes if a controller's validation
rules grow complex enough to warrant extracting them, e.g.:

```php
namespace App\Requests;

class StoreUserRequest
{
    public static function rules(): array
    {
        return [
            'name'  => 'required|max:100',
            'email' => 'required|email|unique:users,email',
        ];
    }
}
```

Then in a controller: `$data = $request->validate(StoreUserRequest::rules());`

For the included User CRUD example, rules are declared inline in
`UserController` per the simple `$request->validate([...])` pattern.
