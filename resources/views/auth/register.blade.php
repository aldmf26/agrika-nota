@extends('layouts.app')

@section('content')
    <div
        style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f9fafb; padding: 3rem 1rem;">
        <div
            style="max-width: 28rem; width: 100%; background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 style="font-size: 1.875rem; font-weight: 700; color: #1b1b18;">Register</h2>
                <p style="color: #4b5563; margin-top: 0.5rem;">Buat akun baru Anda</p>
            </div>

            @if ($errors->any())
                <div
                    style="margin-bottom: 1rem; padding: 1rem; background-color: #fee2e2; border: 1px solid #fca5a5; border-radius: 0.375rem;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="color: #dc2626; font-size: 0.875rem;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div style="margin-bottom: 1rem;">
                    <label for="name"
                        style="display: block; color: #1b1b18; font-weight: 700; margin-bottom: 0.5rem;">Nama</label>
                    <input id="name" type="text" name="name"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem; @error('name') border-color: #ef4444; @enderror"
                        value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="email"
                        style="display: block; color: #1b1b18; font-weight: 700; margin-bottom: 0.5rem;">Email</label>
                    <input id="email" type="email" name="email"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem; @error('email') border-color: #ef4444; @enderror"
                        value="{{ old('email') }}" required>
                    @error('email')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="password"
                        style="display: block; color: #1b1b18; font-weight: 700; margin-bottom: 0.5rem;">Password</label>
                    <input id="password" type="password" name="password"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem; @error('password') border-color: #ef4444; @enderror"
                        required>
                    @error('password')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="password_confirmation"
                        style="display: block; color: #1b1b18; font-weight: 700; margin-bottom: 0.5rem;">Konfirmasi
                        Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;"
                        required>
                </div>

                <button type="submit"
                    style="width: 100%; background-color: #2563eb; color: white; font-weight: 700; padding: 0.5rem; border-radius: 0.375rem; border: none; cursor: pointer; font-size: 1rem; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                    Daftar
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; border-top: 1px solid #d1d5db; padding-top: 1.5rem;">
                <p style="color: #4b5563; font-size: 0.875rem;">Sudah punya akun?</p>
                <a href="{{ route('login') }}"
                    style="color: #2563eb; font-weight: 700; margin-top: 0.5rem; display: inline-block;">
                    Login di sini
                </a>
            </div>
        </div>
    </div>
@endsection
