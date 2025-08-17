<x-filament-panels::page>
    @if($this->payroll)
    
    {{-- PDF-like Layout --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden" style="max-width: 900px; margin: 0 auto;">
        
        {{-- Header with Logo & Title (sama seperti PDF) --}}
        <div class="text-center py-6 border-b-2 border-gray-300">
            <div class="text-2xl font-bold text-blue-600 mb-1">DGE</div>
            <div class="text-sm text-blue-500 mb-4">PT Dian Graha Elektrika</div>
            <h1 class="text-xl font-semibold text-gray-900">PAY SLIP</h1>
        </div>

        {{-- Employee Info Section (3 columns seperti PDF) --}}
        <div class="p-6">
            {{-- Row 1 --}}
            <div class="grid grid-cols-3 gap-6 mb-4 pb-4">
                <div>
                    <span class="font-bold text-gray-900">Empno : </span>
                    <span class="font-normal">{{ $this->payroll->empno }}</span>
                </div>
                <div>
                    <span class="font-bold text-gray-900">Tax Status : </span>
                    <span class="font-normal">K1</span>
                </div>
                <div class="text-left">
                    <span class="font-bold text-gray-900">{{ $this->payroll->formatted_period }}</span>
                </div>
            </div>
            
            {{-- Row 2 --}}
            <div class="grid grid-cols-3 gap-6 mb-6 pb-4 border-b-2 border-gray-300">
                <div>
                    <span class="font-bold text-gray-900">Fullname : </span>
                    <span class="font-normal">{{ $this->payroll->employee->fullname ?? 'N/A' }}</span>
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

            {{-- Income, Deduction, Accumulation Table (sama persis seperti PDF) --}}
            <div class="border border-gray-400">
                {{-- Headers --}}
                <div class="grid grid-cols-3 bg-gray-50 border-b border-gray-400">
                    <div class="px-3 py-2 font-bold text-center border-r border-gray-400">INCOME</div>
                    <div class="px-3 py-2 font-bold text-center border-r border-gray-400">DEDUCTION</div>
                    <div class="px-3 py-2 font-bold text-center">ACCUMULATION</div>
                </div>

                {{-- Content Rows --}}
                <div class="grid grid-cols-3 min-h-96">
                    {{-- INCOME COLUMN --}}
                    <div class="px-3 py-2 border-r border-gray-400">
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Basic Salary</span>
                            <span class="text-sm">@currency($this->payroll->basicsalary)</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Compensatory Day Off</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Wee Hours</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Overtime</span>
                            <span class="text-sm">@currency($this->payroll->overtime)</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">B.Trip Allowance</span>
                            <span class="text-sm">@currency($this->payroll->transport)</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Shift Allowance</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Others**</span>
                            <span class="text-sm"></span>
                        </div>
                        
                        {{-- Dynamic Income Items --}}
                        @if($this->payroll->meal > 0)
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Meal Allowance</span>
                            <span class="text-sm">@currency($this->payroll->meal)</span>
                        </div>
                        @endif
                        
                        {{-- Spacer --}}
                        <div class="py-2"></div>
                        
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Premi-Health Insurance</span>
                            <span class="text-sm">@currency($this->payroll->bpjsperusahaan)</span>
                        </div>
                        
                        {{-- Total Income --}}
                        <div class="flex justify-between py-2 border-t-2 border-gray-400 mt-3 font-bold">
                            <span class="text-sm">TOTAL INCOME</span>
                            <span class="text-sm">@currency($this->payroll->total)</span>
                        </div>
                    </div>

                    {{-- DEDUCTION COLUMN --}}
                    <div class="px-3 py-2 border-r border-gray-400">
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Tax Income</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Jamsostek</span>
                            <span class="text-sm">@currency($this->payroll->jkm)</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Deduct Remark *)</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">BPJS Pension</span>
                            <span class="text-sm">@currency($this->payroll->jht)</span>
                        </div>
                        
                        {{-- Dynamic Deduction Items --}}
                        @if($this->payroll->personaladvance > 0)
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Personal Advance</span>
                            <span class="text-sm">@currency($this->payroll->personaladvance)</span>
                        </div>
                        @endif
                        
                        {{-- Spacer --}}
                        <div class="py-2"></div>
                        
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">BPJS</span>
                            <span class="text-sm">@currency($this->payroll->bpjskaryawan)</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Premi-Health Insurance</span>
                            <span class="text-sm">@currency($this->payroll->bpjsperusahaan)</span>
                        </div>
                        
                        {{-- Total Deduction --}}
                        <div class="flex justify-between py-2 border-t-2 border-gray-400 mt-3 font-bold">
                            <span class="text-sm">TOTAL DEDUCTION</span>
                            <span class="text-sm">@currency($this->payroll->jkm + $this->payroll->jht + $this->payroll->bpjskaryawan + $this->payroll->bpjsperusahaan + $this->payroll->personaladvance)</span>
                        </div>
                    </div>

                    {{-- ACCUMULATION COLUMN --}}
                    <div class="px-3 py-2">
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Basic Salary</span>
                            <span class="text-sm">@currency($this->payroll->basicsalary)</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Compensatory Day Off</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Wee Hours</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm"></span>
                            <span class="text-sm"></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">B.Trip Allowance</span>
                            <span class="text-sm">@currency($this->payroll->transport)</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm">Shift Allowance</span>
                            <span class="text-sm">0</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-dotted border-gray-300">
                            <span class="text-sm"></span>
                            <span class="text-sm"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Take Home Pay Row (sama seperti PDF) --}}
            <div class="border-l border-r border-b border-gray-400">
                <div class="grid grid-cols-6 py-3 bg-gray-50">
                    <div class="px-3 font-bold">TAKE HOME PAY</div>
                    <div class="px-3 font-bold text-right">@currency($this->payroll->thp)</div>
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
        <p class="text-red-600 text-sm mt-2">Record ID: {{ $this->record ?? 'Unknown' }}</p>
    </div>
    @endif
</x-filament-panels::page>