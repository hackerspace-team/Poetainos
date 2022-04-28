<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Notifications\WritingCommented;
use App\Notifications\WritingCommentMentioned;
use App\User;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($writing)
    {
        $filter = [0];

        if (auth()->check()) {
            $filter = User::find(auth()->user()->id)->getBlockedAuthors()->pluck('blocked_user_id');
        }

        $comments = Comment::with('replies')
            ->where('writing_id', $writing)
            ->whereNotIn('user_id', $filter)
            ->orderBy('created_at', 'desc')
            ->simplePaginate($this->pagination);

        $html = view('comments.index', [
            'comments' => $comments,
        ])->render();

        return response()->json([
            'next' => $comments->nextPageUrl(),
            'comments' => $html,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'comment' => 'required|min:2|max:300',
            'writing_id' => 'required|exists:writings,id',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'writing_id' => request('writing_id'),
            'message' => request('comment')
        ]);

        // Update aura
        $comment->author->updateAura();
        $comment->writing->updateAura();

        // Notify author
        if (!$comment->writing->author->is(auth()->user())) {
            $comment->writing->author->notify(new WritingCommented($comment->writing, auth()->user()));
        }

        // Notify @mentions
        $mentionPattern = '/\B@[a-zA-Z0-9_-]+/';
        preg_match_all($mentionPattern, $comment->message, $mentions, PREG_PATTERN_ORDER);
        $mentions = array_unique($mentions[0]);

        foreach ($mentions as $mention) {
            $mention = User::where('username', '=', substr($mention, 1))->first();

            if (
                null !== $mention
                && !$mention->is($comment->writing->author)
                && !$mention->is(auth()->user())
            ) {
                $mention->notify(new WritingCommentMentioned($comment, auth()->user()));
            }
        }

        return view('comments.show', [
            'comment' => $comment,
        ])->render();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
