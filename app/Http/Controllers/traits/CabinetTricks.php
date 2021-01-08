<?php

namespace App\Http\Controllers\traits;

use App\Extra\Privileges\Privilege;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait CabinetTricks {

    private function availableActiveReportRequests()
    {
        $required_priv = Auth::user()->role === 'Raino' ? 'inspect_priv' : 'response_priv';

        return Auth::user()
            ->availableReportRequests(Privilege::fromAllowedList('report_request', [$required_priv]))
            ->where(['status' => 'active'])
            ->orderBy('created_at', 'DESC')
            ->select(['id', 'title', 'created_at']);
    }

    private function fetchChatData(Request $request, int $userChatWithId = null): ?array
    {
        $userChatWith = is_null($userChatWithId) || $userChatWithId === Auth::id() ? $this->getUserChatWith($request) : User::find($userChatWithId);

        if (is_null($userChatWith)) {
            return null;
        }

        $chat = Auth::user()->chatWith($userChatWith);
        if (is_null($chat)) return null;

        $chatHistory = $this->fetchChatHistory($request, $userChatWith);

        return ['chatWith' => $userChatWith, 'chatMessages' => $chatHistory, 'chatId' => $chat->id];
    }

    private function getUserChatWith(Request $request)
    {
        $chatWithId = (int)$request->query('chat-with');
        return Auth::user()->role == 'Raino'
            ? ($chatWithId > 0 ? User::find($chatWithId) : null)
            : User::whereRole('raino')->first();
    }

    /**
     * @param Request $request
     * @param User $user_chat_with
     * @return Collection|ChatMessage[]
     */
    private function fetchChatHistory(Request $request, User $user_chat_with)
    {
        $before = $request->query('before');

        if ($user_chat_with && Auth::user()->id != $user_chat_with->id) {
            return Auth::user()
                ->chatWith($user_chat_with)
                ->messageHistoryGroupedByDate($before);
        }

        return null;
    }
}
