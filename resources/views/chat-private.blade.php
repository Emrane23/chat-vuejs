 <!-- resources/views/chat.blade.php -->
 @extends('layouts.app')
 @section('content')
     <div class="container">
             <chat-private :user="{{ Auth::user() }}" :receiverid="{{ request()->get('receiverid') }}" > </chat-private>
     </div>
 @endsection
