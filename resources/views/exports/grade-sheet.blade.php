<table border>
    {!! $thead !!}
    
    <tbody>
        @foreach($data as $key => $value)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$key}}</td>
                <td>{{$value['NIS']}}</td>
                @php 
                    $topicAvg = [];
                    $finalAvg = [];
                    $minMax = [];
                @endphp
                @foreach($totalTopic as $topicKey => $topicValue)
                    @php 
                        $sumatifAvg = [];
                    @endphp
                    @foreach($assessmentMethodSetting as $assessmentMethodValue)
                            @if(!empty($data[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]))
                                @php 
                                    $countSumatifAvg = array_sum($data[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]) / count($data[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]);
                                    array_push($sumatifAvg, $countSumatifAvg) 
                                @endphp
                                <td>{{$countSumatifAvg}}</td>
                            @else
                                <td></td>
                            @endif
                    @endforeach
                    @if(count($sumatifAvg))
                        <td>
                            @php
                                $countSumatifAvg = Helper::customRound(array_sum($sumatifAvg)/count($sumatifAvg));
                                array_push($topicAvg, $countSumatifAvg);
                                $minMax[$countSumatifAvg] = $topicValue;
                            @endphp
                            {{ $countSumatifAvg }}
                        </td>
                    @else 
                        <td></td>
                    @endif
                    <td></td>
                @endforeach
                <td>
                    @if(count($topicAvg)) 
                    @php
                        $countAvgAvg = Helper::customRound(array_sum($topicAvg)/count($topicAvg));
                        array_push($finalAvg,$countAvgAvg);
                    @endphp    
                        {{ $countAvgAvg }} 
                    @endif
                </td>
                <td>
                    @if(!empty($dataPAS[$key])) 
                        @php 
                            array_push($finalAvg,$dataPAS[$key]);
                        @endphp 
                        {{$dataPAS[$key]}} 
                    @endif
                </td>
                <td>
                    @if(!empty($finalAvg)) 
                        @php 
                            if(count($topicAvg) && !empty($dataPAS[$key])){

                            }
                            //Helper::customRound(array_sum($finalAvg)/count($finalAvg))
                            $finalAvg =Helper::customRound(($countAvgAvg*$avgDiv)+($dataPAS[$key]*$PASDiv));
                        @endphp 
                        {{ $finalAvg }}
                    @endif    
                </td>
                <td>
                    @if(!empty($minMax)) 
                        
                        @php 
                            arsort($minMax);
                            $desc = '';
							$previousPredicate = '';
                        @endphp

                        @foreach($minMax as $minMaxKey => $minMaxValue)
                            @php
								$check = $subjectDescription
								->where('topic_setting_id',$minMaxValue)
								->first();
								if($check != null){
									if($desc != ''){
										$separ = $check->is_english_description ? 'And ' : 'Dan ';
										$desc .= "<br>".Str::replace('[STUDENT_PREDICATE]', "<strong>".Helper::predicate($minMaxKey,$grade_minimum,$check->is_english_description, 'under')."</strong>", Str::replace('[STUDENT_NAME]', Str::title($key), $check->description));
									}else {
										$previousPredicate = Helper::predicate($minMaxKey,$grade_minimum,$check->is_english_description);
										$desc .= Str::replace('[STUDENT_PREDICATE]', "<strong>".Helper::predicate($minMaxKey,$grade_minimum,$check->is_english_description)."</strong>", Str::replace('[STUDENT_NAME]', Str::title($key), $check->description))."<br>";
									}
								}
							@endphp
                        @endforeach

                        {!! $desc !!}
                    @endif  
                </td>
            </tr>
        @endforeach
    </tbody>
</table>