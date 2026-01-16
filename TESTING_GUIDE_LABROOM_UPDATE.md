# Testing Guide - LabRoom.vue Modul Ajar Update

## 📋 Pre-Testing Checklist

- [ ] Pastikan database migration sudah di-run: `php artisan migrate`
- [ ] Pastikan backend server berjalan
- [ ] Pastikan frontend dev server berjalan
- [ ] Clear browser cache jika diperlukan
- [ ] Login sebagai siswa untuk test LabRoom.vue

---

## 🧪 Test Cases

### Test 1: Verifikasi API Endpoint

**Purpose:** Memastikan endpoint API mengembalikan struktur data yang benar

**Steps:**
1. Buka browser dev tools → Network tab
2. Navigasi ke LabRoom.vue untuk suatu kelas
3. Cari request ke `modulAjar/{classId}`
4. Lihat Response

**Expected Response Structure:**
```json
[
  {
    "id": 1,
    "judul": "...",
    "des": "...",
    "modul_id": 5,
    "modul_judul": "...",
    "modul_file_name": "file.pdf",
    "modul_file_path": "modul/file.pdf",
    "modul_extension": "pdf",
    "link_tambahan": "https://..."
  }
]
```

**✅ Pass Criteria:**
- Response berisi semua field yang diharapkan
- `modul_extension` adalah lowercase
- `modul_file_path` adalah valid path

**❌ Fail Criteria:**
- Field hilang atau null padahal seharusnya ada
- Extension tidak lowercase
- Path tidak valid

---

### Test 2: File Display & Download

**Purpose:** Memastikan file modul dapat di-download dengan benar

**Setup:**
- Pastikan ada minimal 1 modul dengan file

**Steps:**
1. Masuk ke LabRoom.vue untuk kelas yang punya modul
2. Lihat bagian "Modul Ajar"
3. Verifikasi file banner ditampilkan dengan icon
4. Klik pada file banner
5. Verify file bisa di-download (atau preview jika PDF di browser)

**Expected Behavior:**
- ✅ File icon muncul sesuai tipe file
- ✅ File name ditampilkan dengan benar
- ✅ Link file bisa di-klik
- ✅ File dapat di-download/preview

**Test Matrix:**

| File Type | Icon | Color | Downloadable | Status |
|-----------|------|-------|-------------|--------|
| .pdf | picture_as_pdf | red | ✅ | - |
| .ppt | slideshow | orange | ✅ | - |
| .pptx | slideshow | orange | ✅ | - |
| .doc | description | blue | ✅ | - |
| .docx | description | blue | ✅ | - |
| .xls | table_chart | green | ✅ | - |
| .xlsx | table_chart | green | ✅ | - |

---

### Test 3: Link Tambahan Display & Navigation

**Purpose:** Memastikan link tambahan ditampilkan dan dapat di-akses

**Setup:**
- Pastikan ada minimal 1 modul dengan `link_tambahan`
- Pastikan ada minimal 1 modul tanpa `link_tambahan`

**Steps:**
1. Masuk ke LabRoom.vue untuk kelas dengan modul
2. Scroll ke bagian "Modul Ajar"
3. Verifikasi modul dengan link_tambahan menampilkan bagian "🔗 Link Tambahan"
4. Verifikasi modul tanpa link_tambahan NOT menampilkan link section
5. Klik pada link yang ada
6. Verifikasi link terbuka di tab baru

**Expected Behavior:**
- ✅ Link section hanya tampil jika ada link
- ✅ Link text dan URL ditampilkan
- ✅ Icon external link muncul
- ✅ Link terbuka di tab baru (target="_blank")

**Examples:**
```
✅ Link: https://youtu.be/watch?v=xxx
✅ Link: https://drive.google.com/file/d/xxx
✅ Link: https://docs.google.com/presentation/d/xxx
✅ Link: https://github.com/example/repo
```

---

### Test 4: Conditional Rendering

**Purpose:** Memastikan element hanya muncul ketika seharusnya

**Setup:**
- Siapkan 3 modul:
  - Modul A: Punya file + punya link
  - Modul B: Punya file, tanpa link
  - Modul C: Tanpa file, punya link

**Test Cases:**

