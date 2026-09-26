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
                            <p class="m-b-5">Welcome Aboard!</p>
                            <p class="m-b-5">Thank you for payment. Your account has been activated.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="panel">
                            <p>Our team will start sending you out properties based on your registered State.</p>
                            <p>Kindly download the Microsoft Teams app and accept our invitation once you receive it.</p>
                            <p>Your questions about the system, subscription and the properties sent to you will be answered on your MSTeam group.</p>
                            <p>We highly recommend that you join our System Introduction Zoom Meetings.</p>
                            <table class="mar50">
                                <tr >
                                    <td class="pad0">Monday</td><td class="pad0"><span class="offset-by-one">08:00 PM EST System Intro</span></td>
                                </tr>
                                <tr>
                                    <td class="pad0">Tuesday</td><td class="pad0"><span class="offset-by-one">11:30 AM EST System Intro</span>
                                            <br/><span class="offset-by-one">06:00 PM EST Beginner</span>
                                            <br/><span class="offset-by-one">07:00 PM EST Intermediate and Advanced</span>
                                        </td>
                                </tr>
                                <tr>
                                    <td class="pad0">Wednesday</td><td class="pad0"><span class="offset-by-one">03:00 PM EST Comp call</span></td>
                                </tr>
                                <tr>
                                    <td class="pad0">Thursday</td><td class="pad0"><span class="offset-by-one">06:00 PM EST Beginner</span>
                                            <br/><span class="offset-by-one">07:00 PM EST Intermediate and Advanced</span>
                                        </td>
                                </tr>
                            </table>

                            <p class="offset-by-one">
                                PC, Mac, or Smart Phone "Zoom" App (Audio and Screen):
                                <br/><a target="_blank" href="https://zoom.us/j/4113274556">https://zoom.us/j/4113274556</a>
                                <br>Phone (Audio Only):
                                <br>1-646-558-8656
                                <br>Meeting ID: 411-327-4556
                                <br>Password: 7023274556
                            </p>
                            <p>Please do not hesitate to ask any one of us for questions. We want to make sure you buy and that you buy right.</p>

                        </td>
                    </tr>

                </table>
            </td>
            <!-- end wrapper -->
        </tr>
    </table>
@endsection
