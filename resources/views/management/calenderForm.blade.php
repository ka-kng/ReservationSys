@extends('layouts.app')

@section('content')

<div class="weekday-controls flex mx-6 mt-4">
    @foreach(['日','月','火','水','木','金','土'] as $i => $day)
        <div class="border">
            <label>{{ $day }}</label>
            <select data-weekday="{{ $i }}" class="weekday-select">
                <option value="">なし</option>
                <option value="morning">午前</option>
                <option value="afternoon">午後</option>
                <option value="all">一日</option>
            </select>
        </div>
    @endforeach
</div>


@endsection
