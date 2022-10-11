@extends('layouts.master')

@section('title', $title)

@section('css')
    <style type="text/css">
        .form-control {
            border-radius: 10px !important;
        }
    </style>
@endsection

@section('content')
    <main id="listar-main" class="listar-main listar-haslayout">
        <div id="listar-content" class="custom-reviews listar-content">
            <div id="listar-addlistingsteps" class="listar-addlistingsteps">
                <section>
                    <fieldset>
                        <div class="dashboard-list-box fl-wrap">
                            @include('partials.my_listing_reviews')
                        </div>
                    </fieldset>
                </section>
            </div>
        </div>
        <!-- section end -->
    </main>
@endsection

@section('js')
    @parent
    <script type="text/javascript">
        function removeRow(response, $form, hashedId) {
            $("#row_" + hashedId).fadeOut();
        }
    </script>
@endsection