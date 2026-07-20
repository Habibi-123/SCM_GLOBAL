<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Country Comparison Engine</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('compare.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Negara A</label>
                    <select name="country_a" class="form-select">
                        <option value="">-- Pilih negara --</option>
                        @foreach ($countries as $c)
                            <option value="{{ $c->code }}" {{ request('country_a') == $c->code ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 text-center fw-bold text-muted">VS</div>

                <div class="col-md-5">
                    <label class="form-label small text-muted">Negara B</label>
                    <select name="country_b" class="form-select">
                        <option value="">-- Pilih negara --</option>
                        @foreach ($countries as $c)
                            <option value="{{ $c->code }}" {{ request('country_b') == $c->code ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Compare</button>
                </div>
            </form>
        </div>
    </div>

    @if ($countryA && $countryB)
        <div class="row g-3">
            @foreach ([['label' => 'GDP', 'key' => 'gdp', 'suffix' => '', 'format' => 'billion'],
                       ['label' => 'Inflasi', 'key' => 'inflation', 'suffix' => '%', 'format' => 'plain'],
                       ['label' => 'Risk Score', 'key' => 'risk', 'suffix' => '', 'format' => 'risk'],
                       ['label' => 'Cuaca', 'key' => 'weather', 'suffix' => '°C', 'format' => 'weather'],
                       ['label' => 'Mata uang', 'key' => 'currency', 'suffix' => '', 'format' => 'currency']] as $row)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-md-end">
                                    @php
                                        $valA = match($row['key']) {
                                            'gdp' => $countryA->latestEconomicIndicator?->gdp,
                                            'inflation' => $countryA->latestEconomicIndicator?->inflation,
                                            'risk' => $countryA->latestRiskScore?->total_score,
                                            'weather' => $countryA->latestWeather?->temperature,
                                            'currency' => $countryA->currency_code,
                                            default => null,
                                        };
                                    @endphp

                                    @if ($row['format'] === 'billion' && $valA)
                                        <h5 class="mb-0">${{ number_format($valA / 1e9, 1) }}B</h5>
                                    @elseif ($row['format'] === 'risk' && $valA)
                                        <h5 class="mb-0">
                                            {{ $valA }}
                                            <span class="badge bg-{{ match($countryA->latestRiskScore->risk_level) { 'high' => 'danger', 'medium' => 'warning', default => 'success' } }}">
                                                {{ ucfirst($countryA->latestRiskScore->risk_level) }}
                                            </span>
                                        </h5>
                                    @else
                                        <h5 class="mb-0">{{ $valA ?? 'N/A' }}{{ $valA ? $row['suffix'] : '' }}</h5>
                                    @endif
                                </div>

                                <div class="col-md-4 text-center">
                                    <small class="text-muted text-uppercase fw-semibold">{{ $row['label'] }}</small>
                                </div>

                                <div class="col-md-4 text-md-start">
                                    @php
                                        $valB = match($row['key']) {
                                            'gdp' => $countryB->latestEconomicIndicator?->gdp,
                                            'inflation' => $countryB->latestEconomicIndicator?->inflation,
                                            'risk' => $countryB->latestRiskScore?->total_score,
                                            'weather' => $countryB->latestWeather?->temperature,
                                            'currency' => $countryB->currency_code,
                                            default => null,
                                        };
                                    @endphp

                                    @if ($row['format'] === 'billion' && $valB)
                                        <h5 class="mb-0">${{ number_format($valB / 1e9, 1) }}B</h5>
                                    @elseif ($row['format'] === 'risk' && $valB)
                                        <h5 class="mb-0">
                                            {{ $valB }}
                                            <span class="badge bg-{{ match($countryB->latestRiskScore->risk_level) { 'high' => 'danger', 'medium' => 'warning', default => 'success' } }}">
                                                {{ ucfirst($countryB->latestRiskScore->risk_level) }}
                                            </span>
                                        </h5>
                                    @else
                                        <h5 class="mb-0">{{ $valB ?? 'N/A' }}{{ $valB ? $row['suffix'] : '' }}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif (request('country_a') || request('country_b'))
        <p class="text-muted text-center py-4">Pilih kedua negara untuk membandingkan.</p>
    @endif
</x-app-layout>