@auth
    <div class="utility-actions">
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="utility-button" type="submit">Sign out</button>
        </form>
    </div>
@endauth
