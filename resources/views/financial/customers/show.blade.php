@include('layouts.header')

<main id="js-page-content" role="main" class="page-content">

    <div class="row">
        <!-- Müşteri Bilgileri -->
        <div class="col-xl-8">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Temel Bilgiler</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Kod:</strong></td>
                                        <td>{{ $customer->code ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ünvan:</strong></td>
                                        <td>{{ $customer->title ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hesap Türü:</strong></td>
                                        <td>
                                            @if($customer->account_type)
                                                <span class="badge badge-info">{{ $customer->account_type }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Telefon:</strong></td>
                                        <td>{{ $customer->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $customer->email ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Vergi Bilgileri</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Vergi Dairesi:</strong></td>
                                        <td>{{ $customer->tax_office ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vergi No:</strong></td>
                                        <td>{{ $customer->tax_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Yetkili Kişi:</strong></td>
                                        <td>{{ $customer->authorized_person ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($customer->address)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary">Adres</h6>
                                    <p class="text-muted">{{ $customer->address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Mali Bilgiler -->
        <div class="col-xl-4">
            <div id="panel-2" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h6 class="text-muted">Mevcut Bakiye</h6>
                                    @if($customer->current_balance > 0)
                                        <h3 class="font-weight-bold text-success">
                                            <i class="fal fa-arrow-up mr-1"></i>Alacağım: ₺{{ number_format($customer->current_balance, 2) }}
                                        </h3>
                                    @elseif($customer->current_balance < 0)
                                        <h3 class="font-weight-bold text-danger">
                                            <i class="fal fa-arrow-down mr-1"></i>Borç: ₺{{ number_format(abs($customer->current_balance), 2) }}
                                        </h3>
                                    @else
                                        <h3 class="font-weight-bold text-secondary">
                                            <i class="fal fa-balance-scale mr-1"></i>Dengeli
                                        </h3>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h6 class="text-muted">Kredi Limiti</h6>
                                    <h3 class="font-weight-bold text-info">
                                        ₺{{ number_format($customer->credit_limit, 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <h6 class="text-muted">Kullanılabilir Kredi</h6>
                                    <h4 class="font-weight-bold text-primary">
                                        ₺{{ number_format($customer->credit_limit - $customer->current_balance, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="{{ route('customers.transactions.index', $customer->id) }}" 
                               class="btn btn-primary">
                                <i class="fal fa-list mr-1"></i>Hareketleri Görüntüle
                            </a>
                            <a href="{{ route('customers.transactions.create', $customer->id) }}" 
                               class="btn btn-success">
                                <i class="fal fa-plus mr-1"></i>Yeni Hareket
                            </a>
                            <button class="btn btn-info" onclick="showMizanReport()">
                                <i class="fal fa-list-ol mr-1"></i>Mizan Raporu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Hareketler -->
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-3" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        @if($transactions->count() > 0)
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon">
                                        <i class="fal fa-info-circle"></i>
                                    </div>
                                    <div class="flex-1 ml-2">
                                        <span class="h5">Müşteri Hareketleri</span>
                                        <br>{{ $customer->title ?? 'Müşteri' }} için son hareketler listeleniyor.
                                    </div>
                                </div>
                            </div>

                            <table id="dt-transactions" class="table table-bordered table-hover table-striped w-100">
                                <thead class="bg-highlight">
                                    <tr>
                                        <th class="text-center" style="width: 20px;"><i class="fal fa-calendar" title="Tarih"></i></th>
                                        <th>Tarih</th>
                                        <th>Hesap</th>
                                        <th>Tip</th>
                                        <th>Tutar</th>
                                        <th>Açıklama</th>
                                        <th style="width: 100px;">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td class="text-center">
                                            <i class="fal fa-calendar text-primary"></i>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold">{{ \Carbon\Carbon::parse($transaction->date)->format('d.m.Y') }}</span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->date)->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fal fa-university text-info mr-2"></i>
                                                <span>{{ $transaction->account_name ?? $transaction->account }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $typeLower = strtolower($transaction->type ?? '');
                                                // Note Issued (ALINAN senet/çek) = Gelir, Check Issued (VERILEN çek) = Gider
                                                $isIncome = in_array($typeLower, ['gelir', 'income', 'gelir girişi', 'note issued', 'noteissued', 'alınan senet']);
                                                // Check Issued (VERILEN çek), expense, gider ve benzeri durumları gider olarak göster
                                                $isExpense = in_array($typeLower, ['gider', 'expense', 'gider çıkışı', 'check issued', 'checkissued', 'check']);
                                                
                                                // Türkçeleştir
                                                $displayType = $transaction->type;
                                                if ($typeLower === 'expense' || $typeLower === 'income' || 
                                                    $typeLower === 'check issued' || $typeLower === 'checkissued' ||
                                                    $typeLower === 'note issued' || $typeLower === 'noteissued') {
                                                    if ($isIncome) {
                                                        $displayType = 'Gelir';
                                                    } elseif ($isExpense) {
                                                        $displayType = 'Gider';
                                                    }
                                                }
                                            @endphp
                                            <span class="badge {{ $isIncome ? 'badge-success' : ($isExpense ? 'badge-danger' : 'badge-secondary') }}">
                                                <i class="fal {{ $isIncome ? 'fa-arrow-up' : ($isExpense ? 'fa-arrow-down' : 'fa-minus') }} mr-1"></i>
                                                {{ $displayType }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $typeLower = strtolower($transaction->type ?? '');
                                                    $isIncome = in_array($typeLower, ['gelir', 'income', 'gelir girişi', 'note issued', 'noteissued', 'alınan senet']);
                                                    $isExpense = in_array($typeLower, ['gider', 'expense', 'gider çıkışı', 'check issued', 'checkissued']);
                                                @endphp
                                                <span class="font-weight-bold {{ $isIncome ? 'text-success' : ($isExpense ? 'text-danger' : 'text-secondary') }}">
                                                    {{ $isIncome ? '+' : ($isExpense ? '-' : '±') }}₺{{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{!! $transaction->description ?? '-' !!}</span>
                                                @if($transaction->created_at)
                                                    <small class="text-muted">
                                                        <i class="fal fa-clock mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($transaction->created_at)->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-xs btn-info" title="Detay">
                                                    <i class="fal fa-eye"></i>
                                                </button>
                                                <button class="btn btn-xs btn-warning" title="Düzenle">
                                                    <i class="fal fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-4">
                                <i class="fal fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Henüz hareket bulunmuyor.</p>
                                <a href="{{ route('customers.transactions.create', $customer->id) }}" class="btn btn-primary">
                                    <i class="fal fa-plus mr-1"></i>İlk Hareketi Ekle
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mizan Raporu Modal -->
    <div class="modal fade" id="mizanModal" tabindex="-1" role="dialog" aria-labelledby="mizanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mizanModalLabel">
                        <i class="fal fa-list-ol mr-2"></i>Mizan Raporu - {{ $customer->title ?? 'Müşteri' }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mizanStartDate">Başlangıç Tarihi</label>
                                <input type="date" class="form-control" id="mizanStartDate" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mizanEndDate">Bitiş Tarihi</label>
                                <input type="date" class="form-control" id="mizanEndDate" value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fal fa-info-circle mr-2"></i>
                        <strong>Mizan Raporu:</strong> Seçilen tarih aralığındaki hesap bakiyelerini gösterir.
                    </div>

                    <div id="mizanContent">
                        <div class="text-center py-4">
                            <i class="fal fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Mizan raporu yükleniyor...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" onclick="generateMizanReport()">
                        <i class="fal fa-download mr-1"></i>Raporu İndir
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<script defer src="{{ asset('js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTables initialization for transactions table
    if (document.getElementById('dt-transactions')) {
        $('#dt-transactions').DataTable({
            responsive: true,
            stateSave: true,
            pageLength: 25,
            order: [[1, 'desc']], // Sort by date descending
            language: { url: "{{ asset('media/data/tr.json') }}" },
            columnDefs: [
                { orderable: false, targets: [0, 6] }, // Icon and actions columns
                { searchable: false, targets: [0, 6] }
            ]
        });
    }
});

// Mizan raporu göster
function showMizanReport() {
    $('#mizanModal').modal('show');
    loadMizanReport();
}

// Mizan raporu yükle
function loadMizanReport() {
    const startDate = document.getElementById('mizanStartDate').value;
    const endDate = document.getElementById('mizanEndDate').value;
    
    // AJAX ile mizan verilerini getir
    fetch(`/api/customers/{{ $customer->id }}/mizan?start_date=${startDate}&end_date=${endDate}`)
        .then(response => response.json())
        .then(data => {
            displayMizanReport(data);
        })
        .catch(error => {
            console.error('Mizan raporu yüklenirken hata:', error);
            document.getElementById('mizanContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fal fa-exclamation-triangle mr-2"></i>
                    Mizan raporu yüklenirken bir hata oluştu.
                </div>
            `;
        });
}

// Mizan raporu göster
function displayMizanReport(data) {
    const content = `
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Hesap Kodu</th>
                        <th>Hesap Adı</th>
                        <th>Borç Bakiyesi</th>
                        <th>Alacak Bakiyesi</th>
                        <th>Net Bakiye</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.accounts.map(account => `
                        <tr>
                            <td><strong>${account.code}</strong></td>
                            <td>${account.name}</td>
                            <td class="text-right">
                                <span class="text-danger">₺${parseFloat(account.debit_balance).toFixed(2)}</span>
                            </td>
                            <td class="text-right">
                                <span class="text-success">₺${parseFloat(account.credit_balance).toFixed(2)}</span>
                            </td>
                            <td class="text-right">
                                <span class="font-weight-bold ${account.net_balance >= 0 ? 'text-success' : 'text-danger'}">
                                    ₺${parseFloat(account.net_balance).toFixed(2)}
                                </span>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="2">TOPLAM</th>
                        <th class="text-right text-danger">₺${parseFloat(data.totals.debit).toFixed(2)}</th>
                        <th class="text-right text-success">₺${parseFloat(data.totals.credit).toFixed(2)}</th>
                        <th class="text-right font-weight-bold">₺${parseFloat(data.totals.net).toFixed(2)}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Toplam Borç</h6>
                        <h4 class="text-danger">₺${parseFloat(data.totals.debit).toFixed(2)}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Toplam Alacak</h6>
                        <h4 class="text-success">₺${parseFloat(data.totals.credit).toFixed(2)}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Net Bakiye</h6>
                        <h4 class="text-primary">₺${parseFloat(data.totals.net).toFixed(2)}</h4>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mizanContent').innerHTML = content;
}

// Mizan raporu indir
function generateMizanReport() {
    const startDate = document.getElementById('mizanStartDate').value;
    const endDate = document.getElementById('mizanEndDate').value;
    
    // PDF olarak indir
    window.open(`/api/customers/{{ $customer->id }}/mizan/pdf?start_date=${startDate}&end_date=${endDate}`, '_blank');
}

// Tarih değiştiğinde raporu yenile
document.getElementById('mizanStartDate').addEventListener('change', loadMizanReport);
document.getElementById('mizanEndDate').addEventListener('change', loadMizanReport);
</script>
