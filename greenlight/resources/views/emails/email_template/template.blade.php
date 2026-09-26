<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Greenlight Property Finder | @yield('title')</title>
    @include('emails.email_template.styles')
</head>
<body>
    <!-- begin page body -->
    <table class="body">
        <tr>
            <td class="center" align="center" valign="top">
                <center>
                    <!-- begin page header -->
                    @include('emails.email_template.header_logo')
                    <!-- end page header -->

                    <!-- begin page container -->
                    <table class="container content dark-theme">
                        <tr>
                            <td>
                                <!-- begin row -->
                                @yield('content')
                                <!-- end row -->
                                <!-- begin divider -->
                                <table class="divider"></table>
                                <!-- end divider -->
                                <!-- begin row -->
                                @include('emails.email_template.footer')
                                <!-- end row -->
                            </td>
                        </tr>
                    </table>
                    <!-- end page container -->

                    <!-- begin page footer -->
                    @include('emails.email_template.copyright')
                    <!-- end page footer -->
                </center>
            </td>
        </tr>
    </table>
    <!-- end page body -->
</body>
</html>