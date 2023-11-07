<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Print</title>
	<style>
		.page-break {
			page-break-after: always;
		}
		.heading_progress_title,
		.heading_progress_title_cover {
			text-align: center;
			margin-top: 0;
		}
		.heading_progress_title {
			background-color: #FCD5B4;
		}


		/* BASIC TEXT */
		.logoB {
			color: red;
			font-weight: 700;
			margin-left: 0px!important;
		}
		.logoA {
			color: #00B050;
			font-weight: 700;
		}
		.logoS {
			color: #0070C0;
			font-weight: 700;
		}
		.logoI {
			color: yellow;
			font-weight: 700;
		}
		.logoC{
			color: #7030A0;
			font-weight: 700;
		}

		.logoB,
		.logoA,
		.logoS,
		.logoI,
		.logoC {
			display: inline-block;
			margin-left: -4px;
			line-height: 0;
		}

		/* BASIC TEXT */

		/* STUDENT DETAILS */
		table tr td {
			padding: 5px
		}
		/* STUDENT DETAILS */

		/* GRADE  SECTION */
		#grade table td,
		#grade table th,
		#extracurricular table td,
		#extracurricular table th,
		#absence table td,
		#absence table th
		{
			border: 1px solid black;
			text-align: center;
			padding: 0;
			color: black;
			span-size: 10.0pt;
			span-weight: 400;
			span-style: normal;
			text-decoration: none;
		}

		#grade table,
		#extracurricular table,
		#absence table
		{
			border: 1px solid black;
			margin-top: 10px;
			table-layout: fixed;
		}
		/* GRADESECTION */

		
        /* TUTWURI LOGO */
        #tutwuri_logo, 
        #student_name {
            margin: 0 auto;
            display: block;
            text-align: center;
        }
        /* TUTWURI LOGO */

		/* Global class */
		table {
			width: 100%;
			/* border: 1px solid black; */
			border-collapse: collapse;
			
		}
		@page {
            margin: 21px 0;
            size: 215mm 330mm;
        }
		body { 
			margin: 21px 21px 21px ; 
			-webkit-print-color-adjust:exact !important;
  			print-color-adjust:exact !important;
			size: 215mm 330mm;
		}
		img {
            width: 250px;
            height: 250px;
            margin: 50px auto!important;
        }
        p {
            margin: 10px auto!important;
        }
		@media print {
			#fase {
				border: none
			}
            footer {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: center;
                padding: 10px;
            }
            body {
                size: 215mm 330mm;
            }
		}
		table#details_ {
			border-collapse:separate; 
  			border-spacing: 0 .5em;
		}

		#fase {
			border: none
		}
		footer {
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			text-align: center;
			padding: 10px;
		}

		/* Global class */
	</style>
