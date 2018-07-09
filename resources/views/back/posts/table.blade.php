@foreach($posts as $post)
    <tr>
        <td>{{ $post->title }}</td>
        <td><img src="{{ thumb($post->image) }}" alt=""></td>
        <td>
            @admin
                <input type="checkbox" name="status" value="{{ $post->id }}" {{ $post->active ? 'checked' : ''}}>
            @else
                <i class="fa fa-{{$post->active ? 'check' : 'times'}}"></i>
            @endadmin
        </td>
        <td>
            <i class="fa fa-{{$post->moderated ? 'check' : 'clock-o'}}"></i>
        </td>
        <td>{{ $post->created_at->formatLocalized('%c') }}</td>
        <td>
            <input type="checkbox" name="seen" value="{{ $post->id }}" {{ is_null($post->ingoing) ?  'disabled' : 'checked'}}>
        </td>
        
        <td>{{ $post->seo_title }}</td>
        @admin
            @unless ($post->moderated)
                 <td>
                    <a class="btn btn-primary btn-xs btn-block accept" href="{{ route('posts.accept', [$post->id]) }}" role="button" title="@lang('Accept')"><span class="fa fa-check"></span></a>
                    <a class="btn btn-primary btn-xs btn-block refuse" href="{{ route('posts.refuse', [$post->id]) }}" role="button" title="@lang('Reject')"><span class="fa fa-times"></span></a>
                </td>
            @else
                <td>Already moderated</td>
            @endunless
        @endadmin
        <td><a class="btn btn-success btn-xs btn-block" href="{{ route('posts.show', [$post->id]) }}" role="button" title="@lang('Show')"><span class="fa fa-eye"></span></a></td>
        <td><a class="btn btn-warning btn-xs btn-block" href="{{ route('posts.edit', [$post->id]) }}" role="button" title="@lang('Edit')"><span class="fa fa-edit"></span></a></td>
        <td><a class="btn btn-danger btn-xs btn-block" href="{{ route('posts.destroy', [$post->id]) }}" role="button" title="@lang('Destroy')"><span class="fa fa-remove"></span></a></td>
    </tr>
@endforeach

