<div class="school scrollable">
    <ul>
        @foreach($reporters as $reporter)
            <li><a class="reporter reporter_{{$reporter->status}}" href="?chat-with={{ $reporter->id }}"><span>шк</span>#{{ $reporter->name }}</a></li>
        @endforeach
    </ul>
</div>
