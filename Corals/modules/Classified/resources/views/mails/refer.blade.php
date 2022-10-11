@extends('Notification::mail.master')

@section('body')

    {!!   trans('Classified::messages.product_send.received_message',['name' => $name , 'product_name'=>$product->name,'product_url'=>$product->getShowURL()]) !!}

    <p>
        {!!  trans('Classified::messages.product_send.user_message',['user_message' => $user_message]) !!}
    </p>


@endsection




