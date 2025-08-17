<x-filament-panels::page>
    @if($payroll)
    
    {{-- PDF-like Layout --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mx-auto" style="max-width: 900px;">
        
        {{-- Header with Logo & Title --}}
        <div class="text-center py-6 border-b-2 border-gray-300">
            <div class="text-2xl font-bold text-blue-600 mb-1">DGE</div>
            <div class="text-sm text-blue-500 mb-4">PT Dian Graha Elektrika</div>
            <h1 class="text-xl font-semibold text-gray-900">PAY SLIP</h1>
        </div>

        {{-- Employee Info Section --}}
        <div class="p-6">
            {{-- Row 1 --}}
            <div class="grid grid-cols-3 gap-6 mb-4 pb-4 text-sm">
                <div>
                    <span class="font-bold text-gray-900">Empno : </span>
                    <span class="font-normal">{{ $payroll->empno }}</span>
                </div>
                <div>
                    <span class="font-bold text-gray-900">Tax Status : </span>
                    <span class="font-normal">K1</span>
                </div>
                <div class="text-left">
                    <span class="font-bold text-gray-900">{{ $payroll->formatted_period ?? 'July 2025' }}</span>
                </div>
            </div>
            
            {{-- Row 2 --}}
            <div class="grid grid-cols-3 gap-6 mb-6 pb-4 border-b-2 border-gray-300 text-sm">
                <div>
                    <span class="font-bold text-gray-900">Fullname : </span>
                    <span class="font-normal">{{ $payroll->employee->fullname ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-bold text-gray-900">NPWP No : </span>
                    <span class="font-normal">-</span>
                </div>
                <div>
                    <span class="font-bold text-gray-900">Jamsostek No : </span>
                    <span class="font-normal">-</span>
                </div>
            </div>

            {{-- Income, Deduction, Accumulation Table --}}
            <div class="border border-gray-800">
                {{-- Headers --}}
                <div class="grid grid-cols-3 bg-gray-50 border-b border-gray-800">
                    <div class="px-3 py-2 font-bold text-left border-r border-gray-800">INCOME</div>
                    <div class="px-3 py-2 font-bold text-left border-r border-gray-800">DEDUCTION</div>
                    <div class="px-3 py-2 font-bold text-left">ACCUMULATION</div>
                </div>

                {{-- Content Rows --}}
                <div class="grid grid-cols-3" style="min-height: 300px;">
                    {{-- INCOME COLUMN --}}
                    <div class="px-3 py-2 border-r border-gray-800 text-xs">
                        <div class="flex justify-between py-1">
                            <span>Basic Salary</span>
                            <span>{{ number_format($payroll->basicsalary ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Compensatory Day Off</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Wee Hours</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Overtime</span>
                            <span>{{ number_format($payroll->overtime ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>B.Trip Allowance</span>
                            <span>{{ number_format($payroll->transport ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Shift Allowance</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Others**</span>
                            <span></span>
                        </div>
                        
                        {{-- Dynamic Income Items --}}
                        @if(($payroll->meal ?? 0) > 0)
                        <div class="flex justify-between py-1">
                            <span>Meal Allowance</span>
                            <span>{{ number_format($payroll->meal, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if(($payroll->otherincome ?? 0) > 0)
                        <div class="flex justify-between py-1">
                            <span>Other Income</span>
                            <span>{{ number_format($payroll->otherincome, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if(($payroll->medical ?? 0) > 0)
                        <div class="flex justify-between py-1">
                            <span>Medical Allowance</span>
                            <span>{{ number_format($payroll->medical, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        {{-- Spacer untuk align dengan PDF --}}
                        <div style="height: 20px;"></div>
                        
                        <div class="flex justify-between py-1">
                            <span>Premi-Health Insurance</span>
                            <span>{{ number_format($payroll->bpjsperusahaan ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        {{-- Total Income --}}
                        <div class="flex justify-between py-2 border-t border-gray-800 mt-3 font-bold">
                            <span>TOTAL INCOME</span>
                            <span>{{ number_format($payroll->total ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- DEDUCTION COLUMN --}}
                    <div class="px-3 py-2 border-r border-gray-800 text-xs">
                        <div class="flex justify-between py-1">
                            <span>Tax Income</span>
                            <span>{{ number_format($payroll->taxamonth ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Jamsostek</span>
                            <span>{{ number_format($payroll->jkm ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Deduct Remark *)</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>BPJS Pension</span>
                            <span>{{ number_format($payroll->jht ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        {{-- Dynamic Deduction Items --}}
                        @if(($payroll->personaladvance ?? 0) > 0)
                        <div class="flex justify-between py-1">
                            <span>Personal Advance</span>
                            <span>{{ number_format($payroll->personaladvance, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if(($payroll->koperasi ?? 0) > 0)
                        <div class="flex justify-between py-1">
                            <span>Koperasi</span>
                            <span>{{ number_format($payroll->koperasi, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if(($payroll->loancar ?? 0) > 0)
                        <div class="flex justify-between py-1">
                            <span>Loan Car</span>
                            <span>{{ number_format($payroll->loancar, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        {{-- Spacer --}}
                        <div style="height: 20px;"></div>
                        
                        <div class="flex justify-between py-1">
                            <span>BPJS</span>
                            <span>{{ number_format($payroll->bpjskaryawan ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Premi-Health Insurance</span>
                            <span>{{ number_format($payroll->bpjsperusahaan ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        {{-- Total Deduction --}}
                        <div class="flex justify-between py-2 border-t border-gray-800 mt-3 font-bold">
                            <span>TOTAL DEDUCTION</span>
                            <span>{{ number_format(
                                ($payroll->taxamonth ?? 0) + 
                                ($payroll->jkm ?? 0) + 
                                ($payroll->jht ?? 0) + 
                                ($payroll->bpjskaryawan ?? 0) + 
                                ($payroll->bpjsperusahaan ?? 0) + 
                                ($payroll->personaladvance ?? 0) + 
                                ($payroll->koperasi ?? 0) + 
                                ($payroll->loancar ?? 0), 
                                0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- ACCUMULATION COLUMN --}}
                    <div class="px-3 py-2 text-xs">
                        <div class="flex justify-between py-1">
                            <span>Basic Salary</span>
                            <span>{{ number_format($payroll->basicsalary ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Compensatory Day Off</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Wee Hours</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span></span>
                            <span></span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>B.Trip Allowance</span>
                            <span>{{ number_format($payroll->transport ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Shift Allowance</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Take Home Pay Row --}}
            <div class="border-l border-r border-b border-gray-800">
                <div class="grid grid-cols-6 py-3 bg-gray-50">
                    <div class="px-3 font-bold">TAKE HOME PAY</div>
                    <div class="px-3 font-bold text-right">{{ number_format($payroll->thp ?? 0, 0, ',', '.') }}</div>
                    <div></div>
                    <div></div>
                    <div class="px-3 font-bold text-right col-span-2">Outstanding Leave = 0</div>
                </div>
            </div>

            {{-- Note Section --}}
            <div class="mt-6">
                <p class="font-bold text-gray-900">Note :</p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex space-x-3">
                <button 
                    wire:click="viewPdf" 
                    type="button" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View PDF
                </button>
                
                <button 
                    wire:click="downloadPdf" 
                    type="button" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </button>
            </div>
        </div>
    </div>

    @else
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <p class="text-red-800">Payroll data not found or failed to load.</p>
        <p class="text-red-600 text-sm mt-2">Record ID: {{ $record ?? 'Unknown' }}</p>
        <div class="mt-4">
            <a href="{{ route('filament.admin.resources.payrolls.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Payrolls
            </a>
        </div>
    </div>
    @endif
</x-filament-panels::page>