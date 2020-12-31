<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} | Smart System</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/edit-room.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"
          integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w=="
          crossorigin="anonymous"/>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>
<body>
<div class="container">
    <main class="main">
        <div class="editor">
            <div class="editor__top-bar">
                <h1 class="heading">{{ $title }}</h1>
                <ul class="editor__tools" id="toolbar">
                    <li class="editor__tool">
                        <button class="editor__tool-btn" data-command="bold">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.1663 5.41675H7.58301V21.6667" fill="none" stroke-width="2"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.1663 21.6667H7.58301" fill="none" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M15.1663 13H7.58301" fill="none" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path
                                    d="M14.625 13.0001C16.7191 13.0001 18.4167 11.3025 18.4167 9.20841C18.4167 7.11433 16.7191 5.41675 14.625 5.41675"
                                    fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path
                                    d="M15.167 21.6667C17.5602 21.6667 19.5003 19.7265 19.5003 17.3333C19.5003 14.9401 17.5602 13 15.167 13"
                                    fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="editor__tool-helper">жирный</p>
                        </button>
                    </li>
                    <li class="editor__tool">
                        <button class="editor__tool-btn" data-command="italic">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.833 21.6668L15.708 5.41675M20.0413 5.41675H15.708H20.0413ZM11.3747 5.41675H15.708H11.3747Z"
                                    fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.1667 21.6667H6.5" fill="none" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                            <p class="editor__tool-helper">курсив</p>
                        </button>
                    </li>
                    <li class="editor__tool">
                        <button class="editor__tool-btn" data-command="bulletedList">
                            <svg width="20" height="14" viewBox="0 0 20 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.667 1.58325H5.66699" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M18.667 7H5.66699" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M18.667 12.4167H5.66699" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M1.33301 1.58325V1.59492" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M1.33301 7V7.01167" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M1.33301 12.4167V12.4284" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <p class="editor__tool-helper">список</p>
                        </button>
                    </li>
                    <li class="editor__tool">
                        <button class="editor__tool-btn" data-command="insertTable">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M8.66699 2.16675C10.4619 2.16675 11.917 3.62182 11.917 5.41675V20.5834C11.917 22.3783 10.4619 23.8334 8.66699 23.8334H5.41699C3.62207 23.8334 2.16699 22.3783 2.16699 20.5834V5.41675C2.16699 3.62182 3.62207 2.16675 5.41699 2.16675H8.66699ZM20.5837 2.16675C22.3786 2.16675 23.8337 3.62182 23.8337 5.41675V20.5834C23.8337 22.3783 22.3786 23.8334 20.5837 23.8334H17.3337C15.5387 23.8334 14.0837 22.3783 14.0837 20.5834V5.41675C14.0837 3.62182 15.5387 2.16675 17.3337 2.16675H20.5837ZM8.66699 4.33341H5.41699C4.81868 4.33341 4.33366 4.81844 4.33366 5.41675V20.5834C4.33366 21.1817 4.81868 21.6667 5.41699 21.6667H8.66699C9.2653 21.6667 9.75033 21.1817 9.75033 20.5834V5.41675C9.75033 4.81844 9.2653 4.33341 8.66699 4.33341ZM20.5837 4.33341H17.3337C16.7354 4.33341 16.2503 4.81844 16.2503 5.41675V20.5834C16.2503 21.1817 16.7354 21.6667 17.3337 21.6667H20.5837C21.182 21.6667 21.667 21.1817 21.667 20.5834V5.41675C21.667 4.81844 21.182 4.33341 20.5837 4.33341Z"
                                      stroke="none"/>
                            </svg>

                            <p class="editor__tool-helper">таблица</p>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="editor__body">
                @if(!isset($noTitleInput))
                    <input class="editor__title-input"
                           value="{{ \Illuminate\Support\Arr::get($content, 'title') ?? '' }}"
                           id="title-input"
                           type="text" name="title" placeholder="Заголовок запроса" autocomplete="off"
                           required>
                @endif
                <div class="editor__textarea" id="editor" data-placeholder="{{ $placeholder ?? '...' }}">
                    {!! purify(\Illuminate\Support\Arr::get($content, 'body') ?? '') !!}
                </div>
            </div>
        </div>
        <div class="presubmit-bar">
            <div class="presubmit-bar__top-bar">
                <button class="presubmit-bar__attach-btn" id="attach-button">
                    <svg width="26" height="26" viewbox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.9448 18.5717L11.1478 19.3687C9.87858 20.6379 7.82079 20.6379 6.55158 19.3687L6.05639 18.8735C4.78719 17.6043 4.78719 15.5465 6.05639 14.2774L9.6467 10.687C10.9159 9.41781 12.9737 9.41781 14.2429 10.687L14.4904 10.9346"
                            fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path
                            d="M13.4803 6.85293L14.2773 6.0559C15.5465 4.7867 17.6043 4.7867 18.8736 6.0559L19.3688 6.5511C20.6379 7.8203 20.6379 9.87809 19.3686 11.1473L15.7784 14.7376C14.5091 16.0068 12.4515 16.0068 11.1822 14.7376L10.9346 14.49"
                            fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <input type="file" id="file-input" multiple hidden>
                <button class="presubmit-bar__submit-btn" type="submit" id="submit-button"
                        data-action="{{ $actionURL }}" data-method="{{ $actionMethod }}"
                        data-redirect="{{ $redirectURL }}">
                    Отправить
                </button>
            </div>
            <div class="presubmit-bar__body">
                @if (isset($hint))
                    <div class="request-raino">
                        <h2>{{ $hint['title'] }}</h2>
                        <span>{{ formatDate($hint['date']) }}</span>
                        <div class="request-raino-describe">{!! purify($hint['body']) !!}</div>
                    </div>
                @endif
                @if (isset($attachments) || $useAttachments)
                    <div class="presubmit-bar__block">
                        <h2 class="presubmit-bar__header">Прикрепленные файлы</h2>
                        <ul class="presubmit-bar__attachments" id="attachments">
                            @if (isset($attachments))
                                @foreach ($attachments as $attachment)
                                    <li class="presubmit-bar__attachment attachment">
                                        <img class="attachment__icon"
                                             src="{{ docIconByFilename($attachment->filename) }}" alt="JPG">
                                        <p class="attachment__filename">{{ pathinfo($attachment->filename)['filename'] }}</p>
                                        <button class="attachment__btn-remove"
                                                data-attachment-id="{{ $attachment->id }}">
                                            <img src="{{ asset('/images/trash.png') }}" alt="Remove">
                                        </button>
                                        <p class="attachment__size">{{ format_size($attachment->size) }}</p>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </main>
    <footer class="status-bar">
        <p class="current-date">Текущая дата: {{ formatDate(now()) }}</p>
    </footer>
</div>

<script src="{{ asset('/js/editor-setup.js') }}"></script>
</body>
</html>
