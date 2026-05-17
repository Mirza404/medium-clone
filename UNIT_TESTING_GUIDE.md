# Unit Testing Guide

This project uses Laravel with Pest. The current unit tests live in `tests/Unit`, and the feature tests live in `tests/Feature`.

The short version:

- Use **unit tests** for small pieces of logic you can test directly, like model helper methods and request rule definitions.
- Use **feature tests** for routes, controllers, authentication, database writes, redirects, JSON responses, and rendered pages.
- Do not try to unit test every file. Test behavior that can break and behavior you rely on.

## How To Run Tests

Run the full test suite:

```bash
php artisan test
```

Run only unit tests:

```bash
php artisan test --testsuite=Unit
```

Run one test file:

```bash
php artisan test tests/Unit/readTimeTest.php
```

## Current Unit Tests

This repo already has unit tests for:

- `Post::readTime()` in `tests/Unit/readTimeTest.php`
- `Post::imageUrl()` in `tests/Unit/imageUrlTest.php`

These are good examples because they test small methods directly.

## What Makes A Good Unit Test Here?

A good unit test usually has this shape:

```php
test('it describes the behavior being tested', function () {
    // Arrange: create the object or data
    // Act: call the method
    // Assert: check the result
});
```

Example:

```php
use App\Models\Post;

test('reading time is zero for content under one minute', function () {
    $post = new Post([
        'content' => 'Short content',
    ]);

    expect($post->readTime())->toBe(0);
});
```

Good test names should describe behavior, not implementation. Prefer:

```php
test('user image url returns null when no image exists', function () {
    //
});
```

Avoid vague names:

```php
test('image test', function () {
    //
});
```

## Unit Tests Worth Writing

Start with `App\Models\User`. These methods are small enough to test directly.

### `User::imageUrl()`

File: `app/Models/User.php`

Test these cases:

- Returns `null` when the user has no image.
- Returns a storage URL when the user has an image.

Example:

```php
use App\Models\User;

test('user image url returns null when no image exists', function () {
    $user = new User;
    $user->image = null;

    expect($user->imageUrl())->toBeNull();
});

test('user image url returns a storage url when image exists', function () {
    $user = new User;
    $user->image = 'avatars/me.jpg';

    expect($user->imageUrl())->toBe('/storage/avatars/me.jpg');
});
```

### `User::getRouteKeyName()`

This app uses usernames in profile URLs. That behavior is defined by `getRouteKeyName()`.

Test this:

- It returns `username`.

Example:

```php
use App\Models\User;

test('user route key name is username', function () {
    expect((new User)->getRouteKeyName())->toBe('username');
});
```

### `User::isFollowedBy()`

Test these cases:

- Returns `false` when passed `null`.
- Returns `false` when another user is not a follower.
- Returns `true` when another user is a follower.

Example:

```php
use App\Models\User;

test('is followed by returns false when user is null', function () {
    $user = User::factory()->create();

    expect($user->isFollowedBy(null))->toBeFalse();
});
```

For the true case, create two users and attach the follower relationship:

```php
use App\Models\User;

test('is followed by returns true when user follows profile', function () {
    $profile = User::factory()->create();
    $follower = User::factory()->create();

    $profile->followers()->attach($follower);

    expect($profile->fresh()->isFollowedBy($follower))->toBeTrue();
});
```

### `User::hasClapped()`

Test these cases:

- Returns `false` when passed `null`.
- Returns `false` when the user has not clapped for the post.
- Returns `true` when the user has clapped for the post.

This method checks the database, so use factories.

Example:

```php
use App\Models\Post;
use App\Models\User;

test('has clapped returns true when user clapped for post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $post->claps()->create([
        'user_id' => $user->id,
    ]);

    expect($user->hasClapped($post))->toBeTrue();
});
```

### `User::hasListed()`

Test these cases:

- Returns `false` when passed `null`.
- Returns `false` when the post is not in one of the user's reading lists.
- Returns `true` when the post is in one of the user's reading lists.

Example:

```php
use App\Models\Post;
use App\Models\ReadingList;
use App\Models\User;

test('has listed returns true when post is in users reading list', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $readingList = ReadingList::factory()->create([
        'user_id' => $user->id,
    ]);

    $readingList->posts()->attach($post);

    expect($user->hasListed($post))->toBeTrue();
});
```

