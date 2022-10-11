@php
function convertNumberToWord($num = false)
{
$num = str_replace(array(',', ' '), '' , trim($num));
if(! $num) {
return false;
}
$num = (int) $num;
$words = array();
$list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
);
$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
$list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion',
'septillion',
'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
);
$num_length = strlen($num);
$levels = (int) (($num_length + 2) / 3);
$max_length = $levels * 3;
$num = substr('00' . $num, -$max_length);
$num_levels = str_split($num, 3);
for ($i = 0; $i < count($num_levels); $i++) { $levels--; $hundreds=(int) ($num_levels[$i] / 100); $hundreds=($hundreds
    ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '' ); $tens=(int) ($num_levels[$i] % 100); $singles='' ; if ( $tens <
    20 ) { $tens=($tens ? ' ' . $list1[$tens] . ' ' : '' ); } else { $tens=(int)($tens / 10); $tens=' ' . $list2[$tens]
    . ' ' ; $singles=(int) ($num_levels[$i] % 10); $singles=' ' . $list1[$singles] . ' ' ; } $words[]=$hundreds . $tens
    . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' ); }
    $commas=count($words); if ($commas> 1) {
    $commas = $commas - 1;
    }
    return implode(' ', $words);
    }
    @endphp

    @if ($PDF)
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <title>{{ $invoice->code }}</title>
    @endif
    <style type="text/css">
        #page-wrap {
            width: 700px;
            margin: 0 auto;
        }

        .center {
            text-align: center;
            vertical-align: text-top;
        }

        .right {
            text-align: right;
            /* padding-right: 50px; */
        }

        .status-label-td .label-info {
            border-color: #00c0ef !important;
            color: #00c0ef !important;
        }

        .status-label-td .label-success {
            border-color: #00a65a !important;
            color: #00a65a !important;
        }

        .status-label-td .label-danger {
            border-color: #dd4b39 !important;
            color: #dd4b39 !important;
        }

        .status-label-td .label-primary {
            border-color: #3c8dbc !important;
            color: #3c8dbc !important;
        }

        .status-label-td.label-warning {
            border-color: #f39c12 !important;
            color: #f39c12 !important;
        }

        .status-label-td .label {
            background: unset !important;
            display: inline-block;
            padding: 15px;
            margin-top: 15px;
            font-size: 15px;
            width: 100px;
            font-weight: 700;
            line-height: 1;
            border-radius: 10px;
            border: 2px solid;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }

        .status-label-td {
            text-align: center;
        }

        .border-full {
            border: 1px solid black;
        }

        .border-top {
            border-top: 1px solid black;
        }

        .border-right {
            border-right: 1px solid black;
        }

        .border-bottom {
            border-bottom: 1px solid black
        }

        .border-left {
            border-left: 1px solid black
        }

    </style>
    @if ($PDF)
        </head>

        <body>
    @endif
    <div id="page-wrap">
        <div class="first-table">
            <table class="border-full">
                <tbody>
                    <tr>
                        <td style="width:20%; vertical-align: top;">
                            <img width=" 100" height="50" src="{{ \Settings::get('site_logo') }}">
                        </td>
                        <td class="border-right">
                            <strong>DIALPLUG PVT LTD</strong>
                            <br>
                            T2313, Ardente Office,
                            <br>
                            One Hoodi Main Rd Junction,
                            <br>
                            Bengaluru,Karnataka 560048
                            <br>
                            GSTIN/UIN: 37AADCB2230M2ZR
                            <br>
                            State Name: Banglore
                            <br>
                            CIN: X88888XX8888XXX888888
                            <br>
                            E-Mail: accounts@dialplug.com
                        </td>
                        <td>
                            <div class="border-bottom border-right">
                                Invoice No.
                                <br>
                                <br>
                                <strong>{{ $invoice->code }}</strong>
                            </div>
                            <div class="border-bottom border-right">
                                <br>
                                <br>
                                <br>
                            </div>
                            <div class="border-right">
                                Supplier Reference
                                <br>
                                <br>
                            </div>
                        </td>
                        <td>
                            <div class="border-bottom">
                                Dated
                                <br>
                                <br>
                                <strong>{{ format_date($invoice->invoice_date) }}</strong>
                            </div>
                            <div class="border-bottom">
                                Mode/Terms of Payment
                                <br>
                                <br>
                                <strong>Due on Receipt</strong>
                            </div>
                            <div>
                                Other Reference(s)
                                <br>
                                <br>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="second-table" style="width: 664px;">
            <table class="border-full">
                <tbody>
                    <tr>
                        <td class="border-right" style="width: 50%">Buyer
                            <br />
                            <strong>{{ $company_name }}</strong>
                            <br>
                            {!! $invoice->present('billing_address') !!}
                            <br>
                            <br>
                            {{-- <br>
                            524, Galleria Commercial Complex
                            <br>
                            DLP Phase IV
                            <br>
                            Gurgaon, Haryana - 122009
                            <br>
                            GSTIN/UIN: 37AADCB2230M2ZR
                            <br>
                            State Name: Banglore --}}
                            <br>
                            <br>
                        </td>
                        <td>Terms of Delivery
                            <br>
                            <strong>
                                Payment Should be Made in Favour of "Dialplug Pvt Ltd" All local
                                taxes or customs shall be borne by buyer Net amount mentioned in
                                invoice should be transferred. Any Implementation customization
                                Integration training/support should be charged Extra.
                            </strong>
                            <br>
                            <br>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="third-table">
            <table class="border-full" style="width: 664px;">
                <tbody>
                    <tr>
                        <td class="border-right border-bottom center" style="width: 5%">
                            Sl
                            <br>
                            No.
                        </td>
                        <td class="border-right border-bottom center" style="width: 50%">
                            <p>Particulars</p>
                        </td>
                        <td class="border-right border-bottom center" style="width: 15%">
                            <p>Rate</p>
                        </td>
                        <td class="border-right border-bottom center" style="width: 10%">
                            <p>per</p>
                        </td>
                        <td class="border-bottom center" style="width: 20%">
                            <p>Amount</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="border-right center" style="width: 5%">
                            1
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                        <td class="border-right center" style="width: 50%">
                            <strong>Product Subscription</strong>
                            <br>
                            <em>{{ $invoice->invoicable ? $invoice->invoicable->getInvoiceReference('pdf') : '-' }}</em>
                            <br>
                            <p class="status-label-td">
                                {!! $invoice->present('status') !!}
                            </p>
                            <br>
                            <br>
                        </td>
                        <td class="border-right" style="width: 15%">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                        <td class="border-right" style="width: 10%">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                        <td style="width: 20%" class="center">
                            {{ $invoice->present('total') }}
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td class="border-right border-top" style="width: 5%">

                        </td>
                        <td class="border-right border-top right" style="width: 50%">
                            Total
                        </td>
                        <td class="border-right border-top" style="width: 15%">

                        </td>
                        <td class="border-right border-top" style="width: 10%">

                        </td>
                        <td class="border-top center" style="width: 20%">
                            {{ $invoice->present('total') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="fourth-table">
            <table class="border-full" style="width: 664px;">
                <tbody>
                    <tr>
                        <td>
                            Amount Chargeable (in words)
                            <br>
			    <strong>
				{{ preg_replace('!\s+!', ' ', ucwords(convertNumberToWord((int) $invoice->total)) . "Dollars Only") }}
			    </strong>
                        </td>
                        <td class="right">E. & O.E</td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- <div class="fifth-table">
            <table class="border-full" style="width: 664px;">
                <tbody>
                    <tr>
                        <td class="border-right" style="width: 50%">HSN/SAC</td>
                        <td class="border-right">Taxable</td>
                        <td class="border-right" colspan="2">Integrated Tax</td>
                        <td>Total</td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right">Value</td>
                        <td class="border-bottom border-right border-top">Rate</td>
                        <td class="border-bottom border-right border-top">Amount</td>
                        <td class="border-bottom">Tax Amount</td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right"></td>
                        <td class="border-bottom border-right">100000</td>
                        <td class="border-bottom border-right">18%</td>
                        <td class="border-bottom border-right">18000</td>
                        <td class="border-bottom">18000</td>
                    </tr>
                    <tr>
                        <td class="border-right"><strong>Total</strong></td>
                        <td class="border-right">100000</td>
                        <td class="border-right"></td>
                        <td class="border-right">18000</td>
                        <td>18000</td>
                    </tr>
                </tbody>
            </table>
        </div> --}}
        {{-- <div class="sixth-table">
            <table class="border-right border-left border-top" style="width: 664px;">
                <tbody>
                    <tr>
                        <td>
                            <p>Tax Amount (in words) :
				<strong>
                                    {{ preg_replace('!\s+!', ' ', ucwords(convertNumberToWord((int) $invoice->total)) . "Dollars Only") }}
                                </strong>
			    </p>
                            <br>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> --}}
        <div class="seventh-table">
            <table class="border-right border-left border-bottom" style="width: 664px;">
                <tbody>
                    <tr>
                        <td>
                            Remarks :
                            <br>
                            Thanks for your business. Our single motto is to delight
                            customers with out service. Looking forward to your continued support
                            <p>Company's PAN : <strong>AAGCK5656C</strong></p>
                        </td>
                        <td>
                            Company's Bank Details :
                            <br>
                            Bank Name : <strong>ICICI Bank Account</strong>
                            <br>
                            A/c No. : <strong>107505001674</strong>
                            <br>
                            Branch & IFS Code : <strong>Sarjapur Road Branch (Banglore) & ICIC0000075</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br>
                            <br>
                        </td>
                        <td class="border-left border-top">
                            <strong>for DIALPLUG PVT LTD</strong>
                            <br>
                            <br>
                            Authorised Signatory
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- <table style="width:100%">
            <tbody>
                <tr>
                    <td style="width:70%; vertical-align: top;padding-top: 20px">
                        <img style="max-width: 250px;" src="{{ \Settings::get('site_logo') }}">
                    </td>
                    <td style="width: 30%; vertical-align: top;">
                        <h2>@lang('Payment::labels.invoice.title')</h2>
                        {{ $invoice->invoicable ? $invoice->invoicable->getInvoiceReference('pdf') : '-' }}<br /><br />
                        <strong>@lang('Payment::labels.invoice.date'):</strong>
                        {{ format_date($invoice->invoice_date) }}<br>
                        <strong>@lang('Payment::labels.invoice.number'):</strong> {{ $invoice->code }}<br>
                        <strong>@lang('Payment::attributes.invoice.due_date'):</strong>
                        {{ format_date($invoice->due_date) }}
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width:50%;vertical-align: top;">
                        <h3>@lang('Payment::labels.invoice.payable_to')</h3>
                        <hr />
                        {!! $invoice->invoicable && method_exists($invoice->invoicable, 'getInvoicePayableTo') ?
                        $invoice->invoicable->getInvoicePayableTo() : '-' !!}
                    </td>
                    <td style="width:50%;vertical-align: top;">
                        <h3>@lang('Payment::labels.invoice.bill_to')</h3>
                        <hr />
                        {{ $invoice->present('customer') }}<br />
                        {{ $invoice->present('email') }}<br />
                        @if ($invoice->present('phone'))
                            {{ $invoice->present('phone') }}<br>
                        @endif
                        {!! $invoice->present('billing_address') !!}
                    </td>
                </tr>
            </tbody>
        </table>
        <p>&nbsp;</p>
        <table style="width:100%;" class="outline-table invoice-items-table">
            <thead>
                <tr class="border-bottom border-right">
                    <th>@lang('Payment::labels.invoice.description')</th>
                    <th style="width: 50px;">@lang('Payment::labels.invoice.quantity')</th>
                    <th style="width: 100px;" class="center">@lang('Payment::labels.invoice.amount')</th>
                </tr>
            </thead>
            <tbody>
                <!-- Display The Invoice Items -->
                @foreach ($invoice->items as $item)
                    <tr class="border-bottom border-right">
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="center" style="padding-right: 5px;">
                            {{ \Payments::currency_convert($item->amount, null, $invoice->currency) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table style="width:100%;">
            <tr>
                <td style="width: 50%;" class="status-label-td">
                    {!! $invoice->present('status') !!}
                </td>
                <td style="width: 50%;">
                    <table style="width:100%;" class="invoice-items-table">
                        <tbody>
                            <tr class="border-bottom">
                                <td class="right">@lang('Payment::labels.invoice.sub_total')</td>
                                <td style="width: 100px;" class="center">{{ $invoice->present('sub_total') }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="right">@lang('Payment::labels.invoice.total')</td>
                                <td class="center">{{ $invoice->present('total') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        @if (!empty($invoice->terms))
            <table>
                <tbody>
                    <tr>
                        <td>
                            <h3>@lang('Payment::attributes.invoice.terms')</h3>
                            {!! $invoice->terms !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif --}}
    </div>
    @if ($PDF)
        </body>

        </html>
    @endif


