// Flow Approval CMS Application
class FlowApprovalCMS {
    constructor() {
        this.data = {
            kategori_pengajuan: [
                {
                    id: 1,
                    nama: "Reimbursement",
                    kode: "REIMB",
                    deskripsi: "Pengajuan penggantian biaya operasional",
                    icon: "fas fa-receipt",
                    warna: "#28a745",
                    status: "aktif",
                    settlement: true
                },
                {
                    id: 2,
                    nama: "Cuti",
                    kode: "CUTI",
                    deskripsi: "Pengajuan cuti karyawan",
                    icon: "fas fa-calendar-alt",
                    warna: "#007bff",
                    status: "aktif",
                    settlement: false
                },
                {
                    id: 3,
                    nama: "Izin Sakit",
                    kode: "SICK",
                    deskripsi: "Pengajuan izin sakit dengan surat dokter",
                    icon: "fas fa-user-md",
                    warna: "#dc3545",
                    status: "aktif",
                    settlement: false
                },
                {
                    id: 4,
                    nama: "Purchase Request",
                    kode: "PR",
                    deskripsi: "Pengajuan pembelian barang/jasa",
                    icon: "fas fa-shopping-cart",
                    warna: "#fd7e14",
                    status: "aktif",
                    settlement: true
                },
                {
                    id: 5,
                    nama: "Budget Request",
                    kode: "BUDGET",
                    deskripsi: "Pengajuan anggaran departemen",
                    icon: "fas fa-money-bill-wave",
                    warna: "#6f42c1",
                    status: "aktif",
                    settlement: true
                }
            ],
            departments: [
                { id: 1, nama: "IT Department", kode: "IT", deskripsi: "Departemen Teknologi Informasi", status: "aktif" },
                { id: 2, nama: "Finance Department", kode: "FIN", deskripsi: "Departemen Keuangan", status: "aktif" },
                { id: 3, nama: "Human Resources", kode: "HR", deskripsi: "Departemen Sumber Daya Manusia", status: "aktif" },
                { id: 4, nama: "Marketing Department", kode: "MKT", deskripsi: "Departemen Pemasaran", status: "aktif" },
                { id: 5, nama: "Operations Department", kode: "OPS", deskripsi: "Departemen Operasional", status: "aktif" }
            ],
            role_levels: [
                { id: 1, nama: "Staff", deskripsi: "Level Staff", department_id: null },
                { id: 2, nama: "Supervisor", deskripsi: "Level Supervisor", department_id: null },
                { id: 3, nama: "Manager", deskripsi: "Level Manager Departemen", department_id: null },
                { id: 4, nama: "Finance Level 1", deskripsi: "Finance Staff Level", department_id: 2 },
                { id: 5, nama: "Finance Level 2", deskripsi: "Finance Supervisor Level", department_id: 2 },
                { id: 6, nama: "Finance Level 3", deskripsi: "Finance Manager Level", department_id: 2 },
                { id: 7, nama: "Direksi", deskripsi: "Direktur/CEO Level", department_id: null }
            ],
            flows: new Map() // key: `kategori_id-department_id`, value: array of flow steps
        };

        this.templates = {
            finansial: [
                { urutan: 1, role_level_id: 3, nama_step: "Approval Manager Departemen", deskripsi: "Persetujuan dari manager departemen pemohon" },
                { urutan: 2, role_level_id: 4, nama_step: "Approval Finance Level 1", deskripsi: "Review dan verifikasi finance staff" },
                { urutan: 3, role_level_id: 5, nama_step: "Approval Finance Level 2", deskripsi: "Review dan persetujuan finance supervisor" },
                { urutan: 4, role_level_id: 6, nama_step: "Approval Finance Level 3", deskripsi: "Persetujuan finance manager" },
                { urutan: 5, role_level_id: 7, nama_step: "Approval Direksi", deskripsi: "Persetujuan final dari direksi" }
            ],
            non_finansial: [
                { urutan: 1, role_level_id: 2, nama_step: "Approval Supervisor", deskripsi: "Persetujuan dari supervisor langsung" },
                { urutan: 2, role_level_id: 3, nama_step: "Approval Manager", deskripsi: "Persetujuan dari manager departemen" }
            ]
        };

        this.currentFlowSteps = [];
        this.editingKategoriId = null;
        this.currentKategoriId = null;
        this.currentDepartmentId = null;
        this.sortableInstance = null;
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    init() {
        console.log('Initializing Flow Approval CMS...');
        this.loadKategoriTable();
        this.loadFlowTable();
        this.bindEvents();
        this.populateSelects();
        
        // Add some sample flows for demonstration
        this.data.flows.set('1-1', [
            { urutan: 1, role_level_id: 3, nama_step: "Approval Manager IT", deskripsi: "Persetujuan manager IT" },
            { urutan: 2, role_level_id: 4, nama_step: "Approval Finance L1", deskripsi: "Review finance level 1" },
            { urutan: 3, role_level_id: 5, nama_step: "Approval Finance L2", deskripsi: "Review finance level 2" },
            { urutan: 4, role_level_id: 6, nama_step: "Approval Finance L3", deskripsi: "Review finance level 3" },
            { urutan: 5, role_level_id: 7, nama_step: "Approval Direksi", deskripsi: "Persetujuan final direksi" }
        ]);
        this.data.flows.set('2-3', [
            { urutan: 1, role_level_id: 2, nama_step: "Approval Supervisor HR", deskripsi: "Persetujuan supervisor HR" },
            { urutan: 2, role_level_id: 3, nama_step: "Approval Manager HR", deskripsi: "Persetujuan manager HR" }
        ]);
        
        this.loadFlowTable();
        console.log('CMS initialized successfully');
    }

    bindEvents() {
        console.log('Binding events...');
        
        // Kategori events
        const simpanKategoriBtn = document.getElementById('simpanKategori');
        if (simpanKategoriBtn) {
            simpanKategoriBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.saveKategori();
            });
        }

