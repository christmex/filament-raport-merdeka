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
                @endphp
                @foreach($totalTopic as $topicKey => $topicValue)
                    @php 
                        $sumatifAvg = [];
                    @endphp
                    @foreach($assessmentMethodSetting as $assessmentMethodValue)
                            @if(!empty($data[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]))
                                <td>{{array_sum($data[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name])}}</td>
                                @php array_push($sumatifAvg, array_sum($data[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name])) @endphp
                            @else
                                <td></td>
                            @endif
                    @endforeach
                    @if(count($sumatifAvg))
                        <td>
                            @php
                                $countSumatifAvg = Helper::customRound(array_sum($sumatifAvg)/count($sumatifAvg));
                                array_push($topicAvg, $countSumatifAvg);
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
                        {{ Helper::customRound(array_sum($finalAvg)/count($finalAvg)) }}
                    @endif    
                </td>
                <td>SOON</td>
            </tr>
        @endforeach
    </tbody>
</table>