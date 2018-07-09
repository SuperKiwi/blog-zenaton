# Easily deal with moderation using Zenaton in a Laravel Project

> The idea is fairly simple : **How can we manage moderation of blogposts using Zenaton in a Laravel project** ?

The basic flow : 
* an user writes a blogpost
* an notification is sent to moderator
* a moderator needs to read it and then validate or refuse it :
    * if validated, a notification is sent to writer telling it has been accepted
    * if refused, a notification is sent to writer telling it has been refused
* if the blogpost is not moderated within 2 days, the blogpost will be automatically validated and the writer will be warned

**With [Zenaton](https://zenaton.com), the implementation of such a workflow becomes easy.**

> Note : we will assume that you are familiar with Laravel basics

## Installation

Register on [Zenaton.com](https://zenaton.com) to get your API key and Application Id.  
Once you have it, head to your `.env` file and add the following lines :

```
ZENATON_APP_ID=[Your application Id]
ZENATON_API_TOKEN=[Your API key]
ZENATON_APP_ENV=dev
```

Install Zenaton worker on your computer/server :

```curl
curl https://install.zenaton.com | sh
```

Launch the Zenaton worker :

```
zenaton start
```

You are now ready to use Zenaton power by listening to your application :

```
zenaton listen --laravel
```

You then need to add Zenaton library to your app :

```
composer require zenaton/zenaton-php
```

> Bonus : add `zenaton.*` to your `.gitignore` in order to keep log files out of versionning.

## Zenaton Directory

Create a `Zenaton` directory inside your `app` directory.  
Add 3 directories inside : `Tasks`, `Workflows` and `Events`.

```
.
├── app
│   ├── Zenaton
│       ├── Tasks
│       ├── Workflows
│       ├── Events
```

**Those directories will contain all your "business" workflow logic.**

### Workflow

Let's start first by writing the logic of the workflow of moderation.

It is quite easy to write as it fits in one single file and needs to be read line after line.

First, create a file named `PostModerationWorkflow.php` inside `Workflows` folder.

```php
namespace App\Zenaton\Workflows;

use App\Models\Post;
use Zenaton\Tasks\Wait;
use Zenaton\Traits\Zenatonable;
use App\Zenaton\Tasks\AcceptPostTask;
use App\Zenaton\Tasks\RefusePostTask;
use Zenaton\Interfaces\WorkflowInterface;
use App\Zenaton\Events\PostModeratedEvent;
use App\Zenaton\Tasks\AskForModerationTask;
use App\Zenaton\Tasks\AcceptPostDueToNoModerationTask;

class PostModerationWorkflow implements WorkflowInterface
{
    use Zenatonable;

    protected $post;

    public $event;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        // First Ask for moderation when a new post is created
        (new AskForModerationTask($this->post))->execute();

        // Wait for 2 days of for an event called PostModeratedEvent
        $this->event = (new Wait(PostModeratedEvent::class))->days(2)->execute();

        if ($this->event) { // If the event occurs, launch task according to the decision held inside the event
            $this->event->decision ? (new AcceptPostTask($this->post))->execute() : (new RefusePostTask($this->post))->execute();

            return;
        }

        // If no event evers occur during the 2 days wait, this task will trigger
        (new AcceptPostDueToNoModerationTask($this->post))->execute();
    }


    public function onEvent($event)
    {
        if ($event instanceof PostModeratedEvent) {
            $this->event = $event;
        }
    }

    public function getId()
    {
        return $this->post->id;
    }
}
```

### Tasks

Let's then create our tasks inside `Tasks` directory. We can list 4 different tasks according to the workflow code above.

**AskForModerationTask.php**

```php
namespace App\Zenaton\Tasks;

use Notification;
use App\Models\User;
use Zenaton\Traits\Zenatonable;
use Zenaton\Interfaces\TaskInterface;

class AskForModerationTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $adminUsers = User::whereRole('moderator')->get();

        Notification::send($adminUsers, new NewPostNeedsModeration($this->post));
    }
}
```

This task uses Laravel Notification facade and sends a notification to all the users that have `moderator` in role column.


**AcceptPostTask.php**

```php
namespace App\Zenaton\Tasks;

use Zenaton\Traits\Zenatonable;
use App\Notifications\PostAccepted;
use Zenaton\Interfaces\TaskInterface;

class AcceptPostTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $this->post->user->notify(new PostAccepted($this->post));
    }
}
```

This task will simply notify the owner of the post that his post has been reviewed and accepted.

**RefusePostTask.php**

```php
namespace App\Zenaton\Tasks;

use Zenaton\Traits\Zenatonable;
use App\Notifications\PostRefused;
use Zenaton\Interfaces\TaskInterface;

class RefusePostTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $this->post->user->notify(new PostRefused($this->post));
    }
}
```

This task will simply notify the owner of the post that his post has been reviewed and refused.

**AcceptPostDueToNoModerationTask.php**

```php
namespace App\Zenaton\Tasks;

use Zenaton\Traits\Zenatonable;
use App\Notifications\PostAccepted;
use Zenaton\Interfaces\TaskInterface;

class AcceptPostDueToNoModerationTask implements TaskInterface
{
    use Zenatonable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function handle()
    { 
        // The post must be set as active when this task is fired as we are expecting blogpost to be automatically published after 2 days without moderation
        $this->post->published = true;
        $this->post->save();

        $this->post->user->notify(new PostAcceptedAfterNoModeration($this->post));
    }
}
```


> Please note that you need to create one your side the notifications listed in all the tasks above.

### Events

Let's then create our event inside `Events` directory.  
The only event `PostModeratedEvent.php` is deadly simple has we are excepting only one public property inside.

This public property holds the result of the moderation : *has the blogpost been accepted or refused ?* 

**PostModeratedEvent.php**

```php
namespace App\Zenaton\Events;

use Zenaton\Interfaces\EventInterface;

class PostModeratedEvent implements EventInterface
{
    public $decision;

    public function __construct(bool $decision)
    {
        $this->decision = $decision;
    }
}
```


## Outside `Zenaton` directory

Now that our logic (Workflow, Tasks and Event) has been defined inside `Zenation` directory, we need to interact with Zenaton from Controllers or other locations inside your Laravel project.

### Launch the workflow

Whenever you create a blogpost, you need to call the workflow that will determine the steps to follow.

For instance, in your `PostController`, you would have something like :

```php
use Illuminate\Http\Request;
use App\ Http\Controllers\Controller;
use App\Zenaton\Workflows\PostModerationWorkflow;

class PostController extends Controller
{
    // ...
    // ...

    public function store(Request $request)
    {
        $post = Post::create($request->all());

        (new PostModerationWorkflow($post))->dispatch();

        return route('posts.index');
    }

    // ...
}
```

### Warn the workflow that a post has been moderated

```php
use App\Post;
use Illuminate\Http\Request;
use App\ Http\Controllers\Controller;
use App\Zenaton\Events\PostModeratedEvent;
use App\Zenaton\Workflows\PostModerationWorkflow;

class PostController extends Controller
{
    // ...
    // ...

    public function moderate(Post $post, $decision)
    {
        $post->published = $decision;
        $post->save();

        $event = new PostModeratedEvent($decision);
        PostModerationWorkflow::whereId($post->id)->send($event);
    }

    // ...
}
```

### Kill the workflow

What if the post is deleted by the writer before it has been moderated and before the 2 days limit ?
You then need to warn the workflow that it can stop :


```php
use App\Post;
use Illuminate\Http\Request;
use App\ Http\Controllers\Controller;
use App\Zenaton\Workflows\PostModerationWorkflow;

class PostController extends Controller
{
    // ...
    // ...

    public function destroy(Post $post)
    {
        PostModerationWorkflow::whereId($post->id)->kill();

        $post->delete();

        return route('posts.index');
    }

    // ...
}
```



## Thoughts

### No Laravel queing system inside workflow

Be careful to always use only events and tasks from Zenaton directory inside your Workflow. **Never directly call the notification system (for example) from the workflow.**

### Post acceptation inside Task or Controller

Should you validate the post inside a Zenaton Task or your controller ?

Question is worth asking. Indeed you could validate `$post` inside `AcceptPostTask.php` or `RefusePostTask.php` instead of doing it inside `PostController.php`.

But be careful as Zenaton Tasks are not synchronous and this could lead to unexpected front-end views.

