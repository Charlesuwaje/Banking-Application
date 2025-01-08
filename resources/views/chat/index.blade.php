@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-gradient-primary text-white text-center">
                <h5 style="color: #495057">Chat Application</h5>
            </div>
            <div class="card-body chat-window" id="messages-container"
                style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                @foreach ($messages as $message)
                    <div class="message {{ $message->user_id === auth()->id() ? 'sent' : 'received' }}">
                        <div class="message-bubble">
                            <p>{{ $message->message }}</p>
                            @if ($message->file)
                                <a href="{{ asset('storage/' . $message->file) }}" target="_blank" class="text-info">View
                                    File</a>
                            @endif
                            <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer bg-light">
                <form id="chat-form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="receiver_id" class="form-label">Recipient</label>
                            <select id="receiver_id" name="receiver_id" class="form-select">
                                <option value="" disabled selected>Select a user</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" name="message" id="message" class="form-control"
                            placeholder="Type your message">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                    <input type="file" name="file" id="file" class="form-control">
                </form>
            </div>
        </div>
    </div>

    <style>
        /* General Styles */
        .chat-window {
            border-radius: 10px;
        }

        .message {
            margin: 10px 0;
            display: flex;
            align-items: flex-start;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message-bubble {
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 70%;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .message.sent .message-bubble {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: #fff;
        }

        .message.received .message-bubble {
            background: #e9ecef;
            color: #495057;
        }

        .form-select,
        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #003f7f);
        }

        a.text-info {
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        a.text-info:hover {
            color: #0056b3;
        }
    </style>

    <script>
        async function fetchMessages() {
            const response = await fetch('{{ route('chat.fetch') }}');
            const data = await response.json();
            console.log(data);

            const messages = Array.isArray(data) ? data : data.messages || [];
            const container = document.getElementById('messages-container');
            container.innerHTML = '';

            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = `message ${msg.user_id === {{ auth()->id() }} ? 'sent' : 'received'}`;
                div.innerHTML = `
        <div class="message-bubble">
            <p>${msg.message || ''}</p>
            ${msg.file ? `<a href="/storage/${msg.file}" target="_blank" class="text-info">View File</a>` : ''}
            <small class="text-muted">${new Date(msg.created_at).toLocaleTimeString()}</small>
        </div>`;
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

        setInterval(fetchMessages, 10000000);
    </script>
@endsection
