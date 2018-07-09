<?php

namespace Framework\Http\Requests;

use Framework\Models\Comment;

class CommentRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = null;

        if(isset($this->comment)) {
            $id = is_a($this->comment, Comment::class) ?  $this->comment->id :  $this->comment;
        }

        return [
            'message' . $id => 'bail|required|max:2000',
        ];
    }
}
