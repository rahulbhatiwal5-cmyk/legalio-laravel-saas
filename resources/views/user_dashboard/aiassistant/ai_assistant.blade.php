@extends('user_dashboard_layout.master')

@section('content')
    <div class="uer_nm">
        <h1>
            {{-- Asistente --}}
            Assistant 
        </h1>
    </div>
    <div class="scroll_div">
        <div class="crt_main" id="append_document">
            <div class="chat-wrapper">
                <div id="chat-box" class="chat-box">
                    <!-- Display previous conversations -->
                    @foreach ($conversations as $conversation)
                        <!-- User message -->
                        <div class="message-container user-container">
                            <div class="chat-message user-message">
                                <div class="message-content">
                                    <p>{{ $conversation->question }}</p>
                                </div>
                                <div class="message-time">
                                    <small>{{ $conversation->created_at->format('h:i A') }}</small>
                                </div>
                            </div>
                            <div class="avatar user-avatar">
                                {{-- <span>You</span> --}}
                                <i class="fa-solid fa-user-plus"></i>
                            </div>
                        </div>

                        <!-- Bot message -->
                        <div class="message-container bot-container">
                            <div class="avatar bot-avatar">
                                {{-- <span>AI</span> --}}
                                <img src="{{ asset('assets/img/ai_bot.png') }}" alt="">
                            </div>
                            <div class="chat-message bot-message">
                                <div class="message-content">
                                    @php
                                        try {
                                            // Try to decode the answer as JSON
                                            $answer = json_decode($conversation->answer, true);

                                            // Check if decoding was successful and it has the expected structure
                                            if (is_array($answer) && isset($answer['message'])) {
                                                echo nl2br(e($answer['message']));

                                                // Check if there's a link to display
                                                if (isset($answer['link_status']) && $answer['link_status'] &&
                                                    isset($answer['link']) && isset($answer['link_name'])) {
                                                    echo '<div class="message-link"><a href="' . e($answer['link']) . '" target="_blank">' . e($answer['link_name']) . '</a></div>';
                                                }
                                            } else {
                                                // If it's not valid JSON or doesn't have the expected structure,
                                                // display it as is (fallback)
                                                echo nl2br(e($conversation->answer));
                                            }
                                        } catch (Exception $e) {
                                            // If any error occurs during JSON parsing, display the raw answer
                                            echo nl2br(e($conversation->answer));
                                        }
                                    @endphp
                                </div>
                                <div class="message-time">
                                    <small>{{ $conversation->created_at->format('h:i A') }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Placeholder message when there are no conversations -->
                    @if ($conversations->isEmpty())
                        <div class="placeholder-message">
                            <img src="{{ asset('assets/images/support_chat_icon.png') }}" alt="Start chatting" width="120">
                            <p>Start the conversation by typing your message below</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="user-input" placeholder="Type your message..." autocomplete="off" />
            <button id="send-btn">
                Send
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>

     function linkify(text) {
        const urlRegex = /(\bhttps?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, function(url) {
            return `<a href="${url}" target="_blank">${url}</a>`;
        });
    }


    document.getElementById('send-btn').addEventListener('click', function () {
        const input = document.getElementById('user-input');
        const message = input.value.trim();
        if (!message) return;

        const chatBox = document.getElementById('chat-box');
        chatBox.querySelector('.placeholder-message')?.remove();

        // Get current time
        const currentTime = new Date();
        const formattedTime = currentTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        // Display user message
        const userContainer = document.createElement('div');
        userContainer.className = 'message-container user-container';
        userContainer.innerHTML = `
            <div class="chat-message user-message">
                <div class="message-content">
                    <p>${message}</p>
                </div>
                <div class="message-time">
                    <small>${formattedTime}</small>
                </div>
            </div>
            <div class="avatar user-avatar">
              <i class="fa-solid fa-user-plus"></i>
            </div>
        `;
        chatBox.appendChild(userContainer);

        input.value = '';

        // Add loading message
        const botContainer = document.createElement('div');
        botContainer.className = 'message-container bot-container';
        botContainer.innerHTML = `
            <div class="avatar bot-avatar">

                   <img src="{{ asset('assets/img/ai_bot.png') }}" alt="">
            </div>
            <div class="chat-message bot-message">
                <div class="message-content">
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        chatBox.appendChild(botContainer);
        chatBox.scrollTop = chatBox.scrollHeight;

        // OldRoute is : test . FAQ
        // Make AJAX request
        fetch('{{ route('test.TAG') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {

            console.log(data);

            // Parse the response
            let botMessage = 'No response.';
            let link = null;
            let linkName = null;
            let linkStatus = false;

            // Check different possible response formats
            if (data.message) {
                // Direct message format
                botMessage = data.message;
                linkStatus = data.link_status || false;
                link = data.link || null;
                linkName = data.link_name || null;
            } else if (data.answer && typeof data.answer === 'object') {
                // Object format answer
                botMessage = data.answer.message || 'No message provided.';
                linkStatus = data.answer.link_status || false;
                link = data.answer.link || null;
                linkName = data.answer.link_name || null;
            } else if (data.data && data.data.answer) {
                try {
                    // Try to parse the answer
                    const answerData = data.data.answer;
                    if (typeof answerData === 'object') {
                        botMessage = answerData.message || 'No message provided.';
                        linkStatus = answerData.link_status || false;
                        link = answerData.link || null;
                        linkName = answerData.link_name || null;
                    } else if (typeof answerData === 'string') {
                        try {
                            const parsedAnswer = JSON.parse(answerData);
                            botMessage = parsedAnswer.message || answerData;
                            linkStatus = parsedAnswer.link_status || false;
                            link = parsedAnswer.link || null;
                            linkName = parsedAnswer.link_name || null;
                        } catch (e) {
                            botMessage = answerData;
                        }
                    }
                } catch (e) {
                    console.error('Error processing answer:', e);
                    botMessage = 'Error processing response.';
                }
            }

            // Format the message with any links
            let formattedMessage = botMessage.replace(/\n/g, '<br>');
            let linkHTML = '';
            if (linkStatus && link && linkName) {
                linkHTML = `<div class="message-link"><a href="${link}" target="_blank">${linkName}</a></div>`;
            }

            // Update the bot message with the response
            const messageContent = botContainer.querySelector('.message-content');
            if (messageContent) {
                messageContent.innerHTML = formattedMessage + linkHTML;

                // Add time
                const botMessageElement = botContainer.querySelector('.chat-message');
                if (botMessageElement) {
                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'message-time';
                    timeDiv.innerHTML = `<small>${formattedTime}</small>`;
                    botMessageElement.appendChild(timeDiv);
                }
            }
        })
        .catch((error) => {
            // If there's an error with the fetch or the response, show an error message
            console.error('Error:', error);
            console.log(error);
            const messageContent = botContainer.querySelector('.message-content');
            if (messageContent) {
                messageContent.innerHTML = 'An error occurred while fetching the response.';

                // Add time even for error message
                const botMessageElement = botContainer.querySelector('.chat-message');
                if (botMessageElement) {
                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'message-time';
                    timeDiv.innerHTML = `<small>${formattedTime}</small>`;
                    botMessageElement.appendChild(timeDiv);
                }
            }
        })
        .finally(() => {
            // Ensure the chat box is scrolled to the bottom
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    });

    document.getElementById('user-input').addEventListener('keyup', function (e) {
        if (e.key === 'Enter') {
            document.getElementById('send-btn').click();
        }
    });

    // Auto-scroll to bottom when loading the page
    window.addEventListener('load', function() {
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endpush
