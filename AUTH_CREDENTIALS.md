# 🔐 Auth Credentials & Role Configuration

## Akun Login untuk Testing

| Role | Email | Password |
|------|-------|----------|
| **IT** | it@gondowangi.com | gondowangi-123 |
| **Logistik SPV** | spv.log@gondowangi.com | gondowangi-123 |
| **Logistik Admin** | adm.log@gondowangi.com | gondowangi-123 |
| **Logistik Operator** | op.log@gondowangi.com | gondowangi-123 |
| **QAC** | qac@gondowangi.com | gondowangi-123 |
| **Produksi** | prod@gondowangi.com | gondowangi-123 |

---

## 📋 Role & Permission Matrix

### A. IT (Full Access)
✅ Akses ke SEMUA fitur di sistem

---

### B. Logistik SPV
**Dapat mengakses:**
- ✅ Dashboard (WMS)
- ✅ On Hand (Full Access)
- ✅ Cycle Count (Approve Only)
- ✅ Riwayat Aktivitas
- ✅ Penerimaan Barang (Full Access)
- ✅ Quality Control (Read Only)
- ✅ PutAway (Full Access)
- ✅ Bin to Bin (Full Access)
- ✅ Reservation (Read Only, dapat membuat untuk kategori FOH-RS)
- ✅ Picking List (Full Access)
- ✅ Return (Full Access - Create & Approve)

**Note:** Dapat membuat Return untuk Material Reject (Rejected Material Type)

---

### C. Logistik Admin
**Dapat mengakses:**
- ✅ Penerimaan Barang (Full Access)
  - Create, Read, Edit, Delete, Approve

---

### D. Logistik Operator
**Dapat mengakses:**
- ✅ PutAway (Full Access)
- ✅ Bin to Bin (Full Access)
- ✅ Picking List (Full Access)

---

### E. QAC (Quality Assurance Control)
**Dapat mengakses:**
- ✅ Quality Control (Full Access)
- ✅ On Hand (Restricted - Hanya material yang:
  - Sudah kadaluarsa, ATAU
  - Akan kadaluarsa dalam 30 hari ke depan)

---

### F. Produksi
**Dapat mengakses:**
- ✅ Reservation (Full Access)
- ✅ Return (Create Only - Kategori Produksi)
  - Dapat membuat return dari kategori "Production"
  - TIDAK dapat approve return

---

## 🔄 Return Module - Role-based Access

### Return.vue - Permission-based Categories

**Logistik SPV:**
- Hanya bisa akses: **Return Material Reject**
- Form akan otomatis hide kategori "Return dari Produksi"

**Produksi:**
- Hanya bisa akses: **Return dari Produksi**
- Form akan otomatis hide kategori "Return Material Reject"

---

## 📊 Database Seeder Info

### Data yang di-seed:
- ✅ 6 Roles dengan permissions sesuai requirement
- ✅ 77 Permissions (Module + Action)
- ✅ 6 Users dengan role masing-masing

### Cara Run Seeder:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

---

## 🔑 Permissioning Strategy

Sistem menggunakan format permission: `module.action`

**Contoh:**
- `incoming.view` - Bisa melihat Penerimaan Barang
- `incoming.create` - Bisa membuat Penerimaan Barang
- `putaway.view` - Bisa melihat Putaway
- `reservation.create` - Bisa membuat Reservation

### Permission Check di Component:
```vue
<script setup>
import { usePermissions } from '@/Composables/usePermissions'

const { hasAnyPermission } = usePermissions()

// Dalam template:
// v-if="hasAnyPermission(['incoming.view', 'incoming.create'])"
</script>
```

---

## 🎯 Special Cases & Restrictions

### 1. Reservation (FOH Only for Logistik SPV)
- Logistik SPV dapat melihat semua reservation (read-only)
- Tapi hanya dapat MEMBUAT untuk `request_type = 'FOH-RS'`
- **Restricted di:** ReservationController (Backend Logic)

### 2. On Hand (Filtered for QAC)
- QAC dapat melihat On Hand
- Namun hanya menampilkan material dengan:
  - `exp_date <= NOW()` (Sudah kadaluarsa), ATAU
  - `exp_date BETWEEN NOW() AND NOW() + 30 DAYS` (30 hari ke depan)
- **Restricted di:** InventoryStockController (Backend Logic)

### 3. Return Categories
- Return.vue menggunakan `userRole` untuk filter kategori yang ditampilkan
- Component ini sudah ter-update untuk check role dan show/hide option

---

## ✅ Checklist Implementasi

- [x] Seeder dengan 6 roles dan passwords
- [x] 77 Permissions defined (Module + Action)
- [x] Users seeded dengan role masing-masing
- [x] Return.vue updated dengan permission check untuk kategori
- [x] AppLayout sudah support role-based sidebar menu
- [x] UserRole computed property di Return.vue
- [x] Form category filter berdasarkan role

---

## 🧪 Testing Rekomendasi

1. **Test IT Account:**
   - Login sebagai IT
   - Verifikasi bisa akses semua menu

2. **Test Logistik SPV:**
   - Login sebagai spv.log@gondowangi.com
   - Buka Return → Verifikasi hanya bisa pilih "Return Material Reject"
   - Coba akses QC → Hanya Read Only

3. **Test Logistik Admin:**
   - Login sebagai adm.log@gondowangi.com
   - Verifikasi hanya bisa akses Penerimaan Barang

4. **Test Produksi:**
   - Login sebagai prod@gondowangi.com
   - Buka Return → Verifikasi hanya bisa pilih "Return dari Produksi"
   - Coba buat return → Harus dengan kategori Produksi

5. **Test QAC:**
   - Login sebagai qac@gondowangi.com
   - Buka On Hand → Verifikasi hanya lihat material expired/30 hari depan
   - Full akses QC

---

**Last Updated:** 2026-01-05
**Status:** ✅ Implementation Complete
