@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ env('PLAN_ID') }}</div>
                    <div class="card-body">
                        <form method="post" action="#">
                            {{--@csrf--}}
                            <div id="dropin-container"></div>
                            <hr />
                            <input type="hidden" name="plan" value="sdsdsd" />
                            <button type="button" class="btn btn-outline-dark d-none" id="payment-button">Pay</button>
                        </form>

                        <form action="" method="post" id="wholesale_register_form" enctype="multipart/form-data">
                            <div id="payment-form"></div>

                            <button style="  padding: -8px; font-size: 22px;  min-width: 208px;  min-height: 45px;"  type="submit" class="es-button left" id="payment_sub"  class="btn btn-default ic" value="">
                                Check Out Now
                            </button>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.braintreegateway.com/js/braintree-2.32.1.min.js"></script>
    <script>


        jQuery.ajax({

                @if(env('APP_ENV')=='production')
                url: "/api/braintree",
                @else
                url: "/braintree/12",
                @endif
            {{--url: "{{ route('token') }}",--}}

        })
            .done(function(res) {
                //
                // var button = document.querySelector('#payment-button');
                //
                // braintree.dropin.create({
                //     authorization: res.data.token,
                //     selector: '#dropin-container'
                // }, function (err, instance) {
                //     button.addEventListener('click', function () {
                //         instance.requestPaymentMethod(function (err, payload) {
                //             // Submit payload.nonce to your server
                //         });
                //     })
                // });

                    // braintree.dropin.create({
                    //     authorization: res.data.token,
                    //     container: '#dropin-container'
                    // }, function (createErr, instance) {
                    //     button.addEventListener('click', function () {
                    //         instance.requestPaymentMethod(function (err, payload) {
                    //             // Submit payload.nonce to your server
                    //             alert("check console log");
                    //             console.log(err);
                    //             console.log(payload);
                    //
                    //         });
                    //     });
                    // });

                    // var button = document.querySelector('#submit-button');

                    braintree.setup(res.data.token, 'dropin', {
                        container: 'payment-form',
                        paypal: {
                            singleUse: false,
                            amount: '30',
                            currency: 'USD'
                        },
                        onReady: function(integration) {
                            checkout = integration;
                            jQuery('#payment-button').removeClass('d-none')
                        },
                        onPaymentMethodReceived: function(obj){
                            console.log(obj)
                            alert(obj)
                        }

                    });



            });
        var checkout;
        // var temp;
        // var submitButton = document.querySelector('#payment-button');
        // // Add a click event listener to your own PayPal button
        // // Note: cross-browser compatibility for click handlers and events are up to you
        // submitButton.querySelector('#payment-button').addEventListener('click', function (event) {
        //     event.preventDefault();
        //
        // }, false);
    </script>
@endsection