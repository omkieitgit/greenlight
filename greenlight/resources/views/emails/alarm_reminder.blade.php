@extends('emails.email_template.template')

@section('content')
    <table class="row">
        <tr>
            <!-- begin wrapper -->
            <td class="wrapper">
                <table class="twelve columns">
                    <tr>
                        <td class="last">
                            <h4>Hi,</h4>
                            {!! $content !!}
                        </td>
                    </tr>

                </table>
            </td>
            <!-- end wrapper -->
        </tr>
    </table>
@endsection
