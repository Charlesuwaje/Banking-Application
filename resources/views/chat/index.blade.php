@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header bg-primary text-white">Chat</div>
            <div class="card-body chat-window" style="height: 400px; overflow-y: auto;" id="messages-container">
                @foreach ($messages as $message)
                    <div class="message {{ $message->user_id === auth()->id() ? 'sent' : 'received' }}">
                        <div class="message-bubble">
                            <p>{{ $message->message }}</p>
                            @if ($message->file)
                                <a href="{{ asset('storage/' . $message->file) }}" target="_blank">View File</a>
                            @endif
                            <small>{{ $message->created_at->format('h:i A') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <form id="chat-form">
                    @csrf
                    <div class="mb-2">
                        <label for="receiver_id" class="form-label">Select Recipient</label>
                        <select id="receiver_id" name="receiver_id" class="form-control">
                            <option value="" disabled selected>Select a user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="text" name="message" id="message" class="form-control mb-2"
                        placeholder="Type your message">
                    <input type="file" name="file" id="file" class="form-control mb-2">
                    <button type="submit" class="btn btn-primary w-100">Send</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .message {
            margin: 10px 0;
        }

        .message.sent {
            text-align: right;
        }

        .message-bubble {
            display: inline-block;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        .message.sent .message-bubble {
            background: #007bff;
            color: #fff;
        }

        .message.received .message-bubble {
            background: #e9ecef;
            color: #495057;
        }
    </style>

    <script>
        async function fetchMessages() {
            const response = await fetch('{{ route('chat.fetch') }}');
            const messages = await response.json();
            const container = document.getElementById('messages-container');
            container.innerHTML = '';

            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = `message ${msg.user_id === {{ auth()->id() }} ? 'sent' : 'received'}`;
                div.innerHTML = `
                <div class="message-bubble">
                    <p>${msg.message || ''}</p>
                    ${msg.file ? `<a href="/storage/${msg.file}" target="_blank">View File</a>` : ''}
                    <small>${new Date(msg.created_at).toLocaleTimeString()}</small>
                </div>
            `;
                container.appendChild(div);
            });
            container.scrollTop = container.scrollHeight;
        }
        
        document.getElementById('chat-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = new FormData(e.target);

            const response = await fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: form
            });

            if (response.ok) {
                e.target.reset();
                fetchMessages();
            }
        });

        setInterval(fetchMessages, 1000000);
        // setInterval(fetchMessages, 3000); 
    </script>
@endsection
