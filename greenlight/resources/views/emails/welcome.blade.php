@extends('emails.email_template.template')

@section('content')
    <table class="row">
        <tr>
            <!-- begin wrapper -->
            <td class="wrapper">
                <table class="twelve columns">
                    <tr>
                        <td class="last">
                            <h4>Hi {{$name}},</h4>
                            <p >Welcome to <a target="_blank" href="{{env('APP_FRONTEND')}}">{{env('APP_FRONTEND')}}</a>!</p>
                            @if(@$is_buyer == true)
                            <p >  If you already paid, Please ignore this email.</p>
                            <p >  Your account is still pending. <br/>In order to have a full buyer access on our website, please pay the $99.97 subscription fee.</p>
                            @endif
                            <p >Kindly login on this link <a  target="_blank" href="{{env('APP_FRONTEND')}}/login">{{env('APP_FRONTEND')}}/login</a> and update your subscription info using your registered account below:</p>
                            <p  >Username: {{$username}}
                                <br/>Email: {{$email}}</p>
                            <p >If you are still having difficulties subscribing, please email willow@greenlightpropertygroup.com.</p>
                        </td>
                    </tr>


                </table>
            </td>
            <!-- end wrapper -->
        </tr>
    </table>
@endsection
