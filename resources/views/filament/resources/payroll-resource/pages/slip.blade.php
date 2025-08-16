<x-filament-panels::page>

<div>
    <div class="border border-2 bg-white shadow rounded-lg p-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="items-center border-b pb-4 mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-700 text-center">PAY SLIP</h3>
            </div>
        </div>

        <!-- Employee Information -->
        <div class="border-b pb-4 mb-6">
            <table class="w-full">
                <tbody>
                    <tr>
                        <td class="p-2 text-left font-medium">Empno : {{$payroll->empno}}</td>
                        <td class="p-2 text-left font-medium">Tax Status : TK0</td>
                        <td class="p-2 text-left font-medium">{{$payroll->period}}</td>
                    </tr>
                    <tr>
                        <td class="p-2 text-left font-medium">Fullname : {{$payroll->fullname}}</td>
                        <td class="p-2 text-left font-medium">NPWP No : {{$payroll->pc ?? '659196090435000'}}</td>
                        <td class="p-2 text-left font-medium">Jamsostek No : 24162159156</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pay Slip Table -->
        <style>
            .payslip-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }

            .payslip-table th {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 8px;
                font-weight: bold;
                text-align: center;
            }

            .payslip-table td {
                border: 1px solid #dee2e6;
                padding: 6px 8px;
                vertical-align: top;
            }

            .amount {
                text-align: right;
                font-family: monospace;
            }

            .section-header {
                background-color: #e9ecef;
                font-weight: bold;
            }

            .total-row {
                background-color: #f8f9fa;
                font-weight: bold;
            }

            .take-home-row {
                background-color: #e7f3ff;
                font-weight: bold;
            }
        </style>

        <table class="payslip-table">
            <thead>
                <tr>
                    <th colspan="2">INCOME</th>
                    <th colspan="2">DEDUCTION</th>
                    <th colspan="2">ACCUMULATION</th>
                </tr>
            </thead>
            <tbody>
                <!-- Basic Salary Row -->
                <tr>
                    <td>Basic Salary</td>
                    <td class="amount">{{number_format($payroll->basicsalary, 0, ',', '.')}}</td>
                    <td>Tax Income</td>
                    <td class="amount">{{number_format($payroll->taxamonth ?? 1428552, 0, ',', '.')}}</td>
                    <td>Basic Salary</td>
                    <td class="amount">{{number_format(($payroll->akum_basicsalary ?? 0) + $payroll->basicsalary, 0, ',', '.')}}</td>
                </tr>

                <!-- Compensatory Day Off -->
                <tr>
                    <td>Compensatory Day Off</td>
                    <td class="amount">0</td>
                    <td>Jamsostek</td>
                    <td class="amount">{{number_format(81000, 0, ',', '.')}}</td>
                    <td>Compensatory Day Off</td>
                    <td class="amount">0</td>
                </tr>

                <!-- Wee Hours -->
                <tr>
                    <td>Wee Hours</td>
                    <td class="amount">0</td>
                    <td>Deduct Remark (*)</td>
                    <td class="amount">0</td>
                    <td>Wee Hours</td>
                    <td class="amount">0</td>
                </tr>

                <!-- Overtime -->
                <tr>
                    <td>Overtime</td>
                    <td class="amount">{{$payroll->overtime > 0 ? number_format($payroll->overtime, 0, ',', '.') : '0'}}</td>
                    <td>BPJS Pension</td>
                    <td class="amount">{{number_format(95590, 0, ',', '.')}}</td>
                    <td>B.Trip Allowance</td>
                    <td class="amount">0</td>
                </tr>

                <!-- B.Trip Allowance -->
                <tr>
                    <td>B.Trip Allowance</td>
                    <td class="amount">0</td>
                    <td></td>
                    <td></td>
                    <td>Shift Allowance</td>
                    <td class="amount">0</td>
                </tr>

                <!-- Shift Allowance -->
                <tr>
                    <td>Shift Allowance</td>
                    <td class="amount">0</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Others Section -->
                <tr>
                    <td>Others**</td>
                    <td class="amount">{{number_format(150000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Phone Allowance -->
                <tr>
                    <td>Phone Allowance</td>
                    <td class="amount">{{number_format(150000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Performance Review -->
                <tr>
                    <td>Performance Review</td>
                    <td class="amount">{{number_format(900000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Parking Allowance -->
                <tr>
                    <td>Parking Allowance</td>
                    <td class="amount">{{number_format(250000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Relocating Allowance -->
                <tr>
                    <td>Relocating Allowance</td>
                    <td class="amount">{{number_format(500000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Additional Allowance -->
                <tr>
                    <td>Additional Allowance</td>
                    <td class="amount">{{number_format(400000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- OT Lumpsum -->
                <tr>
                    <td>OT Lumpsum</td>
                    <td class="amount">{{number_format(700000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Position Allowance -->
                <tr>
                    <td>Position Allowance</td>
                    <td class="amount">{{number_format(1000000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Sign In Bonus -->
                <tr>
                    <td>Sign In Bonus</td>
                    <td class="amount">{{number_format(200000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- BPJS -->
                <tr>
                    <td></td>
                    <td></td>
                    <td>BPJS</td>
                    <td class="amount">{{number_format(120000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Premi-Health Insurance -->
                <tr>
                    <td>Premi-Health Insurance</td>
                    <td class="amount">{{number_format(180000, 0, ',', '.')}}</td>
                    <td>Premi-Health Insurance</td>
                    <td class="amount">{{number_format(180000, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Total Row -->
                <tr class="total-row">
                    <td>TOTAL INCOME</td>
                    <td class="amount">Rp.{{number_format(19280000, 0, ',', '.')}}</td>
                    <td>TOTAL DEDUCTION</td>
                    <td class="amount">Rp.{{number_format(1905142, 0, ',', '.')}}</td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Take Home Pay -->
                <tr class="take-home-row">
                    <td><strong>TAKE HOME PAY</strong></td>
                    <td class="amount"><strong>Rp.{{number_format(17374858, 0, ',', '.')}}</strong></td>
                    <td></td>
                    <td></td>
                    <td><strong>Outstanding Leave = 0</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <!-- Note Section -->
        <div class="mt-6">
            <h4 class="text-lg font-bold text-gray-700">Note :</h4>
        </div>
    </div>
</div>

</x-filament-panels::page>