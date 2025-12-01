<template>
  <AppLayout title="Cycle Count">
    <div class="min-h-screen bg-gray-50 p-2 sm:p-4 md:p-6 transition-colors duration-300">
      
      <div class="mb-6 flex justify-between items-center">
        <div>
           <h2 class="text-xl font-bold text-gray-800">Cycle Count Inventory</h2>
           <p class="text-sm text-gray-500">
             Scan QR Lokasi & Serial Number untuk validasi.
           </p>
        </div>

        <button 
          @click="submitOpname"
          :disabled="form.processing"
          class="flex items-center gap-2 px-4 py-2 rounded shadow transition-colors"
          :class="form.processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white'"
        >
          <span v-if="form.processing">Menyimpan...</span>
          <span v-else>Submit ke Supervisor</span>
        </button>
      </div>

      <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded border border-green-200">
        {{ $page.props.flash.success }}
      </div>

      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="w-full border-collapse border border-blue-300 text-sm">
           <thead class="bg-blue-200 text-gray-800 font-semibold text-center">
            <tr>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Serial Number</th>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Product Name</th>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Onhand</th>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Loc</th>
              
              <th colspan="3" class="border border-blue-400 p-2 bg-blue-300">Input Warehouseman</th>
              
              <th colspan="2" class="border border-blue-400 p-2 bg-blue-300">Hasil</th>
              
              <th colspan="2" class="border border-blue-400 p-2 bg-yellow-200">Area Supervisor</th>
            </tr>
            <tr>
              <th class="border border-blue-400 p-2 bg-blue-100 w-32">Scan Serial</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-24">Scan Bin</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-20">Qty</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-20">Acc</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-20">In Acc</th>
              
              <th class="border border-blue-400 p-2 bg-yellow-100 w-40">Note SPV</th>
              <th class="border border-blue-400 p-2 bg-yellow-100 w-20">Action</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(item, index) in form.items" :key="index">
            <tr class="hover:bg-gray-50 text-center text-gray-700">
              
              <td class="border border-gray-300 p-2 bg-gray-50 text-xs font-mono font-bold text-blue-900">
                  {{ extractSerial(item.serial_number) }}
              </td>
              <td class="border border-gray-300 p-2 text-left bg-gray-50 text-xs truncate max-w-[150px]" :title="item.product_name">
                  {{ item.product_name }}
              </td>
              <td class="border border-gray-300 p-2 bg-gray-50 text-right">
                  <div class="flex flex-col items-end justify-center">
                      <span class="font-bold text-gray-800">
                          {{ formatNumber(item.onhand) }} {{ item.uom }}
                      </span>
                  </div>
              </td>

              <td class="border border-gray-300 p-2 bg-gray-50 font-bold">{{ item.location }}</td>

              <td class="border border-gray-300 p-1">
                <button type="button" @click="openScanner(item, 'scan_serial')"
                    class="w-full py-1 px-2 text-xs rounded border flex items-center justify-center gap-1 transition-colors"
                    :class="checkSerialMatch(item) ? 'bg-green-100 border-green-500 text-green-700 font-bold' : 'bg-white border-gray-300 hover:bg-gray-100'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 6h12v12H6V6z" /></svg>
                    <span class="truncate max-w-[80px]">{{ item.scan_serial ? item.scan_serial : 'Scan SN' }}</span>
                </button>
              </td>

              <td class="border border-gray-300 p-1">
                 <button type="button" @click="openScanner(item, 'scan_bin')"
                    class="w-full py-1 px-2 text-xs rounded border flex items-center justify-center gap-1 transition-colors"
                    :class="checkBinMatch(item) ? 'bg-green-100 border-green-500 text-green-700 font-bold' : 'bg-white border-gray-300 hover:bg-gray-100'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span>{{ item.scan_bin ? item.scan_bin : 'Bin' }}</span>
                </button>
              </td>

              <td class="border border-gray-300 p-1">
                 <input type="number" v-model.number="item.physical_qty" :disabled="!isUnlocked(item)"
                  class="w-full text-center border rounded text-xs py-1 font-bold"
                  :class="isUnlocked(item) ? 'bg-white border-blue-500 text-blue-800' : 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'" placeholder="0">
              </td>

              <td class="border border-gray-300 p-2 text-xs font-bold" :class="getAccuracyColor(item)">{{ calculateAccuracy(item) }}%</td>
              <td class="border border-gray-300 p-2 text-xs font-bold" :class="getVarianceColor(item)">{{ calculateVariance(item) }}%</td>

              <td class="border border-gray-300 p-1 bg-yellow-50">
                  <input type="text" v-model="item.spv_note" 
                    class="w-full text-xs border border-yellow-300 rounded px-1 py-1 bg-white focus:ring-yellow-500 focus:border-yellow-500" 
                    placeholder="Catatan SPV...">
              </td>
              <td class="border border-gray-300 p-1 bg-yellow-50 text-center align-middle">
                  
                  <div v-if="item.status === 'DRAFT' || !item.status" class="flex flex-col items-center justify-center">
                      <span class="text-gray-400 text-[10px] italic">Waiting Submit</span>
                      <span v-if="!isUnlocked(item)" class="text-[9px] text-red-400 mt-1">(Belum Scan)</span>
                  </div>

                  <button v-else-if="item.status === 'REVIEW_NEEDED'" 
                    @click="approveItem(item)"
                    type="button" 
                    class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold py-1 px-3 rounded shadow flex items-center justify-center gap-1 mx-auto transition-transform active:scale-95 animate-pulse">
                      <span>Approve</span>
                  </button>

                  <span v-else-if="item.status === 'APPROVED'" class="text-green-600 font-bold text-[10px] border border-green-600 bg-green-50 px-2 py-0.5 rounded inline-block">
                      DONE
                  </span>
                  
                  <span v-else class="text-gray-400 text-[10px]">-</span>

              </td>
            </tr>
            
            <!-- History Row (Expandable) -->
            <!-- Show if: item is APPROVED OR has history -->
            <tr v-if="item.status === 'APPROVED' || (item.history && item.history_count > 0)" class="bg-gray-50">
              <td colspan="10" class="border border-gray-300 p-2">
                <div class="flex items-center justify-between">
                  <!-- History toggle button - only show if there's actual history -->
                  <button 
                    v-if="item.history && item.history_count > 0"
                    @click="toggleHistory(index)"
                    class="flex items-center gap-2 text-xs text-blue-600 hover:text-blue-800 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="item.showHistory ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>{{ item.showHistory ? 'Sembunyikan' : 'Lihat' }} Riwayat ({{ item.history_count }}x)</span>
                  </button>
                  
                  <!-- If no history, show info text -->
                  <span v-else class="text-xs text-gray-500 italic">
                    Material ini sudah selesai di-cycle count
                  </span>
                  
                  <!-- Repeat button - always show if row is visible -->
                  <button 
                    @click="repeatCycleCount(item)"
                    class="flex items-center gap-1 px-3 py-1 text-xs bg-orange-500 hover:bg-orange-600 text-white rounded shadow transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill=" none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Ulangi Cycle Count</span>
                  </button>
                </div>
                
                <!-- History Table (Collapsible) -->
                <div v-if="item.showHistory && item.history && item.history_count > 0" class="mt-3 overflow-x-auto">
                  <table class="w-full text-xs border border-gray-300">
                    <thead class="bg-gray-200">
                      <tr>
                        <th class="border border-gray-300 p-2">Tanggal</th>
                        <th class="border border-gray-300 p-2">System Qty</th>
                        <th class="border border-gray-300 p-2">Physical Qty</th>
                        <th class="border border-gray-300 p-2">Accuracy</th>
                        <th class="border border-gray-300 p-2">Status</th>
                        <th class="border border-gray-300 p-2">Note SPV</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(h, hIndex) in item.history" :key="hIndex" class="hover:bg-gray-100">
                        <td class="border border-gray-300 p-2 text-center">{{ h.count_date }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ formatNumber(h.system_qty) }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ formatNumber(h.physical_qty) }}</td>
                        <td class="border border-gray-300 p-2 text-center font-bold" :class="h.accuracy === 100 ? 'text-green-600' : 'text-red-600'">{{ h.accuracy }}%</td>
                        <td class="border border-gray-300 p-2 text-center">
                          <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="h.status === 'APPROVED' ? 'bg-green-100 text-green-700 border border-green-500' : 'bg-gray-100 text-gray-600'">
                            {{ h.status }}
                          </span>
                        </td>
                        <td class="border border-gray-300 p-2">{{ h.spv_note || '-' }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div v-if="showScanner" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity">
          <div class="bg-white rounded-lg shadow-2xl w-full max-w-sm mx-4 overflow-hidden relative animate-fade-in-down">
              <div class="bg-gray-100 px-4 py-3 border-b flex justify-between items-center">
                  <h3 class="font-bold text-gray-800 text-sm">Scan: <span class="text-blue-600">{{ scanningField === 'scan_serial' ? 'Serial Number' : 'Lokasi Bin' }}</span></h3>
                  <button @click="closeScanner" class="text-gray-400 hover:text-red-500 font-bold text-2xl leading-none">&times;</button>
              </div>
              <div class="p-0 bg-white relative">
                  <div id="reader" class="w-full" style="min-height: 300px;"></div>
                  <div v-if="isCameraLoading" class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>
              </div>
              <div class="bg-white px-4 py-3 border-t text-center">
                  <button @click="closeScanner" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded shadow">Batal Scan</button>
              </div>
          </div>
      </div>

      <div v-if="showErrorModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm">
         <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 p-6 text-center animate-bounce-short">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
               <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
               </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Scan Ditolak!</h3>
            <p class="text-sm text-gray-500 mb-6">{{ errorMessage }}</p>
            <button @click="showErrorModal = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:text-sm">
               Tutup & Scan Ulang
            </button>
         </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref, onMounted, nextTick, onBeforeUnmount } from 'vue' // Hapus 'watch'
