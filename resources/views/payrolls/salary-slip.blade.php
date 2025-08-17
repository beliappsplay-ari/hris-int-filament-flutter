<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Pay Slip</title>
    <style>
        body {
            background-color: white;
            margin: 0;
            font-family: Arial, sans-serif;
            color: black;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 16px;
        }

        .logo {
            width: 144px;
            height: 48px;
            margin-bottom: 16px;
        }

        h1 {
            text-align: center;
            font-weight: 400;
            font-size: 1.25rem;
            margin-bottom: 16px;
        }

        table.table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.6rem;
            color: black;
            border-bottom: 1px solid #000;
            border-left: 1px solid #000;
            border-top: 1px solid #000;
            border-right: 1px solid #000;
        }

        th,
        td {
            border: 0px solid black;
        }

        th {
            font-weight: 700;
            text-align: left;
        }

        td {
            font-weight: 400;
            vertical-align: top
        }

        .w-1-3 {
            width: 33.3333%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: 700;
        }

        .py-1 {
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .py-0-5 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .py-4 {
            padding-top: 16px;
            padding-bottom: 16px;
        }

        .px-2 {
            padding-left: 8px;
            padding-right: 8px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .font-normal {
            font-weight: 400;
        }

        .float-right {
            float: right
        }
        .data {
            display:flex; 
            justify-content: space-between;
            padding:3px;
        }

        @media (max-width: 640px) {
            .container {
                padding: 8px;
            }

            .logo {
                width: 120px;
                height: 40px;
                margin-bottom: 12px;
            }

            h1 {
                font-size: 1.125rem;
                margin-bottom: 12px;
            }

            table {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Logo DGE --}}
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 18px; font-weight: bold; color: #1E40AF;">DGE</div>
            <div style="font-size: 12px; color: #1E40AF;">PT Dian Graha Elektrika</div>
        </div>
        
        <h1>PAY SLIP</h1>
        
        <table class="table">
            <tbody>
                <tr>
                    <td colspan="2" style="padding-top: 20px;padding-left:10px;" class="w-1-3 font-bold">Empno : <span
                            class="font-normal">{{ $payroll->empno }}</span></td>
                    <td colspan="2" style="padding-top: 20px;padding-left:10px;" class="w-1-3 font-bold text-left">Tax
                        Status : <span class="font-normal">K1</span></td>
                    <td colspan="2" style="padding-top: 20px;padding-left:10px;" class="w-1-3 font-bold text-left">{{ $payroll->formatted_period }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #000;">
                    <td colspan="2" style="padding-bottom: 20px;padding-left:10px;padding-top:10px;"
                        class="w-1-3 font-bold">Fullname : <span class="font-normal">{{ $payroll->employee->fullname ?? 'N/A' }}</span></td>
                    <td colspan="2" style="padding-bottom: 20px;padding-left:10px;padding-top:10px;"
                        class="w-1-3 font-bold text-left">NPWP No : <span class="font-normal">-</span></td>
                    <td colspan="2" style="padding-bottom: 20px;padding-left:10px;padding-top:10px;"
                        class="w-1-3 font-bold text-left">Jamsostek No : <span class="font-normal">-</span></td>
                </tr>
                <tr>
                    <th colspan="2" class="w-1-3 px-2 py-0-5">INCOME</th>
                    <th colspan="2" class="w-1-3 px-2 py-0-5">DEDUCTION</th>
                    <th colspan="2" class="w-1-3 px-2 py-0-5">ACCUMULATION</th>
                </tr>
                <tr>
                    <td colspan="2" class="px-2 py-0-5">
                        <div class="data">
                            <span>Basic Salary </span>
                            <span class="float-right">@currency($payroll->basicsalary)</span>
                        </div>
                        <div class="data">
                            <span>Compensatory Day Off </span>
                            <span class="float-right">0</span>
                        </div>
                        <div class="data">
                            <span>Wee Hours </span>
                            <span class="float-right">0</span>
                        </div>
                        <div class="data">
                            <span>Overtime </span>
                            <span class="float-right">@currency($payroll->overtime)</span>
                        </div>
                        <div class="data">
                            <span>B.Trip Allowance </span>
                            <span class="float-right">@currency($payroll->transport)</span>
                        </div>
                        <div class="data">
                            <span>Shift Allowance </span>
                            <span class="float-right">0</span>
                        </div>
                        <div class="data">
                            <span>Others** </span>
                            <span class="float-right"></span>
                        </div>
                    </td>
                    <td colspan="2" class="px-2 py-0-5">
                        <div class="data">
                            <span>Tax Income</span>
                            <span class="float-right">@currency($payroll->taxamonth)</span>
                        </div>
                        <div class="data">
                            <span>Jamsostek</span>
                            <span class="float-right">@currency($payroll->jkm)</span>
                        </div>
                        <div class="data">
                            <span>Deduct Remark *)</span>
                            <span class="float-right">0</span>
                        </div>
                        <div class="data">
                            <span>BPJS Pension</span>
                            <span class="float-right">@currency($payroll->jht)</span>
                        </div>
                    </td>

                    <td colspan="2" class="px-2 py-0-5">
                        <div class="data">
                            <span>Basic Salary </span>
                            <span class="float-right">@currency($payroll->basicsalary)</span>
                        </div>
                        <div class="data">
                            <span>Compensatory Day Off </span>
                            <span class="float-right">0</span>
                        </div>
                        <div class="data">
                            <span>Wee Hours </span>
                            <span class="float-right">0</span>
                        </div>
                        <div class="data">
                            
                        </div>
                        <div class="data">
                            <span>B.Trip Allowance </span>
                            <span class="float-right">@currency($payroll->transport)</span>
                        </div>
                        <div class="data">
                            <span>Shift Allowance </span>
                            <span class="float-right">0</span>
                        </div>
                        <div class="data">
                            
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2">
                        {{-- Dynamic allowances dari otherincome, meal, dll --}}
                        @if($payroll->meal > 0)
                        <div class="data">
                            <span>Meal Allowance</span>
                            <span class="float-right">@currency($payroll->meal)</span>
                        </div>
                        @endif
                        
                        @if($payroll->otherincome > 0)
                        <div class="data">
                            <span>Other Income</span>
                            <span class="float-right">@currency($payroll->otherincome)</span>
                        </div>
                        @endif
                        
                        @if($payroll->medical > 0)
                        <div class="data">
                            <span>Medical Allowance</span>
                            <span class="float-right">@currency($payroll->medical)</span>
                        </div>
                        @endif
                    </td>
                    <td colspan="2">
                        {{-- Dynamic deductions --}}
                        @if($payroll->personaladvance > 0)
                        <div class="data">
                            <span>Personal Advance</span>
                            <span class="float-right">@currency($payroll->personaladvance)</span>
                        </div>
                        @endif
                        
                        @if($payroll->koperasi > 0)
                        <div class="data">
                            <span>Koperasi</span>
                            <span class="float-right">@payroll->koperasi)</span>
                        </div>
                        @endif
                        
                        @if($payroll->loancar > 0)
                        <div class="data">
                            <span>Loan Car</span>
                            <span class="float-right">@currency($payroll->loancar)</span>
                        </div>
                        @endif
                    </td>
                    <td colspan="2"></td>
                </tr>

                <tr style="border-bottom: 1px solid #000;">
                    <td colspan="2">
                        <div class="data">
                            <span>&nbsp;</span>
                            <span></span>
                        </div>
                        <div class="data">
                            <span>Premi-Health Insurance</span>
                            <span class="float-right">@currency($payroll->bpjsperusahaan)</span>
                        </div>
                        <div class="data">
                            <span>TOTAL INCOME</span>
                            <span class="float-right">@currency($payroll->total)</span>
                        </div>
                    </td>
                    <td colspan="2">
                        <div class="data">
                            <span>BPJS</span>
                            <span class="float-right">@currency($payroll->bpjskaryawan)</span>
                        </div>
                        <div class="data">
                            <span>Premi-Health Insurance</span>
                            <span class="float-right">@currency($payroll->bpjsperusahaan)</span>
                        </div>
                        <div class="data">
                            <span>TOTAL DEDUCTION</span>
                            <span class="float-right">@currency($payroll->taxamonth + $payroll->jkm + $payroll->jht + $payroll->bpjskaryawan + $payroll->personaladvance + $payroll->koperasi + $payroll->loancar)</span>
                        </div>
                    </td>
                    <td colspan="2"></td>
                </tr>

                <tr>
                    <td class="px-2 py-2 font-bold">TAKE HOME PAY</td>
                    <td class="px-2 py-2 font-bold text-right">@currency($payroll->thp)</td>
                    <td></td>
                    <td></td>
                    <td class="px-2 py-2 font-bold text-right">Outstanding Leave = 0</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <p class="mt-2 font-bold" style="margin-top:8px; font-weight:700;">Note :</p>
    </div>
</body>
</html>