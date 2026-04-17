<script src="https://www.paypalobjects.com/donate/sdk/donate-sdk.js" charset="UTF-8"></script>

<div id="paypal-donate-button-container" class="text-center"></div>
<div id="suc" class="alert alert-success alert-dismissible fade position-fixed" role="alert" style="z-index: 9999;bottom: 0;margin: 2.5rem;right: 0;">

    {{-- <p>{{__("messages.donate_suc")}}</p> --}}
    {{-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button> --}}

</div>
@php
$email =  setting('paypal.email') ?? '';
$email_id = setting('paypal.id') ?? '';
@endphp
<script>
    PayPal.Donation.Button({

        env: 'production',

        @if($email_id != '')
            hosted_button_id: '{{$email_id}}',
        @elseif ($email != '')
            business: '{{$email}}',
        @endif

        image: {
            src: 'https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif',
            title: 'PayPal - The safer, easier way to pay online!',
            alt: 'Donate with PayPal button'
        },
        onComplete: function (params) {
            console.log(params);
        },

    }).render('#paypal-donate-button-container');

 </script>