import { Html5QrcodeScanner, Html5QrcodeSupportedFormats } from "html5-qrcode";

const props = defineProps({ initialStocks: Array });
const form = useForm({ items: [] });

// Scanner State
const showScanner = ref(false);
const isCameraLoading = ref(false);
const scanningItem = ref(null); 
const scanningField = ref('');  
let scannerInstance = null;

// Modal State
const showErrorModal = ref(false);
const errorMessage = ref('');

onMounted(() => { 
    loadInitialData();
});

const loadInitialData = () => {
    if (props.initialStocks) {
        form.items = JSON.parse(JSON.stringify(props.initialStocks));
        
        // Initialize showHistory for each item
        form.items.forEach((item) => {
            item.showHistory = false;
        });
    }
}

onBeforeUnmount(() => { if (scannerInstance) scannerInstance.clear().catch(err => console.error(err)); });

// --- HELPER EXTRACTOR ---
const extractSerial = (rawString) => {
    if (!rawString) return '';
    const str = rawString.toString().trim().toUpperCase();
    if (str.includes('|')) {
        const parts = str.split('|');
        if (parts.length >= 3) return parts[2];
    }
    return str;
}

// --- SCANNER LOGIC ---
const openScanner = (item, field) => {
    scanningItem.value = item;
    scanningField.value = field;
    showScanner.value = true;
    isCameraLoading.value = true;
    setTimeout(() => { nextTick(() => { initScanner(); }); }, 300);
}