</head>
<body>

	<h1 class="heading_progress_title_cover">RAPOR PESERTA DIDIK</h1>
	<h1 class="heading_progress_title_cover">
        {{Helper::getSchoolSetting()->school_name_prefix}}
        <span class="logoB">B</span>
        <span class="logoA">A</span>
        <span class="logoS">S</span>
        <span class="logoI">I</span>
        <span class="logoC">C</span>
        <span class="font511880">{{Helper::getSchoolSetting()->school_name_suffix}}</span>
    </h1>

    <div style="text-align: center;">
        <img src="{{asset('tutwuri.png')}}" alt="" id="tutwuri_logo">
    </div>

    <p id="student_name">Nama Peserta Didik :</p>
    <h1 class="heading_progress_title_cover">{{Str::title($student->student_name)}}</h1>

    <section style="margin-bottom: 20px; margin-top: 40px">
        <p id="student_name">NIS / NISN</p>
        <h1 class="heading_progress_title_cover">{{$student->student_nis}}/{{$student->student_nisn}}</h1>
    </section>
    <div style="text-align: center;">
        <img src="{{asset('logo_basic.jpg')}}" alt="" id="tutwuri_logo">
    </div>

	<footer class="heading_progress_title_cover"><h2>Kementerian Pendidikan dan Kebudayaan<br>Republik Indonesia</h2></footer>

	<div class="page-break"></div>

	<h1 class="heading_progress_title">Identitas Peserta Didik</h1>
	<section id="student_details">
		<div style="float: left; width: 100%;">
			<table id="details_">
				<tr>
					<td style="width: 20px">1.</td>
					<td style="width:300px">{{Str::title('Nama lengkap peserta didik')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->student_name)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">2.</td>
					<td style="width:300px">NIS/NISN</td>
					<td style="width: 5px">:</td>
					<td>{{$student->student_nis}}/{{$student->student_nisn}}</td>
				</tr>
				<tr>
					<td style="width: 20px">3.</td>
					<td style="width:300px">{{Str::title('tempat, tanggal lahir')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->born_place)}}, {{Str::title($student->born_date)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">4.</td>
					<td style="width:300px">{{Str::title('jenis kelamin')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty($student->sex) ? Str::title(Helper::getSex($student->sex)) : "-"}}</td>
				</tr>
				<tr>
					<td style="width: 20px">5.</td>
					<td style="width:300px">{{Str::title('Agama')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->religion->name)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">6.</td>
					<td style="width:300px">{{Str::title('Status dalam keluarga')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->status_in_family)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">7.</td>
					<td style="width:300px">{{Str::title('Anak ke')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->sibling_order_in_family)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">8.</td>
					<td style="width:300px">{{Str::title('Alamat peserta didik')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->address)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">9.</td>
					<td style="width:300px">{{Str::title('nomor telepon rumah/HP')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->phone)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">10.</td>
					<td style="width:300px">{{Str::title('pendidikan sebelumnya')}}</td>
					<td style="width: 5px">:</td>
					<td>{{Str::title($student->previous_education)}}</td>
				</tr>
				<tr>
					<td style="width: 20px">11.</td>
					<td style="width:300px">{{Str::title('orang tua')}}</td>
					<td style="width: 5px"></td>
					<td></td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">a. {{Str::title('Nama ayah')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->father_name)) ? Str::title($student->father_name) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">b. {{Str::title('Nama ibu')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->mother_name)) ? Str::title($student->mother_name) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">c. {{Str::title('alamat')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->parent_address)) ? Str::title($student->parent_address) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">c. {{Str::title('nomor telepon/HP')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->parent_phone)) ? Str::title($student->parent_phone) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px">12.</td>
					<td style="width:300px">{{Str::title('pekerjaan orang tua')}}</td>
					<td style="width: 5px"></td>
					<td></td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">a. {{Str::title('ayah')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->father_job)) ? Str::title($student->father_job) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">b. {{Str::title('ibu')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->mother_job)) ? Str::title($student->mother_job) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px">13.</td>
					<td style="width:300px">{{Str::title('wali peserta didik')}}</td>
					<td style="width: 5px"></td>
					<td></td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">a. {{Str::title('nama wali')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->guardian_name)) ? Str::title($student->guardian_name) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">b. {{Str::title('nomor telepon/HP')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->guardian_phone)) ? Str::title($student->guardian_phone) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">c. {{Str::title('Alamat')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->guardian_address)) ? Str::title($student->guardian_address) : "-"  }}</td>
				</tr>
				<tr>
					<td style="width: 20px"></td>
					<td style="width:300px">d. {{Str::title('Pekerjaan')}}</td>
					<td style="width: 5px">:</td>
					<td>{{!empty(Str::title($student->guardian_job)) ? Str::title($student->guardian_job) : "-"  }}</td>
				</tr>
				
			</table>
		</div>
	</section>
	<div style="clear: both;"></div>
	<section id="student_photo" style="float:right; margin-right: 100px;margin-top: 30px">
		<div style="display: inline-block;border:1px solid black;width: 120px;height: 150px;text-align:center; line-height: 100px;margin-right: 10px">
			Pas foto
			3 x4
		</div>
		<section id="sign_principle" style="margin: 5% auto 0; display:inline-block">
			<div class="sign_top">
				<p>Batam, {!!Helper::getSchoolSetting()->school_progress_report_date!!} </p>
				<p>Kepala Sekolah
					{{Helper::getSchoolSetting()->school_name_prefix}}
					<span class="logoB">B</span>
					<span class="logoA">A</span>
					<span class="logoS">S</span>
					<span class="logoI">I</span>
					<span class="logoC">C</span>
					<span class="font511880">{{Helper::getSchoolSetting()->school_name_suffix}}</span>
				</p>
			</div>

			<div class="border_sign" style="margin-top: 60px">
				<p style="text-decoration:underline;text-decoration-thickness: 1px; text-underline-offset: 8px;">{{Helper::getSchoolSetting()->school_principal_name}}</p>
			</div>
		</section>
	</section>
	<div style="clear: both;"></div>
	<footer class=""><h2>Vision : To Know God and God is Known</h2></footer>



	<div class="page-break"></div>
	<!--  -->
	<h1 class="heading_progress_title">Laporan Hasil Belajar Siswa</h1>
	<section id="student_details">
		<div style="float: left; width: 70%;">
			<table>
				<tr>
					<td style="width: 100px">School Name</td>
					<td style="width: 5px">:</td>
					<td>{{Helper::getSchoolSetting()->school_name_prefix}}
						<span class="logoB">B</span>
						<span class="logoA">A</span>
						<span class="logoS">S</span>
						<span class="logoI">I</span>
						<span class="logoC">C</span>
						<span class="font511880">{{Helper::getSchoolSetting()->school_name_suffix}}</span>
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td style="width: 5px">:</td>
					<td>{{Helper::getSchoolSetting()->school_address}}</td>
				</tr>
				<tr>
					<td>Student Name</td>
					<td style="width: 5px">:</td>
					<td><strong>{{Str::title($student->student_name)}}</strong></td>
				</tr>
				<tr>
					<td>NIS/NISN</td>
					<td style="width: 5px">:</td>
					<td>{{$student->student_nis}}/{{$student->student_nisn}}</td>
				</tr>
			</table>
		</div>
		<div style="float: right; width: 30%;">
			<table>
				<tr>
					<td>Class</td>
					<td>:</td>
					<td>{{Helper::numberToRomawi($student->active_classroom_level)}} {{$student->active_classroom_name}}</td>
				</tr>
				<tr>
					<td>Semester</td>
					<td>:</td>
					<td>{{Helper::getSchoolTermName()}}</td>
				</tr>
				<tr>
					<td>Fase</td>
					<td>:</td>
					<td><input type="text" id="fase" style="display:block;width:10px" value="{{request('fase','A')}}"></td>
				</tr>
				<tr>
					<td>Annual Study</td>
					<td>:</td>
					<td>{{Helper::getSchoolYearName()}}</td>
				</tr>
			</table>
		</div>
	</section>
	<div style="clear: both;"></div>
	
	<section id="grade" style="margin-top:20px;">
		<table>
			<thead>
				<tr>
					<th style="vertical-align: middle;width: 5%">No</th>
					<th style="vertical-align: middle;width: 30%">Mata Pelajaran</th>
					<th style="vertical-align: middle;width: 10%">KKM</th>
					<th style="vertical-align: middle;width: 15%">Nilai Akhir</th>
					<th style="vertical-align: middle;width: 40%">Deskripsi</th>
				</tr>
			</thead>
			<tbody>
				@foreach($newData as $key => $value)
				<!-- <tr draggable="true"> -->
				<tr>
					<td>{{$loop->iteration}}</td>
					<td style="text-align: left; padding: 5px">{{$key}}</td>
					<td>{{$value['KKM']}}</td>
					<td>{{Helper::countFinalGrade($value['AVG'],$value['PAS'],$avgDiv, $PASDiv)}}</td>
					<td style="text-align: left; padding: 5px; word-wrap: break-word;">
						@php 
							$desc = '';
						@endphp
						@foreach($value['minMax_topic_id'] as $MixMaxKey => $MixMaxValue)
							@php
								$check = $subjectDescription->where('topic_setting_id',$MixMaxKey)
								->where('subject_user_id', $value['subject_user_id'])
								->where('range_start', '<=', $MixMaxValue)
								->where('range_end', '>=', $MixMaxValue)
								->first();
								if($check != null){
									if($desc != ''){
										$desc .= $check->description;
									}else {
										$desc .= $check->description."<br><br>";
									}
								}else {
									$desc .= "loremloremloremloremloremloremloremloremloremloremloremloremloremloremloremloremloremloremloremloremloremloremlorem";
								}
							@endphp
						@endforeach
						
						{!! Str::replace('[STUDENT_NAME]', Str::title($student->student_name), $desc) !!}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</section>

	<section id="extracurricular" style="margin-top:20px;">
		<table>
			<thead>
				<tr>
					<th style="vertical-align: middle;width: 5%">No</th>
					<th style="vertical-align: middle;width: auto">Ekstrakurikuler</th>
					<th style="vertical-align: middle;width: auto">Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<tr draggable="true">
					<td>1</td>
					<td style="text-align: left; padding: 5px">Modern Dance</td>
					<td style="text-align: left; padding: 5px">asss</td>
				</tr>
			</tbody>
		</table>
	</section>

	<section id="absence" style="margin-top:20px;">
		<h3 style="margin: 10px 0 10px">Ketidakhadiran</h3>
		<table>
			<thead>
				<tr>
					<th style="vertical-align: middle;width: 33.33%;">Sakit</th>
					<th style="vertical-align: middle;width: 33.33%;">Ijin</th>
					<th style="vertical-align: middle;width: 33.33%;">Tanpa Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<tr draggable="true">
					<td>1</td>
					<td>1</td>
					<td>1</td>
				</tr>
			</tbody>
		</table>
	</section>

	<section id="sign" style="margin-top: 50px" >
		<div id="sign_parent" style="float:left;width:30%;">
			<div class="sign_top" style="margin-bottom:80px">
				<p>Mengetahui</p>
				<p>Orang Tua/Wali</p>
			</div>

			<div class="border_sign">
				<p style="visibility:hidden">parentsign</p>
				<hr style="margin-top:-10px;display: block">
			</div>
		</div>
		<div id="sign_main_teacher" style="float:right;width:auto;">
			<div class="sign_top" style="margin-bottom:80px">
				<p>Batam, {!!Helper::getSchoolSetting()->school_progress_report_date!!}</p>
				<p>Wali Kelas</p>
			</div>

			<div class="border_sign">
				<p>{{auth()->user()->name}}</p>
				<hr style="margin-top:-10px;display: block">
			</div>
		</div>
	</section>
	<div style="clear: both;"></div>

	<section id="sign_principle" style="text-align:center; margin: 5% auto 0">
	
		<div class="sign_top">
			<p>Mengetahui</p>
			<p>Kepala Sekolah
				{{Helper::getSchoolSetting()->school_name_prefix}}
				<span class="logoB">B</span>
				<span class="logoA">A</span>
				<span class="logoS">S</span>
				<span class="logoI">I</span>
				<span class="logoC">C</span>
				<span class="font511880">{{Helper::getSchoolSetting()->school_name_suffix}}</span>
			</p>
		</div>

		<div class="border_sign" style="margin-top: 100px">
			<!-- <p style="text-decoration:underline">Rudi wanro situmorang</p> -->
			<p style="text-decoration:underline;text-decoration-thickness: 1px; text-underline-offset: 8px;">{{Helper::getSchoolSetting()->school_principal_name}}</p>
			<!-- <hr> -->
		</div>
	</section>

	
	<footer class=""><h2>Vision : To Know God and God is Known</h2></footer>

	<!-- <div class="page-break"></div> -->
	
</body>
</html>