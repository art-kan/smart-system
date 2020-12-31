
!function setupScrollToBottom() {
    Array.from(document.getElementsByClassName('scroll-to-bottom'))
        .forEach((div) => div.scrollTop = div.scrollHeight);
}();

!function setupChatToggle() {
    const chatUI = document.getElementById('chat');
    const chatOverlayUI = document.getElementById('chat-overlay');
}();

// !function setupChatUpdate() {
//     const messagesList = document.getElementById('messages-list');
//     const pollingURL = messagesList.getAttribute('data-polling-url');
// }();

