@extends('layouts.app')

@section('content')
    <div
        style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f9fafb; padding: 3rem 1rem;">
        <div
            style="max-width: 28rem; width: 100%; background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 style="font-size: 1.875rem; font-weight: 700; color: #1b1b18;">Login</h2>
                <p style="color: #4b5563; margin-top: 0.5rem;">Agrika Nota - Sistem Pencatatan Manual Nota</p>
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

            @if (session('status'))
                <div
                    style="margin-bottom: 1rem; padding: 1rem; background-color: #dcfce7; border: 1px solid #86efac; border-radius: 0.375rem;">
                    <p style="color: #16a34a; font-size: 0.875rem;">{{ session('status') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div style="margin-bottom: 1rem;">
                    <label for="email"
                        style="display: block; color: #1b1b18; font-weight: 700; margin-bottom: 0.5rem;">Email</label>
                    <input id="email" type="email" name="email"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem; @error('email') border-color: #ef4444; @enderror"
                        value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
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
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" name="remember" style="margin-right: 0.5rem;" {{ old('remember') ? 'checked' : '' }}>
                        <span style="color: #1b1b18; font-size: 0.875rem;">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    style="width: 100%; background-color: #2563eb; color: white; font-weight: 700; padding: 0.5rem; border-radius: 0.375rem; border: none; cursor: pointer; font-size: 1rem; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                    Login
                </button>
            </form>

            <div
                style="margin-top: 1rem; padding: 1rem; background-color: #eff6ff; border: 1px solid #93c5fd; border-radius: 0.375rem; font-size: 0.875rem;">
                <p style="font-weight: 700; color: #1e3a8a; margin-bottom: 0.5rem;">Test Accounts:</p>
                <p style="color: #1e40af;"><strong>Admin:</strong> admin@example.com / password</p>
                <p style="color: #1e40af;"><strong>Approver:</strong> approver@example.com / password</p>
                <p style="color: #1e40af;"><strong>Super Admin:</strong> superadmin@example.com / password</p>
            </div>
        </div>
    </div>
@endsection