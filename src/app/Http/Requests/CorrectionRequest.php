<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'remarks' => ['required', 'string', 'max:255'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'rest_start_times.*' => ['required'],
            'rest_end_times.*' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->start_time;
            $end = $this->end_time;

            if ($start && $end && $start >= $end) {
                $validator->errors()->add('attendance_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            $restStarts = $this->rest_start_times ?? [];
            $restEnds = $this->rest_end_times ?? [];

            foreach ($restStarts as $index => $rStart) {
                $rEnd = $restEnds[$index] ?? null;

                if ($rStart && ($rStart < $start || $rStart > $end)) {
                    $validator->errors()->add("rest_start_times.$index", '休憩時間が不適切な値です');
                }

                if ($rEnd && $rEnd > $end) {
                    $validator->errors()->add("rest_end_times.$index", '休憩時間もしくは退勤時間が不適切な値です');
                }
            }
        });
    }
}