        const modalKategori = document.getElementById('modalKategori');
        if (modalKategori) {
            modalKategori.addEventListener('hidden.bs.modal', () => this.resetKategoriForm());
        }

        // Flow events
        const isFinansialCheckbox = document.getElementById('isFinansial');
        if (isFinansialCheckbox) {
            isFinansialCheckbox.addEventListener('change', (e) => this.handleFinansialToggle(e.target.checked));
        }

        const flowDepartmentSelect = document.getElementById('flowDepartment');
        if (flowDepartmentSelect) {
            flowDepartmentSelect.addEventListener('change', (e) => this.handleDepartmentChange(e.target.value));
        }

        const tambahStepBtn = document.getElementById('tambahStep');
        if (tambahStepBtn) {
            tambahStepBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.tambahFlowStep();
            });
        }

        const previewFlowBtn = document.getElementById('previewFlow');
        if (previewFlowBtn) {
            previewFlowBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.generatePreview();
            });
        }

        const simpanFlowBtn = document.getElementById('simpanFlow');
        if (simpanFlowBtn) {
            simpanFlowBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.saveFlow();
            });
        }

        const modalFlow = document.getElementById('modalFlow');
        if (modalFlow) {
            modalFlow.addEventListener('hidden.bs.modal', () => this.resetFlowForm());
        }

        // Form validation
        const namaInput = document.getElementById('nama');
        if (namaInput) {
            namaInput.addEventListener('input', (e) => {
                const kode = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 10);
                const kodeInput = document.getElementById('kode');
                if (kodeInput) {
                    kodeInput.value = kode;
                }
            });
        }

        console.log('Events bound successfully');
    }

    loadKategoriTable() {
        const tbody = document.getElementById('kategoriTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';

        this.data.kategori_pengajuan.forEach((kategori, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="category-icon" style="background-color: ${kategori.warna}">
                            <i class="${kategori.icon}"></i>
                        </div>
                        <div>
                            <strong>${kategori.nama}</strong>
                            <br><small class="text-muted">${kategori.deskripsi}</small>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-secondary">${kategori.kode}</span></td>
                <td><span class="status-badge status-${kategori.status}">${kategori.status}</span></td>
                <td>
                    ${kategori.settlement ? '<span class="settlement-badge">Ya</span>' : '<span class="text-muted">Tidak</span>'}
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary" onclick="window.cms.editKategori(${kategori.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="window.cms.konfigurasiFlow(${kategori.id})" title="Konfigurasi Flow">
                            <i class="fas fa-project-diagram"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="window.cms.hapusKategori(${kategori.id})" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    loadFlowTable() {
        const tbody = document.getElementById('flowTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';

        let index = 1;
        this.data.flows.forEach((steps, key) => {
            const [kategoriId, departmentId] = key.split('-').map(Number);
            const kategori = this.data.kategori_pengajuan.find(k => k.id === kategoriId);
            const department = this.data.departments.find(d => d.id === departmentId);
            
            if (kategori && department) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index++}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="category-icon me-2" style="background-color: ${kategori.warna}; width: 24px; height: 24px;">
                                <i class="${kategori.icon}" style="font-size: 12px;"></i>
                            </div>
                            ${kategori.nama}
                        </div>
                    </td>
                    <td>${department.nama}</td>
                    <td><span class="badge bg-info">${steps.length} Step</span></td>
                    <td><span class="status-badge status-aktif">Aktif</span></td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-primary" onclick="window.cms.editFlow(${kategoriId}, ${departmentId})" title="Edit Flow">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-info" onclick="window.cms.previewFlowModal(${kategoriId}, ${departmentId})" title="Preview">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="window.cms.hapusFlow(${kategoriId}, ${departmentId})" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            }
        });

        if (this.data.flows.size === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-project-diagram fa-2x mb-2 d-block opacity-50"></i>
                    Belum ada flow approval yang dikonfigurasi
                </td>
            `;
            tbody.appendChild(row);
        }
    }

    populateSelects() {
        // Populate department select
        const departmentSelect = document.getElementById('flowDepartment');
        if (departmentSelect) {
            departmentSelect.innerHTML = '<option value="">Pilih Department</option>';
            this.data.departments.forEach(dept => {
                departmentSelect.innerHTML += `<option value="${dept.id}">${dept.nama}</option>`;
            });
        }

        // Populate kategori select for flow modal
        const kategoriSelect = document.getElementById('flowKategori');
        if (kategoriSelect) {
            kategoriSelect.innerHTML = '';
            this.data.kategori_pengajuan.forEach(kategori => {
                kategoriSelect.innerHTML += `<option value="${kategori.id}">${kategori.nama}</option>`;
            });
        }
    }

    editKategori(id) {
        console.log('Edit kategori:', id);
        const kategori = this.data.kategori_pengajuan.find(k => k.id === id);
        if (!kategori) return;

        this.editingKategoriId = id;
        
        document.getElementById('modalKategoriLabel').textContent = 'Edit Kategori Pengajuan';
        document.getElementById('nama').value = kategori.nama;
        document.getElementById('kode').value = kategori.kode;
        document.getElementById('deskripsi').value = kategori.deskripsi || '';
        document.getElementById('icon').value = kategori.icon || '';
        document.getElementById('warna').value = kategori.warna || '#007bff';
        document.getElementById('status').value = kategori.status;
        document.getElementById('settlement').checked = kategori.settlement;
        document.getElementById('kategoriId').value = id;

        const modalElement = document.getElementById('modalKategori');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    konfigurasiFlow(kategoriId) {
        console.log('Konfigurasi flow:', kategoriId);
        this.currentKategoriId = kategoriId;
        const kategori = this.data.kategori_pengajuan.find(k => k.id === kategoriId);
        
        document.getElementById('flowKategori').value = kategoriId;
        document.getElementById('modalFlowLabel').textContent = `Konfigurasi Flow - ${kategori.nama}`;
        
        // Reset form
        document.getElementById('flowDepartment').value = '';
        document.getElementById('isFinansial').checked = false;
        this.currentFlowSteps = [];
        this.renderFlowSteps();
        this.updatePreview();

        const modalElement = document.getElementById('modalFlow');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    handleFinansialToggle(isFinansial) {
        console.log('Financial toggle:', isFinansial);
        if (isFinansial) {
            this.currentFlowSteps = [...this.templates.finansial];
        } else {
            this.currentFlowSteps = [...this.templates.non_finansial];
        }
        this.renderFlowSteps();
        this.updatePreview();
    }

    handleDepartmentChange(departmentId) {
        console.log('Department change:', departmentId);
        this.currentDepartmentId = parseInt(departmentId);
        if (departmentId && this.currentKategoriId) {
            const key = `${this.currentKategoriId}-${departmentId}`;
            const existingFlow = this.data.flows.get(key);
            
            if (existingFlow) {
                this.currentFlowSteps = [...existingFlow];
                // Check if it's a financial flow
                const hasFinanceSteps = existingFlow.some(step => [4, 5, 6].includes(step.role_level_id));
                document.getElementById('isFinansial').checked = hasFinanceSteps;
            } else {
                this.currentFlowSteps = [];
            }
            
            this.renderFlowSteps();
            this.updatePreview();
        }
    }

    tambahFlowStep() {
        const newStep = {
            urutan: this.currentFlowSteps.length + 1,
            role_level_id: 1,
            nama_step: "Step Baru",
            deskripsi: "Deskripsi step"
        };
        this.currentFlowSteps.push(newStep);
        this.renderFlowSteps();
        this.updatePreview();
    }

    renderFlowSteps() {
        const container = document.getElementById('flowSteps');
        if (!container) return;
        
        container.innerHTML = '';

        if (this.currentFlowSteps.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-project-diagram"></i>
                    <p>Belum ada step approval.<br>Pilih template finansial atau tambah step manual.</p>
                </div>
            `;
            return;
        }

        this.currentFlowSteps.forEach((step, index) => {
            const stepDiv = document.createElement('div');
            stepDiv.className = 'flow-step';
            stepDiv.innerHTML = `
                <div class="flow-step-header">
                    <div class="flow-step-number">${index + 1}</div>
                    <div class="flow-step-content">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Role Level</label>
                                <select class="form-select form-select-sm" onchange="window.cms.updateStepRole(${index}, this.value)">
                                    ${this.data.role_levels.map(role => 
                                        `<option value="${role.id}" ${role.id === step.role_level_id ? 'selected' : ''}>${role.nama}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Step</label>
                                <input type="text" class="form-control form-control-sm" value="${step.nama_step}" 
                                       onchange="window.cms.updateStepName(${index}, this.value)">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control form-control-sm" rows="2" 
                                          onchange="window.cms.updateStepDesc(${index}, this.value)">${step.deskripsi}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flow-step-actions">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.cms.hapusStep(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                        <div class="drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(stepDiv);
        });

        // Clean up existing sortable
        if (this.sortableInstance) {
            this.sortableInstance.destroy();
        }

        // Initialize sortable if Sortable is available
        if (typeof Sortable !== 'undefined') {
            this.sortableInstance = new Sortable(container, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: (evt) => {
                    const oldIndex = evt.oldIndex;
                    const newIndex = evt.newIndex;
                    
                    // Reorder array
                    const item = this.currentFlowSteps.splice(oldIndex, 1)[0];
                    this.currentFlowSteps.splice(newIndex, 0, item);
                    
                    // Update urutan
                    this.currentFlowSteps.forEach((step, index) => {
                        step.urutan = index + 1;
                    });
                    
                    this.renderFlowSteps();
                    this.updatePreview();
                }
            });
        }
    }

    updateStepRole(index, roleId) {
        this.currentFlowSteps[index].role_level_id = parseInt(roleId);
        const role = this.data.role_levels.find(r => r.id === parseInt(roleId));
        if (role) {
            this.currentFlowSteps[index].nama_step = `Approval ${role.nama}`;
        }
        this.updatePreview();
    }

    updateStepName(index, name) {
        this.currentFlowSteps[index].nama_step = name;
        this.updatePreview();
    }

    updateStepDesc(index, desc) {
        this.currentFlowSteps[index].deskripsi = desc;
        this.updatePreview();
    }

    hapusStep(index) {
        this.currentFlowSteps.splice(index, 1);
        // Update urutan
        this.currentFlowSteps.forEach((step, idx) => {
            step.urutan = idx + 1;
        });
        this.renderFlowSteps();
        this.updatePreview();
    }

    updatePreview() {
        this.generatePreview();
    }

    generatePreview() {
        const container = document.getElementById('flowPreview');
        if (!container) return;
        
        if (!this.currentDepartmentId) {
            container.innerHTML = '<p class="text-muted">Pilih department untuk melihat preview flow</p>';
            return;
        }

        if (this.currentFlowSteps.length === 0) {
            container.innerHTML = '<p class="text-muted">Belum ada step approval</p>';
            return;
        }

        const department = this.data.departments.find(d => d.id === this.currentDepartmentId);
        let html = `<h6 class="mb-3">Flow untuk ${department.nama}</h6>`;

        this.currentFlowSteps.forEach((step, index) => {
            const role = this.data.role_levels.find(r => r.id === step.role_level_id);
            html += `
                <div class="preview-step flow-connector">
                    <div class="preview-step-number">${index + 1}</div>
                    <div class="preview-step-content">
                        <h6>${step.nama_step}</h6>
                        <small>${role ? role.nama : 'Unknown Role'}</small><br>
                        <small class="text-muted">${step.deskripsi}</small>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    saveKategori() {
        console.log('Saving kategori...');
        const form = document.getElementById('formKategori');
        const formData = new FormData(form);
        
        const kategoriData = {
            nama: formData.get('nama'),
            kode: formData.get('kode'),
            deskripsi: formData.get('deskripsi'),
            icon: formData.get('icon') || 'fas fa-file-alt',
            warna: formData.get('warna'),
            status: formData.get('status'),
            settlement: formData.has('settlement')
        };

        if (!kategoriData.nama || !kategoriData.kode) {
            this.showAlert('Nama dan kode kategori harus diisi!', 'danger');
            return;
        }

        if (this.editingKategoriId) {
            // Update existing
            const index = this.data.kategori_pengajuan.findIndex(k => k.id === this.editingKategoriId);
            if (index !== -1) {
                this.data.kategori_pengajuan[index] = { ...this.data.kategori_pengajuan[index], ...kategoriData };
                this.showAlert('Kategori berhasil diperbarui!', 'success');
            }
        } else {
            // Add new
            const newId = Math.max(...this.data.kategori_pengajuan.map(k => k.id), 0) + 1;
            this.data.kategori_pengajuan.push({ id: newId, ...kategoriData });
            this.showAlert('Kategori berhasil ditambahkan!', 'success');
        }

        this.loadKategoriTable();
        this.populateSelects();
        
        const modalElement = document.getElementById('modalKategori');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }

    saveFlow() {
        console.log('Saving flow...');
        if (!this.currentKategoriId || !this.currentDepartmentId) {
            this.showAlert('Pilih kategori dan department terlebih dahulu!', 'danger');
            return;
        }

        if (this.currentFlowSteps.length === 0) {
            this.showAlert('Tambahkan minimal satu step approval!', 'danger');
            return;
        }

        const key = `${this.currentKategoriId}-${this.currentDepartmentId}`;
        this.data.flows.set(key, [...this.currentFlowSteps]);
        
        this.showAlert('Flow approval berhasil disimpan!', 'success');
        this.loadFlowTable();
        
        const modalElement = document.getElementById('modalFlow');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }

    editFlow(kategoriId, departmentId) {
        console.log('Edit flow:', kategoriId, departmentId);
        this.currentKategoriId = kategoriId;
        this.currentDepartmentId = departmentId;
        
        const kategori = this.data.kategori_pengajuan.find(k => k.id === kategoriId);
        const key = `${kategoriId}-${departmentId}`;
        const flowSteps = this.data.flows.get(key);
        
        if (flowSteps) {
            this.currentFlowSteps = [...flowSteps];
            
            document.getElementById('flowKategori').value = kategoriId;
            document.getElementById('flowDepartment').value = departmentId;
            document.getElementById('modalFlowLabel').textContent = `Edit Flow - ${kategori.nama}`;
            
            // Check if financial flow
            const hasFinanceSteps = flowSteps.some(step => [4, 5, 6].includes(step.role_level_id));
            document.getElementById('isFinansial').checked = hasFinanceSteps;
            
            this.renderFlowSteps();
            this.updatePreview();
            
            const modalElement = document.getElementById('modalFlow');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }

    hapusKategori(id) {
        if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
            this.data.kategori_pengajuan = this.data.kategori_pengajuan.filter(k => k.id !== id);
            
            // Remove related flows
            const keysToDelete = [];
            this.data.flows.forEach((value, key) => {
                if (key.startsWith(`${id}-`)) {
                    keysToDelete.push(key);
                }
            });
            keysToDelete.forEach(key => this.data.flows.delete(key));
            
            this.loadKategoriTable();
            this.loadFlowTable();
            this.populateSelects();
            this.showAlert('Kategori berhasil dihapus!', 'success');
        }
    }

    hapusFlow(kategoriId, departmentId) {
        if (confirm('Apakah Anda yakin ingin menghapus flow ini?')) {
            const key = `${kategoriId}-${departmentId}`;
            this.data.flows.delete(key);
            this.loadFlowTable();
            this.showAlert('Flow berhasil dihapus!', 'success');
        }
    }

    previewFlowModal(kategoriId, departmentId) {
        console.log('Preview flow modal:', kategoriId, departmentId);
        const key = `${kategoriId}-${departmentId}`;
        const flowSteps = this.data.flows.get(key);
        const kategori = this.data.kategori_pengajuan.find(k => k.id === kategoriId);
        const department = this.data.departments.find(d => d.id === departmentId);
        
        if (flowSteps) {
            // Create temporary preview modal
            const modalHtml = `
                <div class="modal fade" id="previewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Preview Flow - ${kategori.nama} (${department.nama})</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="flow-preview">
                                    ${flowSteps.map((step, index) => {
                                        const role = this.data.role_levels.find(r => r.id === step.role_level_id);
                                        return `
                                            <div class="preview-step flow-connector">
                                                <div class="preview-step-number">${index + 1}</div>
                                                <div class="preview-step-content">
                                                    <h6>${step.nama_step}</h6>
                                                    <small>${role ? role.nama : 'Unknown Role'}</small><br>
                                                    <small class="text-muted">${step.deskripsi}</small>
                                                </div>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing preview modal if any
            const existingModal = document.getElementById('previewModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
            
            // Clean up when modal closes
            document.getElementById('previewModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        }
    }

    resetKategoriForm() {
        const form = document.getElementById('formKategori');
        if (form) {
            form.reset();
        }
        document.getElementById('modalKategoriLabel').textContent = 'Tambah Kategori Pengajuan';
        this.editingKategoriId = null;
        document.getElementById('warna').value = '#007bff';
    }

    resetFlowForm() {
        this.currentKategoriId = null;
        this.currentDepartmentId = null;
        this.currentFlowSteps = [];
        document.getElementById('flowDepartment').value = '';
        document.getElementById('isFinansial').checked = false;
        document.getElementById('flowSteps').innerHTML = '';
        document.getElementById('flowPreview').innerHTML = '<p class="text-muted">Pilih department untuk melihat preview flow</p>';
    }

    showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show fade-in-up" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        alertContainer.innerHTML = alertHtml;
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                const alertInstance = bootstrap.Alert.getInstance(alert);
                if (alertInstance) {
                    alertInstance.close();
                }
            }
        }, 3000);
    }
}

// Initialize the application when DOM is ready
let cms;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        cms = new FlowApprovalCMS();
        window.cms = cms;
    });
} else {
    cms = new FlowApprovalCMS();
    window.cms = cms;
}