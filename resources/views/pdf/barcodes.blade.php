<style>
    .label{


        display:inline-block;

        border:1px solid #ddd;

        margin:1mm;

        text-align:center;

    }
</style>
<h1>{{ $name }}</h1>
@for($i = 0;$i < $tags;$i++)
    <div class="label">
        <img src="{{ $barcode }}">
    </div>
@endfor
