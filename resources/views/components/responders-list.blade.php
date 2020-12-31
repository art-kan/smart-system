<div class="school scrollable">
    <ul>
        @foreach($reporters as $reporter)
            <li><a href="?chat-with={{ $reporter->id }}"><span>шк</span>#{{ $reporter->name }}</a></li>
        @endforeach
    </ul>
</div>
