@include('setup.includes.header')
        <main class="content gl-content">
                <div class="container-fluid p-0">
                        <h1 class="h3 mb-3"><strong>Case Input</strong></h1>
                        <div class="row">
                                <div class="col-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        @if ($errors->any())
                                                                <div class="alert alert-danger">
                                                                        <ul>
                                                                        @foreach ($errors->all() as $error)
                                                                                <li>{{ $error }}</li>
                                                                        @endforeach
                                                                        </ul>
                                                                </div>
                                                        @endif
                                                </div>
                                               
                                                <div class="card-body">
                                                        <form  method="POST" id="case_input_form" class="row g-3">
                                                        @csrf
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom01" class="form-label">Property Address*</label>
                                                                        <input type="text" class="form-control" id="validationCustom01" name="address" >
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom02" class="form-label">City</label>
                                                                        <input type="text" class="form-control" id="validationCustom02" required="" name="city">
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom04" class="form-label">State</label>
                                                                        <select class="form-select" id="validationCustom04" required=""  name="state">
                                                                                <option selected="" disabled="" value="">Choose...</option>
                                                                                @foreach($states as $key=>$value)
                                                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                                                @endforeach
                                                                        </select>
                                                                        <!-- <div class="invalid-feedback">
                                                                                Please select a valid state.
                                                                        </div> -->
                                                                </div>

                                                                <div class="col-md-3">
                                                                        <label for="validationCustom03" class="form-label">County</label>
                                                                        <input type="text" class="form-control" id="validationCustom03" required="" name="county">
                                                                        <!-- <div class="invalid-feedback">
                                                                                Please provide a valid city.
                                                                        </div> -->
                                                                </div>
                                                                
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom05" class="form-label">Zip Code</label>
                                                                        <input type="text" class="form-control" id="validationCustom05" required=""  name="zip">
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom05" class="form-label">Parcel ID 1</label>
                                                                        <input type="text" class="form-control" id="validationCustom05" required="" name="parcel_id1">
                                                                </div>
                                                                <!-- <div class="col-12">
                                                                        <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required="">
                                                                                <label class="form-check-label" for="invalidCheck">
                                                                                        Agree to terms and conditions
                                                                                </label>
                                                                                <div class="invalid-feedback">
                                                                                        You must agree before submitting.
                                                                                </div>
                                                                        </div>
                                                                </div> -->
                                                                <div class="col-12">
                                                                        <button class="btn btn-primary" type="button" onclick="save_property()">Save</button>
                                                                </div>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                        </div>

                </div>
        </main>
@include('setup.includes.footer')

<script>
       
        function save_property(){
                var url ="{{ route('save-case-input') }}";
                var _this=$(this);
                var datastring=$('#case_input_form').serialize();
                var method="POST";
                var async_type=false;
                var data_type ='json';
                var functionname ="lender_funder_invite";
                comman_ajax_call(url, datastring, _this, method, async_type, data_type,functionname);
                
        }


function lender_funder_invite(obj, _this)
{

     if(obj['row']['house_id']){
             window.location.href="{{ route('case-input') }}/"+obj['row']['house_id'];
     }
    var message = (obj['message']);

    if (obj['status'] != '')
    {
        $('.funder_message').html(message);
        $('.funder_message').show();
        $('.funder_message').fadeOut(2000, function ()
        {
        });
    }
    else if (obj['status'] != '' && obj['status'] == 'failed')
    {
        //alert_notification3('warning', message, '_common_popup_message', true);

    }
    return false;
}
</script>

