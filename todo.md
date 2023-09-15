- [x] Prevent all action before the admin set the school year and school term

- [ ] Create a new custom student page for super admin, in that table loop data from StudentClassroom model insted of Student model

- [ ] Di bagian Subject Users sediakan bulk untuk copy record ke school year tertentu dan school term tertentu, karna kita menghilangkan delete dan update action, maka untuk mempermudah user kita buat bulk action untuk clone, tapi user bisa pilih clonenya mau apa saja dan mau ganti yang diperlukan

- [ ] Di bagian Homeroom Teacher sediakan bulk untuk copy record ke school year tertentu dan school term tertentu, karna kita menghilangkan delete dan update action, maka untuk mempermudah user kita buat bulk action untuk clone, tapi user bisa pilih clonenya mau apa saja dan mau ganti yang diperlukan

- [ ] Ada fitur naik kelas, nah saat naik kelas, user sebagai homeroom teacher juga akan otomatis dibuatkan homeroom teacher untuk tahun ajaran yang dipilih, sehingga admin tidak perlu repot

- [x] Ada bug di bagian HomeroomTeacherResource, masih bisa terjadi dupliakt data, karna rules untuk ngecek tidak tepat, coba nanti di lihat lagi (homeroom teacher kelas bisa memiliki 2 wali kelas di tahun ajaran dan semester yang sama)

- [x] Ada bug di bagian SubjectUserResource, masih bisa terjadi dupliakt data, karna rules untuk ngecek tidak tepat, coba nanti di lihat lagi (subject user kelas bisa memiliki 2 wali kelas di tahun ajaran dan semester yang sama)

- [ ] As a main teacher, i want be able to print student progrees report so that i can give it to the student

- [ ] As a subject teacher, i want be able to create assessment for specific student in assessment menu so that i can give the grading to that student

- [ ] There is an error in ManageAssessments take a look at here $getHomeroomTeacherIds