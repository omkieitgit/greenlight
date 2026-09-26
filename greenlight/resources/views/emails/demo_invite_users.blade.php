<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>The Estates LLC | Invitation Email</title>
   
    
</head>
<body>
    <h3>{{$info['subject']}}</h3>
    <p>From: EstatesTracking</br>
    To: {{$info['to']}}</br>
    Cc: craig@theestates.com</br>
    Date: {{$info['date']}}</p>
    <!-- begin page body -->
    <table class="body">
        <tr>
            <td class="center" align="center" valign="top">
                <center>
                    <!-- begin page header -->
                    <table class="row header">
                        <tr>
                            <td class="center" align="center">
                                <center>
                                    <!-- begin container -->
                                    <table class="container">
                                        <tr>
                                            <td class="wrapper">
                                                <!-- begin six columns -->
                                                <table class="six columns">
                                                    <tr>
                                                        <td>
                                                            <a href="http://estatestracking.com/assets/img/logo.png"><img src="http://estatestracking.com/assets/img/logo.png" height="30px" /></a>
                                                        </td>
                                                        <td class="expander"></td>
                                                    </tr>
                                                </table>
                                                <!-- end six columns -->
                                            </td>
                                            <td class="wrapper">
                                                <!-- begin six columns -->
                                                <table class="six columns">
                                                    <tr>
                                                        <td class="text-right valign-middle">
                                                            <span class="template-label">Invitation Email</span>
                                                        </td>
                                                        <td class="expander"></td>
                                                    </tr>
                                                </table>
                                                <!-- end six columns -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- end container -->
                                </center>
                            </td>
                        </tr>
                    </table>
                    <!-- end page header -->
                    <!-- begin page container -->
                    <table class="container content dark-theme">
                        <tr>
                            <td>
                                <!-- begin row -->
                                <table class="row">
                                    <tr>
                                        <!-- begin wrapper -->
                                        <td class="wrapper">
                                            <table class="twelve columns">
                                                <tr>
                                                    <td class="last">
                                                        <h4>Welcome to THE ESTATES LLC.</h4>
                                                        <p class="m-b-5">Below is the property link : </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="panel">

                                                        <a href="{{ trim($info['link_anchor'])  }}">{{ trim($info['link_anchor'])  }}</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p class="m-t-15 last">If clicking the URL above does not work, copy and paste the URL into a browser window.</p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <!-- end wrapper -->
                                    </tr>
                                </table>
                                <!-- end row -->
                                <!-- begin divider -->
                                <table class="divider"></table>
                                <!-- end divider -->
                                <!-- begin row -->
                                <table class="row">
                                    <tr>
                                        <!-- begin wrapper -->
                                        <td class="wrapper">
                                            <!-- begin twelve columns -->
                                            <table class="twelve columns">
                                                <tr>
                                                    <td>
                                                        <p>
                                                            Thank you,<br/>
                                                            {{ $footer['TEAM_NAME'] }}<br/>
                                                            {{ $footer['TEAM_DOMAIN'] }}</p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- end twelve columns -->
                                            <p>
                                                <i>This is a system generated email and reply is not required.</i>
                                            </p>
                                        </td>
                                        <!-- end wrapper -->
                                    </tr>
                                </table>
                                <!-- end row -->
                            </td>
                        </tr>
                    </table>
                    <!-- end page container -->
                    <!-- begin page footer -->
                    <table class="row footer">
                        <tr>
                            <td class="center" align="center">
                                <center>
                                    <!-- begin container -->
                                    <table class="container">
                                        <tr>
                                            <td class="wrapper">
                                                <table class="eight  columns">
                                                    <tr>
                                                        <td>© 2018 Copyright: The Estates LLC All rights reserved.</td>
                                                        <td class="expander"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <!-- <td class="wrapper">
                                                <table class="six columns">
                                                    <tr>
                                                        <td class="wrapper text-right valign-middle">
                                                            <a href="javascript:;">About Us</a>
                                                            &nbsp;
                                                            <a href="javascript:;">Privacy Policy</a>
                                                            &nbsp;
                                                            <a href="javascript:;">Terms of Use</a>
                                                        </td>
                                                        <td class="expander"></td>
                                                    </tr>
                                                </table>
                                            </td> -->
                                        </tr>
                                    </table>
                                    <!-- end container -->
                                </center>
                            </td>
                        </tr>
                    </table>
                    <!-- end page footer -->
                </center>
            </td>
        </tr>
    </table>
    <!-- end page body -->
</body>
</html>
<div style="page-break-after: always;"></div>