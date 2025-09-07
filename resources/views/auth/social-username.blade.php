@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Complete Your Registration') }}</div>

                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5>Welcome, {{ $userData['name'] }}!</h5>
                            <p class="text-muted">Please choose a username to complete your registration</p>
                        </div>

                        <form method="POST" action="{{ route('social.username.store') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="name"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" value="{{ $userData['name'] }}" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" value="{{ $userData['email'] }}" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="username"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>
                                <div class="col-md-6">
                                    <input id="username" type="text"
                                        class="form-control @error('username') is-invalid @enderror" name="username"
                                        value="{{ old('username', $suggestedUsername) }}" required autocomplete="username">

                                    <div class="form-text">
                                        <small class="text-muted">Username can contain letters, numbers, and underscores
                                            only.</small>
                                    </div>

                                    <div id="username-feedback" class="mt-1"></div>

                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary" id="submit-btn">
                                        {{ __('Complete Registration') }}
                                    </button>

                                    <a href="{{ route('login') }}" class="btn btn-link">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            const feedback = document.getElementById('username-feedback');
            const submitBtn = document.getElementById('submit-btn');
            let timeoutId;

            usernameInput.addEventListener('input', function() {
                clearTimeout(timeoutId);
                const username = this.value.trim();

                if (username.length < 3) {
                    feedback.innerHTML =
                        '<small class="text-muted">Username must be at least 3 characters long</small>';
                    return;
                }

                // Debounce the API call
                timeoutId = setTimeout(() => {
                    checkUsername(username);
                }, 500);
            });

            function checkUsername(username) {
                fetch('{{ route('social.check.username') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            username: username
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            feedback.innerHTML = '<small class="text-success">✓ ' + data.message + '</small>';
                            usernameInput.classList.remove('is-invalid');
                            usernameInput.classList.add('is-valid');
                            submitBtn.disabled = false;
                        } else {
                            feedback.innerHTML = '<small class="text-danger">✗ ' + data.message + '</small>';
                            usernameInput.classList.remove('is-valid');
                            usernameInput.classList.add('is-invalid');
                            submitBtn.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        feedback.innerHTML =
                            '<small class="text-warning">Error checking username availability</small>';
                    });
            }

            // Initial check
            if (usernameInput.value.trim().length >= 3) {
                checkUsername(usernameInput.value.trim());
            }
        });
    </script>
@endsection