#### 4a: Modul Ajar Section Visibility
```
If moduls.length > 0 → Section should show
If moduls.length == 0 → Section should NOT show
```
✅ Verify section hanya tampil kalau ada modul

#### 4b: File Banner Visibility
```
If modul.modul_file_path → File banner should show
If NOT modul.modul_file_path → Error banner ("Modul tidak ditemukan")
```
✅ Verify file/error banner sesuai kondisi

#### 4c: Link Tambahan Visibility
```
If modul.link_tambahan → Link section should show
If NOT modul.link_tambahan → Link section should NOT show
```
✅ Verify link section hanya tampil kalau ada link

---

### Test 5: Judul & Deskripsi Display

**Purpose:** Memastikan informasi modul ditampilkan dengan benar

**Steps:**
1. Lihat LabRoom.vue
2. Verifikasi judul materi ajar ditampilkan (text-h6)
3. Verifikasi deskripsi ditampilkan di bawah judul (text-caption)
4. Scroll dan verifikasi formatting
5. Test dengan judul/deskripsi yang panjang

**Expected:**
- ✅ Judul bold/large (text-h6)
- ✅ Deskripsi lebih kecil (text-caption)
- ✅ Text wrap properly di mobile
- ✅ Readable dan tidak overlap

---

### Test 6: Absensi Still Works

**Purpose:** Memastikan fitur absensi tidak terpengaruh oleh perubahan

**Steps:**
1. Masuk LabRoom.vue untuk kelas dengan absensi aktif
2. Lihat section "Absensi"
3. Expand absensi section
4. Verify jam buka dan jam tutup ditampilkan
5. Klik "Absen" button jika available
6. Verifikasi absensi berfungsi normal

**Expected:**
- ✅ Absensi section expand/collapse
- ✅ Waktu absensi ditampilkan
- ✅ Absen button berfungsi
- ✅ Data absensi tersimpan

---

### Test 7: Penugasan Still Works

**Purpose:** Memastikan fitur penugasan tidak terpengaruh

**Steps:**
1. Masuk LabRoom.vue untuk kelas dengan penugasan
2. Lihat section "Penugasan"
3. Expand penugasan section
4. Verify tugas ditampilkan dengan judul dan soal
5. Klik untuk melihat detail tugas
6. Verifikasi bisa menambah jawaban/file tugas

**Expected:**
- ✅ Penugasan section expand/collapse
- ✅ Daftar tugas ditampilkan
- ✅ Bisa submit jawaban/file
- ✅ Data terupdate di database

---

### Test 8: Responsive Design

**Purpose:** Memastikan tampilan bagus di berbagai ukuran screen

**Test Sizes:**
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)
- [ ] Mobile Large (480x854)

**Expected:**
- ✅ Card tetap readable
- ✅ File banner tidak overflow
- ✅ Link section wrapping properly
- ✅ Icon dan text aligned
- ✅ Separator visible
- ✅ Click targets (link) accessible

**Mobile Specific:**
- ✅ Text readable tanpa zoom
- ✅ Links easily clickable
- ✅ No horizontal scroll needed

---

### Test 9: Empty States

**Purpose:** Verifikasi behavior ketika data kosong

**Scenarios:**

#### 9a: No Modul at All
```
Setup: Class dengan 0 modul
Expected: "Modul Ajar" section NOT visible
```

#### 9b: Modul but No File
```
Setup: Class dengan 1+ modul tapi semua tanpa file
Expected: "Modul tidak ditemukan" banner shown
```

#### 9c: Modul with Null Fields
```
Setup: Modul dengan judul=null, des=null, link_tambahan=null
Expected: Fields yang null tidak tampil atau tampil empty
```

---

### Test 10: Icon & Color Rendering

**Purpose:** Verifikasi icon dan warna sesuai tipe file

**Test Matrix:**

| File | Expected Icon | Expected Color | Visual Check |
|------|---------------|-----------------|-------------|
| file.pdf | picture_as_pdf | red | ✅ |
| slide.ppt | slideshow | orange | ✅ |
| slide.pptx | slideshow | orange | ✅ |
| doc.doc | description | blue | ✅ |
| doc.docx | description | blue | ✅ |
| data.xls | table_chart | green | ✅ |
| data.xlsx | table_chart | green | ✅ |
| unknown.xyz | file_present | grey | ✅ |

