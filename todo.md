## Urgent

## Planned

## Need to think about it


- [x] Prevent all action before the admin set the school year and school term

- [x] Create a new custom student page for super admin, in that table loop data from StudentClassroom model insted of Student model

- [x] Di bagian Subject Users sediakan bulk untuk copy record ke school year tertentu dan school term tertentu, karna kita menghilangkan delete dan update action, maka untuk mempermudah user kita buat bulk action untuk clone, tapi user bisa pilih clonenya mau apa saja dan mau ganti yang diperlukan

- [x] Di bagian Homeroom Teacher sediakan bulk untuk copy record ke school year tertentu dan school term tertentu, karna kita menghilangkan delete dan update action, maka untuk mempermudah user kita buat bulk action untuk clone, tapi user bisa pilih clonenya mau apa saja dan mau ganti yang diperlukan

- [ ] Ada fitur naik kelas, nah saat naik kelas, user sebagai homeroom teacher juga akan otomatis dibuatkan homeroom teacher untuk tahun ajaran yang dipilih, sehingga admin tidak perlu repot

- [x] Ada bug di bagian HomeroomTeacherResource, masih bisa terjadi dupliakt data, karna rules untuk ngecek tidak tepat, coba nanti di lihat lagi (homeroom teacher kelas bisa memiliki 2 wali kelas di tahun ajaran dan semester yang sama)

- [x] Ada bug di bagian SubjectUserResource, masih bisa terjadi dupliakt data, karna rules untuk ngecek tidak tepat, coba nanti di lihat lagi (subject user kelas bisa memiliki 2 wali kelas di tahun ajaran dan semester yang sama)

- [x] As a main teacher, i want be able to print student progrees report so that i can give it to the student

- [x] X01 - As a subject teacher, i want be able to create assessment for specific student in assessment menu so that i can give the grading to that student

- [x] There is an error in ManageAssessments take a look at here $getHomeroomTeacherIds, i think it's just wrong logic, its done,already check  in 18 september 2023
- [ ] refactor code, in ManageAssessments inside the action dont do select table inside loop, instead, in thatloop, extract the classroom_id,school_year_id and school_term_id in array, then do select database after the loop, dont place the select in loop

- [x] Add seeder for the classroom data
- [x] Add seeder for the subject data 
- [x] X12 - Super admin cant access another page except if em already set the school year and term
- [x] X13 - Bug, saat user sedang mengisi form di modal, saat klik di luar modal tertutup dan data hilang
- [x] X14 - Dibagian Assessments grading, validasi hanya bisa ngisi angka, minimal 0 dan maximal 100
- [x] (important)Feature, tambah filter di menu assessments, pilih hanya topic tertentu, kelas, dan subject dan sebagainya
- [x] Pasang ke semua master data feature soft delete untuk menghindari data hilang
- [x] add master data for setting school information
- [ ] add shoutout plugin for giving information,if the superadmin havent setting the school year and school term
- [ ] please, refarcor the active method on school year model and school term model
- [ ] please refactor code in helper for getSchoolYearName and getSchoolTermName
- [ ] ada bug di AssessmentResouce di bagian student.active_classroom_name kolom, saat disearchakanada error,  cobapikirkanapakah bisa menggunakan belongstothrough
- [x] ada bug,dibagian print, jadi kita berhasil memasukkan hanya 1 jenis assessment method , coba di lihat di web.php di bagian printnya,semisal kita masukkan nilai kinerja terlebih dahulu dan nilai monthly testnya ngg muncul
- [x] add import feature in student page inside main teacher menu
- [x] Feature for change password and name
- [x] Xgg Add feature when press arrow down or up to next and previous text input, also when hit enter and tab it will go to the another number text input
- [ ] add sebuah tombol jika tahun ajaran atau semester yang aktif sudah beda dengan data yang ada, maka ada tombol untuk menambahkan data tersebut sesuai dengan tahun ajaran baru, semisal di menu subject user dan homeroom teacher, jadi saat perpindahan tahun ajaran atau semester, kita tidak usah buat baru input, tapi tinggal klik tombol inline atau pub bulk, untuk tambah ke database dengan data yang sama namun tahun ajaraun atau semester yang sesuai dengan yang sedang aktif sekarang, fitur ini ada hubungannya sama fitur naik kelas siswa, begitu juga dengan siswa
- [ ] di menu student yang di pegang oleh main teacher, nanti main teacher bisa pilih tahun ajaran kapan dan semester berapayang ingin di tampilkan studentnya, buat sebuah filter, ini ada hubungannya sama task yang di atas ini
- [ ] saat nambah guru bisa sekalian nambha mapel nya, bgitu juga sebaliknya, jika nambah mapel; bisa sebaliknyua
- [x] dikarenakan studentbisa punya beberapa kelas, jadi saat di print kelasnya kadang tidak mengambil kelas yang sebenarnya, semisal ada mathhew 1 dan matthew 1 - maths, nah harusnya yg tampil matthew 1 bukan matthew 1 - maths di raport
- [?] bug di bagian hitung rata", tolong di perbaiki lagi script untukhitung rata" ini disebabkan karna matthew 1 dan matthew 1 - maths -> untuk mengatasi ini hapus nilai dari guru yang mengajar sama, nanti akan otomatis ngambil dari guru yg baru


