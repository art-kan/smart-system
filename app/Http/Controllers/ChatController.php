<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function receiveMessage(Request $request)
    {
        // TODO: remove eventually invisible characters
        $request->validate([
            'body' => ['required', 'string', 'min:1'],
            'chat_id' => ['required', 'exists:\App\Models\Chat,id']
        ]);

        $created = ChatMessage::create(
            array_merge($request->only(['body', 'chat_id']), [
                'sent_by' => Auth::user()->id,
            ])
        );

        return back();
    }

    public function pollMessages(Request $request, Chat $chat)
    {
        $request->validate(['client-latest' => ['required', 'date']]);
        return response($chat->messageHistoryGroupedByDate(null, $request->query('client-latest')));
    }
}