## Request Rule Tests Worth Writing

Form request tests are useful because they protect validation rules.

Good candidates:

- `App\Http\Requests\PostCreateRequest`
- `App\Http\Requests\StoreReadingListRequest`
- `App\Http\Requests\ProfileUpdateRequest`

These tests can check the rule arrays directly.

Example:

```php
use App\Http\Requests\StoreReadingListRequest;

test('store reading list request requires a title', function () {
    $request = new StoreReadingListRequest;

    expect($request->rules()['title'])->toContain('required');
});
```

For rules written as strings, use `toBe()` or `toContain()` depending on the structure:

```php
use App\Http\Requests\PostCreateRequest;

test('post create request requires title and content', function () {
    $rules = (new PostCreateRequest)->rules();

    expect($rules['title'])->toBe('required');
    expect($rules['content'])->toBe('required');
});
```

For array rules:

```php
use App\Http\Requests\PostCreateRequest;

test('post create request requires an image file', function () {
    $rules = (new PostCreateRequest)->rules();

    expect($rules['image'])
        ->toContain('required')
        ->toContain('image')
        ->toContain('max:2048');
});
```

## What Should Be Feature Tests Instead?

Some behavior is too connected to routing, controllers, auth, or views to be useful as a unit test.

Write feature tests for:

- Creating a post through `POST /post`
- Creating a reading list through `POST /reading-lists`
- Toggling a clap through `POST /clap/{post}`
- Following or unfollowing a user through `POST /follow/{user}`
- Toggling posts inside reading lists
- Checking that unauthorized users get `401` or `403`
- Checking redirects after forms
- Checking pages display the expected posts

Example feature test idea:

```php
test('authenticated user can clap for a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('clap', $post))
        ->assertOk()
        ->assertJson([
            'clapsCount' => 1,
        ]);

    expect($post->claps()->where('user_id', $user->id)->exists())->toBeTrue();
});
```

That is a feature test because it uses a route, auth, a controller, JSON response, and the database.

## Suggested Order For You

If you want a practical path, write tests in this order:

1. `tests/Unit/UserTest.php`
2. `tests/Unit/StoreReadingListRequestTest.php`
3. `tests/Unit/PostCreateRequestTest.php`
4. `tests/Feature/ClapTest.php`
5. `tests/Feature/FollowerTest.php`
6. `tests/Feature/ReadingListPostTest.php`
7. `tests/Feature/ReadingListTest.php`
8. `tests/Feature/PostTest.php`

This order starts with simple direct unit tests, then moves into behavior that needs HTTP and the database.

## Testing Checklist

Before writing a test, ask:

- What behavior am I protecting?
- Can I call the method directly?
- Does this need auth, a route, a controller, a redirect, a view, or JSON?
- What is the normal case?
- What is the empty or missing-data case?
- What is the unauthorized or invalid case?

If you can call the method directly, it is probably a unit test.

If you need to send a request with `$this->get()`, `$this->post()`, `$this->patch()`, or `$this->delete()`, it is a feature test.

## Common Pest Expectations

Useful expectations:

```php
expect($value)->toBe('exact value');
expect($value)->toBeTrue();
expect($value)->toBeFalse();
expect($value)->toBeNull();
expect($value)->toBeInt();
expect($array)->toContain('required');
```

Useful Laravel assertions:

```php
$response->assertOk();
$response->assertRedirect('/profile');
$response->assertSessionHasNoErrors();
$response->assertSessionHasErrors('email');
$response->assertJson(['attached' => true]);

$this->assertDatabaseHas('claps', [
    'user_id' => $user->id,
    'post_id' => $post->id,
]);
```

## Small Improvement To Existing Tests

In `tests/Unit/readTimeTest.php`, this test:

```php
expect($post->readTime())->toBeLessThan(1.0);
```

could be stricter:

```php
expect($post->readTime())->toBe(0);
```

The method returns `0` for content under one minute, so checking for exactly `0` describes the expected behavior better.

## Final Advice

Do not aim for "test everything." Aim to test behavior that matters:

- Model helper methods used by views
- Validation rules that protect forms
- User actions like clap, follow, create post, and save to reading list
- Authorization rules and ownership checks

Small, readable tests are better than large tests that try to prove too much at once.
