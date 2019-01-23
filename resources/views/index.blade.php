@extends('layouts.app')

@section('content')
    <div style="background-color: lightgrey;">
        <div class="col-md-8 offset-2">
            <a style="text-align: center;" href="{{ route('runRuby') }}" class="btn btn-primary">Click Me</a>
            <h1>{{ $output }}</h1>
        </div>
    </div>
@endsection
