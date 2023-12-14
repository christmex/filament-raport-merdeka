@php 
	$getSchoolSetting = App\Models\SchoolSetting::first();
	$getOnlyFinalAvgAcademic = Helper::getOnlyFinalAvgAcademic($finalNewData,$avgDiv, $PASDiv);
	
@endphp
<table>
	<thead>
		<tr>
			@foreach($tableHeader as $header)
				@if($header == 'No')
				<th style="">{{$header}}</th>
				@elseif(Str::startsWith($header,'Nama Siswa'))
				<th style="">{{$header}}</th>
				@elseif(Str::startsWith($header,'Seni Budaya'))
				<th style="">Seni Budaya</th>
				@else
				<th style=""> {{$header}}</th>
				@endif
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($finalNewData as $student_name => $subjects )
			@php 
				$avg = [];
			@endphp 
			<tr>
				<td>{{$loop->iteration}}</td>
				<td style="text-align: left;padding-left: 2px">{{$student_name}}</td>
				@foreach($subjects as $subjectKey => $subjectValue )
					@php array_push($avg, Helper::countFinalGrade($subjectValue['AVG'],$subjectValue['PAS'],$avgDiv, $PASDiv)); @endphp
					<td>{{ Helper::customRound(Helper::countFinalGrade($subjectValue['AVG'],$subjectValue['PAS'],$avgDiv, $PASDiv))}}</td>
				@endforeach
				@php
					//$countFinalAvgAcademic = round(array_sum($avg) / count($avg),1)/10;
					$countFinalAvgAcademic = array_sum($avg) / count($avg)/10;
					if(count($getStudentCharacter) == count($finalNewData)){
						$countFinalAvgCharacter = Helper::generateCharacterAvg($getStudentCharacter[$student_name]);
					}else {
						$countFinalAvgCharacter = 'unavailable';
					}
				@endphp
				<td>{{ Helper::customRound($countFinalAvgAcademic) }}</td>
				<td> {{ $countFinalAvgCharacter == 'unavailable' ? $countFinalAvgCharacter : Helper::customRound($countFinalAvgCharacter) }}</td>
				@if(count($getStudentCharacter) == count($finalNewData))
					<td>{{Helper::customRound(($countFinalAvgAcademic*75/100+$countFinalAvgCharacter*25/100*2.5))}}</td>
				@else
					<td>Unavailable</td>
				@endif

				<td>
					@php
						if(count($getStudentCharacter) == count($finalNewData)){
							$getRank = Helper::generateRank($getStudentCharacter, $getOnlyFinalAvgAcademic)[$student_name];
						}else {
							$getRank = 'unavailable.';
						}
					@endphp
					
					{{$getRank}}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
