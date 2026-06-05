
<div>
    <table class="table table-striped">
        <thead>
            @foreach ($heads as $item)
                <th>{{ $item }}</th>
            @endforeach
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
        <tfoot>
            {{ $slot['footer'] }}
        </tfoot>
    </table>
</div>
