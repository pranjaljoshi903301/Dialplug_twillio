<footer class="footer">
    <div class="space-50"></div>
    <div class="container">
        <div class="row vertical-align-child">
            {{-- <div class="col-md-4 margin-b-30">
                <div class="margin-b-20">
                    <a href="{{ url('/') }}"> --}}
                        {{-- <img src="{{ \Settings::get('site_logo') }}" alt="" style="width: 40%;"> --}}
                        {{-- <h2 class="text-center"><span class="footer-logo-first">Dial</span><span class="footer-logo-second">Plug</span></h2>
                    </a>
                </div>
                
                <p style="text-align: justify !important">DialPlug is division of DigiClave where we have strong tech experts for Bitrix24 custom plugin and product development. We are specialist in Bitrix24 implementation and customization for business requirements of clients having cloud and self-hosted version both. We have team carrying experience of 100+ Bitrix24 project across the globe.

                    We are expertise in telephony solution integrations, like Asterisk, FreePBX, SIP connectors, SIP Clients,  Cisco and other systems. DialPlug is application having Asterisk and freepbx for telephony solution mainly focusing for tailoring sales domain operation requirements.</p>
            </div> --}}
            <div class="col-md-6 margin-b-30">
                <h2 class="widget title" id="widget-title">Expertise in   CRM Telephony integrations</h2>
                <div class="row text-center">
                    <ul class="list-unstyled col-md-12 col-sm-12">
                        <li class="nav-item ">
                            Asterisk, FreePBX, Vicidial
                        </li>
                        <li class="nav-item ">
                            SIP connectors
                        </li>
                        <li class="nav-item ">
                            SIP Clients
                        </li>
                    </ul>
                    <br>
                    <br>
                </div>
                {{-- <h2 class="widget title" id="widget-title">Quick Links</h2>
                <div class="row text-center">
                    <ul class="list-unstyled col-md-6 col-sm-6">
                        <li class="nav-item ">
                            <a class="nav-link" href="/" target="_self">
                                <i class="fa fa fa-home fa-fw"></i>
                                 Home
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="/contact-us" target="_self">
                                 Contact Us
                            </a>
                        </li>
                    </ul>
                    <ul class="list-unstyled col-md-6 col-sm-6">
                        <li class="nav-item ">
                            <a class="nav-link" href="/bitrix-telephony" target="_self">
                                <i class="fa fa-mobile-phone fa-fw"></i>
                                 Bitrix Telephony
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="/bitrix-mobile" target="_self">
                                <i class="fa fa-volume-control-phone fa-fw"></i>
                                 Mobile Tracker
                            </a>
                        </li>
                    </ul>
                </div> --}}
                {{-- <br>
                <br>
                <br>
                <br> --}}
            </div>
            <div class="col-md-6 margin-b-30 text-center">
                <div class="footer-widget">
                    <h2 class="widget title" id="widget-title">Get in Touch</h2>
                    <center>
                    <div class="contact-address">
                        <i class="ion-location footer-icon"></i> Address : T2313, Ardente Office One Hoodi Main Rd Junction, Bengaluru, Karnataka 560048
                    </div>
                    <div class="contact-number">
                        <i class="ion-social-whatsapp footer-icon"></i> Whatsapp : +91 9929067374
                    </div>
		    <div class="contact-number">
                        <i class="ion-ios-telephone footer-icon"></i> Contact : +91 9929067374
                    </div>
                    <div class="contact-email margin-b-10">
                        <i class="ion-email footer-icon"></i> Email : service-desk@dialplug.com
                    </div>
                    <div class="contact-email margin-b-10">
                        <i class="fa fa-support footer-icon"></i> Support : <a style="color: inherit;" href="https://tawk.to/dialplugservicedesk" target="_blank">https://servicedesk.dialplug.com/</a>
                    </div>
                    <ul class="list-inline social text-center">
                        @foreach(\Settings::get('social_links',[]) as $key=>$link)
                            <li class="list-inline-item">
                                <a href="{{ $link }}" target="_blank"><i class="ion-social-{{ $key }} footer-social-icon"></i></a>
                            </li>
                        @endforeach
                    </ul>
                </center>
                </div>
            </div>
        </div>
        <div class="row vertical-align-child" style="justify-content: center; align-items: center;">
            <p>{!! \Settings::get('footer_text','') !!}</p>
        </div>
    </div>
    <div class="space-20"></div>
</footer>

<style>
    .footer-widget {
        margin: 0 10px;
        text-align: justify;
    }
    #widget-title {
        color: #fff !important;
        margin-bottom: 30px;
        text-transform: uppercase;
        font-size: 20px;
        text-align: center;
        /* border-bottom: 1px solid #7DBA00; */
    }
    #widget-title:after {
        background: #7dba00;
        content: '';
        display: block;
        width: 120px;
        height: 3px;
        margin: 10px auto;
    }
    .contact-number, .contact-email {
        margin: 10px 0;
    }
    .footer-logo-first {
        font-size: 50px !important;
        color: #0E2D7B
    }
    .footer-logo-second {
        font-size: 50px !important;
        color: #7dba00
    }
    .footer-icon {
        font-size: 18px;
        color: #7dba00;
    }
    .contact-address, .contact-number, .contact-email {
        margin-right: 10px;
    }
    .footer-social-icon {
        color: #7dba00;
        font-size: 30px;
    }
</style>
