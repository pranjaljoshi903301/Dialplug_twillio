@extends('layouts.master')
@section('title', $title)
@section('css')
    {!! Html::style('assets/corals/plugins/cropper/cropper.css') !!}
    {!! Html::style('assets/corals/plugins/authy/flags.authy.css') !!}
{!! Html::style('assets/corals/plugins/authy/form.authy.css') !!}
    <style>
        #image_source {
            cursor: pointer;
        }

        .tab-pane {
            padding: 10px;
        }

        .countries-input {
            width: 100%;
            color: #666;
            border-radius: 10px !important;
            border-color: #dadada !important;
        }
    </style>
@endsection
@section('content')
    <main id="listar-main" class="listar-main listar-haslayout">
        <div id="listar-content" class="listar-content">
            {!! Form::model($user = user(), ['url' => url('profile'), 'method'=>'PUT','class'=>'listar-formtheme listar-formaddlisting ajax-form','files'=>true]) !!}
            <fieldset>
                <div class="listar-boxtitle">
                    <h3>@lang('corals-directory-listing-star::auth.profile')</h3>
                </div>
                <div class="listar-dashboardmyprofile">
                    <figure class="listare-profilepic">
                        <img id="image_source" src="{{ user()->picture }}" alt="image description"
                             style="max-width: 250px">
                        {{ Form::hidden('profile_image') }}
                        <figcaption><a class="listar-btnuploadimg" href="javascript:void(0);"><i
                                        class="icon-upload2"></i>@lang('corals-directory-listing-star::auth.click_pic_update')
                            </a>
                        </figcaption>
                    </figure>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield m-0">
                                {!! CoralsForm::text('name','User::attributes.user.name',true) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield m-0">
                                {!! CoralsForm::text('last_name','User::attributes.user.last_name',true) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield m-0">
                                {!! CoralsForm::email('email','User::attributes.user.email',true) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield m-0">
                                {!! CoralsForm::text('phone_country_code','User::attributes.user.phone_country_code',false,null,['id'=>'authy-countries']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield m-0">
                                {!! CoralsForm::text('phone_number','User::attributes.user.phone_number',false,null,['id'=>'authy-cellphone']) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield m-0">
                                {!! CoralsForm::password('password','User::attributes.user.password') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield">
                                {!! CoralsForm::password('password_confirmation','User::attributes.user.password_confirmation') !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group listar-dashboardfield m-0">
                                @if(\TwoFactorAuth::isActive())
                                    {!! CoralsForm::checkbox('two_factor_auth_enabled','User::attributes.user.two_factor_auth_enabled',\TwoFactorAuth::isEnabled($user)) !!}

                                    @if(!empty(\TwoFactorAuth::getSupportedChannels()))
                                        {!! CoralsForm::radio('channel','User::attributes.user.channel', false,\TwoFactorAuth::getSupportedChannels(),\Arr::get($user->getTwoFactorAuthProviderOptions(),'channel', null)) !!}
                                    @endif
                                @endif
                                {!! CoralsForm::text('job_title','User::attributes.user.job_title') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group listar-dashboardfield m-0">
                                {!! CoralsForm::textarea('properties[about]', 'User::attributes.user.about' , false, null,[
                          'class'=>'limited-text',
                          'maxlength'=>250,
                          'help_text'=>'<span class="limit-counter">0</span>/250']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                {!! CoralsForm::formButtons(trans('corals-directory-listing-star::auth.save',['title' => $title_singular]),[],['href'=>url('dashboard')]) !!}
            </fieldset>
        </div>
        {!! Form::close() !!}
        <div class="modal fade modal-image" id="modal-image-crop" tabindex="-1" role="dialog"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <strong>@lang('corals-directory-listing-star::auth.change_image')</strong>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <img width="100%" src="" id="image_cropper" alt="picture 1" class="img-responsive">
                        </div>

                        <div class="row actions my-3">
                            <div class="col-md-8 text-left">
                                                        <span class="btn btn-info btn-file ">@lang('corals-directory-listing-star::auth.browse_files')
                                                            <input type="file" class="custom-file m-t-30" id="cropper"
                                                                   required>
                        </span>

                            </div>
                            <div class="col-md-4 ">

                                <button type="button" class="btn btn-primary rotate" data-method="rotate"
                                        data-option="-30">
                                    <i class="fa fa-undo"></i></button>
                                <button type="button" class="btn btn-primary rotate" data-method="rotate"
                                        data-option="30">
                                    <i class="fa fa-repeat"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success m-r-10 m-l-10"
                                id="Save">@lang('corals-directory-listing-star::auth.save',['title'=>''])</button>
                        <button type="button" class="btn btn-danger"
                                data-dismiss="modal">@lang('corals-directory-listing-star::auth.close')</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
@endsection


@section('js')
    {!! Html::script('assets/corals/plugins/cropper/cropper.js') !!}
    {!! \Html::script('assets/corals/plugins/authy/form.authy.js') !!}

    <script type="text/javascript">
        $('#country-div').on("DOMSubtreeModified", function () {
            $(".countries-input").addClass('form-control');
        });

        $(function () {
/////// Cropper Options set
            var $cropper = $('#image_cropper');
            var options = {
                aspectRatio: 1 / 1,
                minContainerWidth: 350,
                minContainerHeight: 350,
                minCropBoxWidth: 145,
                minCropBoxHeight: 145,
                rotatable: true,
                cropBoxResizable: true,
                crop: function (e) {
                    $("#cropped_value").val(JSON.stringify(e.detail));
                }
            };
///// Show cropper on existing Image
            $("body").on("click", "#image_source", function () {
                var src = $("#image_source").attr("src");
                src = src.replace("/thumb", "");
                $cropper.attr('src', src);
                $("#modal-image-crop").modal("show");
            });
///// Destroy Cropper on Model Hide
            $("#modal-image-crop").on("hide.bs.modal", function () {
                $cropper.cropper('destroy');
                $(".cropper-container").remove();

            });
/// Show Cropper on Model Show
            $("#modal-image-crop").on("show.bs.modal", function () {
                $cropper.cropper(options);
            });
///// Rotate Image
            $("body").on("click", "#modal-image-crop .rotate", function () {
                var degree = $(this).attr("data-option");
                $cropper.cropper('rotate', degree);
            });
///// Saving Image with Ajax Call
            $("body").on("click", "#Save", function () {
                var cropped_image = $cropper.cropper('getCroppedCanvas');
                var canvasURL = cropped_image.toDataURL('image/jpeg');
                $("#image_source").attr('src', canvasURL);
                $("input[name=profile_image]").val(canvasURL);

                $cropper.cropper('destroy');
                $("#modal-image-crop").modal("hide");
            });

////// When user upload image
            $(document).on("change", "#cropper", function () {
                var imagecheck = $(this).data('imagecheck'),
                    file = this.files[0],
                    imagefile = file.type,
                    _URL = window.URL || window.webkitURL;
                img = new Image();
                img.src = _URL.createObjectURL(file);
                img.onload = function () {
                    var match = ["image/jpeg", "image/png", "image/jpg"];
                    if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]))) {
                        alert('Please Select A valid Image File');
                        return false;
                    } else {
                        var reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onloadend = function () { // set image data as background of div
                            $('#image_cropper').attr('src', this.result);
                            $cropper.cropper('destroy');
                            $cropper.cropper(options);
                        }
                    }
                }
            });
        });
    </script>

    <script type="text/javascript">
        function refresh_address(data) {
            $('#profile_addresses').html(data.address_list);
            $('#profile_addresses input').val("");
            $('#profile_addresses select').val("");
        }
    </script>
@endsection
