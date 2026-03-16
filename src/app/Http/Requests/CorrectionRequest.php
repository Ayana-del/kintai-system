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
            'reason' => ['required', 'string', 'max:255'],
            'check_in' => ['required'],
            'check_out' => ['required'],
            'rest_start_times.*' => ['required'],
            'rest_end_times.*' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'reason.required' => '備考を記入してください',
            'check_in.required' => '出勤時間を入力してください',
            'check_out.required' => '退勤時間を入力してください',
            'rest_start_times.*.required' => '休憩開始時間を入力してください',
            'rest_end_times.*.required' => '休憩終了時間を入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->check_in;
            $end = $this->check_out;

            if ($start && $end && $start >= $end) {
                $validator->errors()->add('check_in', '出勤時間もしくは退勤時間が不適切な値です');
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
