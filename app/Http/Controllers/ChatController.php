<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function createMessage(Request $request)
    {
        $request->validate([
            'body' => ['required', 'string', 'min:1'],
            'chat_id' => ['required', 'exists:\App\Models\Chat,id']
        ]);

        $created = ChatMessage::create([
            'body' => preg_replace('/[\x00-\x1F\x7F]/u', '', trim($request->input('body'))),
            'chat_id' => $request->input('chat_id'),
            'sent_by' => Auth::user()->id,
        ]);

        return response($created);
    }

    public function pollMessages(Request $request, Chat $chat)
    {
        $request->validate(['client-latest' => ['required', 'date']]);
        return response($chat->messageHistoryGroupedByDate(null, $request->query('client-latest')));
    }
}