---

### Test 11: Browser Compatibility

**Browsers to Test:**
- [ ] Chrome/Chromium (Latest)
- [ ] Firefox (Latest)
- [ ] Safari (Latest)
- [ ] Edge (Latest)

**Expected:**
- ✅ All icons render properly
- ✅ Links work correctly
- ✅ No console errors
- ✅ Styling looks consistent

---

### Test 12: Console & Error Checking

**Purpose:** Ensure no JavaScript errors

**Steps:**
1. Open Browser Dev Tools → Console
2. Navigasi ke LabRoom.vue
3. Check untuk error messages
4. Verifikasi API calls successful

**Expected:**
- ✅ No red errors in console
- ✅ API request status 200
- ✅ No failed resource loads
- ✅ Warnings only (if any) acceptable

---

### Test 13: Data Persistence

**Purpose:** Verify data doesn't change unexpectedly

**Steps:**
1. Load LabRoom.vue page
2. Note down modul data displayed
3. Refresh page (F5)
4. Verify same data shows again
5. Wait 5 minutes, refresh again
6. Verify data still same

**Expected:**
- ✅ Data consistent across page loads
- ✅ No random changes
- ✅ Cache working properly

---

### Test 14: Performance Check

**Purpose:** Ensure page loads efficiently

**Setup:**
- Open DevTools → Performance tab
- Set throttling to "Fast 3G" (Optional but good)

**Metrics:**
- [ ] Page load time < 3 seconds
- [ ] First contentful paint < 2s
- [ ] No layout shifts
- [ ] Smooth scrolling
- [ ] Icon loading snappy

---

### Test 15: Edge Cases

#### 15a: Very Long File Names
```
Setup: File dengan nama >100 characters
Expected: Text wrap properly, not overflow
```

#### 15b: Very Long URLs
```
Setup: link_tambahan dengan URL yang sangat panjang
Expected: Text wrap, preview di bawah, accessible
```

#### 15c: Special Characters in Judul/Des
```
Setup: Judul dengan emoji, unicode, dll
Expected: Display correctly
```

#### 15d: Missing Data in Response
```
Setup: API return data dengan beberapa field null
Expected: No crash, graceful handling
```

---

## 📊 Test Report Template

```
Test Date: _______________
Tester: ___________________
Browser: _________________
OS: _____________________

Test Case | Result | Notes | Screenshot
-----------|--------|-------|----------
Test 1    | ✅/❌  |       |
Test 2    | ✅/❌  |       |
Test 3    | ✅/❌  |       |
...       | ...    | ...   | ...

Overall Status: ✅ PASS / ❌ FAIL

Issues Found:
1. 
2.
3.

Comments:
```

---

## 🐛 Debugging Tips

### If API Returns 404
- Check route: `/api/modulAjar/{classId}`
- Verify classId is correct and exists
- Check browser console Network tab

### If File Icon Not Showing
- Check file extension in response
- Verify Quasar icons library loaded
- Check browser console for icon errors
- Verify color name is valid Quasar color

### If Link Not Working
- Check URL format in database
- Verify link starts with http:// or https://
- Check if URL is still valid
- Try in new incognito tab

### If Layout Broken
- Check Quasar CSS loaded
- Check for conflicting CSS
- Try clearing cache (Ctrl+Shift+R)
- Check responsive classes applied

### If Data Not Updating
- Check API response in Network tab
- Verify axios call successful
- Check Vue DevTools state
- Try hard refresh of page

---

## ✅ Final Checklist

Before declaring update complete:

- [ ] All 15 test cases passed
- [ ] No console errors
- [ ] Absensi works
- [ ] Penugasan works
- [ ] File icons correct
- [ ] Links accessible
- [ ] Mobile responsive
- [ ] Performance acceptable
- [ ] Database migration run
- [ ] No breaking changes
- [ ] Feature parity achieved
- [ ] Documentation complete

---

## 📞 Support

If issues found during testing:
1. Document the issue in detail
2. Include step-to-reproduce
3. Include screenshot/video
4. Check CHANGELOG_LABROOM_MODUL_AJAR.md
5. Check console for error messages
6. Check database for modul_id, link_tambahan columns