const initScanner = () => {
    if (!document.getElementById('reader')) return;
    const config = { fps: 10, qrbox: { width: 200, height: 200 }, aspectRatio: 1.0, formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE, Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39 ] };
    if (scannerInstance) scannerInstance.clear().catch(e => console.warn(e));
    try {
        scannerInstance = new Html5QrcodeScanner("reader", config, false);
        scannerInstance.render(onScanSuccess, () => {});
        isCameraLoading.value = false;
    } catch (e) { alert("Error Camera"); closeScanner(); }
}

const onScanSuccess = (decodedText) => {
    let resultText = decodedText.trim();
    let isJson = false;

    // Handle JSON (Bin)
    try {
        const parsedData = JSON.parse(resultText);
        if (scanningField.value === 'scan_bin' && parsedData.bin_code) {
            resultText = parsedData.bin_code;
            isJson = true;
        }
    } catch (e) {}

    // Handle Serial Pipe
    if (scanningField.value === 'scan_serial' && !isJson) {
        resultText = extractSerial(resultText);
    }

    resultText = resultText.toUpperCase();

    // --- VALIDASI ---
    if (scanningItem.value && scanningField.value) {
        let expectedValue = '';
        let labelField = '';

        if (scanningField.value === 'scan_bin') {
            expectedValue = cleanStr(scanningItem.value.location);
            labelField = 'Lokasi Bin';
        } else if (scanningField.value === 'scan_serial') {
            expectedValue = extractSerial(scanningItem.value.serial_number);
            labelField = 'Serial Number';
        }

        // Jika TIDAK SESUAI -> Error & Jangan Simpan
        if (resultText !== expectedValue) {
            closeScanner();
            errorMessage.value = `Kode ${labelField} (${resultText}) TIDAK SESUAI target (${expectedValue}).`;
            showErrorModal.value = true;
            return; 
        }

        // Jika SESUAI -> Update Data
        scanningItem.value[scanningField.value] = resultText; 
    }
    
    closeScanner();
}

