@auth
    <div class="utility-actions">
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="utility-button" type="submit">Sign out</button>
        </form>
    </div>
@else
    <div class="utility-actions">
        <a href="{{ route('login') }}">Sign in</a>
    </div>
@endauth
