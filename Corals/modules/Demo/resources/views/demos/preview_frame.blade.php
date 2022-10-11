<style>
    .full-screen-preview {
        height: 100%;
        padding: 0px;
        margin: 0px;
        overflow: hidden
    }

    .full-screen-preview__frame {
        width: 100%;
        background-color: white
    }

    .full-screen-preview__frame.-ios-fix {
        width: 10px;
        min-width: 100%;
        -webkit-overflow-scrolling: touch;
        height: 100% !important
    }

    .preview__header {
        font-size: 12px;
        height: 45px;
        background-color: #262626;
        z-index: 100;
        line-height: 45px;
        margin-bottom: 1px
    }

    .preview__laraship-logo {
        float: left;
        padding: 0 20px
    }

    .preview__switch_demo {
        display: inline-block;
        float: left;
    }

    .preview__switch_demo select {
        display: inline-block !important;
        visibility: visible;
        width: auto;
        z-index: 0;
        margin: 0px 10px;
        position: relative;
        font-size: 14px;
        height: 28px;
        padding: 3px 11px;
        line-height: 1.5;
        color: #82b440;
        font-weight: 700;
        border: none;
        border-radius: 0px;
        -webkit-appearance: inherit;
        -moz-appearance: inherit;
        appearance: inherit;
        cursor: default;
    }

    .preview__switch_demo label {


        display: inline-block;
        position: relative;
        font-size: 14px;
        padding: 5px 1px 0px 10px;
        line-height: 1.5;
        color: #82b440;
        font-weight: 700;
        border: none;
        border-radius: 0px;
        /* -webkit-appearance: menulist-button; */
        -moz-appearance: none;
        cursor: default;
    }

    .preview__laraship-logo a {
        display: inline-block;
        margin-top: 10px;
        text-indent: -9999px;
        line-height: inherit;
        height: 30px;
        width: 170px;
        background-image: url('https://elite.laraship.com/uploads/settings/site_logo_white.png');
        background-size: 170px 33px;
        background-position: bottom;
    }



    .preview__actions {
        float: right
    }


    .preview__action--purchase-form {
        display: inline-block
    }

    .preview__action--item-details {
        display: inline-block
    }

    .preview__action--close {
        border-left: 1px solid #333333
    }

    .preview__action--close a {
        color: #999999;
        text-decoration: none
    }

    .preview__action--close a:hover {
        color: white
    }

    .preview__action--close a i {
        color: white;
        font-size: 10px;
        margin-right: 10px
    }

    @media (max-width: 768px) {
        .preview__switch_demo label,.preview__laraship-logo {
           display: none;
        }

        .preview__header.ecommerce3 {
            padding-left: 0px;
            padding-right: 0px;
        }
    }

    @media (max-width: 568px) {
        .preview__action--close a span {
            display: none
        }
        .e-btn--3d.-color-primary {
            padding: 5px 5px;
        }
    }

    .e-btn--3d.-color-primary {
        box-shadow: 0 2px 0 #6f9a37;
        position: relative;
        background-color: #82b440;
        font-size: 14px;
        padding: 5px 20px;
        line-height: 1.5;
        color: #FFF;
        font-weight: 700;
    }

    .preview__header.demo{
        position: fixed;
        width: 100%;
    }


    .preview__header.braintree{
        top: 49px;
        position: fixed;
        width: 100%;
        z-index: 31;
    }

    .preview__header.ecommerce3 {
        top: 60px;
        position: fixed;
        width: 100%;
        padding-left: 250px;
        padding-right: 10px;
    }

    .preview__header.directory {
        top: 78px;
        position: fixed;
        width: 100%;
    }

    .preview__header.directory2 {

        top: 80px;
        position: fixed;
        width: 100%;
    }

    .preview__header.classifieds {

        top: 80px;
        position: fixed;
        width: 100%;
    }


    .preview__header.classified2 {


    }
    .preview__header.directory .preview__switch_demo {
        float: left;
    }
</style>
<div class="preview__header {{ $selected_demo['key'] }}" data-view="ctaHeader">
    <div class="preview__laraship-logo">
        <a href="https://www.laraship.com">laraship
            Website</a>
    </div>
    <div class="preview__switch_demo">
        <label>Select Demo:</label>
        <select id="switch_demo" class="no-selectize" onChange="window.document.location.href=this.options[this.selectedIndex].value;">
            @foreach($demos as $demo)

                <option {{ $demo['key'] == $selected_demo['key'] ? ' selected ' : '' }} value="{{ $demo['url'] }}">
                    {{ $demo['name'] }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="preview__actions">
        <div class="preview__action--buy">
            <a class="e-btn--3d -color-primary"
               href="{{ $selected_demo['buy_url'] }}">Buy {{ $selected_demo['product']  }} now!</a>
        </div>
    </div>
</div>