- [ ] nanti di dashboard dibuat pilihan school year dan school term, supaya bisa milih, mau bekerjadi data yang mana, namun secara default menggunakan school year dan term yg active, nanti klau user milih ganti di dashboard maka simpan di session, kemudian, di global boot custom function yang akan kita buatkitacek apakah ada session yang aktif? klau ada pakai, kalau tidak gunakan school year dan term yang aktif



IMPORTANT
- [x] di bagian student semester evaluation hanya bisa  sekali saja membuat penilain PAS, tidak bolehlebih dari 1, lakukanbpengecekan saat pembuatan, silahkan ditanya apakah bisa buat 2 PAS?  -> hanya bisa 1 nilai assessment
- [x] ada bug saat  print raport, description  tidak muncul, contoh matematika dimatthew 1 (sudah solve di query  di tambah withoutGlobalScope)
- [x] buat versi inggris di subject description
- [x] buat contoh result saat guru membuat subject description, supaya bisa membayangkan contoh output di raport
- [x] character report bisa pilih anak yg mana dan karakter yang mana saat membuat caharctr raport
- [x] show or hide fase in print raport ->meta name = show_fase, set 0 if u dont want to show the fase
- [x] show or hide kkm in print raport -> meta name = show_top_kkm, set 0 if you want to show kkm in table, set the global kkm if you want to show top kkm
- [x] nilai akhir bulat di raport
- [x] every teacher can see the report sheet.
- [x] random character description, pelajari di print-report-character
- [x] make assessment and student evaluation pagination, load 1 first then maximal 10.
- [x] import student details
- [x] report sheet, harus link ke raport karakter.
- [x] di subject descirpiton kasih juga contoh klo siswa dibawah kkm
- [ ] inform filament ada bug, untuk penggunaan gourp di modal, liihat di menu subject descirption
- [x] error di menu school setting saat create meta
- [x] export student bermasalah
- [x] import student bermasalah
- [ ] add custom text in required confriamtion action
- [x] di live app, ada error klo siswa beda jumlah mapel, mapel yang beda ngg tampil
- [x] export report sheet as excel
- [x] buat grade sheet as excel
- [x] deskripsi kalau semua nilai rendah, semisal 64 dan 60, perlu bimbingan semua
- [x] raport basic curiculum descrpsiny apakah sama dengan yang lain?
- [x] raport basic curiculum ngga pakai kkm jga? pakai tetap dimunculkan
- [x] nilai report sheet di system beda dengan grade sheet di excel
- [x] import joinet class dan joinet at
- [x] raport character dibuat pengisian seperti dibuku character
- [x] report sheet error di tc rinte matthew 1, atas nama zebeedee, | fix, penilaian harus ada isi jangan null
- [x] hapus tombol delete di extracurricular
- [ ] bisa export report sheet via select student?
- [ ] bisa export grade sheet via select student?


#Need to be done
- [x] Tyndell perbedaan di grade sheet and raport sheet.
- [x] cek dari grade sheet, raport sheet, raport, raport character


## HOWTO
1. git fetch origin && git merge origin/main && composer2 install
2. php artisan migrate
3. php artisan shield:generate --resource=SubjectDescriptionResource,StudentDescriptionResource,StudentSemesterEvaluationResource,ReligionResource,ExtracurricularResource,StudentClassroomResource,AspectResource,HabitResource,CharacterReportResource,RangeCharacterDescriptionResource,CharacterDescriptionResource,RaportConfigResource, SubjectGroupResource -> for creating permission and policy file for SubjectDescriptionResource
4. git restore .

## IMPORTANT TO INFORM
- TIngkat SMP dan SMA harus set  KKM yang sama semua guru
- after we finish open the aplication then login with super admin, then go to the student classroom menu, and click student sync
- agar description tampil, guru harus membuat semua description  untuk tiap topic
- untuk moving class, moving class itu adalah kelas yang bukan kelas normal, contoh ada kelas matthew A untuk maths, nah siswanya bisa dari matthew 1,2,3 makanya itu namanya moving class, jika ada guru yg ngajar di moving class, jangan lupa admin masukin siswa siapa aja yg masuk di moving class di menu student classroom
- semua mapel harus masuk sebuah subject group


There is 3 ways to use laravel global scope

1. Create new laravel global scope in new class and use it in the specific model
2. Anonymouse/closure ways, you can define your global scope direcly in your specific model
3. lokal scope if you you think thats is optional or its simple as you want to sort the popular person, you can create something like 