const closeScanner = () => {
    if (scannerInstance) scannerInstance.clear().catch(() => {}).finally(() => { scannerInstance = null; showScanner.value = false; });
    else showScanner.value = false;
}

// --- MATH & VALIDATION ---
const cleanStr = (str) => (str || '').toString().trim().toUpperCase();
const checkSerialMatch = (item) => {
    const scan = cleanStr(item.scan_serial);
    const target = extractSerial(item.serial_number);
    return scan === target && scan.length > 0;
}
const checkBinMatch = (item) => cleanStr(item.scan_bin) === cleanStr(item.location) && !!item.scan_bin;
const isUnlocked = (item) => checkSerialMatch(item) && checkBinMatch(item);

const formatNumber = (num) => new Intl.NumberFormat('en-US').format(num);
const calculateAccuracy = (item) => {
    if (!isUnlocked(item)) return '0.00';
    const physical = item.physical_qty ?? 0;
    const system = item.onhand;
    return system ? ((physical / system) * 100).toFixed(2) : '0.00';
}
const calculateVariance = (item) => {
    if (!isUnlocked(item)) return '0.00';
    const physical = item.physical_qty;
    if (physical === null || physical === undefined || physical === '') return '0.00';
    const system = item.onhand;
    return system ? (((physical - system) / system) * 100).toFixed(2) : '0.00';
}
const getAccuracyColor = (item) => {
    if (!isUnlocked(item)) return 'text-gray-300';
    const acc = parseFloat(calculateAccuracy(item));
    return acc === 100 ? 'text-green-600' : (acc > 100 ? 'text-blue-600' : 'text-red-600');
}
const getVarianceColor = (item) => parseFloat(calculateVariance(item)) === 0 ? 'text-gray-400' : 'text-red-600';

// --- ACTIONS ---

const submitOpname = () => {
    if (confirm('Simpan hasil opname?')) {
        form.post('/transaction/cycle-count/store', {
            onSuccess: () => {
                alert('Berhasil disubmit ke Supervisor!');
            },
            onError: (errors) => {
                console.error(errors);
                alert('Gagal menyimpan data.');
            }
        });
    }
}

const approveItem = (item) => {
    // Validasi Ganda di Frontend
    if (item.status !== 'REVIEW_NEEDED') {
        alert("Item belum disubmit oleh Warehouseman!");
        return;
    }

    if(!item.spv_note && !confirm('Approve tanpa catatan?')) return;
    
    router.post('/transaction/cycle-count/approve', {
        id: item.id, 
        material_id: item.material_id, 
        spv_note: item.spv_note
    }, {
        onSuccess: () => {
            item.status = 'APPROVED'; 
            alert('Item berhasil diapprove!');
        },
        onError: (errors) => {
            // Tangkap error dari controller validasi tadi
            alert('Gagal: Item mungkin belum disubmit.');
        }
    });
}

// Toggle history visibility
const toggleHistory = (index) => {
    form.items[index].showHistory = !form.items[index].showHistory;
}

// Repeat cycle count
const repeatCycleCount = (item) => {
    if (!confirm(`Apakah Anda yakin ingin mengulang cycle count untuk ${item.product_name}?`)) return;
    
    router.post('/transaction/cycle-count/repeat', {
        material_id: item.material_id,
        bin_id: item.bin_id
    }, {
        onSuccess: () => {
            alert('Cycle count baru berhasil dibuat!');
            router.reload();
        },
        onError: (errors) => {
            console.error(errors);
            alert('Gagal membuat cycle count baru.');
        }
    });
}
</script>