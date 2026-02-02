<div class="steps-container" style="margin-bottom: 40px; position: relative; display: flex; justify-content: space-between; align-items: flex-start;">
    <div class="steps-line" style="position: absolute; top: 16px; left: 5%; right: 5%; height: 2px; background: #e2e8f0; z-index: 0;"></div>
    
    @php
        $steps = [
            1 => 'Dorm',
            2 => 'Disiplin',
            3 => 'Kerosakan',
            4 => 'Pelajar Sakit',
            5 => 'Dewan Makan',
            6 => 'Semakan'
        ];
    @endphp

    @foreach($steps as $num => $label)
        <div class="step {{ $currentStep > $num ? 'completed' : ($currentStep == $num ? 'active' : '') }}" style="position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; flex: 1;">
            <div class="step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: {{ $currentStep > $num ? '#10b981' : ($currentStep == $num ? 'var(--accent)' : '#fff') }}; border: 2px solid {{ $currentStep >= $num ? ($currentStep > $num ? '#10b981' : 'var(--accent)') : '#e2e8f0' }}; display: flex; align-items: center; justify-content: center; color: {{ $currentStep >= $num ? '#fff' : '#64748b' }}; font-weight: 700; font-size: 14px; margin-bottom: 8px; transition: all 0.3s; {{ $currentStep == $num ? 'box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);' : '' }}">
                @if($currentStep > $num)
                    ✓
                @else
                    {{ $num }}
                @endif
            </div>
            <div class="step-label" style="font-size: 12px; font-weight: 600; color: {{ $currentStep == $num ? 'var(--accent)' : ($currentStep > $num ? '#10b981' : '#94a3b8') }}; text-align: center;">
                {{ $label }}
            </div>
        </div>
    @endforeach
</div>
