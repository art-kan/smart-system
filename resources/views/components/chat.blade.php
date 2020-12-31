<div class="chat" id="chat">
    <div class="name-school">
        <a href="?chat_with=">
            <svg xmlns="http://www.w3.org/2000/svg" width="51" height="8" viewBox="0 0 51 8" fill="none">
                <path
                    d="M0.646446 3.64645C0.451183 3.84171 0.451183 4.15829 0.646446 4.35355L3.82843 7.53553C4.02369 7.7308 4.34027 7.7308 4.53553 7.53553C4.7308 7.34027 4.7308 7.02369 4.53553 6.82843L1.70711 4L4.53553 1.17157C4.7308 0.976311 4.7308 0.659728 4.53553 0.464466C4.34027 0.269204 4.02369 0.269204 3.82843 0.464466L0.646446 3.64645ZM51 3.5L1 3.5V4.5L51 4.5V3.5Z"
                    fill="black"/>
            </svg>
        </a>
        <h2>{{ $chatData['chatWith']->role == 'Raino' ? 'Raino' : '#' . $chatData['chatWith']->name . ' школа' }}</h2>
    </div>
    <div class="chat-body scrollable scroll-to-bottom">
        @foreach($chatData['chatMessages'] as $date => $messages)
            <div class="chat-date">
                <h5> {{ $date }}</h5>
            </div>
            <div class="message-list" id="messages-list"
                 data-polling-url="{{ route('chat.poll', $chatData['chatId']) }}"
                 data-last-date="{{ $chatData['chatMessages']->keys()->last() }}"
                 data-lastest="{{ $chatData['chatMessages']->last()->last()->created_at }}">
                @foreach($messages as $message)
                    <div class="message">
                        <div class="message-user">
                            @if ($message->sender->role == 'Raino')
                                <div class="message-logo message-logo_raino">
                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.5 10.4167C14.2259 10.4167 15.625 9.01764 15.625 7.29175C15.625 5.56586 14.2259 4.16675 12.5 4.16675C10.7741 4.16675 9.375 5.56586 9.375 7.29175C9.375 9.01764 10.7741 10.4167 12.5 10.4167Z"
                                            stroke="#fff" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path
                                            d="M14.5833 13.5417H10.4167C8.11548 13.5417 6.25 15.4073 6.25 17.7084C6.25 19.4344 7.64911 20.8334 9.375 20.8334H15.625C17.3509 20.8334 18.75 19.4344 18.75 17.7084C18.75 15.4073 16.8845 13.5417 14.5833 13.5417Z"
                                            stroke="#fff" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                                <div class="message-raino-name">
                                    <span>Raino</span>
                                </div>
                            @else
                                <div class="message-logo message-logo_school">
                                    <p>{{ $message->sender->name }}</p>
                                </div>
                                <div class="message-raino-name">
                                    <span>School</span>
                                </div>
                            @endif
                        </div>

                        <div class="message-text">
                            <div class="text"><p>{{ $message->body }}</p></div>
                            <div class="message-text-time"><h5>{{ formatTime($message->created_at) }}</h5></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    <form class="typing" method="POST" action="{{ route('chat.send') }}">
        @csrf
        <input type="text" name="body" placeholder="написать">
        <input type="text" name="chat_id" value="{{ $chatData['chatId'] }}" hidden>
        <button type="submit"><img src="/images/send.png" alt="Send"></button>
    </form>
</div>
