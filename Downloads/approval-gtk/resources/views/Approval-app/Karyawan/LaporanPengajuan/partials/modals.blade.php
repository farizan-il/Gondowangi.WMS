<div class="modal fade" id="notifikasiModal" tabindex="-1" aria-labelledby="notifikasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notifikasiModalLabel">
                    <i class="fas fa-bell me-2"></i>Kirim Notifikasi Pengingat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-break">
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">
                        <i class="fas fa-info-circle me-1"></i>Informasi Notifikasi
                    </h6>
                    <p class="mb-2"><strong>Requester:</strong> <span id="modal-requester-nama"></span></p>
                    <p class="mb-2"><strong>Email:</strong> <span id="modal-requester-email"></span></p>
                    <p class="mb-2"><strong>Pengajuan:</strong> <span id="modal-pengajuan-nomor"></span></p>
                    <p class="mb-0"><strong>Sisa Dana:</strong> Rp. <span id="modal-selisih"></span></p>
                </div>
                
                <h6 class="mb-2">
                    <strong>Anda akan mengirimkan notifikasi kepada requester.</strong>
                </h6>
                <p class="mb-0">
                    Notifikasi ini berfungsi sebagai pengingat bahwa masih terdapat sisa dana dari proses settlement yang belum dikembalikan. 
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                <form method="POST" action="{{ route('kirim-notifikasi-refund') }}" id="notifikasiForm">
                    @csrf
                    <input type="hidden" name="settlement_id" id="hidden-settlement-id" value="">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Kirim Notifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(45deg, #0d6efd, #0a58ca); color: white; border-radius: 15px 15px 0 0; padding: 20px 25px; border: none;">
                <h5 class="modal-title text-white" id="detailModalLabel" style="font-weight: 600; font-size: 1.1rem;">
                    <i class="fas fa-file-alt me-2"></i>Detail Pengajuan & Approval
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body" id="detailModalBody" style="padding: 0; background-color: #f8f9fa;">
                <div class="d-flex justify-content-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="detailModalFooter" style="display: none; background-color: #f8f9fa; border-top: 1px solid #dee2e6; padding: 15px 25px; border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-info" id="btnIntervention" style="border-radius: 8px; padding: 8px 16px; display: none;">
                    <i class="fas fa-calculator me-1"></i>Koreksi Nominal
                </button>
                <button type="button" class="btn btn-success" id="btnApprove" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-check me-1"></i>Setujui
                </button>
                <button type="button" class="btn btn-warning" id="btnRevision" style="border-radius: 8px; padding: 8px 16px; color: white;">
                    <i class="fas fa-edit me-1"></i>Minta Revisi
                </button>
                <button type="button" class="btn btn-danger" id="btnReject" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-times me-1"></i>Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(45deg, #6c757d, #495057); color: white; border-radius: 15px 15px 0 0; padding: 20px 25px; border: none;">
                <h5 class="modal-title" id="approvalModalLabel" style="font-weight: 600;">Konfirmasi Approval</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <form id="approvalForm">
                @csrf
                <div class="modal-body" style="padding: 25px;">
                    <input type="hidden" id="pengajuanId" value="">
                    <input type="hidden" id="approvalStatus" value="">
                    
                    <div class="alert alert-info" id="approvalMessage" style="border: none; border-radius: 10px; background-color: #e3f2fd; color: #1565c0; border-left: 4px solid #2196f3; padding: 15px;"></div>
                    
                    <div class="mb-3">
                        <label for="catatan" class="form-label" style="font-weight: 600; color: #333;">Catatan <span class="text-muted">(Opsional)</span></label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                  placeholder="Berikan catatan untuk keputusan Anda..."
                                  style="border-radius: 8px; border: 1px solid #ddd; padding: 12px; resize: vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 25px; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn" id="confirmApprovalBtn" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-check me-1"></i>Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="settlementDetailModal" tabindex="-1" aria-labelledby="settlementDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-dark">
                <h5 class="modal-title text-white" id="settlementDetailModalLabel">
                    <i class="fas fa-receipt me-2"></i>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="settlementDetailModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat detail settlement...</p>
                </div>
            </div>
            <div class="modal-footer" id="settlementDetailModalFooter" style="display: none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" id="btnSendNotification" style="border-radius: 8px; padding: 8px 16px; display: none;">
                    <i class="fas fa-bell me-1"></i>Kirim Notifikasi
                </button>
                <button type="button" class="btn btn-info" id="btnInterventionSettlement" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-calculator me-1"></i>Revisi Nominal
                </button>
                <button type="button" class="btn btn-success" id="btnSettlementApprove" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-check me-1"></i>Setujui Settlement
                </button>
                <button type="button" class="btn btn-warning" id="btnSettlementRevision" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-edit me-1"></i>Minta Revisi
                </button>
                <button type="button" class="btn btn-danger" id="btnSettlementReject" style="border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-times me-1"></i>Tolak Settlement
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="notificationModalLabel">
                    <i class="fas fa-bell me-2"></i>Kirim Notifikasi Pengembalian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p><strong>Nomor Settlement:</strong> <span id="notifSettlementNumber"></span></p>
                    <p><strong>Nama Requester:</strong> <span id="notifRequesterName"></span></p>
                    <p><strong>Nominal Pengembalian:</strong> <span id="notifRefundAmount" class="text-danger fw-bold"></span></p>
                </div>
                <div class="mb-3">
                    <label for="notificationMessage" class="form-label">Pesan Tambahan (Opsional)</label>
                    <textarea class="form-control" id="notificationMessage" rows="3" placeholder="Masukkan pesan tambahan untuk requester..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmSendNotification">
                    <i class="fas fa-paper-plane me-1"></i>Kirim Notifikasi
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="interventionModal" tabindex="-1" aria-labelledby="interventionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl " >
        <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; border-radius: 15px 15px 0 0; padding: 20px 25px; border: none;">
                <h5 class="modal-title" id="interventionModalLabel" style="font-weight: 600;">
                    <i class="fas fa-calculator me-2"></i>Intervensi Detail Pengajuan - Finance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <form id="interventionForm">
                @csrf
                <div class="modal-body" style="padding: 25px;">
                    <input type="hidden" id="interventionPengajuanId" value="">
                    
                    <div class="alert alert-info" style="border: none; border-radius: 10px; background-color: #e8f4f8; color: #0c5460; border-left: 4px solid #17a2b8; padding: 15px; margin-bottom: 20px;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Intervensi Finance:</strong> Anda dapat mengubah nilai detail pengajuan sebelum proses approval dilanjutkan.
                    </div>
                    
                    <div id="detailItemsContainer">
                        </div>
                    
                    <div class="mb-3">
                        <label for="catatanIntervensi" class="form-label" style="font-weight: 600; color: #333;">
                            Catatan Intervensi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="catatanIntervensi" name="catatan_intervensi" rows="4" 
                                  placeholder="Jelaskan alasan perubahan nilai detail..."
                                  style="border-radius: 8px; border: 1px solid #ddd; padding: 12px; resize: vertical;"></textarea>
                        <small class="text-muted">Wajib diisi untuk mendokumentasikan alasan intervensi</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 25px; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-info" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-save me-1"></i>Simpan Intervensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="settlementInterventionModal" tabindex="-1" aria-labelledby="settlementInterventionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl ">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title text-dark" id="settlementInterventionModalLabel">
                    <i class="fas fa-calculator me-2"></i>Revisi Detail Settlement - Finance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="settlementInterventionForm">
                <div class="modal-body">
                    <input type="hidden" id="settlementInterventionPengajuanId" value="">
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Revisi Settlement
                                </h6>
                                <p class="mb-0">
                                    Sebagai departemen Finance, Anda dapat mengubah detail settlement (keterangan, nominal, dan kategori biaya) 
                                    sebelum menyetujui. Perubahan akan dihitung ulang secara otomatis dan notifikasi akan dikirim ke requester 
                                    setelah layer terakhir Finance menyetujui.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div id="settlementDetailItemsContainer">
                                </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="settlementCatatanIntervensi" class="form-label">
                                <i class="fas fa-sticky-note me-1"></i>
                                Catatan Revisi <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="settlementCatatanIntervensi" 
                                      name="catatan_intervensi" 
                                      rows="3" 
                                      placeholder="Jelaskan alasan perubahan detail settlement..." 
                                      required 
                                      style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 20px; background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 20px;">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning text-dark" style="border-radius: 8px; padding: 10px 20px; font-weight: 600;">
                        <i class="fas fa-save me-1"></i>Simpan Revisi Settlement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>