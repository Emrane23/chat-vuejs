 <!-- resources/views/chat.blade.php -->
 @extends('layouts.app')
 @section('content')
     <div class="container">
        <App :user="{{ Auth::user() }}"></App>
     </div>
 @endsection
